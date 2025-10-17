<?php
// admin/notices.php
session_start();
include("../config/dbconnect.php");

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'];

include("sidebar.php");

$success = $error = "";

// Handle Post Notice
if (isset($_POST['post_notice'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (empty($title) || empty($content)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO notices (title, content, posted_by) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $title, $content, $admin_id);
        if ($stmt->execute()) {
            $success = "Notice posted successfully!";
        } else {
            $error = "Error posting notice.";
        }
    }
}

// Handle Delete Notice
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM notices WHERE id = $id");
    $success = "Notice deleted successfully!";
}

// Fetch all notices
$notices = $conn->query("
    SELECT n.*, a.full_name AS admin_name 
    FROM notices n 
    JOIN admins a ON n.posted_by = a.id 
    ORDER BY n.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post Notices | Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .main-content {
            margin-left: 250px; /* space for sidebar */
            padding: 30px;
            min-height: 100vh;
        }
        .table th, .table td { vertical-align: middle; }
    </style>
</head>
<body>

<div class="main-content">
    <div class="container-fluid">
        <h3 class="fw-bold text-primary mb-4">📢 Post and Manage Notices</h3>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <!-- Post New Notice -->
        <div class="card shadow border-0 mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">➕ Post a New Notice</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Notice Title</label>
                        <input type="text" name="title" class="form-control" placeholder="Enter title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notice Content</label>
                        <textarea name="content" rows="4" class="form-control" placeholder="Enter notice message" required></textarea>
                    </div>
                    <button type="submit" name="post_notice" class="btn btn-primary">Post Notice</button>
                </form>
            </div>
        </div>

        <!-- All Notices -->
        <div class="card shadow border-0 mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">📋 All Notices</h5>

                <?php if ($notices->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Content</th>
                                    <th>Posted By</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1;
                                while ($n = $notices->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= htmlspecialchars($n['title']) ?></td>
                                    <td><?= nl2br(htmlspecialchars($n['content'])) ?></td>
                                    <td><?= htmlspecialchars($n['admin_name']) ?></td>
                                    <td><?= htmlspecialchars($n['created_at']) ?></td>
                                    <td>
                                        <a href="?delete=<?= $n['id'] ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Are you sure you want to delete this notice?');">
                                           Delete
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">No notices posted yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <?php include("../student/footer.php"); ?>
    </div>
</div>

</body>
</html>
