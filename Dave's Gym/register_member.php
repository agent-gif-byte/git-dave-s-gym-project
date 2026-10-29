<?php
session_start();
require_once 'config/db.php'; // Database connection

// 1. Security Check: Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?error=unauthorized");
    exit();
}

$message = "";
$messageType = ""; // 'success' or 'error'

// 2. Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname      = trim($_POST['fullname'] ?? '');
    $contactNumber = trim($_POST['contact_number'] ?? '');
    $address       = trim($_POST['address'] ?? '');
    $planDuration  = (int)($_POST['plan_duration'] ?? 1); // Duration in months
    

    if (!empty($fullname) && !empty($contactNumber) && !empty($address) && $planDuration > 0) {
        
        // Calculate start date (Today) and end date (Today + N months)
        $startDate = date('Y-m-d');
        $endDate   = date('Y-m-d', strtotime("+$planDuration months"));

        if ($planDuration === '1_week') {
            $endDate = date('Y-m-d', strtotime('+1 week'));
        } elseif ($planDuration === '3_months') {
            $endDate = date('Y-m-d', strtotime('+3 months'));
        } elseif ($planDuration === '6_months') {
            $endDate = date('Y-m-d', strtotime('+6 months'));
        } else {
            $endDate = date('Y-m-d', strtotime('+1 month'));
        }

        try {
            $sql = "INSERT INTO members (fullname, contact_number, address, membership_start, membership_end, status) 
                    VALUES (?, ?, ?, ?, ?, 'Active')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$fullname, $contactNumber, $address, $startDate, $endDate]);

            $message = "Member '$fullname' registered successfully! Membership active until $endDate.";
            $messageType = "success";
        } catch (PDOException $e) {
            $message = "Error registering member: " . $e->getMessage();
            $messageType = "error";
        }
    } else {
        $message = "Please fill in all required fields.";
        $messageType = "error";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dave's Gym - Register Member</title>
    <!-- Link your future CSS stylesheet -->
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
                    <li><a href="register_member.php" class="active">Register Member</a></li>
                    <li><a href="manage_members.php">Manage Members</a></li>
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
                <h2>Register New Member</h2>
                <div class="user-badge">
                    Logged in as: <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong> (<?= htmlspecialchars($_SESSION['role']) ?>)
                </div>
            </header>

            <!-- Status Alert Message -->
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?= $messageType ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <!-- Member Registration Form -->
            <div class="form-card">
                <form action="register_member.php" method="POST" id="registerMemberForm">
                    
                    <div class="form-group">
                        <label for="fullname">Full Name *</label>
                        <input type="text" name="fullname" id="fullname" placeholder="e.g. Juan Dela Cruz" required>
                    </div>

                    <div class="form-group">
                        <label for="contact_number">Contact Number *</label>
                        <input type="tel" name="contact_number" id="contact_number" placeholder="e.g. 09123456789" required>
                    </div>

                    <div class="form-group">
                        <label for="address">Address *</label>
                        <textarea name="address" id="address" rows="3" placeholder="Enter complete home address" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="plan_duration">Membership Duration *</label>
                        <select name="plan_duration" id="plan_duration" required>
                            <option value="1_week">1 Week</option>
                            <option value="1_month">1 Month</option>
                            <option value="3_months">3 Months</option>
                            <option value="6_months">6 Months</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Register Member</button>
                        <button type="reset" class="btn-secondary">Clear Form</button>
                    </div>

                </form>
            </div>
        </main>
    </div>
    
</body>
</html>