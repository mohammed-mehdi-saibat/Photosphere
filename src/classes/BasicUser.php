<?php

declare(strict_types=1);


namespace PHOTOSPHERE\classes;

require_once __DIR__ . '/User.php';

// ------------------------BASIC USER CLASS-------------------------
class BasicUser extends User
{
    public const UPLOAD_LIMIT = 10;
    protected int $monthlyUploads;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->monthlyUploads = $data['monthly_uploads'] ?? 0;
    }

    // METHODS
    public function canUpload(): bool
    {
        return $this->monthlyUploads < self::UPLOAD_LIMIT;
    }
    public function incrementUploads(): bool
    {
        if ($this->canUpload()) {
            $this->monthlyUploads++;
            return true;
        }
        return false;
    }
    public function getRemainingUploads(): int
    {
        return self::UPLOAD_LIMIT - $this->monthlyUploads;
    }
    public function resetMonthlyUploads(): void
    {
        $this->monthlyUploads = 0;
    }
    // METHODS 
}
// ------------------------BASIC USER CLASS-------------------------
