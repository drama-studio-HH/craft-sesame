<?php

namespace thedrama\craftsesame\models;

use craft\base\Model;
use craft\elements\User;

class LoginRequestResponse extends Model
{
    public bool $success = false;
    public ?User $user = null;
}