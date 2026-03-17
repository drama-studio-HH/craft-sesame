<?php

namespace thedrama\craftsesame\services;

use Craft;
use craft\base\Component;
use craft\helpers\Template;
use Twig\Markup;

class RenderService extends Component
{
    // render the login form as Twig Markup
    public function renderLoginForm(): Markup
    {
        $template = Craft::$app->getView()->renderTemplate('sesame/_partials/login-or-register', []);
        return Template::raw($template);
    }
}