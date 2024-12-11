<?php
$host = '127.0.0.1';
$username = 'u510162695_ancminimart';
$password = '1Ancminimart';  // Replace with the actual password
$dbname = 'u510162695_ancminimart';
// Connect to the database
$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_selected'])) {
        $tableName = $_POST['table'];
        $ids = isset($_POST['ids']) ? $_POST['ids'] : [];

        if (!empty($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $conn->prepare("DELETE FROM $tableName WHERE id IN ($placeholders)");
            $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);

            if ($stmt->execute()) {
                echo "Selected records deleted successfully.";
            } else {
                echo "Error deleting records: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "No records selected for deletion.";
        }
    } elseif (isset($_POST['delete'])) {
        $tableName = $_POST['table'];
        $id = intval($_POST['id']);

        $stmt = $conn->prepare("DELETE FROM $tableName WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "Record deleted successfully.";
        } else {
            echo "Error deleting record: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Function to display table
function displayTable($conn, $tableName) {
    $sql = "SELECT * FROM $tableName";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<div style='margin-bottom: 20px;'>";
        echo "<h2>" . strtoupper($tableName) . " TABLE</h2>";
        echo "<form method='POST' onsubmit='return confirm(\"Are you sure you want to delete selected records?\");'>";
        echo "<input type='hidden' name='table' value='$tableName'>";
        echo "<table border='1' cellpadding='10' cellspacing='0'>";

        // Get field information for headers
        $fields = $result->fetch_fields();
        echo "<tr>";
        echo "<th style='background-color: #f2f2f2;'>Select</th>"; // Add checkbox column
        foreach ($fields as $field) {
            echo "<th style='background-color: #f2f2f2;'>" . htmlspecialchars($field->name) . "</th>";
        }
        echo "<th style='background-color: #f2f2f2;'>Delete</th>"; // Add Delete column
        echo "</tr>";

        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td><input type='checkbox' name='ids[]' value='" . htmlspecialchars($row['id']) . "'></td>"; // Checkbox for row selection
            foreach ($row as $key => $value) {
                // Mask password for security
                if (strpos(strtolower($key), 'password') !== false) {
                    echo "<td>[MASKED]</td>";
                } else {
                    echo "<td>" . htmlspecialchars($value ?? "NULL") . "</td>";
                }
            }
            // Add delete button
            echo "<td>";
            echo "<form method='POST' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this record?\");'>";
            echo "<input type='hidden' name='table' value='" . htmlspecialchars($tableName) . "'>";
            echo "<input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>";
            echo "<input type='submit' name='delete' value='Delete'>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<br>";
        echo "<input type='submit' name='delete_selected' value='Delete Selected'>";
        echo "</form>";
        echo "</div>";
    } else {
        echo "0 results found in $tableName table";
    }
}

// Display tables
$tables = ['categories', 'media', 'products', 'sales', 'users', 'user_groups'];
foreach ($tables as $table) {
    displayTable($conn, $table);
}

$conn->close();
?>
