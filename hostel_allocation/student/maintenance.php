<?php
// student/maintenance.php
session_start();
include("../config/dbconnect.php");

// Redirect if not logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: ../index.php");
    exit();
}

include("sidebar.php");

$student_id = $_SESSION['student_id'];
$error = $success = "";

// Get student's room info
$sql = "SELECT a.room_id, r.room_number 
        FROM allocations a
        JOIN rooms r ON a.room_id = r.id
        WHERE a.student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$room_data = $stmt->get_result()->fetch_assoc();

// Handle new maintenance request
if (isset($_POST['submit_request'])) {
    $issue_description = trim($_POST['issue_description']);
    $room_id = $room_data['room_id'] ?? null;

    if (empty($issue_description)) {
        $error = "Please describe the issue.";
    } elseif (!$room_id) {
        $error = "No room allocation found.";
    } else {
        $sql = "INSERT INTO maintenance_requests (student_id, room_id, issue_description, status)
                VALUES (?, ?, ?, 'pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $student_id, $room_id, $issue_description);
        if ($stmt->execute()) {
            $success = "✅ Your maintenance request has been submitted.";
        } else {
            $error = "Error submitting request.";
        }
    }
}

// Fetch student's requests
$requests = $conn->query("
    SELECT * FROM maintenance_requests 
    WHERE student_id = $student_id 
    ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Maintenance Requests | Student Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .main-content {
            margin-left: 250px; /* same width as sidebar */
            padding: 30px;
            min-height: 100vh;
        }
        .card {
            border: none;
            border-radius: 12px;
        }
        .table th {
            background-color: #d1e7dd;
        }
        textarea {
            resize: none;
        }
    </style>
</head>
<body>

<div class="main-content">
    <div class="container-fluid">
        <h3 class="mb-4 fw-bold text-success">🧰 Maintenance Requests</h3>

        <!-- Feedback Messages -->
        <?php if (!empty($error)) : ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (!empty($success)) : ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <!-- Submit Request Form -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">📝 Submit a New Request</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Room Number</label>
                        <input type="text" class="form-control" 
                            value="<?= htmlspecialchars($room_data['room_number'] ?? 'N/A') ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Issue Description</label>
                        <textarea name="issue_description" rows="3" class="form-control"
                                  placeholder="Describe the issue (e.g. broken bulb, water leak, etc.)" required></textarea>
                    </div>
                    <button type="submit" name="submit_request" class="btn btn-success">Submit Request</button>
                </form>
            </div>
        </div>

        <!-- Request History -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">📋 Your Maintenance History</h5>

                <?php if ($requests->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Issue Description</th>
                                    <th>Status</th>
                                    <th>Date Submitted</th>
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
                                    <td><?= nl2br(htmlspecialchars($req['issue_description'])) ?></td>
                                    <td><span class="badge bg-<?= $badge ?>"><?= ucfirst($req['status']) ?></span></td>
                                    <td><?= htmlspecialchars($req['submitted_at']) ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">No maintenance requests yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <?php include("footer.php"); ?>
    </div>
</div>

</body>
</html>
