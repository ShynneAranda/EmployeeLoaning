<?php
// Connect to database
include('database.php');
$conn = new mysqli("localhost", "root", "", "employee_management_system");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Define friendly department names
$deptNames = [
    'PROD' => 'Production',
    'MARK' => 'Marketing',
    'FINA' => 'Finance'
];
$sql = "SELECT DeptCode, COUNT(*) as count FROM employeeinfo GROUP BY DeptCode";
$result = $conn->query($sql);

if ($result === false) {
    http_response_code(500);
    echo json_encode(['error' => $conn->error]);
    exit;
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $code = $row['DeptCode'];
    $data[] = [
        'DeptCode' => $code,
        'Department' => $deptNames[$code] ?? $code,  // fallback to code if not found
        'count' => (int)$row['count']
    ];
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($data);
?>