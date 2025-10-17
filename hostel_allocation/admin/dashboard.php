<?php
// admin/dashboard.php
session_start();
include("../config/dbconnect.php");

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

include("sidebar.php");

// Fetch Dashboard Statistics
$total_buildings = $conn->query("SELECT COUNT(*) AS total FROM buildings")->fetch_assoc()['total'];
$total_floors = $conn->query("SELECT COUNT(*) AS total FROM floors")->fetch_assoc()['total'];
$total_rooms = $conn->query("SELECT COUNT(*) AS total FROM rooms")->fetch_assoc()['total'];
$total_students = $conn->query("SELECT COUNT(*) AS total FROM students")->fetch_assoc()['total'];
$total_allocations = $conn->query("SELECT COUNT(*) AS total FROM allocations")->fetch_assoc()['total'];
$total_payments = $conn->query("SELECT COUNT(*) AS total FROM payments WHERE payment_status = 'confirmed'")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Hostel Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .main-content { margin-left: 250px; padding: 30px; min-height: 100vh; }
        .dashboard-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        .dashboard-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 14px rgba(0,0,0,0.12);
        }
        .icon-box {
            font-size: 2rem;
            color: white;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<div class="main-content">
    <div class="container-fluid">
        <h3 class="fw-bold text-success mb-4">👋 Welcome, <?= htmlspecialchars($_SESSION['admin_name']) ?></h3>

        <!-- Dashboard Statistics -->
        <div class="row g-4">
            <div class="col-md-4 col-lg-3">
                <div class="card dashboard-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-box bg-success me-3">
                            🏢
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Buildings</h6>
                            <h4><?= $total_buildings ?></h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-lg-3">
                <div class="card dashboard-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-box bg-primary me-3">
                            🧱
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Floors</h6>
                            <h4><?= $total_floors ?></h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-lg-3">
                <div class="card dashboard-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-box bg-warning me-3">
                            🛏️
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Rooms</h6>
                            <h4><?= $total_rooms ?></h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-lg-3">
                <div class="card dashboard-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-box bg-info me-3">
                            👨‍🎓
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Students</h6>
                            <h4><?= $total_students ?></h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-lg-3">
                <div class="card dashboard-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-box bg-secondary me-3">
                            🗂️
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Allocations</h6>
                            <h4><?= $total_allocations ?></h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-lg-3">
                <div class="card dashboard-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-box bg-danger me-3">
                            💳
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Confirmed Payments</h6>
                            <h4><?= $total_payments ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Optional: You can add recent activity or announcements here -->
        <div class="card mt-5 border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">📢 System Overview</h5>
                <p class="text-muted mb-0">
                    You can use the left sidebar to manage buildings, floors, rooms, payments, and announcements.
                    <br>Keep this dashboard as your central overview of hostel activity.
                </p>
            </div>
        </div>

        <?php include("../student/footer.php"); ?>
    </div>
</div>

</body>
</html>
