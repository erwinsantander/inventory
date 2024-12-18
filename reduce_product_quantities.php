<?php
require_once('includes/load.php'); // Ensure this includes your database connection setup

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!is_array($data)) {
        throw new Exception('Invalid data format');
    }

    $pdo = new PDO("mysql:host=127.0.0.1;dbname=u510162695_ancminimart;charset=utf8", 'u510162695_ancminimart', '1Ancminimart');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    foreach ($data as $item) {
        $barcode = $item['barcode'];
        $quantity = $item['quantity'];

        // Prepare SQL statement to update product quantity
        $stmt = $pdo->prepare("UPDATE products SET quantity = quantity - :quantity WHERE barcode = :barcode AND quantity >= :quantity");
        $stmt->execute(['quantity' => $quantity, 'barcode' => $barcode]);

        if ($stmt->rowCount() === 0) {
            throw new Exception("Failed to update quantity for barcode: $barcode");
        }
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>