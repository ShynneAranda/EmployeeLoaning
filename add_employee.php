<?php
// Include database connection
include('database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and set optional fields (MI and EX) to NULL if they are empty
    $empID = $_POST['EmpID'];
    $ln = $_POST['LN'];
    $fn = $_POST['FN'];
    $mi = empty($_POST['MI']) ? NULL : $_POST['MI'];  // Optional field, set to NULL if empty
    $ex = empty($_POST['EX']) ? NULL : $_POST['EX'];  // Optional field, set to NULL if empty
    $age = $_POST['Age'];
    $deptCode = $_POST['DeptCode'];
    $position = empty($_POST['Position']) ? NULL : $_POST['Position'];  // Optional field, set to NULL if empty
    $region = empty($_POST['Region']) ? NULL : $_POST['Region'];  // Optional field, set to NULL if empty
    $province = empty($_POST['Province']) ? NULL : $_POST['Province'];  // Optional field, set to NULL if empty
    $municipality = empty($_POST['Municipality']) ? NULL : $_POST['Municipality'];  // Optional field, set to NULL if empty
    $brgy = empty($_POST['Brgy']) ? NULL : $_POST['Brgy'];  // Optional field, set to NULL if empty

    // Check for duplicate employee in the same department
    $checkStmt = $conn->prepare("SELECT * FROM employeeinfo WHERE LN = ? AND FN = ? AND DeptCode = ?");
    $checkStmt->bind_param('sss', $ln, $fn, $deptCode);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    // If a matching employee exists, alert the user and stop the insert
    if ($result->num_rows > 0) {
        echo "<script>alert('Error: Employee with the same name already exists in this department.'); window.location.href = 'dashboard.php';</script>";
    } else {
        // Prepare the SQL query to insert new employee
        $stmt = $conn->prepare("INSERT INTO employeeinfo (EmpID, LN, FN, MI, EX, Age, DeptCode, Position, Region, Province, Municipality, Brgy)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Here we assign the nullable values to variables explicitly
        $mi_value = ($mi === NULL) ? NULL : $mi;
        $ex_value = ($ex === NULL) ? NULL : $ex;
        $position_value = ($position === NULL) ? NULL : $position;
        $region_value = ($region === NULL) ? NULL : $region;
        $province_value = ($province === NULL) ? NULL : $province;
        $municipality_value = ($municipality === NULL) ? NULL : $municipality;
        $brgy_value = ($brgy === NULL) ? NULL : $brgy;

        // Bind parameters
        $stmt->bind_param('ississssssss', 
                          $empID, 
                          $ln, 
                          $fn, 
                          $mi_value, 
                          $ex_value, 
                          $age, 
                          $deptCode, 
                          $position_value, 
                          $region_value, 
                          $province_value, 
                          $municipality_value, 
                          $brgy_value);

        // Try executing the statement
        try {
            // Execute the statement
            if ($stmt->execute()) {
                echo "<script>alert('Employee added successfully!'); window.location.href = 'dashboard.php';</script>";
            } else {
                throw new Exception("Error: " . $stmt->error);
            }
        } catch (mysqli_sql_exception $e) {
            // Specific error handling for duplicate entry
            if ($e->getCode() == 1062) {
                echo "<script>alert('Error: Duplicate entry for employee in the department.'); window.location.href = 'dashboard.php';</script>";
            } else {
                echo "<script>alert('An error occurred: " . $e->getMessage() . "'); window.location.href = 'dashboard.php';</script>";
            }
        }

        // Close the statement
        $stmt->close();
    }

    // Close the check statement
    $checkStmt->close();
}
?>
