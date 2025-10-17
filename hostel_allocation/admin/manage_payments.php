<?php
// admin/manage_payments.php
session_start();
include("../config/dbconnect.php");

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

include("sidebar.php");

$success = $error = "";

// Handle Payment Confirmation
if (isset($_GET['confirm'])) {
    $payment_id = intval($_GET['confirm']);
    $update = $conn->prepare("UPDATE payments SET payment_status='confirmed', payment_date=NOW() WHERE id=?");
    $update->bind_param("i", $payment_id);
    if ($update->execute()) {
        $success = "Payment confirmed successfully!";
    } else {
        $error = "Error updating payment.";
    }
}

// Fetch All Payments
$sql = "SELECT p.*, s.full_name, s.matric_no, a.room_id, r.room_number 
        FROM payments p
        JOIN students s ON p.student_id = s.id
        JOIN allocations a ON p.allocation_id = a.id
        JOIN rooms r ON a.room_id = r.id
        ORDER BY p.id DESC";
$payments = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Payments | Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .main-content {
            margin-left: 250px; /* sidebar space */
            padding: 30px;
            min-height: 100vh;
        }
        .table th, .table td { vertical-align: middle; }
    </style>
</head>
<body>

<div class="main-content">
    <div class="container-fluid">
        <h3 class="fw-bold text-primary mb-4">💰 Manage Student Payments</h3>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="card shadow border-0">
            <div class="card-body">
                <h5 class="card-title mb-3">📋 Payment Records</h5>

                <?php if ($payments->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Student Name</th>
                                    <th>Matric No</th>
                                    <th>Room Number</th>
                                    <th>Receipt No</th>
                                    <th>Amount (₦)</th>
                                    <th>Status</th>
                                    <th>Payment Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1;
                                while ($p = $payments->fetch_assoc()): 
                                    $badgeClass = $p['payment_status'] == 'confirmed' ? 'success' : 'warning';
                                ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= htmlspecialchars($p['full_name']) ?></td>
                                    <td><?= htmlspecialchars($p['matric_no']) ?></td>
                                    <td><?= htmlspecialchars($p['room_number']) ?></td>
                                    <td><?= htmlspecialchars($p['receipt_number']) ?></td>
                                    <td><?= number_format($p['amount'], 2) ?></td>
                                    <td><span class="badge bg-<?= $badgeClass ?>"><?= ucfirst($p['payment_status']) ?></span></td>
                                    <td><?= $p['payment_date'] ? htmlspecialchars($p['payment_date']) : '-' ?></td>
                                    <td>
                                        <?php if ($p['payment_status'] === 'unpaid'): ?>
                                            <a href="?confirm=<?= $p['id'] ?>" 
                                               class="btn btn-sm btn-success"
                                               onclick="return confirm('Confirm this payment?');">
                                               Confirm
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Confirmed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center mb-0">No payments found.</p>
                <?php endif; ?>
            </div>
        </div>

        <?php include("../student/footer.php"); ?>
    </div>
</div>

</body>
</html>
