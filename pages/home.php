<?php
session_start();
require_once '../config/db.php';
include('../includes/auth.php');
include('../includes/header.php');

// Redirect to login if not authenticated
if (!isset($_SESSION['username'])) {
    header("Location: ../pages/login.php");
    exit();
}

// Fetch posts from database
try {
    $stmt = $pdo->query("
        SELECT posts.*, users.username 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        ORDER BY posts.created_at DESC
    ");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $posts = [];
    error_log("Error fetching posts: " . $e->getMessage());
}
?>

<!-- Top Bar -->
<div style="text-align: right; padding: 10px; background-color: #f8f9fa; font-family: Arial, sans-serif;">
    Logged in as <strong><?= htmlspecialchars($_SESSION['username']) ?></strong> |
    <a href="logout.php" style="color: #007bff; text-decoration: none;">Logout</a>
</div>

<!-- News Feed Header -->
<h2 style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 20px 0 10px 10px;">News Feed</h2>

<!-- Posts Loop -->
<div style="padding: 0 20px;">
<?php if (!empty($posts)): ?>
    <?php foreach ($posts as $post): ?>
        <div style="border:1px solid #dee2e6; border-radius: 8px; padding:15px; margin-bottom:20px; background-color: #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <strong style="color:#007bff;">@<?= htmlspecialchars($post['username']) ?></strong> |
            <em><?= htmlspecialchars($post['category']) ?></em><br>
            <p style="margin-top:10px;"><?= nl2br(htmlspecialchars($post['content'])) ?></p>

            <?php if ($post['image']): ?>
                <img src="../uploads/<?= htmlspecialchars($post['image']) ?>" alt="Post Image" style="max-width:100%; border-radius:6px; margin-top:10px;"><br>
            <?php endif; ?>

            <small style="color: #6c757d;">Posted on <?= date('d M Y, h:i A', strtotime($post['created_at'])) ?></small>

            <!-- Comments Section -->
            <h4 style="margin-top:20px; font-size:16px; color:#343a40;">Comments:</h4>
            <?php
            $postId = $post['id'];
            try {
                $commentStmt = $pdo->prepare("
                    SELECT comments.*, users.username 
                    FROM comments 
                    JOIN users ON comments.user_id = users.id 
                    WHERE post_id = ? ORDER BY created_at ASC
                ");
                $commentStmt->execute([$postId]);
                $comments = $commentStmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                $comments = [];
                error_log("Error fetching comments: " . $e->getMessage());
            }
            ?>

            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $comment): ?>
                    <div style="margin-left:20px; border-left:2px solid #e9ecef; padding-left:10px; margin-bottom:10px;">
                        <strong style="color:#17a2b8;">@<?= htmlspecialchars($comment['username']) ?>:</strong>
                        <?= nl2br(htmlspecialchars($comment['content'])) ?><br>
                        <small style="color: #6c757d;"><i><?= date('d M Y, h:i A', strtotime($comment['created_at'])) ?></i></small>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="margin-left:20px; color:#6c757d;">No comments yet.</p>
            <?php endif; ?>

            <!-- Comment Form -->
            <form action="../actions/comment_create_action.php" method="POST" style="margin-top:10px;">
                <input type="hidden" name="post_id" value="<?= $postId ?>">
                <textarea name="comment_content" rows="2" cols="60" required placeholder="Add a comment..." style="border:1px solid #ced4da; border-radius:4px; padding:8px; width:100%;"></textarea><br>
                <button type="submit" style="margin-top:5px; background-color:#28a745; color:white; padding:8px 16px; border:none; border-radius:4px;">Comment</button>
            </form>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p style="color:#6c757d;">No posts available. Be the first to share something!</p>
<?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>
