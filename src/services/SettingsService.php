<?php

namespace thedrama\craftsesame\services;

use Craft;
use craft\elements\Asset;
use craft\helpers\Json;
use thedrama\craftsesame\models\Settings;
use thedrama\craftsesame\records\SettingsRecord;
use yii\base\Component;

class SettingsService extends Component
{
    // return settings from the settings record, or the default settings record if it doesn't exist
    public function getSettings(): Settings
    {
        $settingsRecord = $this->getSettingsRecord();
        return new Settings([
            'allowUserRegistration' => $settingsRecord->allowUserRegistration,
            'allowedHosts' => $settingsRecord->allowedHosts,
            'logoSource' => $settingsRecord->logoSource,
            'lifetime' => $settingsRecord->lifetime,
            'redirectUrl' => $settingsRecord->redirectUrl,
        ]);
    }

    // return the settings record for the current or primary site if it exists, else load default values
    public function getSettingsRecord(): SettingsRecord
    {
        $site = Craft::$app->getSites()->getCurrentSite() ?? Craft::$app->getSites()->getPrimarySite();

        $defaultRecord = new SettingsRecord([
            'allowUserRegistration' => true,
            'siteId' => $site->id,
            'logoSource' => '{"type":"asset"}',
            'lifetime' => 15 * 60,
        ]);

        return SettingsRecord::findOne(['siteId' => $site->id]) ?? $defaultRecord;
    }

    // update the settings from the settings controller
    public function saveSettings(array $settings): bool
    {
        $settingsRecord = $this->getSettingsRecord();

        $settingsRecord->allowUserRegistration = $settings['allowUserRegistration'];

        $settingsRecord->allowedHosts = $settings['allowedHosts'];

        $settingsRecord->logoSource = Json::encode([
            'type' => 'asset',
            'elements' => $settings['logoId'],
        ]);

        $settingsRecord->lifetime = $settings['lifetime'];

        $settingsRecord->redirectUrl = $settings['redirectUrl'];

        return $settingsRecord->save();
    }

    // return the user-configured logo URL, or use the published SVG URL
    public function getLogoUrl(): string
    {
        $settings = $this->getSettings();
        $logoSource = $settings->getLogoSourceAsArray();

        return ($logoSource['elements'] ?? null) ?
            Asset::findOne(['id' => $logoSource['elements']])->url :
            Craft::$app->assetManager->getPublishedUrl('@thedrama/craftsesame/resources/dist/svg/icon.svg', true);
    }

    // redirect URL from the settings, or the base URL for the current or primary site
    public function getRedirectUrl(): string
    {
        $settings = $this->getSettings();
        $sitesService = Craft::$app->getSites();
        return $settings->redirectUrl ?? ($sitesService->currentSite ?? $sitesService->primarySite)->baseUrl;
    }
}