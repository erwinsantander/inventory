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

// Function to display the columns of the 'product' table
function display_product_table_columns() {
    $conn = connect_db();
    
    // Query to get column names from the 'product' table
    $query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'product' AND TABLE_SCHEMA = '" . DB_NAME . "'";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // Display the column names
        echo "Columns in the 'product' table:<br>";
        while ($row = $result->fetch_assoc()) {
            echo $row['COLUMN_NAME'] . "<br>";
        }
    } else {
        echo "No columns found for the 'product' table.";
    }

    // Close the connection
    $conn->close();
}

// Call the function to display the columns
display_product_table_columns();
?>
