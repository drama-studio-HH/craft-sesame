<?php

namespace thedrama\craftsesame\controllers;

use Craft;
use craft\web\Controller;
use thedrama\craftsesame\base\AlreadyLoggedInControllerTrait;
use thedrama\craftsesame\records\AuthenticationRecord;
use thedrama\craftsesame\Sesame;
use yii\web\Response;

class SesameController extends Controller
{
    // prevent a logged-in user's request from being processed
    use AlreadyLoggedInControllerTrait;

    protected int|bool|array $allowAnonymous = [
        'register',
        'login',
    ];

    public function actionRegister(): ?Response
    {
        $this->requirePostRequest();

        $email = $this->request->getRequiredBodyParam('email');

        $authService = Sesame::getInstance()->authenticationService;

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->redirectToPostedUrl(null, 'sesame/register-failure');
        }

        // check if the user is allowed to register
        $loginResponse = $authService->requestLogin($email);

        if ($loginResponse->success) {
            // if the requested user does not exist, we will create it
            if (!$loginResponse->user) {
                // new users need a password, so set a new password
                // also disable activation email, since we will send an email anyway
                $this->request->setBodyParams(array_merge($this->request->getBodyParams(), [
                    // if the user is ever meant to log in with a password, they will need to request a new password eventually...
                    'password' => $authService->getToken(16),
                    'sendActivationEmail' => false
                ]));
                // execute Craft's native save-user action
                Craft::$app->runAction('users/save-user');
            }

            // persist the authentication request in the database
            $authService->storeAuth($email);

            Craft::$app->getSession()->setFlash('register__success', Craft::t('sesame', 'Your login request was handled successfully. If an account with the specified email exists, a mail will be sent to it.'));

            return $this->redirectToPostedUrl();
        }

        Craft::$app->getSession()->setFlash('register__danger', Craft::t('sesame', 'Unfortunately we could not process your request. Please try again later, or with a different mail.'));

        return $this->redirectToPostedUrl();
    }

    public function actionLogin(string $token): ?Response
    {
        $authService = Sesame::getInstance()->authenticationService;

        $authRecord = AuthenticationRecord::findOne(['token' => $token]);

        // check if we have a request with such a token
        $loginResponse = $authService->handleAuthRequest($authRecord);

        if ($loginResponse->success && $authService->login($authRecord)) {
            return $this->redirect($loginResponse->redirectUrl);
        } else {
            // if we have any errors, they will be passed to the template
            return $this->renderTemplate('sesame/login-failure', ['errors' => $loginResponse->errors]);
        }
    }

}