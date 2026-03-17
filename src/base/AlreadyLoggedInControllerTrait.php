<?php

namespace thedrama\craftsesame\base;

use Craft;
use thedrama\craftsesame\Sesame;

// any request to this controller should not be executed if the user is already logged in
trait AlreadyLoggedInControllerTrait
{
    public function beforeAction($action): bool
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        // if we have a user identity (the user is logged in), set a flash for the view to handle, and redirect the user to the redirect URL instead of processing the request further
        if (Craft::$app->getUser()->getIdentity()) {
            Craft::$app->getSession()->setFlash('alreadyLoggedIn', Craft::t('sesame', 'You are already logged in.'));
            $this->redirect(Sesame::getInstance()->settingsService->getRedirectUrl());
            return false;
        }

        return true;
    }
}