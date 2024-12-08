<?php
$host = '127.0.0.1';
$dbname = 'u510162695_ancminimart';
$username = 'u510162695_ancminimart';
$password = '1Ancminimart';

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to access the 'users' table
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

// Check if query returned results
if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        echo "id: " . $row["id"] . " - Name: " . $row["name"] . " - Email: " . $row["email"] . "<br>";
    }
} else {
    echo "0 results";
}

// Close connection
$conn->close();
?>
