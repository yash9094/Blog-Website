<?php
session_start();

include "inc/config.php";
include "inc/header.php";

// Initialize variables for form data
$title = "";
$content = "";
$error = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $title = $_POST['title'];
    $content = $_POST['content'];
    $image = $_FILES['image'];

    // Basic validation
    if (empty($title) || empty($content)) {
        $error = "Title and content are required.";
    } else {
        $encodedImage = null;

        // Image Handling
        if (!empty($image['name'])) {
            $uploadOk = 1;

            // Check if file is an image
            $check = getimagesize($image["tmp_name"]);
            if ($check === false) {
                $error = "File is not an image.";
                $uploadOk = 0;
            }
            // Check image size
            if ($image["size"] > 5000000) {
                $error = "Sorry, your file is too large.";
                $uploadOk = 0;
            }

            // Check image format
            $imageFileType = strtolower(pathinfo($image["name"], PATHINFO_EXTENSION));
            if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }

            // If upload is okay, process the image
            if ($uploadOk == 0) {
                $error = "Sorry, your image was not uploaded.";
            } else {
                // Read the image content
                $imageData = file_get_contents($image["tmp_name"]);
                $encodedImage = base64_encode($imageData); // Base64 encode the image data
                // Append the encoded image to the content
                $content .= "<img src='data:image/jpeg;base64,{$encodedImage}' alt='Post Image' />";
            }
        }

        
        $stmt = $conn->prepare("INSERT INTO posts (title, content, user_id) VALUES (?, ?, ?)");
        $userId = $_SESSION['user_id'];
        $stmt->bind_param("ssi", $title, $content,$userId);

        if ($stmt->execute()) {

            $_SESSION['success_message'] = "Post uploaded successfully!";
        
            header("Location: add_post.php");
            exit();
        } else {
            $error = "Error: " . $stmt->error;
        }
    }
}

// Displaying Posts of User
// Fetch posts from the database
$userId = $_SESSION['user_id']; 
$query = "SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC"; 
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Post</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
<div id="toast" class="toast"></div>
    <div class="post-container">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="post-success"><?php echo $_SESSION['success_message']; ?></div>
            <?php unset($_SESSION['success_message']);  ?>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="post-error"><?php echo $error; ?></div>
        <?php endif; ?>
        <h1 class="post-heading">Add New Post</h1>

        <form action="add_post.php" method="POST" enctype="multipart/form-data">
            <div class="post-form-group">
                <label for="title" class="post-lbl">Title:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>">
            </div>
            <div class="post-form-group">
                <label for="content" class="post-lbl">Content:</label>
                <textarea id="content" name="content" rows="10"><?php echo htmlspecialchars($content); ?></textarea>
            </div>
            <div class="post-form-group">
                <label for="image">Image (optional):</label>
                <input type="file" id="image" name="image" accept="image/*">
            </div>
            <button type="submit" class="post-btn">Add Post</button>
        </form>

        <h2 class="existing-post-header">Your Posts</h2>
        <div class="posts-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="user-post">
                    <h3 id="post-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p>
                        <?php
                    
                        echo nl2br(htmlspecialchars($row['content']));
                        ?>
                    </p>

                    <p><em>Posted on <?php echo date('Y-m-d H:i:s', strtotime($row['created_at'])); ?></em></p>
                    <a href="edit_post.php?id=<?php echo $row['id']; ?>" class="post-edit-btn">Edit</a>
                    <a href="delete_post.php?id=<?php echo $row['id']; ?>" class="post-delete-btn" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                </div>
            <?php endwhile; ?>
            <?php else: ?>
                <p>You Have No Post Yet!</p>
            <?php endif; ?>
        </div>

    </div>
    
       
</body>

<?php
include "inc/footer.php";
?>

</html>
