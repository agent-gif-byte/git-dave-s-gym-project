<?php
session_start();
require_once 'config/db.php'; // Database connection

// 1. Security Check: Only logged-in Admins should access staff management
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: dashboard.php?error=unauthorized");
    exit();
}

$message = "";
$messageType = "";

// 2. Handle POST Actions (Create Staff, Reset PIN, Delete Staff)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ACTION A: Register New Staff
    if ($action === 'create_staff') {
        $name    = trim($_POST['name'] ?? '');
        $role    = $_POST['role'] ?? 'Staff';
        $pinCode = trim($_POST['pin_code'] ?? '');

        if (!empty($name) && !empty($pinCode)) {
            $hashedPin = password_hash($pinCode, PASSWORD_DEFAULT);

            try {
                $stmt = $pdo->prepare("INSERT INTO staff (name, role, pin_code) VALUES (?, ?, ?)");
                $stmt->execute([$name, $role, $hashedPin]);
                $message = "Staff profile created successfully!";
                $messageType = "success";
            } catch (PDOException $e) {
                $message = "Error creating staff profile.";
                $messageType = "error";
            }
        } else {
            $message = "Please fill in all required fields.";
            $messageType = "error";
        }
    }

    // ACTION B: Reset / Renew PIN Code
    if ($action === 'reset_pin') {
        $staffId   = $_POST['staff_id'] ?? null;
        $newPinCode = trim($_POST['new_pin'] ?? '');

        if ($staffId && !empty($newPinCode)) {
            $hashedPin = password_hash($newPinCode, PASSWORD_DEFAULT);

            try {
                $stmt = $pdo->prepare("UPDATE staff SET pin_code = ? WHERE id = ?");
                $stmt->execute([$hashedPin, $staffId]);
                $message = "Security PIN renewed successfully!";
                $messageType = "success";
            } catch (PDOException $e) {
                $message = "Error resetting PIN.";
                $messageType = "error";
            }
        }
    }

    // ACTION C: Delete Staff Profile
    if ($action === 'delete_staff') {
        $staffId = $_POST['staff_id'] ?? null;

        // Prevent self-deletion
        if ($staffId == $_SESSION['user_id']) {
            $message = "You cannot delete your own logged-in account!";
            $messageType = "error";
        } elseif ($staffId) {
            try {
                $stmt = $pdo->prepare("DELETE FROM staff WHERE id = ?");
                $stmt->execute([$staffId]);
                $message = "Staff profile deleted successfully.";
                $messageType = "success";
            } catch (PDOException $e) {
                $message = "Error deleting staff profile.";
                $messageType = "error";
            }
        }
    }
}

// 3. Fetch all staff members from database
try {
    $stmt = $pdo->query("SELECT id, name, role FROM staff ORDER BY id DESC");
    $allStaff = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $allStaff = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dave's Gym - Staff Management</title>
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
                    <li><a href="expiring_members.php">Expiring Monitoring</a></li>

                    <!-- admin user can only access this to create new staff member -->
                    <?php if ($_SESSION['role'] === 'Admin'): ?>
                        <li><a href="register_staff.php" class="active">Staff Management</a></li>
                    <?php endif; ?>

                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <header class="header">
                <h2>Staff & Admin Management</h2>
                <div class="user-badge">
                    Logged in as: <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong> (<?= htmlspecialchars($_SESSION['role']) ?>)
                </div>
            </header>

            <!-- Alert Message -->
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?= $messageType ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <!-- Form Card: Register New Staff -->
            <div class="form-card" style="margin-bottom: 30px;">
                <h3>Add New Profile</h3>
                <form action="register_staff.php" method="POST">
                    <input type="hidden" name="action" value="create_staff">
                    
                    <div class="form-group">
                        <label for="name">Staff Name *</label>
                        <input type="text" name="name" id="name" placeholder="e.g. Maria Santos" required>
                    </div>

                    <div class="form-group">
                        <label for="role">Role *</label>
                        <select name="role" id="role" required>
                            <option value="Staff">Staff</option>
                            <option value="Admin">Admin (Owner)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="pin_code">Security PIN (4-6 digits) *</label>
                        <input type="password" name="pin_code" id="pin_code" maxlength="6" autocomplete="new-password" placeholder="Set initial PIN" required>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Create Profile</button>
                    </div>
                </form>
            </div>

            <!-- Table Card: Manage Staff Profiles -->
            <div class="table-card">
                <h3>Existing Profiles</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Renew / Reset PIN</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($allStaff) > 0): ?>
                            <?php foreach ($allStaff as $staff): ?>
                                <tr>
                                    <td><?= $staff['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($staff['name']) ?></strong></td>
                                    <td><span class="badge"><?= htmlspecialchars($staff['role']) ?></span></td>
                                    <td>
                                        <!-- Inline form to renew PIN -->
                                        <form action="register_staff.php" method="POST" style="display: flex; gap: 5px;">
                                            <input type="hidden" name="action" value="reset_pin">
                                            <input type="hidden" name="staff_id" value="<?= $staff['id'] ?>">
                                            <input type="password" name="new_pin" placeholder="New PIN" maxlength="6" autocomplete="new-password" required style="width: 100px; padding: 5px;">
                                            <button type="submit" class="btn-secondary" style="padding: 5px 10px;">Update</button>
                                        </form>
                                    </td>
                                    <td>
                                        <!-- Form to delete staff -->
                                        <?php if ($staff['id'] != $_SESSION['user_id']): ?>
                                            <form action="register_staff.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this staff account?');">
                                                <input type="hidden" name="action" value="delete_staff">
                                                <input type="hidden" name="staff_id" value="<?= $staff['id'] ?>">
                                                <button type="submit" class="btn-action delete">Delete</button>
                                            </form>
                                        <?php else: ?>
                                            <small style="color: #888;">(Current User)</small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center;">No staff members found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </main>
    </div>

</body>
</html>