<?php

namespace thedrama\craftsesame\events;

use craft\base\Event;
use craft\elements\User;

class LoginEvent extends Event
{
    public User|null $user = null;
    public bool $success = false;
}