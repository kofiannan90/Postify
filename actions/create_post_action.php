<?php
session_start();
require_once '../config/db.php';

// Validate user and request method
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $category = $_POST['category'] ?? '';
    $content = trim($_POST['content']);
    $imagePath = null;

    if (empty($category) || empty($content)) {
        die("Both category and content are required.");
    }

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = '../uploads/';
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $imageName;
        $fileExt = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExt, $allowedTypes)) {
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                die("Failed to upload image.");
            }
            $imagePath = $imageName;
        } else {
            die("Invalid image format. Allowed: jpg, jpeg, png, gif.");
        }
    }

    // Insert into posts
    $stmt = $pdo->prepare("INSERT INTO posts (user_id, category, content, image, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$userId, $category, $content, $imagePath]);

    header("Location: ../pages/home.php");
    exit;
} else {
    echo "Unauthorized access.";
}
?>
