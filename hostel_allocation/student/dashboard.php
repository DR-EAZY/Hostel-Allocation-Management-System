<?php
// student/dashboard.php
session_start();
include("../config/dbconnect.php");

if (!isset($_SESSION['student_id'])) {
    header("Location: ../index.php");
    exit();
}

$student_name = $_SESSION['student_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard | Hostel Management</title>
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

        /* About section styling */
        .about-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .about-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-bottom: 1px solid #ddd;
        }

        .dashboard-grid .card {
            border: none;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .dashboard-grid .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<?php include("sidebar.php"); ?>

<!-- Main Content -->
<div class="main-content">
    <div class="container-fluid">
        <h3 class="mb-4 fw-bold text-success">🎓 Welcome, <?= htmlspecialchars($student_name) ?>!</h3>

        <!-- Dashboard Grid Quick Links -->
        <!-- Dashboard Grid Quick Links -->
<div class="row g-4 dashboard-grid mb-4">
    <div class="col-md-4">
        <a href="book_room.php" class="text-decoration-none text-dark">
            <div class="card text-center p-3 shadow-sm">
                <h2>🏢</h2>
                <h6>Book Room</h6>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="room_allocation.php" class="text-decoration-none text-dark">
            <div class="card text-center p-3 shadow-sm">
                <h2>🛏️</h2>
                <h6>My Room Allocation</h6>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="payment_status.php" class="text-decoration-none text-dark">
            <div class="card text-center p-3 shadow-sm">
                <h2>💳</h2>
                <h6>Payment Status</h6>
            </div>
        </a>
    </div>
    <!-- <div class="col-md-4">
        <a href="receipt.php" class="text-decoration-none text-dark">
            <div class="card text-center p-3 shadow-sm">
                <h2>💰</h2>
                <h6>Receipt</h6>
            </div>
        </a>
    </div> -->
    <div class="col-md-4">
        <a href="maintenance.php" class="text-decoration-none text-dark">
            <div class="card text-center p-3 shadow-sm">
                <h2>🧰</h2>
                <h6>Maintenance Requests</h6>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="notices.php" class="text-decoration-none text-dark">
            <div class="card text-center p-3 shadow-sm">
                <h2>📢</h2>
                <h6>Notice Board</h6>
            </div>
        </a>
    </div>
</div>


        <!-- About Hostel Section -->
        <div class="about-card">
            <img src="../assets/host.jpeg" alt="Hostel Image">
            <div class="p-4">
                <h4 class="text-success fw-bold">🏫 About Our Hostel</h4>
                <p>
                    Welcome to the <strong>Hostel Allocation Management System</strong> of 
                    <em>[Your School Name]</em>. This platform helps students easily book hostel rooms,
                    check their payment status, report maintenance issues, and stay updated with official
                    announcements from the hostel management.
                </p>
                <p>
                    Our hostel is dedicated to providing a safe, comfortable, and student-friendly environment.
                    We believe in fostering community, discipline, and convenience for all residents.
                </p>
                <p class="mb-0"><strong>Contact:</strong> hosteloffice@school.edu | +234 800 123 4567</p>
            </div>
        </div>

        <!-- Footer -->
        <?php include("footer.php"); ?>
    </div>
</div>

</body>
</html>
