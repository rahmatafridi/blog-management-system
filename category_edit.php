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
    header("Location: category_list.php");
    exit;
}

$error = "";
$success = "";

$stmt = $conn->prepare("SELECT name FROM blog_categories WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($name);
if (!$stmt->fetch()) {
    $stmt->close();
    header("Location: category_list.php");
    exit;
}
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_name = trim($_POST['name']);
    if (empty($new_name)) {
        $error = "Category name is required.";
    } else {
        $slug = generateSlug($new_name);
        // Check if slug or name already exists for other categories
        $stmt = $conn->prepare("SELECT id FROM blog_categories WHERE (slug = ? OR name = ?) AND id != ?");
        $stmt->bind_param("ssi", $slug, $new_name, $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "Another category with this name or slug already exists.";
        } else {
            $stmt->close();
            $stmt = $conn->prepare("UPDATE blog_categories SET name = ?, slug = ? WHERE id = ?");
            $stmt->bind_param("ssi", $new_name, $slug, $id);
            if ($stmt->execute()) {
                $success = "Category updated successfully.";
                $name = $new_name;
            } else {
                $error = "Error updating category.";
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
    <title>Edit Blog Category - Blog Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
    <div class="container mt-4">
        <h2>Edit Blog Category</h2>
        <a href="category_list.php" class="btn btn-secondary mb-3">Back to Categories</a>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="post" action="category_edit.php?id=<?php echo $id; ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Category Name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required autofocus />
            </div>
            <button type="submit" class="btn btn-primary">Update Category</button>
        </form>
    </div>
</body>
</html>
