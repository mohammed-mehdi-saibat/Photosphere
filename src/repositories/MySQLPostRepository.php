<?php

declare(strict_types=1);

namespace PHOTOSPHERE\repositories;

use Exception;
use PHOTOSPHERE\database\Database;
use PHOTOSPHERE\interfaces\PostRepositoryInterface;
use PHOTOSPHERE\classes\Post;
use PDO;

class MySQLPostRepository implements PostRepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findById(int $id): ?Post
    {
        $stmt = $this->db->prepare("SELECT * FROM posts WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $stmt = $this->db->prepare("SELECT t.name FROM tags t JOIN post_tags pt ON t.id = pt.tag_id WHERE pt.post_id = :id");
        $stmt->execute(['id' => $id]);
        $data['tags'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return new Post($data);
    }

    public function save(Post $post): bool
    {
        try {
            $this->db->beginTransaction();

            $data = [
                'user_id'      => $post->getUserId(),
                'title'        => $post->getTitle(),
                'description'  => $post->getPostDescription(),
                'file_path'    => $post->getFilePath(),
                'file_size'    => $post->getFileSize(),
                'mime_type'    => $post->getMimeType(),
                'dimensions'   => $post->getDimensions(),
                'status'       => $post->getStatus(),
                'view_count'   => $post->getViewCount(),
                'created_at'   => $post->getCreatedAt()->format('Y-m-d H:i:s'),
                'published_at' => $post->getPublishedAt() ? $post->getPublishedAt()->format('Y-m-d H:i:s') : null,
            ];

            if ($post->getPostId() === null) {
                $sql = "INSERT INTO posts (user_id, title, description, file_path, file_size, mime_type, dimensions, status, view_count, created_at, published_at) 
                        VALUES (:user_id, :title, :description, :file_path, :file_size, :mime_type, :dimensions, :status, :view_count, :created_at, :published_at)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute($data);
                $postId = (int)$this->db->lastInsertId();
            } else {
                $postId = $post->getPostId();
                $sql = "UPDATE posts SET title = :title, description = :description, status = :status, view_count = :view_count WHERE id = :id";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'status' => $data['status'],
                    'view_count' => $data['view_count'],
                    'id' => $postId
                ]);
            }

            $this->syncTags($postId, $post->getTags());

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    private function syncTags(int $postId, array $tags): void
    {
        $stmt = $this->db->prepare("DELETE FROM post_tags WHERE post_id = :post_id");
        $stmt->execute(['post_id' => $postId]);

        foreach ($tags as $tagName) {
            $slug = str_replace(' ', '-', strtolower(trim($tagName)));

            $stmt = $this->db->prepare("INSERT IGNORE INTO tags (name, slug) VALUES (:name, :slug)");
            $stmt->execute(['name' => $tagName, 'slug' => $slug]);

            $stmt = $this->db->prepare("SELECT id FROM tags WHERE name = :name");
            $stmt->execute(['name' => $tagName]);
            $tagId = $stmt->fetchColumn();

            $stmt = $this->db->prepare("INSERT IGNORE INTO post_tags (post_id, tag_id) VALUES (:post_id, :tag_id)");
            $stmt->execute(['post_id' => $postId, 'tag_id' => $tagId]);
        }
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM posts WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
