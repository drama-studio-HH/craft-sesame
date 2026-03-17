<?php

namespace thedrama\craftsesame\helpers;

use craft\db\Table as CraftTable;

abstract class Table extends CraftTable
{
    public const AUTHENTICATION = '{{%sesame_authentication}}';

    public const SETTINGS = '{{%sesame_settings}}';
}