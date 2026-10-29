<?php
require_once 'config/db.php';

try {
    // Fetch staff members dynamically from the database
    $stmt = $pdo->query("SELECT id, name, role FROM staff");
    $staffMembers = $stmt->fetchAll();
} catch (PDOException $e) {
    $staffMembers = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dave's Gym Membership Management System - Login</title>
    <!-- Link your future CSS file here -->
    <link rel="stylesheet" href="assets/login.css">

</head>
<body>

    <div class="login-container">
        <h2>Dave's Gym Management - Login</h2>

        <!-- Form submits data to login.php -->
        <form action="login.php" method="POST" id="loginForm">

            <div class="form-group">
                <label for="user_select">User Profile:</label>
                <select name="user_id" id="user_select" required>
                    <option value="" disabled selected>Select user profile</option>
                    <?php foreach ($staffMembers as $staff): ?>
                        <option value="<?= htmlspecialchars($staff['id']) ?>">
                            <?= htmlspecialchars($staff['name']) ?> (<?= htmlspecialchars($staff['role']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="pin_input">Security PIN:</label>
                <input type="password" name="pin_code" id="pin_input" maxlength="6" autocomplete="new-password">
            </div>

            <div class="form-group">
                <button type="submit" name="login_btn">Login</button>
            </div>
        </form>
    </div>

</body>
</html>