<?php
session_start();
include 'db.php';

// Protect the page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'uploader') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get all posts by the user
$stmt = $conn->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Posts</title>
    <link rel="stylesheet" href="assets/my_posts.css">
</head>
<body>
<nav>
    <ul>
        <!-- other links -->
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

    <h2>My Uploaded Posts</h2>

    <?php if ($result->num_rows > 0): ?>
        <div class="post-gallery">
            <?php while ($post = $result->fetch_assoc()): ?>
                <div class="post-item">
                <img src="<?= htmlspecialchars($post['media_url']) ?>" alt="Post Image" width="300">
                    <p><small>Posted on <?= $post['created_at'] ?></small></p>
                </div>
            <?php endwhile; ?>
            <?php
// Assuming $post is your post record from the database
?>

<form action="delete_post.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');">
    <input type="hidden" name="post_id" value="<?= $post['post_id'] ?>">
    <button type="submit">Delete</button>
</form>

        </div>
    <?php else: ?>
        <p>You haven't uploaded any posts yet.</p>
    <?php endif; ?>
</body>
</html>
