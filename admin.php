<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
    <div class="admin-container">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?> (WAJMANN)</h2>
        <nav>
    <ul>
        <!-- other links -->
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>


        <div class="admin-links">
            <a href="manage_posts.php">Manage Posts</a>
            <a href="manage_comments.php">Manage Comments</a>
            <a href="manage_users.php">Manage Users</a>
        </div>
    </div>
</body>
</html>
