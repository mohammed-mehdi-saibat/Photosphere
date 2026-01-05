<?php

require_once './config/db.php';
require_once './src/database/database.php';

$db = Database::getInstance();


if ($db) {
    echo 'Success! You are connected to photosphere!';
} else {
    throw new ('Connection failed!');
}
