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