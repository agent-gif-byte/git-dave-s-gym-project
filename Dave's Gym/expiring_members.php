<?php
session_start();

date_default_timezone_set('Asia/Manila');

$today = date('Y-m-d');

require_once 'config/db.php'; // Database connection

// 1. Security Check: Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?error=unauthorized");
    exit();
}

// Filter option: default to showing members expiring in the next 7 days or already expired
$filter = $_GET['filter'] ?? 'upcoming'; 

try {
    
    if ($filter === 'expired') {
        // Show already expired members
        $sql = "SELECT * FROM members WHERE membership_end < ? ORDER BY membership_end ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$today]);
    } else {
        // Show members expiring in the next 7 days (and not yet past today)
        $sevenDaysLater = date('Y-m-d', strtotime('+7 days'));
        $sql = "SELECT * FROM members WHERE membership_end BETWEEN ? AND ? ORDER BY membership_end ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$today, $sevenDaysLater]);
    }
    
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $members = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dave's Gym - Expiring Monitoring</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <h2>Dave's Gym</h2>
            <nav>
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="register_member.php">Register Member</a></li>
                    <li><a href="manage_members.php">Manage Members</a></li>
                    <li><a href="expiring_members.php" class="active">Expiring Monitoring</a></li>

                    <!-- admin user can only access this to create new staff member -->
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>
                        <li><a href="register_staff.php">Add Staff Profile</a></li>
                    <?php endif; ?>

                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <header class="header">
                <h2>Membership Expiration Monitoring</h2>
                <div class="user-badge">
                    Logged in as: <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong> (<?= htmlspecialchars($_SESSION['role']) ?>)
                </div>
            </header>

            <!-- Filter Tabs / Action Bar -->
            <div class="action-bar" style="display: flex; gap: 10px; margin-bottom: 20px;">
                <a href="expiring_members.php?filter=upcoming" class="btn-secondary <?= $filter === 'upcoming' ? 'active-tab' : '' ?>" style="text-decoration: none;">Expiring Soon (Next 7 Days)</a>
                <a href="expiring_members.php?filter=expired" class="btn-secondary <?= $filter === 'expired' ? 'active-tab' : '' ?>" style="text-decoration: none;">Already Expired</a>
            </div>

            <!-- Members Table Section -->
            <div class="table-card">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Contact</th>
                            <th>Address</th>
                            <th>Expiration Date</th>
                            <th>Status / Urgency</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($members) > 0): ?>
                            <?php foreach ($members as $member): ?>
                                <?php 
                                    $isExpired = $member['membership_end'] < $today;
                                    $statusClass = $isExpired ? 'status-expired' : 'status-warning';
                                    $statusText = $isExpired ? 'Expired' : 'Expiring Soon';
                                ?>
                                <tr>
                                    <td><?= $member['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($member['fullname']) ?></strong></td>
                                    <td><?= htmlspecialchars($member['contact_number']) ?></td>
                                    <td><?= htmlspecialchars($member['address']) ?></td>
                                    <td><?= htmlspecialchars($member['membership_end']) ?></td>
                                    <td><span class="badge <?= $statusClass ?>"><?= $statusText ?></span></td>
                                    <td>
                                        <a href="edit_member.php?id=<?= $member['id'] ?>" class="btn-action edit">Renew / Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 20px;">No members found matching this filter. Great job!</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

</body>
</html>