<?php

namespace thedrama\craftsesame\services;

use Craft;
use craft\base\Component;
use craft\helpers\Template;
use Twig\Markup;

class RenderService extends Component
{
    // render the login form as Twig Markup
    public function renderLoginForm(array $variables): Markup
    {
        $template = Craft::$app->getView()->renderTemplate('sesame/_partials/login-or-register', $variables);
        return Template::raw($template);
    }
}