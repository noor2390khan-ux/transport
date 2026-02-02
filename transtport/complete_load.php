<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'User') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['load_id'])) {
    $load_id = $_POST['load_id'];
    $user_id = $_SESSION['user_id'];

    // Verify ownership and status
    $stmt = $pdo->prepare("SELECT l.id FROM loads l 
                          JOIN assignments a ON l.id = a.load_id 
                          WHERE l.id = ? AND a.user_id = ? AND l.status = 'Pending'");
    $stmt->execute([$load_id, $user_id]);

    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("UPDATE loads SET status = 'Completed' WHERE id = ?");
        $stmt->execute([$load_id]);
        header("Location: user_dashboard.php?success=completed");
    } else {
        header("Location: user_dashboard.php?error=unauthorized");
    }
} else {
    header("Location: user_dashboard.php");
}
exit;
?>