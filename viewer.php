<?php
session_start();
include 'db.php';

// Ensure user is logged in
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "viewer") {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$role = $_SESSION["role"];

// Handle comment deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_comment_id"])) {
    $comment_id = $_POST["delete_comment_id"];

    // Allow deletion if user is the commenter or an admin
    $stmt = $conn->prepare("DELETE FROM comments WHERE comment_id = ? AND (user_id = ? OR ? = 'admin')");
    $stmt->bind_param("iis", $comment_id, $user_id, $role);
    $stmt->execute();
}

// Fetch all posts
$post_query = "SELECT posts.id, posts.media_url, posts.created_at, users.username 
               FROM posts 
               JOIN users ON posts.user_id = users.user_id 
               ORDER BY posts.created_at DESC";
$post_result = $conn->query($post_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Viewer Dashboard | ViewNPost</title>
    <link rel="stylesheet" href="assets/viewer.css">
</head>
<body>
    <h2>Welcome, <?= htmlspecialchars($_SESSION["username"]) ?></h2>
    <h3>All Posts</h3>
    <nav>
    <ul>
        <!-- other links -->
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>


    <?php while ($post = $post_result->fetch_assoc()): ?>
        <div class="post">
            <img src="<?= htmlspecialchars($post['media_url']) ?>" alt="Post Image">
            <p>Posted by <strong><?= htmlspecialchars($post['username']) ?></strong> on <?= $post['created_at'] ?></p>

            <!-- Fetch and display comments -->
            <div class="comments">
                <h4>Comments:</h4>
                <?php
                    $comment_stmt = $conn->prepare(
                        "SELECT comments.comment_id, comments.comment_text, comments.created_at, users.username, users.user_id AS commenter_id 
                         FROM comments 
                         JOIN users ON comments.user_id = users.user_id 
                         WHERE comments.post_id = ? 
                         ORDER BY comments.created_at ASC"
                    );
                    
                    $comment_stmt->bind_param("i", $post["id"]);
                    $comment_stmt->execute();
                    $comment_result = $comment_stmt->get_result();
                    
                    while ($comment = $comment_result->fetch_assoc()):
                ?>
                    <div class="comment">
                        <p><strong><?= htmlspecialchars($comment['username']) ?>:</strong> <?= nl2br(htmlspecialchars($comment['comment_text'])) ?></p>
                        <small>on <?= $comment['created_at'] ?></small>

                        <?php if ($comment['commenter_id'] == $user_id || $role == 'admin'): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="delete_comment_id" value="<?= $comment['comment_id'] ?>">
                                <button type="submit">Delete</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>

            <!-- Add new comment -->
            <form method="POST" action="comment.php">
                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                <textarea name="comment_text" required placeholder="Add your comment..."></textarea>
                <button type="submit">Comment</button>
            </form>
        </div>
    <?php endwhile; ?>
</body>
</html>
