<?php
session_start();
include 'db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "viewer") {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $post_id = $_POST["post_id"];
    $comment_text = trim($_POST["comment_text"]);
    $user_id = $_SESSION["user_id"];

    if (!empty($comment_text)) {
        $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment_text) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $post_id, $user_id, $comment_text);
        $stmt->execute();
    }
}

header("Location: viewer.php");
exit();
