<?php

declare(strict_types=1);




require_once __DIR__ . '/../autoload.php';

use PHOTOSPHERE\database\Database;



$db = Database::getInstance();


if ($db) {
    echo 'Success! You are connected to photosphere!';
} else {
    throw new Exception('Connection failed!');
}
