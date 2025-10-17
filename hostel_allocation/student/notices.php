<?php
// student/notices.php
session_start();
include("../config/dbconnect.php");

// Redirect if not logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: ../index.php");
    exit();
}

include("sidebar.php");

// Fetch all notices
$sql = "SELECT n.*, a.full_name AS admin_name 
        FROM notices n 
        JOIN admins a ON n.posted_by = a.id
        ORDER BY n.created_at DESC";
$notices = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notice Board | Student Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .main-content {
            margin-left: 250px; /* sidebar width */
            padding: 30px;
            min-height: 100vh;
        }
        .notice-card {
            border: none;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .notice-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        .notice-title {
            color: #0d6efd;
            font-weight: 600;
        }
        .notice-meta {
            font-size: 0.9rem;
            color: #6c757d;
        }
    </style>
</head>
<body>

<div class="main-content">
    <div class="container-fluid">
        <h3 class="mb-4 fw-bold text-success">📢 Notice Board</h3>

        <?php if ($notices->num_rows > 0): ?>
            <div class="row">
                <?php while ($n = $notices->fetch_assoc()): ?>
                    <div class="col-md-8 mb-4">
                        <div class="notice-card p-4">
                            <h5 class="notice-title mb-2"><?= htmlspecialchars($n['title']) ?></h5>
                            <p class="mb-3"><?= nl2br(htmlspecialchars($n['content'])) ?></p>
                            <div class="d-flex justify-content-between align-items-center notice-meta">
                                <span>Posted by <?= htmlspecialchars($n['admin_name']) ?></span>
                                <span><?= date("M d, Y • h:i A", strtotime($n['created_at'])) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center shadow-sm border-0">
                No notices available at the moment.
            </div>
        <?php endif; ?>

        <?php include("footer.php"); ?>
    </div>
</div>

</body>
</html>
