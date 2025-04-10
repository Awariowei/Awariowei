<?php
include 'db.php';

$success = "";
$error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // Check if an admin already exists
    $check = $conn->query("SELECT * FROM users WHERE role = 'admin'");
    if ($check->num_rows > 0) {
        $error = "An admin already exists.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')");
        $stmt->bind_param("ss", $username, $password);

        if ($stmt->execute()) {
            session_start();
            $_SESSION["user_id"] = $conn->insert_id;
            $_SESSION["username"] = $username;
            $_SESSION["role"] = "admin";
            header("Location: admin.php");
            exit();
        } else {
            $error = "Error creating admin: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Admin - ViewNPost</title>
    <link rel="stylesheet" href="assets/create_admin.css">
</head>
<body>

    <form method="POST">
        <h2>Create Admin</h2>
        <input type="text" name="username" placeholder="Enter admin username" required>
        <input type="password" name="password" placeholder="Enter admin password" required>
        <button type="submit">Create Admin</button>

        <?php if ($error): ?>
            <div class="msg error"><?= $error ?></div>
        <?php endif; ?>
    </form>

</body>
</html>