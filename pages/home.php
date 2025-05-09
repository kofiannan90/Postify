<?php
require_once '../config/db.php';
include('../includes/auth.php');
include('../includes/header.php');

if (!isset($_SESSION['username'])) {
    header("Location: ../pages/login.php");
    exit();
}

// Fetch posts
try {
    $stmt = $pdo->query("
        SELECT posts.*, users.username, categories.name AS category_name
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        LEFT JOIN categories ON posts.category_id = categories.id
        ORDER BY posts.created_at DESC
    ");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $posts = [];
    error_log("Error fetching posts: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>News Feed</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --bg-light: #ffffff;
            --bg-dark: #121212;
            --text-light: #000000;
            --text-dark: #f8f9fa;
            --primary: #007bff;
            --secondary: #6c757d;
            --success: #28a745;
            --danger: #dc3545;
        }

        body {
            display: grid;
            grid-template-columns: 1fr minmax(0, 800px) 1fr;
            grid-template-rows: auto 1fr auto;
            grid-gap: 10px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background-color: var(--bg-light);
            color: var(--text-light);
            transition: background-color 0.3s ease, color 0.3s ease;
            min-height: 100vh;
        }

        body>* {
            grid-column: 2;
        }

        .full-width {
            grid-column: 1 / -1;
            padding: 15px 20px;
            background-color: #f8f9fa;
            text-align: right;
        }

        .dark-mode {
            background-color: var(--bg-dark);
            color: var(--text-dark);
        }

        .dark-mode .full-width {
            background-color: #1f1f1f;
            color: var(--text-dark);
        }

        .header-btn,
        .create-post-btn {
            padding: 8px 16px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
        }

        .post-container {
            padding: 20px;
        }

        .post-card {
            background-color: var(--bg-light);
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 40px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .dark-mode .post-card {
            background-color: #1e1e1e;
            border-color: #444;
        }

        .comment {
            margin-left: 20px;
            border-left: 2px solid #e9ecef;
            padding-left: 10px;
            margin-bottom: 10px;
        }

        .dark-mode .comment {
            border-left-color: #666;
        }

        textarea {
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 8px;
        }

        button,
        a.button-link {
            cursor: pointer;
        }

        a.button-link {
            color: var(--primary);
            text-decoration: none;
        }

        .text-muted {
            color: var(--secondary);
        }

        .like-btn,
        .dislike-btn {
            background: none;
            border: none;
            cursor: pointer;
        }

        .like-btn {
            color: var(--success);
        }

        .dislike-btn {
            color: var(--danger);
        }
    </style>
</head>

<body>

    <div class="full-width">
        Logged in as <strong><?= htmlspecialchars($_SESSION['username']) ?></strong> |
        <a href="logout.php" class="button-link">Logout</a>
    </div>

    <div class="full-width">
        <button id="dark-mode-toggle" class="header-btn">Toggle Dark Mode</button>
    </div>

    <h2>News Feed</h2>

    <div style="text-align: right; margin-bottom: 10px;">
        <a href="create_post.php" class="create-post-btn">+ Create Post</a>
    </div>

    <div class="post-container">
        <?php if (!empty($posts)): ?>
            <?php foreach ($posts as $post): ?>
                <?php
                $postId = $post['id'];
                $likeCount = $pdo->query("SELECT COUNT(*) FROM likes WHERE post_id = $postId AND is_like = 1")->fetchColumn();
                $dislikeCount = $pdo->query("SELECT COUNT(*) FROM likes WHERE post_id = $postId AND is_like = 0")->fetchColumn();
                ?>
                <div class="post-card">
                    <strong style="color: var(--primary);">@<?= htmlspecialchars($post['username']) ?></strong> |
                    <em><?= htmlspecialchars($post['category_name']) ?></em>
                    <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>

                    <?php if (!empty($post['image'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($post['image']) ?>" alt="Post Image" style="max-width:100%; border-radius:6px; margin-top:10px;"><br>
                    <?php endif; ?>

                    <small class="text-muted">Posted on <?= date('d M Y, h:i A', strtotime($post['created_at'])) ?></small><br>

                    <div class="" style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
                        <form method="POST" action="../actions/like_post_action.php" style="display:inline;">
                            <input type="hidden" name="post_id" value="<?= $postId ?>">
                            <input type="hidden" name="action" value="like">
                            <button type="submit" class="like-btn">👍 Like (<?= $likeCount ?>)</button>
                        </form>

                        <form method="POST" action="../actions/like_post_action.php" style="display:inline;">
                            <input type="hidden" name="post_id" value="<?= $postId ?>">
                            <input type="hidden" name="action" value="dislike">
                            <button type="submit" class="dislike-btn">👎 Dislike (<?= $dislikeCount ?>)</button>
                        </form>
                    </div>

                    <!-- Comments -->
                    <h4>Comments:</h4>
                    <?php
                    $commentStmt = $pdo->prepare("
                        SELECT comments.*, users.username 
                        FROM comments 
                        JOIN users ON comments.user_id = users.id 
                        WHERE post_id = ? 
                        ORDER BY created_at ASC
                    ");
                    $commentStmt->execute([$postId]);
                    $comments = $commentStmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>

                    <?php if (!empty($comments)): ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="comment">
                                <strong style="color: #17a2b8;">@<?= htmlspecialchars($comment['username']) ?>:</strong>
                                <?= nl2br(htmlspecialchars($comment['content'])) ?><br>
                                <small class="text-muted"><?= date('d M Y, h:i A', strtotime($comment['created_at'])) ?></small>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No comments yet.</p>
                    <?php endif; ?>

                    <form action="../actions/delete_post_action.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');" style="display:inline;">
                        <input type="hidden" name="post_id" value="<?= $postId ?>">
                        <button type="submit" class="button-link" style="color:red; background:none; border:none; padding:0; cursor:pointer;">Delete</button>
                    </form>



                    <?php if ($_SESSION['user_id'] == $post['user_id']): ?>
                        <div style="margin-top:10px;">
                            <a href="edit_post.php?id=<?= $postId ?>" class="button-link">Edit</a> |
                            <form action="../actions/delete_post_action.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');" style="display:inline;">
                                <input type="hidden" name="post_id" value="<?= $postId ?>">
                                <button type="submit" class="button-link" style="color:red; background:none; border:none; padding:0; cursor:pointer;">Delete</button>
                            </form>

                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">No posts available. Be the first to share something!</p>
        <?php endif; ?>
    </div>

    <?php include('../includes/footer.php'); ?>

    <script>
        const body = document.body;
        const toggle = document.getElementById('dark-mode-toggle');

        function setDarkMode(enabled) {
            if (enabled) {
                body.classList.add('dark-mode');
                localStorage.setItem('dark-mode', 'enabled');
            } else {
                body.classList.remove('dark-mode');
                localStorage.setItem('dark-mode', 'disabled');
            }
        }

        toggle.addEventListener('click', () => {
            const darkEnabled = body.classList.contains('dark-mode');
            setDarkMode(!darkEnabled);
        });

        // Load preference
        window.addEventListener('DOMContentLoaded', () => {
            const saved = localStorage.getItem('dark-mode');
            if (saved === 'enabled') {
                setDarkMode(true);
            }
        });
    </script>
</body>

</html>