<?php

namespace thedrama\craftsesame\models;

use craft\base\Model;
use DateInterval;
use DateTime;

class AuthenticationData extends Model
{
    public string $token;
    public int $userId;
    public DateTime $dateCreated;
    // lifetime is counted in seconds, not milliseconds
    public int $lifetime;
    public bool $tokenUsed = false;

    public function getIsExpired(): bool
    {
        return $this->tokenUsed || $this->dateCreated->add(new DateInterval('PT'.$this->lifetime.'S')) < new DateTime();
    }
}
