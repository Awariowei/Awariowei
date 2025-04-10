<?php
session_start();

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'guest';

// Kill the session
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logged Out</title>
    <link rel="stylesheet" href="assets/logout.css">
</head>
<body>
    <div class="message-box">
        <h2>✅ Successfully Logged Out</h2>
        <p>Come back soon, <strong>@<?= htmlspecialchars($username) ?></strong>!</p>
        <br>
        <a href="login.php" class="login-link">Login Again</a>
    </div>
</body>
</html>
