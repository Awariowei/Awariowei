<?php
session_start();
include 'db.php';

// Only admins allowed
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}

// Handle delete request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_post_id'])) {
    $post_id = $_POST['delete_post_id'];
    $delete = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $delete->bind_param("i", $post_id);
    $delete->execute();
}

// Fetch all posts with uploader username
$posts = $conn->query("
    SELECT posts.id, posts.content, posts.media_url, posts.created_at, users.username 
    FROM posts 
    JOIN users ON posts.user_id = users.user_id 
    ORDER BY posts.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Posts | Admin</title>
    <link rel="stylesheet" href="assets/manage_posts.css">
</head>
<body>
<nav>
    <ul>
        <!-- other links -->
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

    <div class="form-container">
        <h2>All Uploaded Posts</h2>

        <?php if ($posts->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Uploader</th>
                    <th>Content</th>
                    <th>Media</th>
                    <th>Time</th>
                    <th>Action</th>
                </tr>
                <?php while ($post = $posts->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($post['username']) ?></td>
                        <td><?= nl2br(htmlspecialchars($post['content'])) ?></td>
                        <td>
                            <?php if (!empty($post['media_url'])): ?>
                                <img src="<?= htmlspecialchars($post['media_url']) ?>" alt="media" style="max-width: 100px;">
                            <?php else: ?>
                                None
                            <?php endif; ?>
                        </td>
                        <td><?= $post['created_at'] ?></td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Delete this post?');">
                                <input type="hidden" name="delete_post_id" value="<?= $post['id'] ?>">
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No posts found.</p>
        <?php endif; ?>

        <br>
        <a href="admin.php" class="back-link">← Back to Dashboard</a>
    </div>
</body>
</html>
