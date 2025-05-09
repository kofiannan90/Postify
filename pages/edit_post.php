<?php
// session_start();
require_once '../config/db.php';
include('../includes/auth.php');
include('../includes/header.php');

$postId = $_GET['id'] ?? null;

if (!$postId || !is_numeric($postId)) {
    die("Invalid post ID.");
}

// Fetch post
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
$stmt->execute([$postId, $_SESSION['user_id']]);
$post = $stmt->fetch();

if (!$post) {
    die("Post not found or unauthorized access.");
}

// Fetch categories
$categoriesStmt = $pdo->query("SELECT id, name FROM categories");
$categories = $categoriesStmt->fetchAll();
?>

<h2 style="padding-left: 20px;">Edit Post</h2>

<form action="../actions/edit_post_action.php" method="POST" enctype="multipart/form-data" style="padding: 20px;">
    <input type="hidden" name="post_id" value="<?= htmlspecialchars($post['id']) ?>">

    <label for="category">Category:</label><br>
    <select name="category_id" required>
        <?php foreach ($categories as $category): ?>
            <option value="<?= $category['id'] ?>" <?= $category['id'] == $post['category_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($category['name']) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label for="content">Content:</label><br>
    <textarea name="content" rows="5" required><?= htmlspecialchars($post['content']) ?></textarea><br><br>

    <?php if ($post['image']): ?>
        <p>Current Image:</p>
        <img src="../uploads/<?= htmlspecialchars($post['image']) ?>" style="max-width: 200px;"><br><br>
    <?php endif; ?>

    <label for="image">Change Image (optional):</label><br>
    <input type="file" name="image" accept="image/*"><br><br>

    <button type="submit">Update Post</button>
</form>

<?php include('../includes/footer.php'); ?>
