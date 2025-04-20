<?php
include('database.php');

// Check if the necessary POST parameters are set
if (isset($_POST['loanid'], $_POST['paymentDate'], $_POST['amount'], $_POST['method'], $_POST['empID'])) {
    $loanID = $_POST['loanid'];
    $empID = $_POST['empID'];  // Get EmpID from the form
    $paymentDate = $_POST['paymentDate'];
    $amount = $_POST['amount'];
    $method = $_POST['method'];

    // Prepare the SQL query
    $stmt = $conn->prepare("INSERT INTO emp_payments (LoanID, EmpID, PaymentDate, PaymentAmount, PaymentMethod) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisis", $loanID, $empID, $paymentDate, $amount, $method);

    // Execute the query and check for success
    if ($stmt->execute()) {
        echo 'success';
    } else {
        // If there's an error, return the error message
        echo 'Error: ' . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    echo 'Error: Missing parameters';
}

// Close the database connection
$conn->close();
?>
