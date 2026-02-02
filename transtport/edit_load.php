<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: admin_dashboard.php");
    exit;
}

// Fetch all drivers
$stmt = $pdo->query("SELECT id, username FROM users WHERE role = 'User' ORDER BY username ASC");
$drivers = $stmt->fetchAll();

// Fetch current load and its driver
$stmt = $pdo->prepare("SELECT l.*, a.user_id as driver_id 
                      FROM loads l 
                      LEFT JOIN assignments a ON l.id = a.load_id 
                      WHERE l.id = ?");
$stmt->execute([$id]);
$load = $stmt->fetch();

if (!$load) {
    header("Location: admin_dashboard.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $from_city = $_POST['from_city'];
    $to_city = $_POST['to_city'];
    $weight = $_POST['weight'];
    $vehicle_type = $_POST['vehicle_type'];
    $description = $_POST['description'];
    $driver_id = $_POST['driver_id'] ?? null;

    $pdo->beginTransaction();
    try {
        $status = (!empty($driver_id)) ? 'Pending' : 'Available';
        if ($load['status'] == 'Completed')
            $status = 'Completed';

        $stmt = $pdo->prepare("UPDATE loads SET from_city = ?, to_city = ?, weight = ?, vehicle_type = ?, description = ?, status = ? WHERE id = ?");
        $stmt->execute([$from_city, $to_city, $weight, $vehicle_type, $description, $status, $id]);

        // Remove old assignment if any
        $stmt = $pdo->prepare("DELETE FROM assignments WHERE load_id = ?");
        $stmt->execute([$id]);

        // Add new assignment if provided
        if (!empty($driver_id)) {
            $stmt = $pdo->prepare("INSERT INTO assignments (load_id, user_id) VALUES (?, ?)");
            $stmt->execute([$id, $driver_id]);
        }

        $pdo->commit();
        header("Location: admin_dashboard.php?success=updated");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Update failed: " . $e->getMessage();
    }
}

$cities = ["Karachi", "Lahore", "Islamabad", "Faisalabad", "Multan", "Peshawar", "Quetta", "Sialkot", "Gujranwala", "Hyderabad"];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Load - Admin</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="auth-page role-admin">
    <div class="auth-container">
        <form action="" method="POST" class="auth-form">
            <h2>Edit Load</h2>
            <p>Update load details below</p>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label>Origin City</label>
                <select name="from_city" required>
                    <?php foreach ($cities as $city): ?>
                        <option value="<?php echo $city; ?>" <?php echo ($load['from_city'] == $city) ? 'selected' : ''; ?>>
                            <?php echo $city; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Destination City</label>
                <select name="to_city" required>
                    <?php foreach ($cities as $city): ?>
                        <option value="<?php echo $city; ?>" <?php echo ($load['to_city'] == $city) ? 'selected' : ''; ?>>
                            <?php echo $city; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Weight (tons)</label>
                <input type="number" step="0.01" name="weight" value="<?php echo $load['weight']; ?>" required>
            </div>

            <div class="form-group">
                <label>Vehicle Type</label>
                <select name="vehicle_type" required>
                    <option value="20ft Container" <?php echo ($load['vehicle_type'] == '20ft Container') ? 'selected' : ''; ?>>20ft Container</option>
                    <option value="40ft Container" <?php echo ($load['vehicle_type'] == '40ft Container') ? 'selected' : ''; ?>>40ft Container</option>
                    <option value="Open Trailer" <?php echo ($load['vehicle_type'] == 'Open Trailer') ? 'selected' : ''; ?>>Open Trailer</option>
                    <option value="Mazda" <?php echo ($load['vehicle_type'] == 'Mazda') ? 'selected' : ''; ?>>Mazda
                    </option>
                    <option value="Other" <?php echo ($load['vehicle_type'] == 'Other') ? 'selected' : ''; ?>>Other
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label>Assign Driver</label>
                <select name="driver_id">
                    <option value="">No Assignment (Available for Marketplace)</option>
                    <?php foreach ($drivers as $driver): ?>
                        <option value="<?php echo $driver['id']; ?>" <?php echo ($load['driver_id'] == $driver['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($driver['username']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Description</label>
                <input type="text" name="description" value="<?php echo htmlspecialchars($load['description']); ?>">
            </div>

            <button type="submit" class="btn btn-primary">Update Load</button>
            <a href="admin_dashboard.php" class="btn"
                style="display:block; text-align:center; margin-top:1rem;">Back</a>
        </form>
    </div>
</body>

</html>