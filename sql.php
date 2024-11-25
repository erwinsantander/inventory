<?php
// Define database connection settings
define( 'DB_HOST', '127.0.0.1' );          // Set database host
define( 'DB_USER', 'u510162695_ancminimart' ); // Set database user
define( 'DB_PASS', '1Ancminimart' );         // Set database password
define( 'DB_NAME', 'u510162695_ancminimart' ); // Set database name

// Function to connect to the database
function connect_db() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// Function to add 'expiration_date' column to the 'product' table
function add_expiration_date_column() {
    $conn = connect_db();
    
    // SQL query to add the 'expiration_date' column to the 'product' table
    $query = "ALTER TABLE products ADD COLUMN expiration_date DATE";

    if ($conn->query($query) === TRUE) {
        echo "Column 'expiration_date' added successfully to the 'product' table.";
    } else {
        echo "Error adding column: " . $conn->error;
    }

    // Close the connection
    $conn->close();
}

// Call the function to add the expiration_date column
add_expiration_date_column();
?>
