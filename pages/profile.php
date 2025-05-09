<?php
session_start();
require_once '../config/db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch user data
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// If user not found, redirect to home
if (!$user) {
    header('Location: home.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <!-- Add your CSS link here -->
</head>
<body>

<div class="profile-container">
    <h1>Welcome, <?= htmlspecialchars($user['username']); ?></h1>
    
    <div class="profile-info">
        <img src="../uploads/<?= $user['profile_picture'] ? $user['profile_picture'] : 'default-avatar.png'; ?>" alt="Profile Picture" width="150">
        <p>Email: <?= htmlspecialchars($user['email']); ?></p>
        <p>Bio: <?= htmlspecialchars($user['bio']); ?></p>
    </div>

    <!-- Option to edit profile -->
    <a href="edit_profile.php" class="button">Edit Profile</a>
</div>

</body>
</html>
