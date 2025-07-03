<?php
require_once 'includes/config.php';

header('Content-Type: application/json');

// Validate input
$errors = [];
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$rating = intval($_POST['rating'] ?? 0);
$comments = trim($_POST['comments'] ?? '');

if (empty($name)) {
    $errors[] = 'Name is required.';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email is required.';
}

if ($rating < 1 || $rating > 5) {
    $errors[] = 'Please select a valid rating.';
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

try {
    // Insert feedback into database
    $stmt = $pdo->prepare("INSERT INTO feedback (name, email, rating, comments) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $rating, $comments]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for your feedback! We appreciate your time.'
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to submit feedback. Please try again later.'
    ]);
}
?>