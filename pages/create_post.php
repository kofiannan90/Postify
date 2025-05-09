<?php
session_start();
require_once '../config/db.php';
include('../includes/auth.php');
include('../includes/header.php');

// Fetch categories dynamically from DB
$stmt = $pdo->query("SELECT id, name FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2 style="padding-left: 20px; margin-top: 20px;">Create a New Post</h2>

<form id="postForm" action="../actions/create_post_action.php" method="POST" enctype="multipart/form-data" style="padding: 20px; max-width: 600px; font-family: Arial, sans-serif; background: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
    
    <!-- Category Dropdown -->
    <label for="category"><strong>Category:</strong></label><br>
    <select name="category_id" id="category" required style="width:100%; padding:10px; margin-bottom:10px; border: 1px solid #ccc; border-radius: 5px;">
        <option value="">-- Select Category --</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= htmlspecialchars($cat['id']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <!-- Content -->
    <label for="content"><strong>Content:</strong></label><br>
    <textarea name="content" id="content" rows="5" required placeholder="Share your thoughts..." style="width:100%; padding:10px; margin-bottom:10px; border:1px solid #ccc; border-radius: 5px;"></textarea><br>

    <!-- Upload Image -->
    <label for="image"><strong>Upload Image:</strong></label><br>
    <input type="file" name="image" id="image" accept="image/*" style="margin-bottom:10px;"><br>

    <!-- Preview -->
    <div id="preview-container" style="margin-top:10px;">
        <img id="image-preview" src="#" alt="Image Preview" style="max-width: 100%; display: none; border-radius: 4px;"/>
    </div>

    <!-- Validation Message -->
    <p id="error-message" style="color: red; display: none; margin-top: 10px;"></p>

    <!-- Submit -->
    <button type="submit" style="margin-top:10px; padding:10px 20px; background-color:#007bff; color:white; border:none; border-radius:5px; cursor:pointer;">Post</button>
</form>

<script>
// Image Preview Logic
document.getElementById('image').addEventListener('change', function(event) {
    const preview = document.getElementById('image-preview');
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
});

// Form Validation
document.getElementById('postForm').addEventListener('submit', function(event) {
    const category = document.getElementById('category').value.trim();
    const content = document.getElementById('content').value.trim();
    const errorMessage = document.getElementById('error-message');

    if (!category || !content) {
        event.preventDefault();
        errorMessage.textContent = "Please fill in both category and content.";
        errorMessage.style.display = 'block';
    } else {
        errorMessage.style.display = 'none';
    }
});
</script>

<?php include('../includes/footer.php'); ?>
