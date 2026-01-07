<?php

declare(strict_types=1);

namespace PHOTOSPHERE\interfaces;

interface Taggable
{
    public function addTag(string $tag): void;
    public function removeTag(string $tag): void;
    public function getTags(): array;
    public function hasTag(string $tag): bool;
    public function clearTags(): void;
}
    