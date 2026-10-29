<?php
session_start();
require_once 'config/db.php'; // Database connection

// 1. Security check: Kick back to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?error=unauthorized");
    exit();
}

// 2. Fetch Dashboard Analytics
try {
    // Total members count
    $stmtTotal = $pdo->query("SELECT COUNT(*) AS total FROM members");
    $totalMembers = $stmtTotal->fetch()['total'];

    // Active members count (membership end date is today or in the future)
    $stmtActive = $pdo->query("SELECT COUNT(*) AS active FROM members WHERE membership_end >= CURDATE()");
    $activeMembers = $stmtActive->fetch()['active'];

    // Members about to expire within the next 7 days
    $stmtExpiring = $pdo->query("SELECT COUNT(*) AS expiring FROM members WHERE membership_end BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)");
    $aboutToExpire = $stmtExpiring->fetch()['expiring'];

} catch (PDOException $e) {
    // Fallback numbers if table is empty or query fails
    $totalMembers = 0;
    $activeMembers = 0;
    $aboutToExpire = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dave's Gym - Dashboard</title>
    <!-- Link your CSS file here once you start styling -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Sidebar or Navigation Bar -->
    <div class="sidebar">
        <h2>Dave's Gym</h2>
        <ul>
            <li><a href="dashboard.php" class="active">Dashboard</a></li>
            <li><a href="register_member.php">Register Member</a></li>
            <li><a href="manage_members.php">Manage Member</a></li>
            <li><a href="expiring_members.php">Expiring Monitoring</a></li>

            <!-- admin user can only access this to create new staff member -->
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>
                <li><a href="register_staff.php">Add Staff Profile</a></li>
            <?php endif; ?>

            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        
        <!-- Top Header -->
        <header class="dashboard-header">
            <h2>Dashboard Overview</h2>
            <div class="user-info">
                <span>Welcome, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong> (<?= htmlspecialchars($_SESSION['role']) ?>)</span>
            </div>
        </header>

        <!-- Metric Cards Section -->
        <section class="metrics-grid">
            
            <div class="card">
                <h3>Total Members</h3>
                <p class="number"><?= $totalMembers ?></p>
            </div>

            <div class="card">
                <h3>Active Members</h3>
                <p class="number"><?= $activeMembers ?></p>
            </div>

            <div class="card">
                <h3>About to Expire</h3>
                <p class="number"><?= $aboutToExpire ?></p>
            </div>

        </section>

    </div>

</body>
</html>