<?php
// Database connection details
$host = '127.0.0.1';
$dbname = 'u510162695_ancminimart';
$username = 'u510162695_ancminimart';
$password = '1Ancminimart';

try {
    // Create a new PDO instance
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL to add a new column 'contact_number' to the 'users' table
    $sql = "ALTER TABLE users ADD COLUMN contact_number VARCHAR(15)";

    // Execute the query
    $db->exec($sql);
    echo "Column 'contact_number' added successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close the connection
$db = null;
?>