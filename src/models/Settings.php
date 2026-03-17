<?php

namespace thedrama\craftsesame\models;

use craft\base\Model;
use craft\helpers\Json;

class Settings extends Model
{
    public array|string|null $allowedHosts = [];
    // TODO: the logo source should be able to be configured to a static URL instead of an asset
    // for now, the logo is either the Sesame logo, or a user-uploaded Asset
    public string $logoSource = '{"type":"asset"}';
    // lifetime, in seconds
    public int $lifetime = 15 * 60;
    public ?string $redirectUrl;

    // some settings are configured as multiline strings
    // credit: https://github.com/verbb/knock-knock/blob/craft-5/src/models/Settings.php#L77
    public function getSettingAsMultiline(string $setting): string
    {
        if (is_array($this->$setting)) {
            return implode(PHP_EOL, $this->$setting);
        }

        if (is_string((string)$this->$setting)) {
            return (string)$this->$setting;
        }

        return '';
    }

    public function getAllowedHosts(): array
    {
        return $this->_normalizeList($this->allowedHosts);
    }

    public function getLogoSourceAsArray(): array
    {
        return Json::decode($this->logoSource, true);
    }

    // credit: https://github.com/verbb/knock-knock/blob/craft-5/src/models/Settings.php#L114
    private function _normalizeList(array|string|null $value): array
    {
        if (is_array($value)) {
            // Handle legacy format: array with a single multi-line string
            if (count($value) === 1 && is_string($value[0]) && str_contains($value[0], "\n")) {
                return array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $value[0])));
            }

            return array_filter($value);
        }

        if (is_string($value)) {
            return array_filter(array_map('trim', explode(PHP_EOL, $value)));
        }

        return [];
    }

}
