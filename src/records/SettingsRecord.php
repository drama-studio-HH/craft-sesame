<?php

namespace thedrama\craftsesame\records;

use craft\db\ActiveRecord;

// see migrations/Install.php for which fields this record has
class SettingsRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%sesame_settings}}';
    }

}