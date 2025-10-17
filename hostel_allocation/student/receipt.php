<?php
// student/receipt.php
session_start();
include("../config/dbconnect.php");
include("sidebar.php");

if (!isset($_SESSION['student_id'])) {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['ref'])) {
    echo "<h3 style='color:red;text-align:center;'>Invalid receipt reference!</h3>";
    exit();
}

$ref = $_GET['ref'];
$student_id = $_SESSION['student_id'];

// Fetch payment + room + student details (with building and floor)
$sql = "
    SELECT 
        p.*, 
        s.full_name, 
        s.matric_no, 
        s.email, 
        r.room_number, 
        r.capacity,
        b.building_name, 
        f.floor_name
    FROM payments p
    JOIN allocations a ON p.allocation_id = a.id
    JOIN students s ON a.student_id = s.id
    JOIN rooms r ON a.room_id = r.id
    LEFT JOIN buildings b ON r.building_id = b.id
    LEFT JOIN floors f ON r.floor_id = f.id
    WHERE p.receipt_number = ? AND s.id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $ref, $student_id);
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows === 0) {
    echo "<h3 style='color:red;text-align:center;'>No receipt found!</h3>";
    exit();
}

$receipt = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hostel Voucher | Hostel Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body { background: #f8f9fa; }
        .receipt {
            max-width: 750px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        @media print {
            .no-print { display: none; }
            body { background: white; }
        }
    </style>
</head>
<body class="bg-light">
<div class="content p-4">
    <div class="receipt">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-success">🏠 Hostel Allocation Management</h3>
            <p><strong>Official Hostel Voucher</strong></p>
        </div>
        <hr>

        <div class="row mb-3">
            <div class="col-md-6">
                <p><strong>Student Name:</strong><br><?= htmlspecialchars($receipt['full_name']) ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Matric No:</strong><br><?= htmlspecialchars($receipt['matric_no']) ?></p>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <p><strong>Email:</strong><br><?= htmlspecialchars($receipt['email']) ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Receipt Number:</strong><br><?= htmlspecialchars($receipt['receipt_number']) ?></p>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <p><strong>Room Number:</strong><br><?= htmlspecialchars($receipt['room_number']) ?></p>

                <p><strong>Building:</strong><br><?= htmlspecialchars($receipt['building_name'] ?? 'N/A') ?></p>
<p><strong>Floor:</strong><br><?= htmlspecialchars($receipt['floor_name'] ?? 'N/A') ?></p>

            </div>
            <div class="col-md-6">
                <p><strong>Floor:</strong><br><?= htmlspecialchars($receipt['floor_name']) ?></p>
            </div>
        </div>


        <div class="mb-3">
            <p><strong>Building:</strong><br><?= htmlspecialchars($receipt['building_name']) ?></p>
        </div>

        <div class="mb-3">
            <p><strong>Amount:</strong><br>₦<?= number_format($receipt['amount'], 2) ?></p>
        </div>

        <div class="mb-3">
            <p><strong>Status:</strong><br>
                <?php if ($receipt['payment_status'] === 'confirmed'): ?>
                    <span class="badge bg-success">Confirmed</span>
                <?php else: ?>
                    <span class="badge bg-warning text-dark">
                        Awaiting Confirmation — Present this voucher at the Hostel Office
                    </span>
                <?php endif; ?>
            </p>
        </div>

        <div class="mb-3">
            <p><strong>Payment Date:</strong><br>
                <?= $receipt['payment_date'] ? $receipt['payment_date'] : 'Not yet confirmed' ?>
            </p>
        </div>

        <hr>
        <div class="text-center mt-4">
            <p class="text-muted mb-2">Thank you for booking your hostel room.</p>
            <button class="btn btn-success no-print" onclick="window.print()">🖨️ Print Voucher</button>
            <a href="payment_status.php" class="btn btn-secondary no-print">⬅ Back</a>
        </div>
    </div>
</div>
<?php include("footer.php"); ?>
</body>
</html>
