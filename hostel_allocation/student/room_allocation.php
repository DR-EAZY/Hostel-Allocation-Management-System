<?php
// student/room_allocation.php
session_start();
include("../config/dbconnect.php");

// Redirect if not logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: ../index.php");
    exit();
}

include("sidebar.php");

$student_id = $_SESSION['student_id'];

// ✅ Fetch student's room allocation details
$sql = "SELECT a.*, r.room_number, r.capacity, r.status AS room_status
        FROM allocations a
        JOIN rooms r ON a.room_id = r.id
        WHERE a.student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$allocation = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Room Allocation | Student Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .main-content {
            margin-left: 250px; /* Sidebar width */
            padding: 30px;
            min-height: 100vh;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
            background: #fff;
        }
        .card h5 {
            color: #198754;
        }
        .badge {
            font-size: 0.9rem;
        }
        footer {
            text-align: center;
            padding: 15px;
            background: #198754;
            color: white;
            margin-top: 40px;
            border-radius: 0 0 10px 10px;
        }
    </style>
</head>
<body>

<div class="main-content">
    <div class="container-fluid">
        <h3 class="mb-4 fw-bold text-success">🏠 My Room Allocation</h3>

        <?php if ($allocation): ?>
            <div class="card p-4 mb-4">
                <h5 class="card-title mb-3">📋 Allocation Details</h5>

                <div class="row mb-2">
                    <div class="col-md-6">
                        <p><strong>Room Number:</strong> <?= htmlspecialchars($allocation['room_number']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Room Capacity:</strong> <?= htmlspecialchars($allocation['capacity']) ?> Students</p>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-6">
                        <p><strong>Room Status:</strong>
                            <?php 
                                $badgeClass = $allocation['room_status'] === 'available' ? 'success' : 
                                              ($allocation['room_status'] === 'full' ? 'danger' : 'warning');
                            ?>
                            <span class="badge bg-<?= $badgeClass ?>">
                                <?= ucfirst($allocation['room_status']) ?>
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Allocation Date:</strong> 
                            <?= htmlspecialchars($allocation['allocated_at'] ?? 'Not available') ?>
                        </p>
                    </div>
                </div>

                <hr>
                <p class="text-success fw-bold">
                    ✅ You are currently allocated to Room <?= htmlspecialchars($allocation['room_number']) ?>.
                </p>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center shadow-sm border-0">
                You have not been allocated a room yet.<br>
                Please complete your booking and payment at the hostel office.
            </div>
        <?php endif; ?>

        <?php include("footer.php"); ?>
    </div>
</div>

</body>
</html>
