<?php
session_start();
require_once '../config/db.php';
include('../includes/auth.php');
include('../includes/header.php');

// Categories - Can later be fetched from DB
$categories = ['News', 'Event', 'Update', 'Announcement', 'Other'];

// Handle form success feedback
if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo "<div style='background:#d4edda; padding:10px; color:#155724; border-left:5px solid #28a745;'>Post created successfully.</div>";
}
?>

<div style="max-width:700px; margin:30px auto; padding:20px; background-color:#f9f9f9; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
    <h2 style="text-align:center; font-family:Segoe UI, sans-serif;">Create a New Post</h2>

    <form action="../actions/create_post_action.php" method="POST" enctype="multipart/form-data" style="margin-top:20px;">
        <!-- Category -->
        <label for="category" style="display:block; font-weight:bold; margin-bottom:5px;">Select Category:</label>
        <select name="category" id="category" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px;">
            <option value="">-- Choose Category --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
            <?php endforeach; ?>
        </select>

        <!-- Post Content -->
        <label for="content" style="display:block; font-weight:bold; margin:15px 0 5px;">Post Content:</label>
        <textarea name="content" id="content" rows="6" required placeholder="What's on your mind?" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px;"></textarea>

        <!-- Image Upload -->
        <label for="image" style="display:block; font-weight:bold; margin:15px 0 5px;">Attach an Image (optional):</label>
        <input type="file" name="image" id="image" accept="image/*" style="margin-bottom:15px;">

        <!-- Submit -->
        <button type="submit" style="background-color:#007bff; color:white; border:none; padding:10px 20px; border-radius:5px; cursor:pointer;">Post</button>
    </form>
</div>

<?php include('../includes/footer.php'); ?>