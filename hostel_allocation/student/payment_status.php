<?php
session_start();
include("../config/dbconnect.php");
include("sidebar.php");

if (!isset($_SESSION['student_id'])) {
    header("Location: ../index.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch payment info
$sql = "SELECT p.*, r.room_number
        FROM payments p
        JOIN allocations a ON p.allocation_id = a.id
        JOIN rooms r ON a.room_id = r.id
        WHERE a.student_id = ?
        ORDER BY p.id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$payment = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Status | Student Portal</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        /* Sidebar space fix */
        .main-content {
            margin-left: 250px; /* Same width as sidebar */
            padding: 20px;
            min-height: 100vh;
        }
        </style>
        </head>
<body class="bg-light">
<div class="main-content">
    <div class="container-fluid">
        <h3 class="mb-4 fw-bold">🧾 Payment Status</h3>

        <?php if ($payment): ?>
            <div class="card shadow border-0">
                <div class="card-body">
                    <p><strong>Room Number:</strong> <?= htmlspecialchars($payment['room_number']) ?></p>
                    <p><strong>Receipt Number:</strong> <?= htmlspecialchars($payment['receipt_number']) ?></p>
                    <p><strong>Amount:</strong> ₦<?= number_format($payment['amount'], 2) ?></p>
                    <p><strong>Status:</strong>
                        <?php if ($payment['payment_status'] === 'confirmed'): ?>
                            <span class="badge bg-success">Confirmed</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">Pending Confirmation</span>
                        <?php endif; ?>
                    </p>
                    <p><strong>Date:</strong> <?= $payment['payment_date'] ? $payment['payment_date'] : 'Not yet confirmed' ?></p>

                    <hr>
                    <a href="receipt.php?ref=<?= urlencode($payment['receipt_number']) ?>" class="btn btn-outline-primary mt-3">
    View / Print Receipt
</a>

                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                No payment record found yet. Please book a room.
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include("footer.php"); ?>
</body>
</html>
