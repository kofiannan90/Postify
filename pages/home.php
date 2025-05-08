<?php
include('../includes/auth.php');
include('../includes/header.php');
require_once '../config/db.php';

// Get all posts
$stmt = $pdo->query("SELECT posts.*, users.username FROM posts 
                     JOIN users ON posts.user_id = users.id 
                     ORDER BY posts.created_at DESC");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>News Feed</h2>

<?php foreach ($posts as $post): ?>
    <div style="border:1px solid #ccc; padding:10px; margin-bottom:20px;">
        <strong>@<?= htmlspecialchars($post['username']) ?></strong> |
        <em><?= htmlspecialchars($post['category']) ?></em><br>
        <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>

        <?php if ($post['image']): ?>
            <img src="../uploads/<?= htmlspecialchars($post['image']) ?>" style="max-width:300px;"><br>
        <?php endif; ?>

        <small>Posted on <?= date('d M Y, h:i A', strtotime($post['created_at'])) ?></small>

        <!-- Comments Section -->
        <h4 style="margin-top:10px;">Comments:</h4>
        <?php
        $postId = $post['id'];
        $commentStmt = $pdo->prepare("SELECT comments.*, users.username FROM comments 
                                      JOIN users ON comments.user_id = users.id 
                                      WHERE post_id = ? ORDER BY created_at ASC");
        $commentStmt->execute([$postId]);
        $comments = $commentStmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <?php foreach ($comments as $comment): ?>
            <div style="margin-left:20px; border-left:2px solid #ccc; padding-left:10px;">
                <strong>@<?= htmlspecialchars($comment['username']) ?>:</strong>
                <?= nl2br(htmlspecialchars($comment['content'])) ?><br>
                <small><i><?= date('d M Y, h:i A', strtotime($comment['created_at'])) ?></i></small>
            </div>
        <?php endforeach; ?>

        <!-- Comment Form -->
        <form action="../actions/comment_create_action.php" method="POST" style="margin-top:10px;">
            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
            <textarea name="comment_content" rows="2" cols="40" required placeholder="Add a comment..."></textarea><br>
            <button type="submit">Comment</button>
        </form>
    </div>
<?php endforeach; ?>

<?php include('../includes/footer.php'); ?>