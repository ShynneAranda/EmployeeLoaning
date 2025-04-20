<?php
include('database.php');
$conn = new mysqli("localhost", "root", "", "employee_management_system");

if (isset($_GET['loanid'])) {
    $loanID = $_GET['loanid'];

    // Prepare the SQL query to get payment records for the specified LoanID
    $query = "
        SELECT PaymentID, PaymentDate, PaymentAmount, PaymentMethod
        FROM emp_payments
        WHERE LoanID = ?
        ORDER BY PaymentDate DESC
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $loanID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if there are results
    if ($result->num_rows > 0) {
        $payments = [];
        while ($row = $result->fetch_assoc()) {
            $payments[] = $row;
        }

        // Return the payment records as a JSON response
        echo json_encode($payments);
    } else {
        echo json_encode([]); // No payments found
    }
} else {
    echo json_encode(['error' => 'No loan ID provided.']);
}
?>
