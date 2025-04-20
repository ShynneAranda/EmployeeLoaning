<?php
include 'database.php';

// Total Employees per Department
$deptQuery = "SELECT d.DeptDesc, COUNT(e.EmpID) AS Total 
              FROM departmentinfo d
              LEFT JOIN employeeinfo e ON d.DeptCode = e.DeptCode
              GROUP BY d.DeptCode";
$deptResult = $conn->query($deptQuery);

// Loan Summary
$loanQuery = "SELECT LoanStatus, COUNT(*) as Count FROM loan GROUP BY LoanStatus";
$loanResult = $conn->query($loanQuery);
?>

<div class="overview-section">
    <h3>Department Summary</h3>
    <ul>
        <?php while($row = $deptResult->fetch_assoc()): ?>
            <li><strong><?= $row['DeptDesc'] ?>:</strong> <?= $row['Total'] ?> employees</li>
        <?php endwhile; ?>
    </ul>

    <h3>Loan Status</h3>
    <ul>
        <?php while($row = $loanResult->fetch_assoc()): ?>
            <li><strong><?= $row['LoanStatus'] ?>:</strong> <?= $row['Count'] ?> loans</li>
        <?php endwhile; ?>
    </ul>
</div>
