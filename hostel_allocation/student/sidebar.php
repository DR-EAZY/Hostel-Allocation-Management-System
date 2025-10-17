<?php
// student/sidebar.php
if (!isset($_SESSION)) {
    session_start();
}
$student_name = $_SESSION['student_name'] ?? "Student";
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar Layout -->
<div class="d-flex flex-column flex-shrink-0 p-3 bg-success text-white vh-100 position-fixed" style="width: 250px;">
    <div class="text-center mb-4">
        <img src="../assets/logo.jpeg" alt="School Logo" width="80" class="rounded-circle mb-2">
        <h5 class="fw-bold">Hostel Portal</h5>
        <small>Welcome, <?= htmlspecialchars($student_name) ?></small>
    </div>

    <hr>

    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link text-white <?= $current_page == 'dashboard.php' ? 'active' : '' ?>"><strong>🏠</strong> Dashboard</a>
        </li>
        <li>
            <a href="book_room.php" class="nav-link text-white <?= $current_page == 'book_room.php' ? 'active' : '' ?>"><strong>🏢</strong> Book Room</a>
        </li>
        <li>
            <a href="room_allocation.php" class="nav-link text-white <?= $current_page == 'room_allocation.php' ? 'active' : '' ?>"><strong>🛏️</strong> Room Allocation</a>
        </li>
        <li>
            <a href="payment_status.php" class="nav-link text-white <?= $current_page == 'payment_status.php' ? 'active' : '' ?>"><strong>💳</strong> Payment Status</a>
        </li>
        <!-- <li>
            <a href="receipt.php" class="nav-link text-white <?= $current_page == 'receipt.php' ? 'active' : '' ?>"><strong>💰</strong> Receipt</a>
        </li> -->
        <li>
            <a href="maintenance.php" class="nav-link text-white <?= $current_page == 'maintenance.php' ? 'active' : '' ?>"><strong>🧰</strong> Maintenance</a>
        </li>
        <li>
            <a href="notices.php" class="nav-link text-white <?= $current_page == 'notices.php' ? 'active' : '' ?>"><strong>📢</strong> Notice Board</a>
        </li>
    </ul>

    <hr>
    <div class="mt-auto text-center">
        <a href="../logout.php" class="btn btn-outline-light btn-sm w-100">🚪 Logout</a>
    </div>
</div>
