<?php
// ------------------------------MODERATOR CLASS--------------------------------
declare(strict_types=1);

namespace PHOTOSPHERE\classes;

require_once __DIR__ . "/User.php";

class Moderator extends User
{
    protected string $moderatorLevel;

    public function __construct(array $data)
    {
        $this->moderatorLevel = $data['moderator_level'] ?? 'junior';
        parent::__construct($data);
    }

    // GETTERS
    public function getModeratorLevel()
    {
        return $this->moderatorLevel;
    }
    // GETTERS
}

// ------------------------------MODERATOR CLASS--------------------------------