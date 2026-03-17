<?php

namespace thedrama\craftsesame\models;

use craft\base\Model;

class AuthenticationResponse extends Model
{
    public bool $success = false;
    public array $errors = [];
    public string $redirectUrl;
}