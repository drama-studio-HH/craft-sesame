<?php

namespace thedrama\craftsesame\services;

use Craft;
use craft\elements\User;
use DateTime;
use thedrama\craftsesame\events\EmailEvent;
use thedrama\craftsesame\events\LoginEvent;
use thedrama\craftsesame\events\MailEvent;
use thedrama\craftsesame\events\ModifyTemplateEvent;
use thedrama\craftsesame\events\RegisterAllowedHostsEvent;
use thedrama\craftsesame\Sesame;
use thedrama\craftsesame\models\AuthenticationData;
use thedrama\craftsesame\models\AuthenticationResponse;
use thedrama\craftsesame\models\LoginRequestResponse;
use thedrama\craftsesame\records\AuthenticationRecord;
use yii\base\Component;

class AuthenticationService extends Component
{
    public const EVENT_REGISTER_ALLOWED_HOSTS_EVENT = 'registerAllowedHosts';
    public const EVENT_MODIFY_TEMPLATE_EVENT = 'modifyTemplate';
    public const EVENT_BEFORE_SEND_MAIL_EVENT = 'beforeSendMail';
    public const EVENT_AFTER_LOGIN_EVENT = 'afterLogin';

    // use Craft's random string method for generating tokens, for URL tokens and for user passwords
    public function getToken(int $length = 32): string
    {
        return Craft::$app->getSecurity()->generateRandomString($length);
    }

    // check if a given email can login or register
    public function requestLogin(string $email): LoginRequestResponse
    {
        // a user could already exist for the given email
        $user = Craft::$app->getUsers()->getUserByUsernameOrEmail($email);

        $response = new LoginRequestResponse();
        $settings = Sesame::getInstance()->settingsService->getSettings();

        // if the email does not have an associated user, and user registration is not allowed, fail
        if (!$user && !$settings->allowUserRegistration) {
            $response->success = false;
            return $response;
        }

        $config = Craft::$app->config->getConfigFromFile('sesame');
        $allowedHosts = array_merge($config['allowedHosts'] ?? [], $settings->getAllowedHosts());

        $event = new RegisterAllowedHostsEvent([
            'hosts' => $allowedHosts,
        ]);

        // allow other plugins or modules to modify the allowed hosts
        $this->trigger(self::EVENT_REGISTER_ALLOWED_HOSTS_EVENT, $event);
        $allowedHosts = $event->hosts;

        // empty list for allowed hosts implies that any email domain is allowed
        if (count($allowedHosts) == 0) {
            $response->success = true;
        } else {
            // retrieve the TLD from the email address, and check for its presence in the configured hosts
            $hostParts = explode('.', substr($email, strrpos($email, '@') + 1));
            $response->success = in_array(join('.', array_slice($hostParts, -2, 2)), $allowedHosts);
        }

        if ($user) {
            $response->user = $user;
            // a user could have previously registered with a domain which since then has been removed from the allowed hosts list
            // in this case, they should still be allowed to log in
            $response->success = true;
        }

        return $response;
    }

    // persist the authentication request to the database, and send the Login mail
    public function storeAuth(string $email): void
    {
        $user = User::find()->email($email)->one();
        $settings = Sesame::getInstance()->settingsService->getSettings();
        $token = $this->getToken();
        if ($user) {
            $record = new AuthenticationRecord();
            $record->userId = $user->id;
            $record->token = $token;
            $record->lifetime = $settings->lifetime; // load the lifetime from the plugin settings
            $record->dateCreated = new DateTime();

            $template = Craft::$app->view->renderTemplate('sesame/email/login', [
                'token' => $token
            ]);

            $templateEvent = new ModifyTemplateEvent([
                'template' => $template,
            ]);

            // allow other plugins to make modifications to the email template
            $this->trigger(self::EVENT_MODIFY_TEMPLATE_EVENT, $templateEvent);
            $template = $templateEvent->template;

            $mail = Craft::$app
                ->getMailer()
                ->compose()
                ->setTo($email)
                ->setSubject(Craft::t('sesame', 'Your Login for {siteName}', ['siteName' => Craft::$app->getSites()->currentSite->name]))
                ->setHtmlBody($template);

            $mailEvent = new MailEvent([
                'mail' => $mail
            ]);

            // allow other plugins to make modifications to the sent mail
            $this->trigger(self::EVENT_BEFORE_SEND_MAIL_EVENT, $mailEvent);
            $mail = $mailEvent->mail;

            $mail->send();

            $record->save(false);
        }
    }

    // for a given auth record, check if it can authenticate
    public function handleAuthRequest(?AuthenticationRecord $authRecord): AuthenticationResponse
    {
        $response = new AuthenticationResponse();

        if (!$authRecord) {
            $response->success = false;
            $response->errors[] = ['message' => 'The specified token does not exist.'];
            return $response;
        }

        if ($authRecord->tokenUsed) {
            $response->success = false;
            $response->errors[] = ['message' => 'The specified token is already used.'];
            return $response;
        }

        if ($authRecord->getIsExpired()) {
            $response->success = false;
            $response->errors[] = ['message' => 'The specified token is expired.'];
            return $response;
        }

        $response->success = true;
        $response->redirectUrl = Sesame::getInstance()->settingsService->getRedirectUrl();

        return $response;
    }

    // log the user in, and consume the token if the login was successful
    public function login(AuthenticationRecord $authRecord): bool
    {
        $user = Craft::$app->getUsers()->getUserById($authRecord->userId);

        $success = Craft::$app->getUser()->login($user);

        $loginEvent = new LoginEvent([
            'user' => $user,
            'success' => $success,
        ]);

        // let plugins decide if the login attempt should be successful or not
        $this->trigger(self::EVENT_AFTER_LOGIN_EVENT, $loginEvent);
        $success = $loginEvent->success;

        if ($success) {
            $authRecord->tokenUsed = true;
            $authRecord->save(false);
            $userSettings = Craft::$app->getProjectConfig()->get('users') ?? [];
            $requireEmailVerification = $userSettings['requireEmailVerification'] ?? true;
            if ($requireEmailVerification && !$user->suspended) {
                Craft::$app->getUsers()->activateUser($user);
            }
            return true;
        }

        // log the user out if they were successfully logged in but deemed a failure by an event
        if (Craft::$app->getUser()->getIdentity()) {
            Craft::$app->getUser()->logout();
        }

        // TODO: decide if logging the user in but failing to do so should consume the token
        return false;
    }

}