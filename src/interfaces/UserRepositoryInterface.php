<?php

namespace PHOTOSPHERE\interfaces;

require_once __DIR__ . '/../classes/classes.php';

use PHOTOSPHERE\classes\User;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;

    public function save(User $user): bool;

    public function delete(int $id): bool;
}
