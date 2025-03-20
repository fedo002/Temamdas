<?php
// get-referrals.php - Ajax endpoint to get referrals for the referral tree
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set JSON response header
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];
$parent_id = isset($_GET['parent_id']) ? intval($_GET['parent_id']) : 0;
$level = isset($_GET['level']) ? intval($_GET['level']) : 1;

// For security, if parent_id is 0, we're looking at the user's direct referrals (level 1)
if ($parent_id === 0) {
    $parent_id = $user_id;
    $level = 1;
}

// Get database connection
$conn = $GLOBALS['db']->getConnection();

// Verify that the requested user is in the user's referral network
if ($parent_id !== $user_id) {
    // Check if this is a valid referral in the user's network
    $is_valid = false;
    
    if ($level === 2) {
        // Check if parent is a direct referral of the user
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE id = ? AND referrer_id = ?");
        $stmt->bind_param('ii', $parent_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $is_valid = ($row['count'] > 0);
    } elseif ($level === 3) {
        // Check if parent is a level 2 referral (a referral of user's direct referral)
        $stmt = $conn->prepare("SELECT u1.referrer_id 
                              FROM users u1 
                              JOIN users u2 ON u1.referrer_id = u2.id
                              WHERE u1.id = ? AND u2.referrer_id = ?");
        $stmt->bind_param('ii', $parent_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $is_valid = ($result->num_rows > 0);
    }
    
    if (!$is_valid) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid referral request']);
        exit;
    }
}

// Get referrals
$members = [];

$stmt = $conn->prepare("SELECT id, username, created_at FROM users WHERE referrer_id = ? ORDER BY created_at DESC");
$stmt->bind_param('i', $parent_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $members[] = [
        'id' => $row['id'],
        'username' => htmlspecialchars($row['username']),
        'joined_date' => date('d.m.Y', strtotime($row['created_at']))
    ];
}

echo json_encode([
    'status' => 'success',
    'level' => $level,
    'parent_id' => $parent_id,
    'members' => $members
]);