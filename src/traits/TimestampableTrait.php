<?php

declare(strict_types=1);

namespace PHOTOSPHERE\traits;

use DateTimeInterface;
use DateTime;

trait TimestampableTrait
{
    protected ?DateTimeInterface $createdAt = null;
    protected ?DateTimeInterface $updatedAt = null;

    public function initializeTimestamps(): void
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function updateTimestamps(): void
    {
        $this->updatedAt = new DateTime();
    }

    // GETTERS 
    public function getCreatedAt(?string $format = null): string|DateTimeInterface|null
    {
        if ($format && $this->createdAt) {
            return $this->createdAt->format($format);
        }
        return $this->createdAt;
    }

    public function getUpdatedAt(?string $format = null): string|DateTimeInterface|null
    {
        if ($format && $this->updatedAt) {
            return $this->updatedAt->format($format);
        }
        return $this->updatedAt;
    }
    // GETTERS 
}
