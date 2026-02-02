<?php
session_start();
require_once 'db.php';

// Access Control
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'User') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get available loads
$search = $_GET['search'] ?? '';
$sql = "SELECT * FROM loads WHERE status = 'Available'";
$params = [];
if (!empty($search)) {
    $sql .= " AND (from_city LIKE ? OR to_city LIKE ?)";
    $params = ["%$search%", "%$search%"];
}
$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$availableLoads = $stmt->fetchAll();

// Get my tasks
$stmt = $pdo->prepare("SELECT l.* FROM loads l 
                      JOIN assignments a ON l.id = a.load_id 
                      WHERE a.user_id = ? AND l.status IN ('Pending', 'Completed')
                      ORDER BY l.created_at DESC");
$stmt->execute([$user_id]);
$myTasks = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard - Logistics</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="dashboard-page role-user">
    <nav class="sidebar driver-sidebar">
        <div class="sidebar-header">
            <h3>LogiTrans</h3>
        </div>
        <ul class="nav-links">
            <li class="active"><a href="user_dashboard.php">Dashboard</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <main class="main-content">
        <header class="top-bar">
            <h1>Driver Dashboard</h1>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </div>
        </header>

        <section class="section-container">
            <div class="card">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h2>Available Loads (Marketplace)</h2>
                    <form method="GET" class="filter-form" style="display: flex; gap: 0.5rem;">
                        <input type="text" name="search" placeholder="Search City..."
                            value="<?php echo $_GET['search'] ?? ''; ?>" style="padding: 0.4rem; font-size: 0.875rem;">
                        <button type="submit" class="btn btn-primary btn-sm"
                            style="width: auto; padding: 0.4rem 1rem;">Filter</button>
                    </form>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Route</th>
                                <th>Weight</th>
                                <th>Vehicle</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($availableLoads)): ?>
                                <tr>
                                    <td colspan="5" style="text-align:center;">No available loads at the moment.</td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach ($availableLoads as $load): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($load['from_city'] . ' → ' . $load['to_city']); ?></td>
                                    <td><?php echo $load['weight']; ?> tons</td>
                                    <td><?php echo $load['vehicle_type']; ?></td>
                                    <td><?php echo htmlspecialchars($load['description']); ?></td>
                                    <td>
                                        <form action="claim_load.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="load_id" value="<?php echo $load['id']; ?>">
                                            <button type="submit" class="btn btn-primary btn-sm">Claim Load</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="section-container" style="margin-top: 2rem;">
            <div class="card">
                <div class="card-header">
                    <h2>My Tasks</h2>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Route</th>
                                <th>Weight</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($myTasks)): ?>
                                <tr>
                                    <td colspan="4" style="text-align:center;">You haven't claimed any loads yet.</td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach ($myTasks as $task): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($task['from_city'] . ' → ' . $task['to_city']); ?></td>
                                    <td><?php echo $task['weight']; ?> tons</td>
                                    <td><span
                                            class="status-badge status-<?php echo strtolower($task['status']); ?>"><?php echo $task['status']; ?></span>
                                    </td>
                                    <td>
                                        <?php if ($task['status'] == 'Pending'): ?>
                                            <form action="complete_load.php" method="POST" style="display:inline;">
                                                <input type="hidden" name="load_id" value="<?php echo $task['id']; ?>">
                                                <button type="submit" class="btn btn-success btn-sm">Mark as Completed</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted">Completed</span>
                                        <?php endif; ?>
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