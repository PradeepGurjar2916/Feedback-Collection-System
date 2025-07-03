<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=feedback_export_' . date('Y-m-d') . '.csv');

// Create output stream
$output = fopen('php://output', 'w');

// Write CSV headers
fputcsv($output, ['ID', 'Name', 'Email', 'Rating', 'Comments', 'Submission Date']);

// Fetch data and write to CSV
$stmt = $pdo->query("SELECT * FROM feedback ORDER BY submission_date DESC");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, [
        $row['id'],
        $row['name'],
        $row['email'],
        $row['rating'],
        $row['comments'],
        $row['submission_date']
    ]);
}

fclose($output);
exit;
?>