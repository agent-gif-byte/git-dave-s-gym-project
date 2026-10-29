<?php
session_start();
require_once 'config/db.php'; // Database connection

// 1. Security Check: Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?error=unauthorized");
    exit();
}

$message = "";
$messageType = "";

// 2. Get Member ID from URL
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header("Location: manage_members.php");
    exit();
}

// 3. Handle Form Submission (When updates are saved)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname      = trim($_POST['fullname'] ?? '');
    $contactNumber = trim($_POST['contact_number'] ?? '');
    $address       = trim($_POST['address'] ?? '');
    $membershipEnd = trim($_POST['membership_end'] ?? '');
    $status        = trim($_POST['status'] ?? 'Active');

    if (!empty($fullname) && !empty($contactNumber) && !empty($address) && !empty($membershipEnd)) {
        try {
            $sql = "UPDATE members SET fullname = ?, contact_number = ?, address = ?, membership_end = ?, status = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$fullname, $contactNumber, $address, $membershipEnd, $status, $id]);

            $message = "Member updated successfully!";
            $messageType = "success";
        } catch (PDOException $e) {
            $message = "Error updating member: " . $e->getMessage();
            $messageType = "error";
        }
    } else {
        $message = "Please fill in all required fields.";
        $messageType = "error";
    }
}

// 4. Fetch Current Member Data to fill the form
try {
    $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
    $stmt->execute([$id]);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$member) {
        header("Location: manage_members.php");
        exit();
    }
} catch (PDOException $e) {
    header("Location: manage_members.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dave's Gym - Edit Member</title>
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
                    <li><a href="manage_members.php" class="active">Manage Members</a></li>
                    <li><a href="expiring_members.php">Expiring Monitoring</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <header class="header">
                <h2>Edit Member: <?= htmlspecialchars($member['fullname']) ?></h2>
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

            <!-- Edit Member Form -->
            <div class="form-card">
                <form action="edit_member.php?id=<?= $id ?>" method="POST">
                    
                    <div class="form-group">
                        <label for="fullname">Full Name *</label>
                        <input type="text" name="fullname" id="fullname" value="<?= htmlspecialchars($member['fullname']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="contact_number">Contact Number *</label>
                        <input type="tel" name="contact_number" id="contact_number" value="<?= htmlspecialchars($member['contact_number']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="address">Address *</label>
                        <textarea name="address" id="address" rows="3" required><?= htmlspecialchars($member['address']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="membership_end">Membership Expiration Date *</label>
                        <input type="date" name="membership_end" id="membership_end" value="<?= htmlspecialchars($member['membership_end']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="status">Status *</label>
                        <select name="status" id="status" required>
                            <option value="Active" <?= $member['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                            <option value="Expired" <?= $member['status'] === 'Expired' ? 'selected' : '' ?>>Expired</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Save Changes</button>
                        <a href="manage_members.php" class="btn-secondary" style="text-decoration: none; text-align: center; display: inline-block; line-height: normal;">Back to Members</a>
                    </div>

                </form>
            </div>
        </main>
    </div>

</body>
</html>