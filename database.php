<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "employee_management_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// If the connection is successful, $conn will be available for use in other files.
?>
