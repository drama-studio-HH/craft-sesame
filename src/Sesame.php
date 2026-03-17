<?php

namespace thedrama\craftsesame;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\enums\CmsEdition;
use craft\events\PluginEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\helpers\Json;
use craft\helpers\UrlHelper;
use craft\services\Plugins;
use craft\web\twig\variables\CraftVariable;
use craft\web\UrlManager;
use craft\web\View;
use thedrama\craftsesame\models\Settings;
use thedrama\craftsesame\services\AuthenticationService;
use thedrama\craftsesame\services\RenderService;
use thedrama\craftsesame\services\SettingsService;
use thedrama\craftsesame\variables\Sesame as SesameVariable;
use yii\base\Event;

/**
 * sesame plugin
 *
 * @method static Sesame getInstance()
 * @method Settings getSettings()
 */
class Sesame extends Plugin
{
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = true;
    public CmsEdition $minCmsEdition = CmsEdition::Pro;
    public string $minVersionRequired = '5.0.0';

    public static function config(): array
    {
        return [
            'components' => [
                'authenticationService' => ['class' => AuthenticationService::class],
                'settingsService' => ['class' => SettingsService::class],
                'renderService' => ['class' => RenderService::class],
            ],
        ];
    }

    public function init(): void
    {
        parent::init();

        $this->_attachEventHandlers();
        $this->_registerTemplateRoots();
        $this->_registerCpRoutes();
        $this->_registerSiteRoutes();
        $this->_registerVariable();
    }

    protected function createSettingsModel(): ?Model
    {
        return new Settings();
    }

    public function getSettingsResponse(): mixed
    {
        return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('sesame/settings'));
    }

    private function _attachEventHandlers(): void
    {
        Event::on(Plugins::class, Plugins::EVENT_AFTER_INSTALL_PLUGIN, function (PluginEvent $event) {
            if ($event->plugin !== $this) {
                return;
            }

            // set the requireEmailVerification setting to false
            $projectConfig = Craft::$app->getProjectConfig();
            $settings = $projectConfig->get('users') ?? [];

            if (Craft::$app->edition->value >= CmsEdition::Pro->value) {
                $settings['requireEmailVerification'] = false;
                $settings['allowPublicRegistration'] = true;
            }

            $projectConfig->set('users', $settings, 'Update user settings.');
        });
    }

    private function _registerTemplateRoots(): void
    {
        Event::on(View::class, View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS, function (RegisterTemplateRootsEvent $event) {
            $event->roots['sesame'] = $this->getBasePath() . DIRECTORY_SEPARATOR . 'templates/_special';
        });
    }

    private function _registerCpRoutes(): void
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function (RegisterUrlRulesEvent $event) {
            $event->rules['sesame/settings'] = 'sesame/settings/settings';
        });
    }

    private function _registerSiteRoutes(): void
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_SITE_URL_RULES, function (RegisterUrlRulesEvent $event) {
            $event->rules['sesame/<token:[A-Za-z0-9_-]{32}>'] = 'sesame/sesame/login';
        });
    }

    private function _registerVariable(): void
    {
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $event) {
            $event->sender->set('sesame', SesameVariable::class);
        });
    }
}
