<?php
// Include the database connection file
include('database.php');

// SQL query to count the total number of employees
$sql = "SELECT COUNT(*) AS total_employees FROM employeeinfo";
$result = $conn->query($sql);

// Fetch the result
$row = $result->fetch_assoc();

// Output the total number of employees
echo $row['total_employees'];

// Close the database connection
$conn->close();
?>
