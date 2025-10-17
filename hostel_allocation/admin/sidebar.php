<?php
// admin/sidebar.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$admin_name = $_SESSION['admin_name'] ?? 'Admin';

// Get the current filename for active link highlighting
$current = basename($_SERVER['PHP_SELF']);
?>

<!-- Admin Sidebar -->
<style>
    .admin-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 250px;
        background-color: #0d6efd; /* primary blue to match admin theme */
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 18px 12px;
        z-index: 999;
        box-shadow: 2px 0 6px rgba(0,0,0,0.08);
    }
    .admin-sidebar .brand {
        text-align: center;
        margin-bottom: 8px;
    }
    .admin-sidebar .brand img {
        width: 72px;
        height: 72px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid rgba(255,255,255,0.12);
    }
    .admin-sidebar h5 { margin-top: 8px; font-weight: 700; }
    .admin-nav { margin-top: 18px; }
    .admin-nav a {
        display: flex;
        gap: 10px;
        align-items: center;
        padding: 10px 14px;
        color: #fff;
        text-decoration: none;
        border-radius: 8px;
        margin-bottom: 6px;
        font-size: 15px;
    }
    .admin-nav a .icon { font-size: 18px; width: 26px; text-align: center; }
    .admin-nav a:hover { background: rgba(255,255,255,0.08); }
    .admin-nav a.active {
        background: rgba(255,255,255,0.18);
        border-left: 4px solid rgba(255,255,255,0.9);
    }
    .admin-sidebar .bottom {
        text-align: center;
        padding-top: 10px;
        border-top: 1px solid rgba(255,255,255,0.08);
    }
    .admin-sidebar .bottom a { color: #fff; text-decoration: none; display:block; margin-top:8px; }
    .admin-sidebar small { color: rgba(255,255,255,0.85); display:block; }
    /* ensure main content spacing when sidebar included */
    @media (min-width: 768px) {
        body { --admin-sidebar-width: 250px; }
    }
</style>

<div class="admin-sidebar" role="navigation" aria-label="Admin sidebar">
    <div>
        <div class="brand">
            <!-- Update path to your logo if desired -->
            <img src="../assets/logo.png" alt="Logo" onerror="this.style.display='none'">
            <h5>Hostel Admin</h5>
            <small><?= htmlspecialchars($admin_name) ?></small>
        </div>

        <nav class="admin-nav">
            <a href="dashboard.php" class="<?= $current === 'dashboard.php' ? 'active' : '' ?>">
                <span class="icon">📊</span> Dashboard
            </a>

            <a href="manage_buildings.php" class="<?= $current === 'manage_buildings.php' ? 'active' : '' ?>">
                <span class="icon">🏢</span> Manage Buildings
            </a>

            <a href="manage_floors.php" class="<?= $current === 'manage_floors.php' ? 'active' : '' ?>">
                <span class="icon">🧱</span> Manage Floors
            </a>

            <a href="manage_rooms.php" class="<?= $current === 'manage_rooms.php' ? 'active' : '' ?>">
                <span class="icon">🏠</span> Manage Rooms
            </a>

            <a href="manage_payments.php" class="<?= $current === 'manage_payments.php' ? 'active' : '' ?>">
                <span class="icon">💳</span> Manage Payments
            </a>

            <a href="maintenance.php" class="<?= $current === 'maintenance.php' ? 'active' : '' ?>">
                <span class="icon">🧰</span> Maintenance Requests
            </a>

            <a href="notices.php" class="<?= $current === 'notices.php' ? 'active' : '' ?>">
                <span class="icon">📢</span> Post Notices
            </a>

            <a href="students.php" class="<?= $current === 'students.php' ? 'active' : '' ?>">
                <span class="icon">👨‍🎓</span> Students
            </a>
        </nav>
    </div>

    <div class="bottom">
        <small>Signed in as</small>
        <strong><?= htmlspecialchars($admin_name) ?></strong>
        <a href="../logout.php">🚪 Logout</a>
        <div style="margin-top:8px; font-size:12px; color:rgba(255,255,255,0.85)">
            © <?= date('Y') ?> Hostels
        </div>
    </div>
</div>
