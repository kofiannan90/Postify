<?php
session_start();
require_once '../config/db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch the current user data from the database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: home.php');
    exit();
}

// Handle the form submission for updating the profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $bio = trim($_POST['bio']);
    $profilePicture = $_FILES['profile_picture']['name'];
    $errorMessages = [];

    // Input validation
    if (empty($username)) {
        $errorMessages[] = "Username is required.";
    }

    // Validate file upload if a new file is chosen
    if ($profilePicture) {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExtension = strtolower(pathinfo($profilePicture, PATHINFO_EXTENSION));
        $maxFileSize = 5 * 1024 * 1024; // 5MB max size

        // Check file type and size
        if (!in_array($fileExtension, $allowedExtensions)) {
            $errorMessages[] = "Invalid file type. Only jpg, jpeg, png, and gif are allowed.";
        }

        if ($_FILES['profile_picture']['size'] > $maxFileSize) {
            $errorMessages[] = "File size exceeds 5MB.";
        }

        // If no errors, proceed with the file upload
        if (empty($errorMessages)) {
            // Upload the file
            $targetDir = "../uploads/";
            $targetFile = $targetDir . basename($profilePicture);
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile)) {
                // File uploaded successfully
            } else {
                $errorMessages[] = "There was an error uploading the file.";
            }
        }
    } else {
        // Use the existing profile picture if no new one is uploaded
        // $profilePicture = $user['profile_picture'];
    }

    // Update the user data in the database if there are no errors
    if (empty($errorMessages)) {
        $stmt = $pdo->prepare("UPDATE users SET username = ?, bio = ?, profile_picture = ? WHERE id = ?");
        $stmt->execute([$username, $bio, $profilePicture, $userId]);

        // Redirect to the profile page after successful update
        header('Location: profile.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <!-- Add your CSS link here -->
    <style>
        /* Simple styling for the form */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
        }
        label {
            font-weight: bold;
        }
        input[type="text"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Edit Profile</h1>

    <!-- Display any error messages -->
    <?php if (!empty($errorMessages)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errorMessages as $message): ?>
                    <li><?= htmlspecialchars($message); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="edit_profile.php" method="POST" enctype="multipart/form-data">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']); ?>" required>

        <label for="bio">Bio</label>
        <textarea id="bio" name="bio"><?= htmlspecialchars($user['bio']); ?></textarea>

        <label for="profile_picture">Profile Picture</label>
        <input type="file" id="profile_picture" name="profile_picture">

        <button type="submit">Save Changes</button>
    </form>
</div>

</body>
</html>
