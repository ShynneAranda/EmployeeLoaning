<?php 
$conn = new mysqli("localhost", "root", "", "employee_management_system");

if (isset($_GET['loanid'])) {
    $loanid = $_GET['loanid'];

    $stmt = $conn->prepare("SELECT PaymentDate, PaymentAmount, PaymentMethod FROM emp_payments WHERE LoanID = ?");
    $stmt->bind_param("s", $loanid);
    $stmt->execute();

    $result = $stmt->get_result();
    $payments = [];

    while ($row = $result->fetch_assoc()) {
        $payments[] = [
            'PaymentDate' => date("F d, Y", strtotime($row['PaymentDate'])),
            'PaymentAmount' => $row['PaymentAmount'],
            'PaymentMethod' => $row['PaymentMethod']
        ];
    }

    echo json_encode($payments);
}
?>
