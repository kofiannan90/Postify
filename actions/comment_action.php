<?php
require_once '../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = $_POST['post_id'] ?? null;
    $content = $_POST['comment_content'] ?? '';
    $userId = $_SESSION['user_id'] ?? null;

    if ($postId && $userId && !empty(trim($content))) {
        $stmt = $pdo->prepare("INSERT INTO comments (user_id, post_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $postId, $content]);

        $commentId = $pdo->lastInsertId();

        $fetch = $pdo->prepare("
            SELECT comments.content, comments.created_at, users.username 
            FROM comments JOIN users ON comments.user_id = users.id 
            WHERE comments.id = ?
        ");
        $fetch->execute([$commentId]);
        $comment = $fetch->fetch(PDO::FETCH_ASSOC);

        echo json_encode(['status' => 'success', 'comment' => $comment]);
        exit();
    }
}

echo json_encode(['status' => 'error']);
