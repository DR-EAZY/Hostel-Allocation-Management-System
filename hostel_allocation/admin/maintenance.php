<?php
// admin/maintenance.php
session_start();
include("../config/dbconnect.php");

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

include("sidebar.php");

$success = $error = "";

// Handle Status Update
if (isset($_POST['update_status'])) {
    $request_id = intval($_POST['request_id']);
    $new_status = $_POST['status'];

    $update = $conn->prepare("UPDATE maintenance_requests SET status = ? WHERE id = ?");
    $update->bind_param("si", $new_status, $request_id);

    if ($update->execute()) {
        $success = "Maintenance request updated successfully!";
    } else {
        $error = "Error updating maintenance status.";
    }
}

// Fetch all maintenance requests
$sql = "SELECT m.*, s.full_name, s.matric_no, r.room_number 
        FROM maintenance_requests m
        JOIN students s ON m.student_id = s.id
        JOIN rooms r ON m.room_id = r.id
        ORDER BY m.id DESC";
$requests = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Maintenance | Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .main-content {
            margin-left: 250px;
            padding: 30px;
            min-height: 100vh;
        }
        .table th, .table td { vertical-align: middle; }
    </style>
</head>
<body>

<div class="main-content">
    <div class="container-fluid">
        <h3 class="fw-bold text-primary mb-4">🧰 Manage Maintenance Requests</h3>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="card shadow border-0">
            <div class="card-body">
                <h5 class="card-title mb-3">📋 All Maintenance Requests</h5>

                <?php if ($requests->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Student Name</th>
                                    <th>Matric No</th>
                                    <th>Room No</th>
                                    <th>Issue</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1;
                                while ($req = $requests->fetch_assoc()): 
                                    $badge = match($req['status']) {
                                        'resolved' => 'success',
                                        'in_progress' => 'warning',
                                        default => 'secondary'
                                    };
                                ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= htmlspecialchars($req['full_name']) ?></td>
                                    <td><?= htmlspecialchars($req['matric_no']) ?></td>
                                    <td><?= htmlspecialchars($req['room_number']) ?></td>
                                    <td><?= nl2br(htmlspecialchars($req['issue_description'])) ?></td>
                                    <td><span class="badge bg-<?= $badge ?>"><?= ucfirst($req['status']) ?></span></td>
                                    <td><?= htmlspecialchars($req['submitted_at']) ?></td>
                                    <td>
                                        <form method="POST" class="d-flex gap-2">
                                            <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                                            <select name="status" class="form-select form-select-sm" required>
                                                <option value="pending" <?= $req['status']=='pending'?'selected':'' ?>>Pending</option>
                                                <option value="in_progress" <?= $req['status']=='in_progress'?'selected':'' ?>>In Progress</option>
                                                <option value="resolved" <?= $req['status']=='resolved'?'selected':'' ?>>Resolved</option>
                                            </select>
                                            <button type="submit" name="update_status" class="btn btn-sm btn-success">Update</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center mb-0">No maintenance requests found.</p>
                <?php endif; ?>
            </div>
        </div>

        <?php include("../student/footer.php"); ?>
    </div>
</div>

</body>
</html>
