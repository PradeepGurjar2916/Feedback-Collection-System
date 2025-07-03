<?php
require_once '../includes/auth.php';

// Handle logout request
if (isset($_GET['logout'])) {
    adminLogout();
    header("Location: login.php");
    exit;
}

// Check login status
if (!isAdminLoggedIn()) {
    header("Location: login.php");
    exit;
}

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");

// Database operations with error handling
try {
    // Get all feedback
    $stmt = $pdo->query("SELECT * FROM feedback ORDER BY submission_date DESC");
    $feedback = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get rating statistics
    $statsStmt = $pdo->query("
        SELECT 
            rating,
            COUNT(*) as count,
            ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM feedback), 1) as percentage
        FROM feedback
        GROUP BY rating
        ORDER BY rating DESC
    ");
    $stats = $statsStmt->fetchAll();
    
    // Calculate average rating
    $avgStmt = $pdo->query("SELECT AVG(rating) as avg_rating FROM feedback");
    $avgRating = round($avgStmt->fetch()['avg_rating'], 1);
    
    // Initialize rating distribution array
    $ratings = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
    foreach ($stats as $stat) {
        $ratings[$stat['rating']] = $stat['count'];
    }
    
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .rating-badge {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: bold;
            color: white;
        }
        .chart-container {
            height: 300px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Feedback Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="export_csv.php">Export CSV</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="dashboard.php?logout=1" onclick="return confirm('Are you sure you want to logout?');">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Feedback</h5>
                        <h2 class="card-text"><?php echo count($feedback); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Average Rating</h5>
                        <h2 class="card-text"><?php echo !empty($feedback) ? $avgRating.'/5' : 'N/A'; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Latest Feedback</h5>
                        <h2 class="card-text">
                            <?php echo !empty($feedback) ? (new DateTime($feedback[0]['submission_date']))->format('M j, Y') : 'N/A'; ?>
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($feedback)): ?>
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Rating Distribution</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="ratingChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Rating Statistics</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Rating</th>
                                    <th>Count</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stats as $stat): ?>
                                <tr>
                                    <td>
                                        <span class="rating-badge bg-<?php 
                                            echo $stat['rating'] >= 4 ? 'success' : 
                                                 ($stat['rating'] >= 3 ? 'info' : 'danger'); 
                                        ?>">
                                            <?php echo $stat['rating']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $stat['count']; ?></td>
                                    <td><?php echo $stat['percentage']; ?>%</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">All Feedback</h5>
                <div>
                    <a href="export_csv.php" class="btn btn-sm btn-light">Export CSV</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Rating</th>
                                <th>Comments</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($feedback as $item): ?>
                            <tr>
                                <td><?php echo $item['id']; ?></td>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo htmlspecialchars($item['email']); ?></td>
                                <td>
                                    <span class="rating-badge bg-<?php 
                                        echo $item['rating'] >= 4 ? 'success' : 
                                             ($item['rating'] >= 3 ? 'info' : 'danger'); 
                                    ?>">
                                        <?php echo $item['rating']; ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($item['comments']); ?></td>
                                <td><?php echo (new DateTime($item['submission_date']))->format('M j, Y H:i'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-info">No feedback submissions found.</div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <?php if (!empty($feedback)): ?>
    <script>
        // Rating distribution chart
        const ctx = document.getElementById('ratingChart').getContext('2d');
        const ratingChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['5 Stars', '4 Stars', '3 Stars', '2 Stars', '1 Star'],
                datasets: [{
                    label: 'Number of Ratings',
                    data: [<?php echo implode(', ', $ratings); ?>],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.7)',
                        'rgba(40, 167, 69, 0.5)',
                        'rgba(23, 162, 184, 0.5)',
                        'rgba(220, 53, 69, 0.5)',
                        'rgba(220, 53, 69, 0.7)'
                    ],
                    borderColor: [
                        'rgba(40, 167, 69, 1)',
                        'rgba(40, 167, 69, 1)',
                        'rgba(23, 162, 184, 1)',
                        'rgba(220, 53, 69, 1)',
                        'rgba(220, 53, 69, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
    <?php endif; ?>
    
    <script>
        // Handle logout
        document.querySelector('a[href="?logout=1"]').addEventListener('click', function(e) {
            e.preventDefault();
            fetch('login.php?logout=1')
                .then(() => window.location.href = 'login.php');
        });
    </script>
</body>
</html>