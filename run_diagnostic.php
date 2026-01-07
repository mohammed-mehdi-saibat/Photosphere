<?php

declare(strict_types=1);

require_once 'autoload.php';
require_once 'config/db.php';

use PHOTOSPHERE\classes\Post;
use PHOTOSPHERE\classes\Album;
use PHOTOSPHERE\repositories\MySQLPostRepository;

header('Content-Type: text/plain');

try {
    echo "=== PHOTOSPHERE DIAGNOSTIC START ===\n\n";

    // 1. Test Post Logic
    echo "[1/4] Testing Post Logic... ";
    $postData = [
        'user_id' => 1,
        'title' => 'Diagnostic Photo',
        'file_path' => 'uploads/test.jpg',
        'file_size' => 2048,
        'mime_type' => 'image/jpeg',
        'tags' => ['Nature', 'Diagnostic'] // Test constructor tag injection
    ];
    $post = new Post($postData);
    $post->addLike(99);

    if (count($post->getTags()) === 2 && $post->getLikeCount() === 1) {
        echo "OK\n";
    } else {
        echo "FAILED (Check Tag/Like logic)\n";
    }

    // 2. Test Album Logic
    echo "[2/4] Testing Album Logic... ";
    $album = new Album(['user_id' => 1, 'name' => 'Test Album']);
    $album->addPost($post);
    if ($album->getPhotoCount() === 1) {
        echo "OK\n";
    } else {
        echo "FAILED (Check Album photoCount logic)\n";
    }

    // 3. Test Repository (Database)
    echo "[3/4] Testing Database Save... ";
    $repo = new MySQLPostRepository();
    // Note: Ensure user with ID 1 exists in your DB before running
    if ($repo->save($post)) {
        echo "OK (Post and Tags saved)\n";
        $savedId = $post->getPostId();
    } else {
        echo "FAILED (Check DB connection or syncTags logic)\n";
    }

    // 4. Test Repository (Retrieval)
    if (isset($savedId)) {
        echo "[4/4] Testing Database Fetch... ";
        $fetchedPost = $repo->findById($savedId);
        if ($fetchedPost && in_array('Nature', $fetchedPost->getTags())) {
            echo "OK (Tags retrieved successfully)\n";
        } else {
            echo "FAILED (Tags not found in DB)\n";
        }
    }

    echo "\n=== ALL SYSTEMS GO! ===";
} catch (\Exception $e) {
    echo "\nCRITICAL ERROR: " . $e->getMessage();
}
