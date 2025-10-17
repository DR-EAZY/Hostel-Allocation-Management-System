<?php
// admin/manage_rooms.php
session_start();
include("../config/dbconnect.php");

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Handle Add Room
if (isset($_POST['add_room'])) {
    $room_number = trim($_POST['room_number']);
    $capacity = intval($_POST['capacity']);
    $floor_id = intval($_POST['floor_id']);

    if (empty($room_number) || $capacity <= 0 || $floor_id <= 0) {
        $error = "All fields are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO rooms (floor_id, room_number, capacity, occupied, status) VALUES (?, ?, ?, 0, 'available')");
        $stmt->bind_param("isi", $floor_id, $room_number, $capacity);
        if ($stmt->execute()) {
            $success = "Room added successfully.";
        } else {
            $error = "Error adding room.";
        }
    }
}

// Handle Delete Room
if (isset($_GET['delete'])) {
    $room_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $room_id);
    if ($stmt->execute()) {
        $success = "Room deleted successfully.";
    } else {
        $error = "Error deleting room.";
    }
}

// Handle Edit Room
if (isset($_POST['update_room'])) {
    $room_id = intval($_POST['room_id']);
    $room_number = trim($_POST['room_number']);
    $capacity = intval($_POST['capacity']);
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE rooms SET room_number = ?, capacity = ?, status = ? WHERE id = ?");
    $stmt->bind_param("sisi", $room_number, $capacity, $status, $room_id);
    if ($stmt->execute()) {
        $success = "Room updated successfully.";
    } else {
        $error = "Error updating room.";
    }
}

// Fetch buildings and floors
$buildings = $conn->query("SELECT * FROM buildings ORDER BY building_name ASC");
$selected_building = isset($_POST['building_id']) ? intval($_POST['building_id']) : 0;
$floors = $selected_building
    ? $conn->query("SELECT * FROM floors WHERE building_id = $selected_building ORDER BY floor_number ASC")
    : $conn->query("SELECT * FROM floors ORDER BY building_id, floor_number ASC");

// Fetch all rooms
$rooms = $conn->query("
    SELECT r.id, r.room_number, r.capacity, r.occupied, r.status,
           f.floor_number, b.building_name AS building_name
    FROM rooms r
    LEFT JOIN floors f ON r.floor_id = f.id
    LEFT JOIN buildings b ON f.building_id = b.id
    ORDER BY b.building_name, f.floor_number, r.room_number
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Rooms | Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .main-content { margin-left: 250px; padding: 30px; min-height: 100vh; }
        .card { border: none; border-radius: 12px; box-shadow: 0 3px 8px rgba(0,0,0,0.1); }
        .card h5 { color: #198754; }
        .table td, .table th { vertical-align: middle; }
    </style>
</head>
<body>

<?php include("sidebar.php"); ?>

<div class="main-content">
    <div class="container-fluid">
        <h3 class="fw-bold text-success mb-4">🏢 Manage Hostel Rooms</h3>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <!-- Add Room Form -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">➕ Add New Room</h5>
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Select Building</label>
                            <select name="building_id" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Choose Building --</option>
                                <?php while ($b = $buildings->fetch_assoc()): ?>
                                    <option value="<?= $b['id'] ?>" <?= $selected_building == $b['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($b['name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Select Floor</label>
                            <select name="floor_id" class="form-select" required>
                                <option value="">-- Choose Floor --</option>
                                <?php while ($f = $floors->fetch_assoc()): ?>
                                    <option value="<?= $f['id'] ?>">Floor <?= htmlspecialchars($f['floor_number']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Room Number</label>
                            <input type="text" name="room_number" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Capacity</label>
                            <input type="number" name="capacity" min="1" class="form-control" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" name="add_room" class="btn btn-success">Add Room</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Rooms List -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">🏘️ Existing Rooms</h5>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-success">
                            <tr>
                                <th>Building</th>
                                <th>Floor</th>
                                <th>Room Number</th>
                                <th>Capacity</th>
                                <th>Occupied</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($rooms->num_rows > 0): ?>
                                <?php while ($r = $rooms->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($r['building_name']) ?></td>
                                        <td><?= htmlspecialchars($r['floor_number']) ?></td>
                                        <td><?= htmlspecialchars($r['room_number']) ?></td>
                                        <td><?= $r['capacity'] ?></td>
                                        <td><?= $r['occupied'] ?></td>
                                        <td>
                                            <span class="badge bg-<?= $r['status'] == 'available' ? 'success' : ($r['status'] == 'full' ? 'danger' : 'warning') ?>">
                                                <?= ucfirst($r['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $r['id'] ?>">Edit</button>
                                            <a href="?delete=<?= $r['id'] ?>" onclick="return confirm('Delete this room?')" class="btn btn-sm btn-danger">Delete</a>
                                        </td>
                                    </tr>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editModal<?= $r['id'] ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST">
                                                    <div class="modal-header bg-success text-white">
                                                        <h5 class="modal-title">Edit Room <?= htmlspecialchars($r['room_number']) ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="room_id" value="<?= $r['id'] ?>">
                                                        <div class="mb-3">
                                                            <label class="form-label">Room Number</label>
                                                            <input type="text" name="room_number" value="<?= htmlspecialchars($r['room_number']) ?>" class="form-control" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Capacity</label>
                                                            <input type="number" name="capacity" value="<?= $r['capacity'] ?>" min="1" class="form-control" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Status</label>
                                                            <select name="status" class="form-select">
                                                                <option value="available" <?= $r['status'] == 'available' ? 'selected' : '' ?>>Available</option>
                                                                <option value="full" <?= $r['status'] == 'full' ? 'selected' : '' ?>>Full</option>
                                                                <option value="under_maintenance" <?= $r['status'] == 'under_maintenance' ? 'selected' : '' ?>>Under Maintenance</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" name="update_room" class="btn btn-success">Save Changes</button>
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center text-muted">No rooms added yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php include("../student/footer.php"); ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
