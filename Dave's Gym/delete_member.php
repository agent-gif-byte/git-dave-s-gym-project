<?php
session_start();
require_once 'config/db.php'; // Database connection

// 1. Security Check: Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?error=unauthorized");
    exit();
}

// 2. Get and validate the Member ID from the URL
$id = $_GET['id'] ?? null;

if ($id && is_numeric($id)) {
    try {
        // 3. Prepare and execute the delete query
        $stmt = $pdo->prepare("DELETE FROM members WHERE id = ?");
        $stmt->execute([$id]);
        
        // Redirect back with success message or just back to the list
        header("Location: manage_members.php?msg=deleted");
        exit();

    } catch (PDOException $e) {
        // Handle deletion errors if needed
        header("Location: manage_members.php?error=delete_failed");
        exit();
    }
} else {
    // If ID is invalid, redirect back
    header("Location: manage_members.php");
    exit();
}
?>