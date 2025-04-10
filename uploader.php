<?php
session_start();

// Check if the user is logged in and has the 'uploader' role
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'uploader') {
    header("Location: login.php"); // Redirect to login if not logged in or not an uploader
    exit();
}

include 'db.php'; // Include your database connection file

$upload_success = "";
$upload_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["upload_image"])) {
    // Define upload directory
    $upload_dir = "uploads/"; // Create this directory in your project

    // Get file information
    $file_name = $_FILES["image"]["name"];
    $file_tmp = $_FILES["image"]["tmp_name"];
    $file_size = $_FILES["image"]["size"];
    $file_error = $_FILES["image"]["error"];

    // Get caption
    $caption = trim($_POST["caption"]);

    // Basic file validation
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if ($file_error === 0) {
        if (in_array($file_ext, $allowed_extensions)) {
            if ($file_size <= 2 * 1024 * 1024) { // Example: 2MB limit
                $new_file_name = uniqid('', true) . '.' . $file_ext; // Create a unique filename
                $destination = $upload_dir . $new_file_name;

                if (move_uploaded_file($file_tmp, $destination)) {
                    // Image uploaded successfully, now store in the database
                    $user_id = $_SESSION["user_id"];
                    $stmt = $conn->prepare("INSERT INTO posts (user_id, content, media_url) VALUES (?, ?, ?)");
                    $stmt->bind_param("iss", $user_id, $caption, $destination);

                    if ($stmt->execute()) {
                        $upload_success = "Image uploaded successfully!";
                    } else {
                        $upload_error = "Error saving image details to the database.";
                        // Optionally delete the uploaded file if database insertion fails
                        unlink($destination);
                    }
                    $stmt->close();
                } else {
                    $upload_error = "Failed to move the uploaded file.";
                }
            } else {
                $upload_error = "File size exceeds the limit (2MB).";
            }
        } else {
            $upload_error = "Invalid file type. Allowed types: jpg, jpeg, png, gif.";
        }
    } else {
        $upload_error = "Error during file upload.";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Uploader Dashboard | ViewNPost</title>
    <link rel="stylesheet" href="assets/uploader.css"> </head>
<body>
    <div class="container">
        <header>
            <h1>Welcome <?php echo $_SESSION["username"]; ?> </h1>
            <nav>
                <ul>
                    <!-- <li><a href="uploader_dashboard.php">Upload Image</a></li> -->
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </header>

        <main class="form-container">
            <h2>Upload a New Image</h2>

            <?php if ($upload_success): ?>
                <p class="success"><?php echo $upload_success; ?></p>
            <?php endif; ?>

            <?php if ($upload_error): ?>
                <p class="error"><?php echo $upload_error; ?></p>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <label for="image">Select Image:</label><br>
                <input type="file" name="image" id="image" required><br><br>

                <label for="caption">Caption (Optional):</label><br>
                <textarea name="caption" id="caption" rows="3" style="width: 90%; padding: 10px; margin: 10px 0; border-radius: 4px; border: 1px solid #ccc;"></textarea><br><br>

                <button type="submit" name="upload_image">Upload</button>
            </form>
        </main>
        <a href="my_posts.php"><button>View My Posts</button></a>

        
        <footer>
            <p>&copy; <?php echo date("Y"); ?> ViewNPost</p>
        </footer>
    </div>

    
</body>
</html>