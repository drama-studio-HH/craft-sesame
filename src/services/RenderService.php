<?php

namespace thedrama\craftsesame\services;

use Craft;
use craft\base\Component;
use craft\helpers\Template;
use Twig\Markup;

class RenderService extends Component
{
    // render the login form as Twig Markup
    public function renderLoginForm(bool $applyFloatLabels = true, bool $renderFlashes = false): Markup
    {
        $template = Craft::$app->getView()->renderTemplate('sesame/_partials/login-or-register', [
            'applyFloatLabels' => $applyFloatLabels,
            'renderFlashes' => $renderFlashes,
        ]);
        return Template::raw($template);
    }
}