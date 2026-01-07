<?php

declare(strict_types=1);


namespace PHOTOSPHERE\classes;

require_once __DIR__ . '/User.php';

use DateTime;

// ------------------------PRO USER CLASS-------------------------
class ProUser extends User
{
    protected ?DateTime $subscriptionStart = null;
    protected ?DateTime $subscriptionEnd = null;

    public function __construct(array $data)
    {
        parent::__construct($data);

        if (!empty($data['subscription_start'])) {
            $this->subscriptionStart = new DateTime($data['subscription_start']);
        }

        if (!empty($data['subscription_end'])) {
            $this->subscriptionEnd = new DateTime($data['subscription_end']);
        }
    }

    // METHODS
    public function isSubscriptionActive(): bool
    {
        if ($this->subscriptionEnd === null) {
            return true;
        }
        $now = new DateTime();
        return $now < $this->subscriptionEnd;
    }
    public function getSubscriptionEnd(): ?DateTime
    {
        return $this->subscriptionEnd;
    }
    // METHODS
}
// ------------------------PRO USER CLASS-------------------------
