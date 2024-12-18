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

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!is_array($data)) {
        throw new Exception('Invalid data format');
    }

    foreach ($data as $item) {
        $barcode = $item['barcode'];
        $quantity = $item['quantity'];

        // Prepare SQL statement to update product quantity
        $stmt = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE barcode = ? AND quantity >= ?");
        $stmt->bind_param('isi', $quantity, $barcode, $quantity);

        if (!$stmt->execute()) {
            throw new Exception("Failed to update quantity for barcode: $barcode");
        }

        if ($stmt->affected_rows === 0) {
            throw new Exception("No rows updated for barcode: $barcode");
        }
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    error_log("Error updating product quantities: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    $conn->close();
}
?>