<?php
// Database credentials
$host = '127.0.0.1';
$username = 'u510162695_ancminimart';
$password = '1Ancminimart'; // Replace with the actual password
$dbname = 'u510162695_ancminimart';

// Create a connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL to add a new column
$sql = "ALTER TABLE users ADD COLUMN alternative_email VARCHAR(255)";

// Execute the query
if ($conn->query($sql) === TRUE) {
    echo "Column 'alternative_email' added successfully.";
} else {
    echo "Error adding column: " . $conn->error;
}

// Close connection
$conn->close();
?>