<?php
// Database configuration
define('DB_HOST', '127.0.0.1'); // Database host
define('DB_USER', 'u510162695_ancminimart'); // Database user
define('DB_PASS', '1Ancminimart'); // Database password
define('DB_NAME', 'u510162695_ancminimart'); // Database name

// Establish a connection to the database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL to add columns to the users table
$sql = "
    ALTER TABLE `users`
    ADD COLUMN `token` VARCHAR(100) DEFAULT NULL,
    ADD COLUMN `reset_token_at` TIMESTAMP NULL DEFAULT NULL;
";

// Execute the query
if ($conn->query($sql) === TRUE) {
    echo "Columns `token` and `reset_token_at` added successfully.";
} else {
    echo "Error updating table: " . $conn->error;
}

// Close the connection
$conn->close();
?>
