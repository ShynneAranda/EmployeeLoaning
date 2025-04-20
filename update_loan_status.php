<?php
// Connect to database
include('database.php');
$conn = new mysqli("localhost", "root", "", "employee_management_system");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $loanid = $_POST['loanid'];
    $status = $_POST['status'];

    // Update loan status
    $query = "UPDATE loan SET LoanStatus = ? WHERE LoanID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $status, $loanid);

    if ($stmt->execute()) {
        echo "Loan status updated successfully.";
    } else {
        echo "Error updating loan status: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
