<?php
session_start();
include 'db.php';

// Allow only admins
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}

// Handle delete request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_user_id'])) {
    $user_id = $_POST['delete_user_id'];

    // Optional: prevent admin from deleting themselves
    if ($_SESSION["user_id"] == $user_id) {
        $error = "You cannot delete yourself!";
    } else {
        $delete = $conn->prepare("DELETE FROM users WHERE id = ?");
        $delete->bind_param("i", $user_id);
        $delete->execute();
    }
}

// Fetch all users
$users = $conn->query("SELECT user_id, name, username, email, role FROM users ORDER BY role DESC, username ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users | Admin</title>
    <link rel="stylesheet" href="assets/manage_users.css">
</head>
<body>
<nav>
    <ul>
        <!-- other links -->
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

    <div class="form-container">
        <h2>All Users</h2>

        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

        <?php if ($users->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
                <?php while ($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= ucfirst($user['role']) ?></td>
                        <td>
                            <?php if ($_SESSION["user_id"] != $user['user_id']): ?>
                                <form method="POST" onsubmit="return confirm('Delete this user?');">
                                    <input type="hidden" name="delete_user_id" value="<?= $user['user_id'] ?>">
                                    <button type="submit">Delete</button>
                                </form>
                            <?php else: ?>
                                <span class="self-label">You</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No users found.</p>
        <?php endif; ?>

        <br>
        <a href="admin.php" class="back-link">← Back to Dashboard</a>
    </div>
</body>
</html>
