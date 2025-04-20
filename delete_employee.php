<?php
include('database.php');

// Get employee ID from POST request
$empID = $_POST['id'] ?? null;
if (!$empID) {
    echo "Error: Missing employee ID";
    exit;
}

// Check if the employee has any loan records
$stmt = $conn->prepare("SELECT COUNT(*) FROM loan WHERE EmpID = ?");
$stmt->bind_param("i", $empID);
$stmt->execute();
$stmt->bind_result($loanCount);
$stmt->fetch();
$stmt->close();

// If there are loan records, show an alert and prevent deletion
if ($loanCount > 0) {
    echo "Oops! This employee has an existing loan. Please delete the loan first before attempting to delete the employee. If you need assistance, feel free to check the loan details.";

} else {
    // Proceed with deletion if no loans are found
    $conn->begin_transaction();

    try {
        // Step 1: Delete related payment records (if any)
        $stmt = $conn->prepare("DELETE FROM emp_payments WHERE EmpID = ?");
        $stmt->bind_param("i", $empID);
        $stmt->execute();
        $stmt->close();

        // Step 2: Delete the employee record
        $stmt = $conn->prepare("DELETE FROM employeeinfo WHERE EmpID = ?");
        $stmt->bind_param("i", $empID);
        $stmt->execute();

        // Commit the transaction
        $conn->commit();

        if ($stmt->affected_rows > 0) {
            echo "Employee deleted successfully";
        } else {
            echo "Failed to delete employee. Please check if the ID exists.";
        }

        $stmt->close();
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Error during deletion: " . $e->getMessage() . "'); window.location.href = 'dashboard.php';</script>";
    }
}

$conn->close();

?>
