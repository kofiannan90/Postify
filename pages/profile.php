<?php
// session_start();
require_once '../config/db.php';

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch user details including profile picture
$stmt = $pdo->prepare("SELECT username, email, profile_picture, created_at FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    echo "User not found.";
    exit();
}

$profileImage = !empty($user['profile_picture']) && file_exists("../uploads/{$user['profile_picture']}")
    ? "../uploads/" . $user['profile_picture']
    : "../assets/default-avatar.png"; // fallback image
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Profile</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f0f2f5; padding: 20px; }
        .container {
            max-width: 500px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.08);
            text-align: center;
        }
        img.profile-pic {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #007BFF;
            margin-bottom: 15px;
        }
        h2 { margin-bottom: 10px; }
        .profile-info p { margin: 8px 0; }
        .btn-edit {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            display: inline-block;
            margin-top: 20px;
        }
        .btn-edit:hover { background-color: #0056b3; }
    </style>
</head>
<body>

<div class="container">
    <img src="<?= htmlspecialchars($profileImage); ?>" alt="Profile Picture" class="profile-pic">
    <h2><?= htmlspecialchars($user['username']); ?></h2>

    <div class="profile-info">
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></p>
        <p><strong>Joined:</strong> <?= date("F j, Y", strtotime($user['created_at'])); ?></p>
    </div>

    <a class="btn-edit" href="edit_profile.php">Edit Profile</a>
</div>

</body>
</html>
