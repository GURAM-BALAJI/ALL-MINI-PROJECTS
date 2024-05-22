<?php
$servername = 'localhost';
$dbname = 'juice_shop';
$username = 'root';
$password = '';

// Create connection
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Output error message if connection fails
    echo "Connection failed: " . $e->getMessage();
    // Terminate script execution
    die();
}
?>
