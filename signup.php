<?php
session_start();
include 'db.php';

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = trim($_POST["name"]);
    $username = trim($_POST["username"]);
    $email    = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $role     = $_POST["role"];

    // Check if email or username already exists
    $check = $conn->prepare("SELECT user_id FROM users WHERE email = ? OR username = ?");
    $check->bind_param("ss", $email, $username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "A user already exists with this email or username.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, username, email, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $username, $email, $password, $role);
        
        if ($stmt->execute()) {
            $success = "Account created! You can now log in.";
        } else {
            $error = "Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Signup | ViewNPost</title>
    <link rel="stylesheet" href="assets/signup.css">
</head>
<body>
    <div class="form-container">
        <h2>Register on ViewNPost</h2>

        <?php if ($success): ?>
            <p class="success"><?= $success ?></p>
        <?php endif; ?>

        <?php if ($error): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required><br>
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            
            <label>Select Role:</label>
            <select name="role" required>
                <option value="viewer">Viewer</option>
                <option value="uploader">Uploader</option>
            </select><br>

            <button type="submit">Sign Up</button>
        </form>

        <p class="login-link">Already have an account? <a href="login.php">Login here</a>.</p>
    </div>
</body>
</html>
