<?php
$host = '127.0.0.1';
$dbname = 'u510162695_ancminimart';
$username = '1Ancminimart';
$password = 'u510162695_ancminimart';

try {
    // Establish a database connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Set PDO to throw exceptions on error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL query to add the new column 'contact_number'
    $sql = "ALTER TABLE users ADD COLUMN contact_number VARCHAR(15)";

    // Execute the query
    $pdo->exec($sql);

    echo "Column 'contact_number' added successfully to the 'users' table.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
