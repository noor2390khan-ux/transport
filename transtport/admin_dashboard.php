<?php
session_start();
require_once 'db.php';

// Access Control
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}

// Fetch stats
$totalLoads = $pdo->query("SELECT COUNT(*) FROM loads")->fetchColumn();
$availableLoads = $pdo->query("SELECT COUNT(*) FROM loads WHERE status = 'Available'")->fetchColumn();
$pendingLoads = $pdo->query("SELECT COUNT(*) FROM loads WHERE status = 'Pending'")->fetchColumn();
$completedLoads = $pdo->query("SELECT COUNT(*) FROM loads WHERE status = 'Completed'")->fetchColumn();

// Fetch all drivers
$stmt = $pdo->query("SELECT id, username FROM users WHERE role = 'User' ORDER BY username ASC");
$drivers = $stmt->fetchAll();

// Fetch all loads for the table
$stmt = $pdo->query("SELECT l.*, u.username as driver_name 
                    FROM loads l 
                    LEFT JOIN assignments a ON l.id = a.load_id 
                    LEFT JOIN users u ON a.user_id = u.id 
                    ORDER BY l.created_at DESC");
$loads = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Logistics</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="dashboard-page role-admin">
    <nav class="sidebar admin-sidebar">
        <div class="sidebar-header">
            <h3>LogiTrans</h3>
        </div>
        <ul class="nav-links">
            <li class="active"><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <main class="main-content">
        <header class="top-bar">
            <h1>Admin Dashboard</h1>
            <div class="user-info">
                <span>Welcome,
                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                </span>
            </div>
        </header>

        <section class="stats-grid">
            <div class="stat-card">
                <h3>Total Loads</h3>
                <p>
                    <?php echo $totalLoads; ?>
                </p>
            </div>
            <div class="stat-card">
                <h3>Available</h3>
                <p>
                    <?php echo $availableLoads; ?>
                </p>
            </div>
            <div class="stat-card">
                <h3>Pending</h3>
                <p>
                    <?php echo $pendingLoads; ?>
                </p>
            </div>
            <div class="stat-card">
                <h3>Completed</h3>
                <p>
                    <?php echo $completedLoads; ?>
                </p>
            </div>
        </section>

        <section class="action-section">
            <div class="card">
                <div class="card-header">
                    <h2>Post New Load</h2>
                </div>
                <form action="post_load.php" method="POST" id="postLoadForm" class="horizontal-form">
                    <select name="from_city" required>
                        <option value="">Origin City</option>
                        <option value="Karachi">Karachi</option>
                        <option value="Lahore">Lahore</option>
                        <option value="Islamabad">Islamabad</option>
                        <option value="Faisalabad">Faisalabad</option>
                        <option value="Multan">Multan</option>
                        <option value="Peshawar">Peshawar</option>
                        <option value="Quetta">Quetta</option>
                        <option value="Sialkot">Sialkot</option>
                        <option value="Gujranwala">Gujranwala</option>
                        <option value="Hyderabad">Hyderabad</option>
                    </select>
                    <select name="to_city" required>
                        <option value="">Destination City</option>
                        <option value="Karachi">Karachi</option>
                        <option value="Lahore">Lahore</option>
                        <option value="Islamabad">Islamabad</option>
                        <option value="Faisalabad">Faisalabad</option>
                        <option value="Multan">Multan</option>
                        <option value="Peshawar">Peshawar</option>
                        <option value="Quetta">Quetta</option>
                        <option value="Sialkot">Sialkot</option>
                        <option value="Gujranwala">Gujranwala</option>
                        <option value="Hyderabad">Hyderabad</option>
                    </select>
                    <input type="number" step="0.01" name="weight" placeholder="Weight (tons)" required>
                    <select name="vehicle_type" required>
                        <option value="">Vehicle Type</option>
                        <option value="20ft Container">20ft Container</option>
                        <option value="40ft Container">40ft Container</option>
                        <option value="Open Trailer">Open Trailer</option>
                        <option value="Mazda">Mazda</option>
                        <option value="Other">Other</option>
                    </select>
                    <select name="driver_id">
                        <option value="">Select Driver (Optional)</option>
                        <?php foreach ($drivers as $driver): ?>
                            <option value="<?php echo $driver['id']; ?>">
                                <?php echo htmlspecialchars($driver['username']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" name="description" placeholder="Description">
                    <button type="submit" class="btn btn-primary">Post Load</button>
                </form>
            </div>
        </section>

        <section class="table-section">
            <div class="card">
                <div class="card-header">
                    <h2>Manage Loads</h2>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Route</th>
                                <th>Weight</th>
                                <th>Vehicle</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($loads as $load): ?>
                                <tr>
                                    <td>#
                                        <?php echo $load['id']; ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($load['from_city'] . ' → ' . $load['to_city']); ?>
                                    </td>
                                    <td>
                                        <?php echo $load['weight']; ?> tons
                                    </td>
                                    <td>
                                        <?php echo $load['vehicle_type']; ?>
                                    </td>
                                    <td><span class="status-badge status-<?php echo strtolower($load['status']); ?>">
                                            <?php echo $load['status']; ?>
                                        </span></td>
                                    <td>
                                        <?php echo $load['driver_name'] ? htmlspecialchars($load['driver_name']) : 'Not Assigned'; ?>
                                    </td>
                                    <td>
                                        <a href="edit_load.php?id=<?php echo $load['id']; ?>" class="btn-icon">Edit</a>
                                        <a href="delete_load.php?id=<?php echo $load['id']; ?>" class="btn-icon btn-danger"
                                            onclick="return confirm('Are you sure?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
    <script src="assets/js/script.js"></script>
</body>

</html>