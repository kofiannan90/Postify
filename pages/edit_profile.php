<?php
session_start();
require_once '../config/db.php';

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch current user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: home.php');
    exit();
}

$successMessage = '';
$errorMessages = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password']; // Raw input
    $confirmPassword = $_POST['confirm_password'];

    // Basic input validation
    if (empty($username)) {
        $errorMessages[] = "Username is required.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessages[] = "A valid email address is required.";
    }

    if (!empty($password)) {
        if (strlen($password) < 6) {
            $errorMessages[] = "Password must be at least 6 characters long.";
        } elseif ($password !== $confirmPassword) {
            $errorMessages[] = "Passwords do not match.";
        }
    }

    // If validation passes
    if (empty($errorMessages)) {
        // If password is provided, hash it and update all fields
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
            $stmt->execute([$username, $email, $hashedPassword, $userId]);
        } else {
            // Only update username and email
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $stmt->execute([$username, $email, $userId]);
        }

        $successMessage = "Profile updated successfully.";
        // Refresh user data
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <style>
        body { font-family: Arial; background-color: #f7f7f7; padding: 20px; }
        .container { max-width: 500px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h2 { text-align: center; }
        input, button { width: 100%; padding: 10px; margin-top: 10px; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
<div class="container">
    <h2>Edit Your Profile</h2>

    <?php if (!empty($errorMessages)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errorMessages as $error): ?>
                    <li><?= htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($successMessage): ?>
        <div class="success"><?= htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']); ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>

        <label>New Password (leave blank to keep current)</label>
        <input type="password" name="password">

        <label>Confirm Password</label>
        <input type="password" name="confirm_password">

        <button type="submit">Update Profile</button>
    </form>
</div>
</body>
</html>
