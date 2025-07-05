<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$sql = "SELECT * FROM blog_categories ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Blog Categories - Blog Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
    <div class="container mt-4">
        <h2>Blog Categories</h2>
        <a href="category_add.php" class="btn btn-success mb-3">Add New Category</a>
        <a href="logout.php" class="btn btn-secondary mb-3 float-end">Logout</a>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['slug']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td>
                                <a href="category_edit.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                <a href="category_delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center">No categories found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
