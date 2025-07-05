<?php
session_start();
require_once "config.php";
require_once "functions.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$name = "";
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    if (empty($name)) {
        $error = "Category name is required.";
    } else {
        $slug = generateSlug($name);
        // Check if slug or name already exists
        $stmt = $conn->prepare("SELECT id FROM blog_categories WHERE slug = ? OR name = ?");
        $stmt->bind_param("ss", $slug, $name);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "Category with this name or slug already exists.";
        } else {
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO blog_categories (name, slug) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $slug);
            if ($stmt->execute()) {
                $success = "Category added successfully.";
                $name = "";
            } else {
                $error = "Error adding category.";
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add Blog Category - Blog Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
    <div class="container mt-4">
        <h2>Add Blog Category</h2>
        <a href="category_list.php" class="btn btn-secondary mb-3">Back to Categories</a>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="post" action="category_add.php">
            <div class="mb-3">
                <label for="name" class="form-label">Category Name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required autofocus />
            </div>
            <button type="submit" class="btn btn-primary">Add Category</button>
        </form>
    </div>
</body>
</html>
