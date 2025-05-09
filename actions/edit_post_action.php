<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $postId = (int)$_POST['post_id'];
    $categoryId = (int)$_POST['category_id'];
    $content = trim($_POST['content']);

    if (!$postId || !$categoryId || empty($content)) {
        die("Missing required fields.");
    }

    // Verify ownership
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
    $stmt->execute([$postId, $_SESSION['user_id']]);
    $post = $stmt->fetch();

    if (!$post) {
        die("Unauthorized or post not found.");
    }

    // Handle image update
    $newImageName = $post['image']; // default to old image
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = '../uploads/';
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $targetFile = $uploadDir . $imageName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                // Optionally delete old image
                if ($post['image'] && file_exists($uploadDir . $post['image'])) {
                    unlink($uploadDir . $post['image']);
                }
                $newImageName = $imageName;
            } else {
                die("Image upload failed.");
            }
        } else {
            die("Invalid image format.");
        }
    }

    // Update post
    $updateStmt = $pdo->prepare("UPDATE posts SET category_id = ?, content = ?, image = ? WHERE id = ?");
    $updateStmt->execute([$categoryId, $content, $newImageName, $postId]);

    header("Location: ../pages/home.php?updated=1");
    exit();
} else {
    echo "Unauthorized access.";
}
