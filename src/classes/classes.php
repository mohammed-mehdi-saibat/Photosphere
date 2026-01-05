<?php

namespace PHOTOSPHERE\classes;

use DateTime;

// ------------------------PARENT CLASS-------------------------
abstract class User
{
    protected ?int $id = null;
    protected string $username;
    protected string $email;
    protected string $passwordHash;
    protected string $role;
    protected bool $isActive;
    protected ?DateTime $lastLogin = null;
    protected ?DateTime $createdAt = null;
    protected ?string $bio = null;
    protected ?string $profilePicture = null;



    public function __construct(array $data)
    {
        $this->id = isset($data['id']) ? (int)($data['id']) : null;
        $this->username = $data['username'];
        $this->email = $data['email'];
        $this->passwordHash = $data['password_hash'];
        $this->role = $data['role'] ?? 'basic';
        $this->bio = $data['bio'] ?? null;
        $this->profilePicture = $data['profile_picture'] ?? 'default.png';
        $this->isActive = (bool)($data['is_active'] ?? true);

        if (!empty($data['last_login'])) {
            $this->lastLogin = new DateTime($data['last_login']);
        }

        if (!empty($data['created_at'])) {
            $this->createdAt = new DateTime($data['created_at']);
        } else {
            $this->createdAt = new DateTime();
        }
    }




    // GETTERS
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getUserName(): string
    {
        return $this->username;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    public function getRole(): string
    {
        return $this->role;
    }
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }
    public function getBio(): ?string
    {
        return $this->bio;
    }
    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }
    public function getIsActive(): bool
    {
        return $this->isActive;
    }
    // GETTERS


    // METHODS
    public function setPassword(string $plainPassword): void
    {
        $this->passwordHash = password_hash($plainPassword, PASSWORD_DEFAULT);
    }
    public function updateProfile(?string $bio, ?string $profilePicture): void
    {
        $this->bio = $bio;
        $this->profilePicture = $profilePicture;
    }
    public function suspend(): void
    {
        $this->isActive = false;
    }
    public function setId(int $id): void
    {
        if ($this->id === null) {
            $this->id = $id;
        }
    }
    // METHODS
    // VERIFY
    public static function isValidEmail(string $email)
    {
        return (bool)(filter_var($email, FILTER_VALIDATE_EMAIL));
    }
    public function verifyPassword(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->passwordHash);
    }
    // VERIFY
}
// ------------------------PARENT CLASS-------------------------

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

// ------------------------POST CLASS-------------------------
class Post
{
    protected ?int $id = null;
    protected int $userId;
    protected string $title;
    protected ?string $description = null;
    protected string $filePath;
    protected int $fileSize;
    protected string $mimeType;
    protected ?string $dimensions = null;
    protected string $status;
    protected int $viewCount;
    protected ?DateTime $createdAt = null;
    protected ?DateTime $updatedAt = null;
    protected ?DateTime $publishedAt = null;

    public function __construct(array $data)
    {
        $this->id = isset($data['id']) ? (int)($data['id']) : null;
        $this->userId = (int)$data['user_id'];
        $this->title = $data['title'];
        $this->description = $data['description'] ?? null;
        $this->filePath = $data['file_path'];
        $this->fileSize = (int)($data['file_size']);
        $this->mimeType = $data['mime_type'];
        $this->dimensions = $data['dimensions'] ?? null;
        $this->status = $data['status'] ?? 'draft';
        $this->viewCount = (int)($data['view_count'] ?? 0);

        $this->createdAt = !empty($data['created_at']) ? new DateTime($data['created_at']) : new DateTime();
        $this->updatedAt = !empty($data['updated_at']) ? new DateTime($data['updated_at']) : null;

        if (!empty($data['published_at'])) {
            $this->publishedAt = new DateTime($data['published_at']);
        }
    }

    // METHODS
    public function publish(): void
    {
        $this->status = 'published';
        $this->publishedAt = new DateTime();
    }

    public function isPublished(): bool
    {
        return $this->status === "published" && $this->publishedAt !== null;
    }
    // METHODS
}
// ------------------------POST CLASS-------------------------
