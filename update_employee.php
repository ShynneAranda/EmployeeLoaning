<?php
include('database.php');

$id = $_POST['id'];
$FN = $_POST['FN'];
$MI = $_POST['MI'];
$LN = $_POST['LN'];
$age = $_POST['age'];
$position = $_POST['position'];
$Brgy = $_POST['Brgy'];
$Municipality = $_POST['Municipality'];
$Province = $_POST['Province'];
$Region = $_POST['Region'];

$sql = "UPDATE employeeinfo 
        SET FN = ?, MI = ?, LN = ?, Age = ?, Position = ?, Brgy = ?, Municipality = ?, Province = ?, Region = ?
        WHERE EmpID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssisssssi", $FN, $MI, $LN, $age, $position, $Brgy, $Municipality, $Province, $Region, $id);

if ($stmt->execute()) {
    echo "Success";
} else {
    echo "Error: " . $stmt->error;
}
?>
 