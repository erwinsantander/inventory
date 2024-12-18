<?php
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
$barcode = $data['barcode'];
$quantityDifference = $data['quantityDifference'];

// Prepare and bind
$stmt = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE barcode = ?");
$stmt->bind_param("is", $quantityDifference, $barcode);

// Execute the statement
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update product quantity']);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>