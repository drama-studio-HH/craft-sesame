<?php

namespace thedrama\craftsesame\events;

use craft\base\Event;

class LoginEvent extends Event
{
    public mixed $user = null;
    public bool $success = false;
}