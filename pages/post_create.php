<?php 
include('../includes/auth.php'); 
include('../includes/header.php'); 
?>
<h2>Create a Post</h2>
<form action="../actions/post_create_action.php" method="POST" enctype="multipart/form-data">
    <textarea name="content" placeholder="What's on your mind?" required></textarea><br>
    
    <select name="category" required>
        <option value="Event">Event</option>
        <option value="News">News</option>
        <option value="Update">Update</option>
    </select><br>
    
    <input type="file" name="image" accept="image/*"><br>
    
    <button type="submit">Post</button>
</form>
<?php include('../includes/footer.php'); ?>
