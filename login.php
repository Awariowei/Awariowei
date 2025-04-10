<?php
session_start();
include 'db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT user_id, name, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $name, $username, $hashed_password, $role);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION["user_id"] = $id;
            $_SESSION["name"] = $name;
            $_SESSION["username"] = $username;
            $_SESSION["role"] = $role;

            // Redirect based on role
            if ($role === 'viewer') {
                header("Location: viewer.php");
            } elseif ($role === 'uploader') {
                header("Location: uploader.php");
            } elseif ($role === 'admin') {
                header("Location: admin.php");
            }
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Username not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login | ViewNPost</title>
    <link rel="stylesheet" href="assets/login.css">
</head>
<body>
    <div class="form-container">
        <h2>Login to ViewNPost</h2>

        <?php if ($error): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </form>

        <p class="login-link">Don't have an account? <a href="signup.php">Sign up here</a>.</p>
    </div>
</body>
</html>
