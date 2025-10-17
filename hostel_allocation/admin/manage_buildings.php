<?php
// admin/manage_buildings.php
session_start();
include("../config/dbconnect.php");

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

include("sidebar.php");

$success = $error = "";

// Handle Add Building
if (isset($_POST['add_building'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);

    if (empty($name)) {
        $error = "Building name is required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO buildings (name, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $description);
        if ($stmt->execute()) {
            $success = "Building added successfully!";
        } else {
            $error = "Error adding building.";
        }
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM buildings WHERE id = $id");
    $success = "Building deleted successfully!";
}

// Fetch all buildings
$buildings = $conn->query("SELECT * FROM buildings ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Buildings | Hostel Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .main-content { margin-left: 250px; padding: 30px; }
        .table th, .table td { vertical-align: middle; }
    </style>
</head>
<body>

<div class="main-content">
    <div class="container-fluid">
        <h3 class="fw-bold text-primary mb-4">🏢 Manage Buildings</h3>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Add Building Form -->
        <div class="card shadow border-0 mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">➕ Add New Building</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Building Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Ramat Hall" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description (optional)</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="e.g. Female Hostel, 3 floors"></textarea>
                    </div>
                    <button type="submit" name="add_building" class="btn btn-primary">Add Building</button>
                </form>
            </div>
        </div>

        <!-- Buildings List -->
        <div class="card shadow border-0">
            <div class="card-body">
                <h5 class="card-title mb-3">🏠 Existing Buildings</h5>

                <?php if ($buildings->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-primary">
                                <tr>
                                    <th>ID</th>
                                    <th>Building Name</th>
                                    <th>Description</th>
                                    <th>Date Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($b = $buildings->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $b['id'] ?></td>
                                        <td><?= htmlspecialchars($b['building_name']) ?></td>
                                        <td><?= htmlspecialchars($b['description']) ?></td>
                                        <td><?= $b['created_at'] ?? '-' ?></td>
                                        <td>
                                            <a href="?delete=<?= $b['id'] ?>" 
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Are you sure you want to delete this building?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No buildings added yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <?php include("../student/footer.php"); ?>
    </div>
</div>

</body>
</html>
