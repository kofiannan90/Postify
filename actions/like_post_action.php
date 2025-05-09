<?php
require_once '../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = $_POST['post_id'] ?? null;
    $action = $_POST['action'] ?? null;
    $userId = $_SESSION['user_id'] ?? null;

    if ($postId && $action && $userId) {
        try {
            // Delete existing like/dislike by the user
            $stmt = $pdo->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?");
            $stmt->execute([$userId, $postId]);

            // Insert new like/dislike
            $isLike = $action === 'like' ? 1 : 0;
            $stmt = $pdo->prepare("INSERT INTO likes (user_id, post_id, is_like) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $postId, $isLike]);

            // Get updated counts
            $likeStmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ? AND is_like = 1");
            $likeStmt->execute([$postId]);
            $likes = $likeStmt->fetchColumn();

            $dislikeStmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ? AND is_like = 0");
            $dislikeStmt->execute([$postId]);
            $dislikes = $dislikeStmt->fetchColumn();

            echo json_encode(['status' => 'success', 'likes' => $likes, 'dislikes' => $dislikes]);
            exit();
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit();
        }
    }
}

echo json_encode(['status' => 'invalid']);
