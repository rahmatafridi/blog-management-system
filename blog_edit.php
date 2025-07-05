<?php
session_start();
require_once "config.php";
require_once "functions.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header("Location: blog_list.php");
    exit;
}

$error = "";
$success = "";

$status_options = ['draft', 'published'];

// Fetch categories for dropdown
$categories = [];
$cat_result = $conn->query("SELECT id, name FROM blog_categories ORDER BY name ASC");
if ($cat_result) {
    while ($row = $cat_result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Fetch existing blog data
$stmt = $conn->prepare("SELECT seo_title, name, slug, status, content, image, date, author, seo_keyword, seo_description, category_id FROM blogs WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($seo_title, $name, $slug, $status, $content, $image, $date, $author, $seo_keyword, $seo_description, $category_id);
if (!$stmt->fetch()) {
    $stmt->close();
    header("Location: blog_list.php");
    exit;
}
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $seo_title = trim($_POST['seo_title']);
    $name = trim($_POST['name']);
    $status = in_array($_POST['status'], $status_options) ? $_POST['status'] : 'draft';
    $content = $_POST['content'];
    $date = $_POST['date'];
    $author = trim($_POST['author']);
    $seo_keyword = trim($_POST['seo_keyword']);
    $seo_description = trim($_POST['seo_description']);
    $category_id = intval($_POST['category_id']);

    if (empty($name)) {
        $error = "Name is required.";
    } else {
        $new_slug = generateSlug($name);

        // Check if slug already exists for other blogs
        $stmt = $conn->prepare("SELECT id FROM blogs WHERE slug = ? AND id != ?");
        $stmt->bind_param("si", $new_slug, $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "Another blog with this slug already exists.";
        }
        $stmt->close();

        if (empty($error)) {
            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] != UPLOAD_ERR_NO_FILE) {
                $uploaded_image = uploadImage($_FILES['image']);
                if ($uploaded_image === false) {
                    $error = "Failed to upload image. Allowed types: jpg, jpeg, png, gif.";
                } else {
                    // Delete old image file if exists
                    if ($image && file_exists('uploads/' . $image)) {
                        unlink('uploads/' . $image);
                    }
                    $image = $uploaded_image;
                }
            }

            if (empty($error)) {
                $stmt = $conn->prepare("UPDATE blogs SET seo_title = ?, name = ?, slug = ?, status = ?, content = ?, image = ?, date = ?, author = ?, seo_keyword = ?, seo_description = ?, category_id = ? WHERE id = ?");
                $stmt->bind_param("ssssssssssii", $seo_title, $name, $new_slug, $status, $content, $image, $date, $author, $seo_keyword, $seo_description, $category_id, $id);
                if ($stmt->execute()) {
                    $success = "Blog updated successfully.";
                    $slug = $new_slug;
                } else {
                    $error = "Error updating blog.";
                }
                $stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Blog - Blog Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
</head>
<body>
    <div class="container mt-4">
        <h2>Edit Blog</h2>
        <a href="blog_list.php" class="btn btn-secondary mb-3">Back to Blogs</a>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="post" action="blog_edit.php?id=<?php echo $id; ?>" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="seo_title" class="form-label">SEO Title</label>
                <input type="text" name="seo_title" id="seo_title" class="form-control" value="<?php echo htmlspecialchars($seo_title); ?>" />
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required />
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <?php foreach ($status_options as $option): ?>
                        <option value="<?php echo $option; ?>" <?php echo ($status === $option) ? 'selected' : ''; ?>><?php echo ucfirst($option); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Blog Content</label>
                <textarea name="content" id="content" class="form-control"><?php echo htmlspecialchars($content); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Main Image</label>
                <?php if ($image): ?>
                    <div class="mb-2">
                        <img src="uploads/<?php echo htmlspecialchars($image); ?>" alt="Current Image" style="max-width: 200px;" />
                    </div>
                <?php endif; ?>
                <input type="file" name="image" id="image" class="form-control" accept="image/*" />
            </div>
            <div class="mb-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" name="date" id="date" class="form-control" value="<?php echo htmlspecialchars($date); ?>" />
            </div>
            <div class="mb-3">
                <label for="author" class="form-label">Author</label>
                <input type="text" name="author" id="author" class="form-control" value="<?php echo htmlspecialchars($author); ?>" />
            </div>
            <div class="mb-3">
                <label for="seo_keyword" class="form-label">SEO Keyword</label>
                <input type="text" name="seo_keyword" id="seo_keyword" class="form-control" value="<?php echo htmlspecialchars($seo_keyword); ?>" />
            </div>
            <div class="mb-3">
                <label for="seo_description" class="form-label">SEO Description</label>
                <textarea name="seo_description" id="seo_description" class="form-control"><?php echo htmlspecialchars($seo_description); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select name="category_id" id="category_id" class="form-select">
                    <option value="0">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($category_id == $cat['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Blog</button>
        </form>
    </div>
    <script>
        CKEDITOR.replace('content');
    </script>
</body>
</html>
