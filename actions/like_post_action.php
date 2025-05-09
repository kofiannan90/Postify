<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $postId = (int)$_POST['post_id'];
    $isLike = ($_POST['action'] === 'like') ? 1 : 0;

    // Upsert like/dislike
    $stmt = $pdo->prepare("
        INSERT INTO post_likes (user_id, post_id, is_like)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE is_like = VALUES(is_like)
    ");
    $stmt->execute([$userId, $postId, $isLike]);

    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'unauthorized']);
}
