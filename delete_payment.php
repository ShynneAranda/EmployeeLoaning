<?php
include('database.php');

// Check if the payment ID is provided
if (isset($_GET['paymentid'])) {
    $paymentID = $_GET['paymentid'];

    // Prepare the SQL query
    $stmt = $conn->prepare("DELETE FROM emp_payments WHERE PaymentID = ?");
    $stmt->bind_param("i", $paymentID);

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
    echo 'Error: Missing payment ID';
}

// Close the database connection
$conn->close();
?>
