<?php
require_once '../config/db.php';
session_start();

$errors = [];
$uploadDir = '../uploads/';
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
$maxFileSize = 2 * 1024 * 1024; // 2MB

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $bio = trim($_POST["bio"] ?? '');

    // Validate basic fields
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // Check if email exists
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "Email is already registered.";
        }
    }

    // Handle profile picture upload
    $profilePicPath = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = basename($_FILES['profile_picture']['name']);
        $fileSize = $_FILES['profile_picture']['size'];
        $fileType = mime_content_type($fileTmpPath);

        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = "Only JPEG, PNG, or GIF files are allowed.";
        } elseif ($fileSize > $maxFileSize) {
            $errors[] = "Profile picture size must not exceed 2MB.";
        } else {
            $newFileName = uniqid('avatar_', true) . '.' . pathinfo($fileName, PATHINFO_EXTENSION);
            $destination = $uploadDir . $newFileName;
            if (move_uploaded_file($fileTmpPath, $destination)) {
                $profilePicPath = 'uploads/profile_pictures/' . $newFileName;
            } else {
                $errors[] = "Failed to upload profile picture.";
            }
        }
    }

    // Insert into database
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, bio, profile_picture) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$username, $email, $hashedPassword, $bio, $profilePicPath])) {
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['username'] = $username;
            header("Location: home.php");
            exit;
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }
}
?>
<form method="POST" action="" enctype="multipart/form-data">
    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required value="<?= isset($username) ? htmlspecialchars($username) : '' ?>">
    </div>

    <div class="form-group">
        <label for="email">Email address</label>
        <input type="email" id="email" name="email" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
    </div>

    <div class="form-group">
        <label for="password">Password (min 6 characters)</label>
        <input type="password" id="password" name="password" required minlength="6">
    </div>

    <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
    </div>

    <div class="form-group">
        <label for="bio">Short Bio</label>
        <textarea id="bio" name="bio" rows="4"><?= isset($bio) ? htmlspecialchars($bio) : '' ?></textarea>
    </div>

    <div class="form-group">
        <label for="profile_picture">Profile Picture (JPG, PNG, GIF – max 2MB)</label>
        <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
    </div>

    <button type="submit" class="submit-btn">Register</button>
</form>
