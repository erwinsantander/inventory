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

// Fetch column names dynamically
$sql = "SHOW COLUMNS FROM products";
$columnsResult = $conn->query($sql);

if ($columnsResult->num_rows > 0) {
    // Output table headers
    echo "<table border='1'><tr>";
    $columns = [];
    while ($column = $columnsResult->fetch_assoc()) {
        echo "<th>" . $column['Field'] . "</th>";
        $columns[] = $column['Field']; // Save column names for data display
    }
    echo "</tr>";

    // Fetch and display data
    $dataSql = "SELECT * FROM product";
    $dataResult = $conn->query($dataSql);

    if ($dataResult->num_rows > 0) {
        while ($row = $dataResult->fetch_assoc()) {
            echo "<tr>";
            foreach ($columns as $col) {
                echo "<td>" . $row[$col] . "</td>";
            }
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='" . count($columns) . "'>No data available</td></tr>";
    }
    echo "</table>";
} else {
    echo "The product table doesn't exist or has no columns.";
}

// Close connection
$conn->close();
?>
