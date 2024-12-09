<?php
// Connect to database
$host = '127.0.0.1';
$username = 'u510162695_ancminimart';
$password = '1Ancminimart';  // Replace with the actual password
$dbname = 'u510162695_ancminimart';

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $tableName = $_POST['table'];
    $id = intval($_POST['id']);

    $sql = "DELETE FROM $tableName WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        echo "Record deleted successfully.";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Function to display table
function displayTable($conn, $tableName) {
    $sql = "SELECT * FROM $tableName";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<div style='margin-bottom: 20px;'>";
        echo "<h2>" . strtoupper($tableName) . " TABLE</h2>";
        echo "<table border='1' cellpadding='10' cellspacing='0'>";

        // Get field information for headers
        $fields = $result->fetch_fields();
        echo "<tr>";
        foreach ($fields as $field) {
            echo "<th style='background-color: #f2f2f2;'>" . $field->name . "</th>";
        }
        echo "<th style='background-color: #f2f2f2;'>Delete</th>"; // Add Delete column
        echo "</tr>";

        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $key => $value) {
                // Mask password for security
                if (strpos(strtolower($key), 'password') !== false) {
                    echo "<td>[MASKED]</td>";
                } else {
                    echo "<td>" . ($value ?? "NULL") . "</td>";
                }
            }
            // Add delete button
            echo "<td>";
            echo "<form method='POST' onsubmit='return confirm(\"Are you sure you want to delete this record?\");'>";
            echo "<input type='hidden' name='table' value='$tableName'>";
            echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";
            echo "<input type='submit' name='delete' value='Delete'>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
    } else {
        echo "0 results found in $tableName table";
    }
}

// Display tables
displayTable($conn, 'categories');
displayTable($conn, 'media');
displayTable($conn, 'products');
displayTable($conn, 'sales');
displayTable($conn, 'users');
displayTable($conn, 'user_groups');

$conn->close();
?>
