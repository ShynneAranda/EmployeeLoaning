<?php
include('database.php');

// Check if the loan ID is provided
if (isset($_GET['loanid'])) {
    $loanID = $_GET['loanid'];

    // Prepare the SQL query to delete the loan and related payments
    $conn->begin_transaction();
    try {
        // Delete related payments first
        $stmt = $conn->prepare("DELETE FROM emp_payments WHERE LoanID = ?");
        $stmt->bind_param("i", $loanID);
        $stmt->execute();

        // Then delete the loan
        $stmt = $conn->prepare("DELETE FROM loan WHERE LoanID = ?");
        $stmt->bind_param("i", $loanID);
        $stmt->execute();

        $conn->commit();
        echo 'success';
    } catch (Exception $e) {
        $conn->rollback();
        echo 'Error: ' . $e->getMessage();
    }

    // Close the statement
    $stmt->close();
} else {
    echo 'Error: Missing loan ID';
}

// Close the database connection
$conn->close();
?>
