<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $from_city = trim($_POST['from_city']);
    $to_city = trim($_POST['to_city']);
    $weight = $_POST['weight'];
    $vehicle_type = $_POST['vehicle_type'];
    $description = trim($_POST['description']);
    $driver_id = $_POST['driver_id'] ?? null;

    if (empty($from_city) || empty($to_city) || empty($weight) || empty($vehicle_type)) {
        header("Location: admin_dashboard.php?error=fields");
        exit;
    }

    if ($weight <= 0) {
        header("Location: admin_dashboard.php?error=weight");
        exit;
    }

    $status = (!empty($driver_id)) ? 'Pending' : 'Available';

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("INSERT INTO loads (from_city, to_city, weight, vehicle_type, description, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$from_city, $to_city, $weight, $vehicle_type, $description, $status]);
        $load_id = $pdo->lastInsertId();

        if (!empty($driver_id)) {
            $stmt = $pdo->prepare("INSERT INTO assignments (load_id, user_id) VALUES (?, ?)");
            $stmt->execute([$load_id, $driver_id]);
        }

        $pdo->commit();
        header("Location: admin_dashboard.php?success=1");
    } catch (Exception $e) {
        $pdo->rollBack();
        header("Location: admin_dashboard.php?error=failed");
    }
    exit;
}
?>