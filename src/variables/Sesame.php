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

    public function renderLoginForm(mixed $applyFloatLabels = true, mixed $renderFlashes = false): Markup
    {
        return SesamePlugin::getInstance()->renderService->renderLoginForm((bool)$applyFloatLabels, (bool)$renderFlashes);
    }
}