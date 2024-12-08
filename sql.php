<?php
$host = '127.0.0.1';
$dbname = 'u510162695_ancminimart';
$username = '1Ancminimart';
$password = 'u510162695_ancminimart';

try {
    // Create a PDO instance to connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully"; 
}
catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
