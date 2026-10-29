<?php
session_start();
require_once 'config/db.php'; // Database connection

// 1. Security Check: Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?error=unauthorized");
    exit();
}

$search = trim($_GET['search'] ?? '');

try {
    // 2. Fetch members (with optional search filter)
    if (!empty($search)) {
        $stmt = $pdo->prepare("SELECT * FROM members WHERE fullname LIKE ? OR contact_number LIKE ? ORDER BY id DESC");
        $stmt->execute(["%$search%", "%$search%"]);
    } else {
        $stmt = $pdo->query("SELECT * FROM members ORDER BY id DESC");
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
    <title>Dave's Gym - Manage Members</title>

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
                    <li><a href="manage_members.php" class="active">Manage Members</a></li>
                    <li><a href="expiring_members.php">Expiring Monitoring</a></li>

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
                <h2>Manage Members</h2>
                <div class="user-badge">
                    Logged in as: <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong> (<?= htmlspecialchars($_SESSION['role']) ?>)
                </div>
            </header>

            <!-- Search & Filter Section -->
            <div class="action-bar">
                <form action="manage_members.php" method="GET" class="search-form">
                    <input type="text" name="search" placeholder="Search by name or contact..." value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn-secondary">Search</button>
                    <?php if (!empty($search)): ?>
                        <a href="manage_members.php" class="btn-reset">Clear</a>
                    <?php endif; ?>
                </form>
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
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($members) > 0): ?>
                            <?php foreach ($members as $member): ?>
                                <?php 
                                    // Dynamically check if membership is expired based on current date
                                    $isExpired = strtotime($member['membership_end']) < time();
                                    $statusClass = $isExpired ? 'status-expired' : 'status-active';
                                    $statusText = $isExpired ? 'Expired' : 'Active';
                                ?>
                                <tr>
                                    <td><?= $member['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($member['fullname']) ?></strong></td>
                                    <td><?= htmlspecialchars($member['contact_number']) ?></td>
                                    <td><?= htmlspecialchars($member['address']) ?></td>
                                    <td><?= htmlspecialchars($member['membership_start']) ?></td>
                                    <td><?= htmlspecialchars($member['membership_end']) ?></td>
                                    <td><span class="badge <?= $statusClass ?>"><?= $statusText ?></span></td>
                                    <td>
                                        <!-- Placeholder action buttons for future features -->
                                        <a href="edit_member.php?id=<?= $member['id'] ?>" class="btn-action edit">Edit</a>
                                        <a href="delete_member.php?id=<?= $member['id'] ?>" class="btn-action delete" onclick="return confirm('Are you sure you want to delete <?= htmlspecialchars($member['fullname'], ENT_QUOTES) ?>? This action cannot be undone.');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 20px;">No members found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

</body>
</html>