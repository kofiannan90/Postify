<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $content = htmlspecialchars(trim($_POST['content']));
    $category = htmlspecialchars($_POST['category']);
    
    $imagePath = null;

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../uploads/";
        $filename = basename($_FILES['image']['name']);
        $targetFile = $targetDir . time() . "_" . $filename;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowed) && $_FILES['image']['size'] < 5 * 1024 * 1024) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = basename($targetFile); // Store relative path
            }
        }
    }

    // Save post to database
    $stmt = $pdo->prepare("INSERT INTO posts (user_id, content, category, image) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $content, $category, $imagePath]);

    header("Location: ../pages/home.php");
    exit;
}
?>
