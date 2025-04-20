<?php
include('database.php');

$dept = $_GET['dept'] ?? ''; // Get department if set, otherwise empty string

// Map department names to department codes
$deptMap = [
    'FINANCE' => 'FINA',
    'PRODUCTION' => 'PROD',
    'MARKETING' => 'MARK'
];

$employees = [];

// If a department is specified, fetch employees for that department
if ($dept) {
    $deptCode = $deptMap[strtoupper($dept)] ?? ''; // Get the department code from the map
    if ($deptCode) {
        $sql = "SELECT EmpID, LN, FN, MI, EX, Age, Position, Region, Province, Municipality, Brgy 
                FROM employeeinfo WHERE DeptCode = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $deptCode);
    } else {
        $employees = []; // If department not found in the map, return empty
    }
} else {
    // Default: fetch all employees (fix query by adding DeptCode for consistency)
    $sql = "SELECT EmpID, LN, FN, MI, EX, Age, Position, Region, Province, Municipality, Brgy 
            FROM employeeinfo"; // Removed WHERE clause

    $stmt = $conn->prepare($sql); // No need for DeptCode if it's fetching all employees
}

// Execute the query and fetch the results
if ($stmt) {
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $fullName = $row['FN'] 
            . ' ' . ($row['MI'] ? $row['MI'] . '. ' : '') 
            . $row['LN'] 
            . ($row['EX'] ? ', ' . $row['EX'] : '');

            $address = $row['Brgy'] . ', ' . $row['Municipality'] . ', ' . $row['Province'] . ', ' . $row['Region'];

            $employees[] = [
                'EmpID' => $row['EmpID'],
                'FullName' => $fullName,
                'Age' => $row['Age'],
                'Position' => $row['Position'],
                'Address' => $address
            ];
        }
    } else {
        // Handle query execution failure (error logging can be added here)
        error_log("Query failed: " . $stmt->error);
        $employees = [];
    }
} else {
    // Handle statement preparation failure (error logging can be added here)
    error_log("Failed to prepare query: " . $conn->error);
    $employees = [];
}

echo json_encode($employees); // Output the employee data as JSON
?>
