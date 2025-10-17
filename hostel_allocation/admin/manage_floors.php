<?php
// admin/manage_floors.php
session_start();
include("../config/dbconnect.php");

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

include("sidebar.php");

$success = $error = "";

// Fetch all buildings (for dropdown)
$buildings = $conn->query("SELECT * FROM buildings ORDER BY building_name ASC");

// Handle Add Floor
if (isset($_POST['add_floor'])) {
    $building_id = intval($_POST['building_id']);
    $floor_name = trim($_POST['floor_name']);

    if (empty($building_id) || empty($floor_name)) {
        $error = "All fields are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO floors (building_id, floor_name) VALUES (?, ?)");
        $stmt->bind_param("is", $building_id, $floor_name);
        if ($stmt->execute()) {
            $success = "Floor added successfully!";
        } else {
            $error = "Error adding floor.";
        }
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM floors WHERE id = $id");
    $success = "Floor deleted successfully!";
}

// Fetch floors with building name
$floors = $conn->query("
    SELECT f.*, b.building_name AS building_name 
    FROM floors f 
    JOIN buildings b ON f.building_id = b.id 
    ORDER BY b.building_name ASC, f.id ASC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Floors | Hostel Management</title>
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
        <h3 class="fw-bold text-primary mb-4">🧱 Manage Floors</h3>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Add Floor Form -->
        <div class="card shadow border-0 mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">➕ Add New Floor</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Select Building</label>
                        <select name="building_id" class="form-select" required>
                            <option value="">-- Choose Building --</option>
                            <?php while ($b = $buildings->fetch_assoc()): ?>
                                <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Floor Name</label>
                        <input type="text" name="floor_name" class="form-control" placeholder="e.g. Ground Floor, 1st Floor" required>
                    </div>

                    <button type="submit" name="add_floor" class="btn btn-primary">Add Floor</button>
                </form>
            </div>
        </div>

        <!-- Floors List -->
        <div class="card shadow border-0">
            <div class="card-body">
                <h5 class="card-title mb-3">🏢 Existing Floors</h5>

                <?php if ($floors->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Building</th>
                                    <th>Floor Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; while ($f = $floors->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= htmlspecialchars($f['building_name']) ?></td>
                                        <td><?= htmlspecialchars($f['floor_name']) ?></td>
                                        <td>
                                            <a href="?delete=<?= $f['id'] ?>" 
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Are you sure you want to delete this floor?')">
                                               Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No floors added yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <?php include("../student/footer.php"); ?>
    </div>
</div>

</body>
</html>
