<?php

namespace thedrama\craftsesame\variables;

use thedrama\craftsesame\models\Settings;
use thedrama\craftsesame\Sesame as SesamePlugin;
use Twig\Markup;

class Sesame
{
    public function getSettings(): Settings
    {
        return SesamePlugin::getInstance()->settingsService->getSettings();
    }

    public function getLogoUrl(): string
    {
        return SesamePlugin::getInstance()->settingsService->getLogoUrl();
    }

    public function renderLoginForm(array $options): Markup
    {
        $applyFloatLabels = $options['applyFloatLabels'] ?? true;
        $renderFlashes = $options['renderFlashes'] ?? false;
        return SesamePlugin::getInstance()->renderService->renderLoginForm($applyFloatLabels, $renderFlashes);
    }
}