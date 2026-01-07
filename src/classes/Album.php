<?php
// ------------------ALBUM CLASS------------------
declare(strict_types=1);

namespace PHOTOSPHERE\classes;

use DateTime;
use PHOTOSPHERE\traits\TimestampableTrait;

class Album
{
    use TimestampableTrait;

    protected ?int $id = null;
    protected int $userId;
    protected string $name;
    protected ?string $description = null;
    protected ?int $coverPhotoId = null;
    protected bool $isPrivate = false;
    protected int $photoCount = 0;
    protected array $posts = [];

    public function __construct(array $data)
    {
        $this->id = isset($data['id']) ? (int)$data['id'] : null;
        $this->userId = (int)$data['user_id'];
        $this->name = $data['name'];
        $this->description = $data['description'] ?? null;
        $this->coverPhotoId = isset($data['cover_photo_id']) ? (int)$data['cover_photo_id'] : null;
        $this->isPrivate = (bool)($data['is_private'] ?? false);
        $this->photoCount = (int)($data['photo_count'] ?? 0);

        $this->createdAt = !empty($data['created_at']) ? new DateTime($data['created_at']) : new DateTime();
        $this->updatedAt = !empty($data['updated_at']) ? new DateTime($data['updated_at']) : null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getUserId(): int
    {
        return $this->userId;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function getCoverPhotoId(): ?int
    {
        return $this->coverPhotoId;
    }
    public function isPrivate(): bool
    {
        return $this->isPrivate;
    }
    public function getPhotoCount(): int
    {
        return $this->photoCount;
    }
    public function getPosts(): array
    {
        return $this->posts;
    }

    public function addPost(Post $post): void
    {
        $this->posts[] = $post;
        $this->photoCount++;
    }

    public function setPrivacy(bool $isPrivate): void
    {
        $this->isPrivate = $isPrivate;
        $this->updateTimestamps();
    }
}

// ------------------ALBUM CLASS------------------