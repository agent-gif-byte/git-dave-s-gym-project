<?php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
    $pinCode = $_POST['pin_code'] ?? '';

    if (empty($userId) || empty($pinCode)) {
        header("Location: index.php?error=emptyfields");
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM staff WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if ($user) {
            // Verify PIN (Use password_verify if using hashed passwords, or direct check for testing)
            if (password_verify($pinCode, $user['pin_code'])) {
                
                // Set session data
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role'] = $user['role'];

                header("Location: dashboard.php");
                exit();
            } else {
                header("Location: index.php?error=invalidpin");
                exit();
            }
        } else {
            header("Location: index.php?error=usernotfound");
            exit();
        }

    } catch (PDOException $e) {
        header("Location: index.php?error=sqlerror");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>