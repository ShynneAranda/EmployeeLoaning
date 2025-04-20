<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "employee_management_system";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT COUNT(*) AS activeCount 
        FROM loan 
        WHERE LoanStatus IN ('Pending', 'Approved', 'Disbursed', 'Partially Paid')";

$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    echo $row['activeCount'];
} else {
    echo "0";
}

$conn->close();
?>
