<?php

declare(strict_types=1);

namespace PHOTOSPHERE\repositories;

use PHOTOSPHERE\database\Database;
use PDO;
use Exception;

class AlbumRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }


    public function createAlbum(int $userId, string $name, string $description, bool $isPrivate): int
    {
        // 1. Permission check: Only Pro, Moderator, or Admin can create private albums
        if ($isPrivate && !$this->isUserPro($userId)) {
            throw new Exception("Only pro and super accounts can create private albums.");
        }

        try {
            $sql = "INSERT INTO albums (user_id, name, description, is_private, created_at, updated_at) 
                    VALUES (:user_id, :name, :description, :is_private, NOW(), NOW())";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'user_id'     => $userId,
                'name'        => $name,
                'description' => $description,
                'is_private'  => $isPrivate ? 1 : 0
            ]);

            return (int)$this->db->lastInsertId();
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new Exception("You already have an album named '$name'.");
            }
            throw $e;
        }
    }


    public function addPostToAlbum(int $albumId, int $postId, int $userId): bool
    {
        if (!$this->isAlbumOwner($albumId, $userId)) {
            throw new Exception("You can't modify this album");
        }

        if ($this->getPostCount($albumId) >= 100) {
            throw new Exception("An album can't hold more than 100 posts");
        }

        $sql = "INSERT IGNORE INTO album_posts (album_id, post_id) VALUES (:aid, :pid)";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute(['aid' => $albumId, 'pid' => $postId]);

        if ($result && $stmt->rowCount() > 0) {
            $this->syncPostCount($albumId);
            return true;
        }

        return false;
    }


    public function removePostFromAlbum(int $albumId, int $postId, int $userId): bool
    {
        if (!$this->isAlbumOwner($albumId, $userId)) {
            throw new Exception("You can't modify this album");
        }

        $sql = "DELETE FROM album_posts WHERE album_id = :aid AND post_id = :pid";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute(['aid' => $albumId, 'pid' => $postId]);

        if ($result && $stmt->rowCount() > 0) {
            $this->syncPostCount($albumId);
            return true;
        }
        return false;
    }


    public function getAlbumWithPosts(int $albumId, int $currentUserId, int $page = 1, int $perPage = 30): ?array
    {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT a.*, u.username as owner_name 
                FROM albums a 
                JOIN users u ON a.user_id = u.id 
                WHERE a.id = :album_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['album_id' => $albumId]);
        $album = $stmt->fetch();

        if (!$album) return null;

        if ($album['is_private'] && (int)$album['user_id'] !== $currentUserId) {
            throw new Exception("This album is private");
        }

        $postSql = "SELECT p.* FROM posts p
                    JOIN album_posts ap ON p.id = ap.post_id
                    WHERE ap.album_id = :album_id
                    ORDER BY p.created_at DESC
                    LIMIT :limit OFFSET :offset";

        $postStmt = $this->db->prepare($postSql);
        $postStmt->bindValue(':album_id', $albumId, PDO::PARAM_INT);
        $postStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $postStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $postStmt->execute();

        $album['posts'] = $postStmt->fetchAll();
        return $album;
    }


    public function getUserAlbums(int $userId, bool $includePrivate = true): array
    {
        $sql = "SELECT * FROM albums WHERE user_id = :user_id";
        if (!$includePrivate) {
            $sql .= " AND is_private = 0";
        }
        $sql .= " ORDER BY updated_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }


    public function updateAlbum(int $albumId, int $userId, array $data): bool
    {
        if (!$this->isAlbumOwner($albumId, $userId)) {
            throw new Exception("Unauthorized action");
        }

        $fields = [];
        $params = ['id' => $albumId];

        foreach (['name', 'description', 'is_private', 'cover_photo_id'] as $key) {
            if (isset($data[$key])) {
                $fields[] = "$key = :$key";
                $params[$key] = ($key === 'is_private') ? (int)$data[$key] : $data[$key];
            }
        }

        if (empty($fields)) return false;

        $sql = "UPDATE albums SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = :id";
        return $this->db->prepare($sql)->execute($params);
    }


    public function deleteAlbum(int $albumId, int $userId): bool
    {
        if (!$this->isAlbumOwner($albumId, $userId)) {
            throw new Exception("Unauthorized action");
        }

        $stmt = $this->db->prepare("DELETE FROM albums WHERE id = ?");
        return $stmt->execute([$albumId]);
    }



    private function isUserPro(int $userId): bool
    {
        $stmt = $this->db->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $role = $stmt->fetchColumn();
        return in_array($role, ['pro', 'moderator', 'admin']);
    }

    private function isAlbumOwner(int $albumId, int $userId): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM albums WHERE id = ? AND user_id = ?");
        $stmt->execute([$albumId, $userId]);
        return (bool)$stmt->fetchColumn();
    }

    private function getPostCount(int $albumId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM album_posts WHERE album_id = ?");
        $stmt->execute([$albumId]);
        return (int)$stmt->fetchColumn();
    }

    private function syncPostCount(int $albumId): void
    {
        $count = $this->getPostCount($albumId);
        $stmt = $this->db->prepare("UPDATE albums SET photo_count = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$count, $albumId]);
    }
}
