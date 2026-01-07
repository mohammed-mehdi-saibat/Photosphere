<?php
// ------------------------ADMIN CLASS--------------------------
declare(strict_types=1);

namespace PHOTOSPHERE\classes;

require_once __DIR__ . '/Moderator.php';

class Admin extends Moderator
{
    public function __construct(array $data)
    {
        $data['moderator_level'] = 'lead';
        parent::__construct($data);
    }
}

// ------------------------ADMIN CLASS--------------------------