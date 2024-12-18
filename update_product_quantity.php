<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database credentials
$host = '127.0.0.1';
$username = 'u510162695_ancminimart';
$password = '1Ancminimart';
$dbname = 'u510162695_ancminimart';

// Create a connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the data from the request
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit;
}

foreach ($data as $item) {
    $barcode = $item['barcode'];
    $quantityDifference = $item['quantityDifference'];

    // Prepare and bind
    $stmt = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE barcode = ?");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("is", $quantityDifference, $barcode);

    // Execute the statement
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Execute failed: ' . $stmt->error]);
        exit;
    }
}

// Close the statement and connection
$stmt->close();
$conn->close();

echo json_encode(['success' => true]);
?>