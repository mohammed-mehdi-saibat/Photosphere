<?php

namespace PHOTOSPHERE\repositories;

use PHOTOSPHERE\database\Database;



use PHOTOSPHERE\interfaces\UserRepositoryInterface;
use PHOTOSPHERE\classes\User;
use PHOTOSPHERE\classes\BasicUser;
use PHOTOSPHERE\classes\ProUser;
use PDO;

class MySQLUserRepository implements UserRepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? $this->mapToUser($data) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);


        return $data ? $this->mapToUser($data) : null;
    }

    public function save(User $user): bool
    {
        $data = [
            'username' => $user->getUserName(),
            'email' => $user->getEmail(),
            'password_hash' => $user->getPasswordHash(),
            'role' => $user->getRole(),
            'bio' => $user->getBio(),
            'profile_picture' => $user->getProfilePicture(),
            'is_active' => $user->getIsActive() ? 1 : 0
        ];

        if ($user->getId() === null) {
            $sql = "INSERT INTO users (username, email, password_hash, role, bio, profile_picture, is_active) 
            VALUES (:username, :email, :password_hash, :role, :bio, :profile_picture, :is_active)";

            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute($data);

            if ($success) {
                $newId = (int)$this->db->lastInsertId();
                $user->setId($newId);
            }

            return $success;
        } else {
            $data['id'] = $user->getId();

            $sql = "UPDATE users SET 
            username = :username,
            email = :email,
            password_hash = :password_hash,
            role = :role,
            bio = :bio,
            profile_picture = :profile_picture,
            is_active = :is_active
            WHERE id = :id";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute($data);
        }
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function mapToUser(array $data): User
    {
        return match ($data['role']) {
            'pro' => new ProUser($data),
            'basic' => new BasicUser($data),
            default => new BasicUser($data),
        };
    }
}
