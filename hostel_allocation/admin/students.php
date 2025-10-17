<?php
// admin/students.php
session_start();
include("../config/dbconnect.php");

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

include("sidebar.php");

$success = $error = "";

// Handle Delete Student
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM students WHERE id = $id");
    $success = "Student deleted successfully!";
}

// --- Filters ---
$search = $_GET['search'] ?? '';
$alloc_filter = $_GET['allocation'] ?? '';
$payment_filter = $_GET['payment'] ?? '';

// Build Query with Filters
$sql = "
    SELECT 
        s.id, 
        s.full_name, 
        s.matric_no, 
        s.email, 
        a.status AS allocation_status,
        r.room_number,
        p.payment_status
    FROM students s
    LEFT JOIN allocations a ON s.id = a.student_id
    LEFT JOIN rooms r ON a.room_id = r.id
    LEFT JOIN payments p ON p.allocation_id = a.id
    WHERE 1=1
";

if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $sql .= " AND (s.full_name LIKE '%$search%' OR s.matric_no LIKE '%$search%' OR s.email LIKE '%$search%')";
}

if (!empty($alloc_filter)) {
    $sql .= " AND a.status = '$alloc_filter'";
}

if (!empty($payment_filter)) {
    $sql .= " AND p.payment_status = '$payment_filter'";
}

$sql .= " ORDER BY s.id DESC";
$students = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Students | Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .main-content { margin-left: 250px; padding: 30px; min-height: 100vh; }
        .table th, .table td { vertical-align: middle; }
        .filter-bar {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }
    </style>
</head>
<body>

<div class="main-content">
    <div class="container-fluid">
        <h3 class="fw-bold text-primary mb-4">👨‍🎓 Manage Students</h3>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <!-- Filter Bar -->
        <form method="GET" class="filter-bar row g-3 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                       class="form-control" placeholder="Search by name, matric no, or email">
            </div>

            <div class="col-md-3">
                <select name="allocation" class="form-select">
                    <option value="">All Allocations</option>
                    <option value="confirmed" <?= $alloc_filter=='confirmed'?'selected':'' ?>>Confirmed</option>
                    <option value="pending" <?= $alloc_filter=='pending'?'selected':'' ?>>Pending</option>
                    <option value="none" <?= $alloc_filter=='none'?'selected':'' ?>>None</option>
                </select>
            </div>

            <div class="col-md-3">
                <select name="payment" class="form-select">
                    <option value="">All Payments</option>
                    <option value="confirmed" <?= $payment_filter=='confirmed'?'selected':'' ?>>Confirmed</option>
                    <option value="unpaid" <?= $payment_filter=='unpaid'?'selected':'' ?>>Unpaid</option>
                    <option value="none" <?= $payment_filter=='none'?'selected':'' ?>>None</option>
                </select>
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-success w-100">Filter</button>
            </div>
        </form>

        <!-- Student Table -->
        <div class="card shadow border-0">
            <div class="card-body">
                <h5 class="card-title mb-3">📋 All Registered Students</h5>

                <?php if ($students->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Full Name</th>
                                    <th>Matric No</th>
                                    <th>Email</th>
                                    <th>Room</th>
                                    <th>Allocation</th>
                                    <th>Payment</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1;
                                while ($s = $students->fetch_assoc()):
                                    $allocBadge = match($s['allocation_status']) {
                                        'confirmed' => 'success',
                                        'pending' => 'warning',
                                        default => 'secondary'
                                    };
                                    $payBadge = match($s['payment_status']) {
                                        'confirmed' => 'success',
                                        'unpaid' => 'warning',
                                        default => 'secondary'
                                    };
                                ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= htmlspecialchars($s['full_name']) ?></td>
                                    <td><?= htmlspecialchars($s['matric_no']) ?></td>
                                    <td><?= htmlspecialchars($s['email']) ?></td>
                                    <td><?= $s['room_number'] ? htmlspecialchars($s['room_number']) : '<span class="text-muted">Not Assigned</span>' ?></td>
                                    <td><span class="badge bg-<?= $allocBadge ?>"><?= ucfirst($s['allocation_status'] ?? 'None') ?></span></td>
                                    <td><span class="badge bg-<?= $payBadge ?>"><?= ucfirst($s['payment_status'] ?? 'None') ?></span></td>
                                    <td>
                                        <a href="?delete=<?= $s['id'] ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Are you sure you want to delete this student?');">
                                           Delete
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">No students found with the selected filters.</p>
                <?php endif; ?>
            </div>
        </div>

        <?php include("../student/footer.php"); ?>
    </div>
</div>

</body>
</html>
