<?php
session_start();
include 'db.php';

// Only allow admins
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}

// Handle delete request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_comment_id'])) {
    $comment_id = $_POST['delete_comment_id'];
    $delete = $conn->prepare("DELETE FROM comments WHERE id = ?");
    $delete->bind_param("i", $comment_id);
    $delete->execute();
}

// Fetch comments with post content and user
$comments = $conn->query("
    SELECT comments.comment_id, comments.comment_text, comments.created_at, 
           users.username AS commenter, 
           posts.content AS post_content
    FROM comments
    JOIN users ON comments.user_id = users.user_id
    JOIN posts ON comments.post_id = posts.id
    ORDER BY comments.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Comments | Admin</title>
    <link rel="stylesheet" href="assets/manage_comments.css">
</head>
<body>
<nav>
    <ul>
        <!-- other links -->
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

    <div class="form-container">
        <h2>All Comments</h2>

        <?php if ($comments->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Commenter</th>
                    <th>Comment</th>
                    <th>Post</th>
                    <th>Time</th>
                    <th>Action</th>
                </tr>
                <?php while ($comment = $comments->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($comment['commenter']) ?></td>
                        <td><?= nl2br(htmlspecialchars($comment['comment'])) ?></td>
                        <td><?= nl2br(htmlspecialchars($comment['post_content'])) ?></td>
                        <td><?= $comment['created_at'] ?></td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Delete this comment?');">
                                <input type="hidden" name="delete_comment_id" value="<?= $comment['id'] ?>">
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No comments found.</p>
        <?php endif; ?>

        <br>
        <a href="admin.php" class="back-link">← Back to Dashboard</a>
    </div>
</body>
</html>
