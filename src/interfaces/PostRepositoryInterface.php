<?php

declare(strict_types=1);

namespace PHOTOSPHERE\interfaces;

use PHOTOSPHERE\classes\Post;

interface PostRepositoryInterface
{
    public function findById(int $id): ?Post;
    public function save(Post $post): bool;
    public function delete(int $id): bool;
}
