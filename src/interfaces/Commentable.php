<?php

declare(strict_types=1);

namespace PHOTOSPHERE\interfaces;

interface Commentable
{
    public function addComment(string $content, int $userId): int;
    public function removeComment(int $commentId): bool;
    public function getComments(): array;
    public function getCommentCount(): int;
}
