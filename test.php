<?php


require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/autoload.php';

use PHOTOSPHERE\repositories\MySQLUserRepository;
use PHOTOSPHERE\classes\BasicUser;
use PHOTOSPHERE\classes\ProUser;

$repo = new MySQLUserRepository();

echo "--- 1. Creating New User ---\n";
$userData = [
    'username' => 'TechExplorer_' . rand(1, 999),
    'email'    => 'test_' . uniqid() . '@example.com',
    'password_hash' => password_hash('123456', PASSWORD_DEFAULT),
    'role'     => 'pro',
    'subscription_end' => '2026-12-31 23:59:59'
];

$newUser = new ProUser($userData);
$saveResult = $repo->save($newUser);

echo "--- 1. Creating New User ---\n";
$mehdiData = [
    'username' => 'Mohammed Mehdi' . rand(1, 999),
    'email' => 'test_' . uniqid() . '@example.com',
    'password_hash' => password_hash('mehdi123', PASSWORD_DEFAULT),
    'role' => 'basic',
    'subscription_end' => date('Y-m-d H:i:s')
];

$mehdiUser = new BasicUser($mehdiData);
$saveMehdi = $repo->save($mehdiUser);

if ($saveResult) {
    echo "Success! User saved with ID: " . $newUser->getId() . "\n";
}

echo "\n--- 2. Updating Bio ---\n";
$newUser->updateProfile("Software architect in the making!", "avatar.png");
$repo->save($newUser);
echo "Profile updated in database.\n";

echo "\n--- 3. Fetching from DB ---\n";
$fetchedUser = $repo->findById($newUser->getId());

echo "Fetched Username: " . $fetchedUser->getUserName() . "\n";
echo "Class Type: " . get_class($fetchedUser) . "\n";

if ($fetchedUser instanceof ProUser) {
    echo "Subscription Active: " . ($fetchedUser->isSubscriptionActive() ? 'Yes' : 'No') . "\n";
}
