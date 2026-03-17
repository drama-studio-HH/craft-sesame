<?php

namespace thedrama\craftsesame\events;

use craft\base\Event;

class RegisterAllowedHostsEvent extends Event
{
    public ?array $hosts = null;
}