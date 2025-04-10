<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['post_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role']; // 'admin' or 'uploader'
$post_id = $_POST['post_id'];

// Check if user owns the post or is an admin
$query = $conn->prepare("SELECT user_id FROM posts WHERE id = ?");
$query->bind_param("i", $post_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    echo "Post not found.";
    exit();
}

$post = $result->fetch_assoc();

if ($user_role === 'admin' || $post['user_id'] == $user_id) {
    $delete = $conn->prepare("DELETE FROM posts WHERE post_id = ?");
    $delete->bind_param("i", $post_id);
    $delete->execute();

    // Optionally, also delete comments related to the post
    $delete_comments = $conn->prepare("DELETE FROM comments WHERE post_id = ?");
    $delete_comments->bind_param("i", $post_id);
    $delete_comments->execute();

    header("Location: my_posts.php");
    exit();
} else {
    echo "Unauthorized to delete this post.";
    exit();
}
?>
