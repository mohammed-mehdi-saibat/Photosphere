<?php

// ---------------------------------POST CLASS----------------------------------
declare(strict_types=1);

namespace PHOTOSPHERE\classes;

use DateTime;
use PHOTOSPHERE\interfaces\Taggable;
use PHOTOSPHERE\interfaces\Likeable;
use PHOTOSPHERE\interfaces\Commentable;
use PHOTOSPHERE\traits\TaggableTrait;
use PHOTOSPHERE\traits\TimestampableTrait;

class Post implements Taggable, Likeable, Commentable
{
    use TaggableTrait, TimestampableTrait;

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
    protected ?DateTime $publishedAt = null;

    protected int $likeCount = 0;
    protected int $commentCount = 0;

    public function __construct(array $data)
    {
        $this->id = isset($data['id']) ? (int)$data['id'] : null;
        $this->userId = (int)$data['user_id'];
        $this->title = $data['title'];
        $this->description = $data['description'] ?? null;
        $this->filePath = $data['file_path'];
        $this->fileSize = (int)$data['file_size'];
        $this->mimeType = $data['mime_type'];
        $this->dimensions = $data['dimensions'] ?? null;
        $this->status = $data['status'] ?? 'draft';
        $this->viewCount = (int)($data['view_count'] ?? 0);

        $this->createdAt = !empty($data['created_at']) ? new DateTime($data['created_at']) : new DateTime();
        $this->updatedAt = !empty($data['updated_at']) ? new DateTime($data['updated_at']) : null;

        if (!empty($data['published_at'])) {
            $this->publishedAt = new DateTime($data['published_at']);
        }

        if (isset($data['tags']) && is_array($data['tags'])) {
            foreach ($data['tags'] as $tag) {
                $this->addTag($tag);
            }
        }
    }

    public function getPostId(): ?int
    {
        return $this->id;
    }
    public function getUserId(): int
    {
        return $this->userId;
    }
    public function getTitle(): string
    {
        return $this->title;
    }
    public function getPostDescription(): ?string
    {
        return $this->description;
    }
    public function getFilePath(): string
    {
        return $this->filePath;
    }
    public function getFileSize(): int
    {
        return $this->fileSize;
    }
    public function getMimeType(): string
    {
        return $this->mimeType;
    }
    public function getDimensions(): ?string
    {
        return $this->dimensions;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
    public function getViewCount(): int
    {
        return $this->viewCount;
    }
    public function getPublishedAt(): ?DateTime
    {
        return $this->publishedAt;
    }

    public function isPublic(): bool
    {
        return $this->status === 'published';
    }

    public function publish(): void
    {
        $this->status = 'published';
        $this->publishedAt = new DateTime();
        $this->updateTimestamps();
    }

    public function archive(): void
    {
        $this->status = 'archived';
        $this->updateTimestamps();
    }

    public function addComment(string $content, int $userId): int
    {
        $this->commentCount++;
        return 0;
    }

    public function removeComment(int $commentId): bool
    {
        if ($this->commentCount > 0) $this->commentCount--;
        return true;
    }

    public function getComments(): array
    {
        return [];
    }
    public function getCommentCount(): int
    {
        return $this->commentCount;
    }

    public function addLike(int $userId): bool
    {
        $this->likeCount++;
        return true;
    }

    public function removeLike(int $userId): bool
    {
        if ($this->likeCount > 0) $this->likeCount--;
        return true;
    }

    public function isLikedBy(int $userId): bool
    {
        return false;
    }
    public function getLikeCount(): int
    {
        return $this->likeCount;
    }
    public function getLikedBy(): array
    {
        return [];
    }

    protected function loadTagsFromDatabase(): void {}
}

// ---------------------------------POST CLASS----------------------------------