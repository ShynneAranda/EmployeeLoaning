<?php
include('database.php');

// Check if the necessary POST parameters are set
if (isset($_POST['empID'], $_POST['loanAmount'], $_POST['loanDate'], $_POST['loanReason'])) {
    // Retrieve form data
    $empID = $_POST['empID'];  // Employee ID from the form input
    $loanAmount = $_POST['loanAmount'];
    $loanDate = $_POST['loanDate'];
    $loanReason = $_POST['loanReason'];

    // Prepare the SQL query to insert a new loan
    $stmt = $conn->prepare("INSERT INTO loan (EmpID, LoanAmount, LoanDate, LoanReason, LoanStatus) VALUES (?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("iiss", $empID, $loanAmount, $loanDate, $loanReason);

    // Execute the query and check for success
    if ($stmt->execute()) {
        echo 'success';  // Return success message
    } else {
        // If there's an error, return the error message
        echo 'Error: ' . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    echo 'Error: Missing parameters';  // Handle missing parameters
}

// Close the database connection
$conn->close();
?>
