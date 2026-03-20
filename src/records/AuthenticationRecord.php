<?php

namespace thedrama\craftsesame\records;

use craft\db\ActiveRecord;
use DateInterval;
use DateTime;
use DateTimeZone;

// see migrations/Install.php for which fields this record has
class AuthenticationRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%sesame_authentication}}';
    }

    public function getIsExpired(): bool
    {
        $dateCreated = DateTime::createFromFormat('Y-m-d H:i:s', $this->dateCreated, new DateTimeZone('UTC'));
        return $this->tokenUsed || $dateCreated->add(new DateInterval('PT'.$this->lifetime.'S')) < new DateTime();
    }

}