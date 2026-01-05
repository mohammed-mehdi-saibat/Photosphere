<?php

namespace PHOTOSPHERE\classes;

use DateTime;

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
