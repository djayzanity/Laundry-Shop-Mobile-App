<?php
session_start();
require 'connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the latest order statuses
$stmt = $pdo->prepare("SELECT id, _transaction FROM _statement WHERE user_id = ?");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$orderStatuses = [];
foreach ($orders as $order) {
    $transactionData = json_decode($order['_transaction'], true);
    $status = $transactionData['status'] ?? 'Pending';
    $orderStatuses[] = [
        'id' => $order['id'],
        'status' => $status
    ];
}

// Return the order statuses as JSON
echo json_encode(['status' => 'success', 'orders' => $orderStatuses]);
?>
