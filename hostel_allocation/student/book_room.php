<?php
session_start();
include("../config/dbconnect.php");
include("sidebar.php");

// ensure student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: ../index.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$errors = [];
$success = "";

// Check if student already has an allocation
$chkAlloc = $conn->prepare("SELECT * FROM allocations WHERE student_id = ?");
$chkAlloc->bind_param("i", $student_id);
$chkAlloc->execute();
$resAlloc = $chkAlloc->get_result();
$hasAllocation = $resAlloc->num_rows > 0;
$existingAllocation = $hasAllocation ? $resAlloc->fetch_assoc() : null;

// Handle booking
if (isset($_POST['book_room']) && !$hasAllocation) {
    $room_id = intval($_POST['room_id']);
    $conn->begin_transaction();

    try {
        $roomStmt = $conn->prepare("SELECT id, capacity, occupied, status FROM rooms WHERE id = ? FOR UPDATE");
        $roomStmt->bind_param("i", $room_id);
        $roomStmt->execute();
        $room = $roomStmt->get_result()->fetch_assoc();

        if (!$room) throw new Exception("Selected room does not exist.");
        if ($room['status'] === 'under_maintenance') throw new Exception("Room under maintenance.");
        if ($room['occupied'] >= $room['capacity']) throw new Exception("Room is already full.");

        // Create allocation
        $allocStmt = $conn->prepare("INSERT INTO allocations (student_id, room_id, allocation_date, status) VALUES (?, ?, CURDATE(), 'pending')");
        $allocStmt->bind_param("ii", $student_id, $room_id);
        if (!$allocStmt->execute()) throw new Exception("Could not create allocation.");
        $allocation_id = $conn->insert_id;

        // Generate unique receipt number
        $receipt_number = "RCP-" . date('Ymd-His') . "-" . substr(md5(uniqid()), 0, 6);
        $amount = 0.00;

        // Create payment record (unpaid)
        $payStmt = $conn->prepare("INSERT INTO payments (student_id, allocation_id, receipt_number, amount, payment_status) VALUES (?, ?, ?, ?, 'unpaid')");
        $payStmt->bind_param("iisd", $student_id, $allocation_id, $receipt_number, $amount);
        if (!$payStmt->execute()) throw new Exception("Could not generate payment record.");

        // Update room occupancy
        $newOccupied = $room['occupied'] + 1;
        $newStatus = ($newOccupied >= $room['capacity']) ? 'full' : 'available';
        $updRoom = $conn->prepare("UPDATE rooms SET occupied = ?, status = ? WHERE id = ?");
        $updRoom->bind_param("isi", $newOccupied, $newStatus, $room_id);
        $updRoom->execute();

        $conn->commit();

        // Redirect to payment status page (voucher can be printed there)
        header("Location: payment_status.php");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        $errors[] = $e->getMessage();
    }
}

// Fetch available rooms
$roomsRes = $conn->query("SELECT id, room_number, capacity, occupied, status FROM rooms WHERE status != 'full' ORDER BY room_number ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Room | Student Portal</title>
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
        <h3 class="mb-4 fw-bold">📌 Book a Room</h3>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $err) echo htmlspecialchars($err) . "<br>"; ?>
            </div>
        <?php endif; ?>

        <?php if ($hasAllocation): ?>
            <div class="alert alert-info">
                You already have an allocation (Room #: <?= htmlspecialchars($existingAllocation['room_id']) ?>). 
                Please contact the hostel admin if you need assistance.
            </div>
        <?php else: ?>
            <div class="card shadow border-0">
                <div class="card-body">
                    <form method="POST">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-success">
                                    <tr>
                                        <th>Select</th>
                                        <th>Room Number</th>
                                        <th>Capacity</th>
                                        <th>Occupied</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($r = $roomsRes->fetch_assoc()):
                                        $availableSlots = $r['capacity'] - $r['occupied']; ?>
                                        <tr>
                                            <td>
                                                <?php if ($r['status'] === 'available' && $availableSlots > 0): ?>
                                                    <input type="radio" name="room_id" value="<?= $r['id'] ?>" required>
                                                <?php else: ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($r['room_number']) ?></td>
                                            <td><?= $r['capacity'] ?></td>
                                            <td><?= $r['occupied'] ?></td>
                                            <td><?= ucfirst($r['status']) ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <button type="submit" name="book_room" class="btn btn-primary">Book Selected Room</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include("footer.php"); ?>
</body>
</html>
