<?php

namespace thedrama\craftsesame\controllers;

use Craft;
use craft\web\Controller;
use thedrama\craftsesame\Sesame;
use yii\web\Response;

class SettingsController extends Controller
{
    protected array|int|bool $allowAnonymous = true;

    public function actionSettings(): Response
    {
        $settings = Sesame::getInstance()->settingsService->getSettings();

        // render the control panel settings
        return $this->renderTemplate('sesame/settings', [
            'settings' => $settings,
        ]);
    }

    public function actionSaveSettings(): Response
    {
        $this->requirePostRequest();
        $bodySettings = $this->request->getRequiredBodyParam('settings');
        $settingsService = Sesame::getInstance()->settingsService;

        // persist the settings to the database
        if ($settingsService->saveSettings($bodySettings)) {
            Craft::$app->getSession()->setNotice('Settings saved.');
        }

        return $this->redirectToPostedUrl();
    }
}