<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: home.php');
    exit();
}

// Handle form submission for updating profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $bio = $_POST['bio'];
    $profilePicture = $_FILES['profile_picture']['name'];

    if ($profilePicture) {
        // Handle image upload
        $targetDir = "../uploads/";
        $targetFile = $targetDir . basename($profilePicture);
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile);
    } else {
        $profilePicture = $user['profile_picture']; // Keep existing picture
    }

    $stmt = $pdo->prepare("UPDATE users SET username = ?, bio = ?, profile_picture = ? WHERE id = ?");
    $stmt->execute([$username, $bio, $profilePicture, $userId]);

    header('Location: profile.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <!-- Add your CSS link here -->
</head>
<body>

<h1>Edit Profile</h1>

<form action="edit_profile.php" method="POST" enctype="multipart/form-data">
    <label for="username">Username</label>
    <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']); ?>" required>

    <label for="bio">Bio</label>
    <textarea id="bio" name="bio"><?= htmlspecialchars($user['bio']); ?></textarea>

    <label for="profile_picture">Profile Picture</label>
    <input type="file" id="profile_picture" name="profile_picture">

    <button type="submit">Save Changes</button>
</form>

</body>
</html>
