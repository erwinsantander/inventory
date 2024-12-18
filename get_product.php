<?php
// Database connection configuration
$host = '127.0.0.1';
$dbname = 'u510162695_ancminimart';
$username = 'u510162695_ancminimart';
$password = '1Ancminimart';

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Set error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get barcode and requested quantity from GET request
    $barcode = $_GET['barcode'] ?? '';
    $requested_quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 0;

    // Prepare SQL statement to fetch product with quantity check
    $stmt = $pdo->prepare("SELECT barcode, name, sale_price, quantity 
                            FROM products 
                            WHERE barcode = :barcode 
                            LIMIT 1");
    $stmt->execute(['barcode' => $barcode]);

    // Fetch the product
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Send JSON response
    header('Content-Type: application/json');
    
    if ($product) {
        if ($requested_quantity > $product['quantity']) {
            // Requested quantity exceeds available stock
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Out of Stock']);
        } else {
            // Return product details
            echo json_encode($product);
        }
    } else {
        // Product not found or out of stock
        header('HTTP/1.1 404 Not Found');
        echo json_encode(['error' => 'Product not found or out of stock']);
    }

} catch(PDOException $e) {
    // Handle database errors
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>