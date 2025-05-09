<?php
session_start();
require_once '../config/db.php';
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized: Session missing.");
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_SESSION['user_id'], $_GET['id'])) {
    $postId = (int)$_GET['id'];




    // Fetch post to verify ownership and get image name
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
    $stmt->execute([$postId, $_SESSION['user_id']]);
    $post = $stmt->fetch();

    if (!$post) {
        die("Unauthorized or post not found.");
    }

    // Delete image if exists
    if (!empty($post['image'])) {
        $imagePath = '../uploads/' . $post['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    // Delete post
    $deleteStmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $deleteStmt->execute([$postId]);

    header("Location: ../pages/home.php?deleted=1");
    exit();
} else {
    echo "Unauthorized or invalid request.";
}
