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

    $pdo->beginTransaction();
    try {
        // Check if load is still available
        $stmt = $pdo->prepare("SELECT status FROM loads WHERE id = ? FOR UPDATE");
        $stmt->execute([$load_id]);
        $load = $stmt->fetch();

        if ($load && $load['status'] == 'Available') {
            // Update load status
            $stmt = $pdo->prepare("UPDATE loads SET status = 'Pending' WHERE id = ?");
            $stmt->execute([$load_id]);

            // Create assignment
            $stmt = $pdo->prepare("INSERT INTO assignments (load_id, user_id) VALUES (?, ?)");
            $stmt->execute([$load_id, $user_id]);

            $pdo->commit();
            header("Location: user_dashboard.php?success=claimed");
        } else {
            $pdo->rollBack();
            header("Location: user_dashboard.php?error=taken");
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        header("Location: user_dashboard.php?error=failed");
    }
} else {
    header("Location: user_dashboard.php");
}
exit;
?>