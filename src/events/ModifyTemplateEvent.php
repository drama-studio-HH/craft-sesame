<?php

namespace thedrama\craftsesame\events;

use craft\base\Event;

class ModifyTemplateEvent extends Event
{
    public ?string $template = null;
}