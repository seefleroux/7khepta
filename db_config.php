<?php
// Database configuration
$host = 'localhost';
$dbname = 'track_field_strategic_plan';
$username = 'root';  // Change as needed
$password = '';      // Change as needed

try {
    // Create a PDO instance
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}
?> 