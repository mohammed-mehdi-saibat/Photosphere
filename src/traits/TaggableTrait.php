<?php

declare(strict_types=1);

namespace PHOTOSPHERE\traits;


trait TaggableTrait
{
    protected array $tags = [];

    public function addTag(string $tag): void
    {
        $normalized = $this->normalizetag($tag);
        if (!$this->hasTag($normalized)) {
            $this->tags[] = $normalized;
        }
    }

    public function removeTag(string $tag): void
    {
        $normalized = $this->normalizeTag($tag);
        $this->tags = array_filter($this->tags, fn($t) => $t !== $normalized);
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function hasTag(string $tag): bool
    {
        return in_array($this->normalizeTag($tag), $this->tags);
    }

    public function clearTags(): void
    {
        $this->tags = [];
    }

    public function normalizeTag(string $tag): string
    {
        return strtolower(trim($tag));
    }
}
