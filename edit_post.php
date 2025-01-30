<?php
session_start();
include "inc/config.php";
include "inc/header.php";

$title = "";
$content = "";
$error = "";

if (isset($_GET['id'])) {
    $postId = $_GET['id'];
    $userId = $_SESSION['user_id'];

    $query = "SELECT * FROM posts WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $postId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $post = $result->fetch_assoc();
        $title = $post['title'];
        $content = $post['content'];
    } else {
        $error = "Post not found.";
    }
} else {
    header("Location: add_post.php"); 
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

            
            if ($uploadOk == 0) {
                $error = "Sorry, your image was not uploaded.";
            } else {

                $imageData = file_get_contents($image["tmp_name"]);
                $encodedImage = base64_encode($imageData); 
                $content .= "<img src='data:image/jpeg;base64,{$encodedImage}' alt='Post Image' />";
            }
        }

        $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ssii", $title, $content, $postId, $userId);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Post updated successfully!";
            header("Location: add_post.php");
            exit();
        } else {
            $error = "Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="post-container">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="post-success"><?php echo $_SESSION['success_message']; ?></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="post-error"><?php echo $error; ?></div>
        <?php endif; ?>
        <h1 class="post-heading">Edit Post</h1>

        <form action="edit_post.php?id=<?php echo $postId; ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="post_id" value="<?php echo $postId; ?>">
            <div class="post-form -group">
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
            <button type="submit" class="post-btn">Edit Post</button>
        </form>
    </div>
</body>

<?php
include "inc/footer.php";
?>
</html>