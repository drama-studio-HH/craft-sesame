<?php

namespace thedrama\craftsesame\events;

use craft\base\Event;

class MailEvent extends Event
{
    public mixed $mail = null;
}