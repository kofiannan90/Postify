<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$category = trim($_POST['category']);
$content = trim($_POST['content']);
$image = null;

// Handle image upload
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/';
    $filename = uniqid() . '_' . basename($_FILES['image']['name']);
    $targetPath = $uploadDir . $filename;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        $image = $filename;
    }
}

// Insert into database
$stmt = $pdo->prepare("INSERT INTO posts (user_id, category, content, image, created_at) VALUES (?, ?, ?, ?, NOW())");
$stmt->execute([$userId, $category, $content, $image]);

header("Location: ../pages/create_post.php?success=1");
exit();
