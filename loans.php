<?php
include('database.php');
$conn = new mysqli("localhost", "root", "", "employee_management_system");

$query = "
    SELECT 
        l.LoanID, l.LoanAmount, l.LoanDate, l.LoanReason, l.LoanStatus,
        e.EmpID, CONCAT(e.LN, ', ', e.FN, ' ', e.MI) AS FullName,
        e.DeptCode, e.Position
    FROM 
        loan l
    JOIN 
        employeeinfo e ON l.EmpID = e.EmpID
    ORDER BY 
        FIELD(l.LoanStatus, 'Pending', 'Approved', 'Disbursed', 'Partially Paid', 'Fully Paid', 'Rejected'),
        l.LoanDate DESC
";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Loans</title>
<link rel="stylesheet" href="css/loans.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<a href="dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>

<div class="top-bar">
    <div class="top-bar-content">
        <h2 class="title">Employee Loan Records</h2>
        <input type="text" placeholder="Search anything..." class="search-bar" id="searchInput">
        <button class="add-btn" onclick="openAddLoanModal()">+ New Loan</button>

    </div>
</div>

<!-- Modal Overlay -->
<div id="modalOverlay" style="display:none;"></div>

<!-- Add Loan Modal -->
<div id="addLoanModal" style="display:none;">
    <span class="close-btn" onclick="closeAddLoanModal()">&times;</span>
    <h3>Add Employee Loan</h3>
    <form id="addLoanForm">
    <label for="empID">Employee ID:</label>
<input type="text" id="empID" name="empID" required><br>





        <label for="loanAmount">Loan Amount:</label>
        <input type="number" id="loanAmount" name="loanAmount" required><br>

        <label for="loanDate">Loan Date:</label>
        <input type="date" id="loanDate" name="loanDate" required><br>

        <label for="loanReason">Loan Reason:</label>
        <textarea id="loanReason" name="loanReason" required></textarea><br>

        <button type="submit">Add Loan</button>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>Loan ID</th>
            <th>Employee ID</th>
            <th>Name</th>
            <th>Department</th>
            <th>Position</th>
            <th>Loan Amount</th>
            <th>Loan Date</th>
            <th>Reason</th>
            <th>Status</th>
            <th>Payment Record</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $row['LoanID'] ?></td>
            <td><?= $row['EmpID'] ?></td>
            <td><?= $row['FullName'] ?></td>
            <td><?= $row['DeptCode'] ?></td>
            <td><?= $row['Position'] ?></td>
            <td>₱<?= number_format($row['LoanAmount']) ?></td>
            <td><?= date("F d, Y", strtotime($row['LoanDate'])) ?></td>
            <td><?= $row['LoanReason'] ?></td>
            <td class="status <?= strtolower(str_replace(' ', '-', $row['LoanStatus'])) ?>">
                <select class="status-dropdown" data-loanid="<?= $row['LoanID'] ?>" onchange="updateLoanStatus(<?= $row['LoanID'] ?>, this.value)">
                    <option value="Pending" <?= $row['LoanStatus'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Approved" <?= $row['LoanStatus'] == 'Approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="Disbursed" <?= $row['LoanStatus'] == 'Disbursed' ? 'selected' : '' ?>>Disbursed</option>
                    <option value="Partially Paid" <?= $row['LoanStatus'] == 'Partially Paid' ? 'selected' : '' ?>>Partially Paid</option>
                    <option value="Fully Paid" <?= $row['LoanStatus'] == 'Fully Paid' ? 'selected' : '' ?>>Fully Paid</option>
                    <option value="Rejected" <?= $row['LoanStatus'] == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
            </td>
            <td>
            <button class="view-btn" 
    data-loanid="<?= $row['LoanID'] ?>"
    data-empid="<?= $row['EmpID'] ?>"
    onclick="showPayments(this)">View</button>



                <button class="delete-btn" onclick="deleteLoan(<?= $row['LoanID'] ?>)"><i class="fas fa-trash"></i> Delete</button>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- Payment Modal -->
<div id="paymentModal" style="display:none;">
    <span class="close-btn" onclick="closeModal()">&times;</span>
    <h3>Payment Records</h3>
    <button class="add-payment-btn" onclick="openAddPaymentForm()">+ Add Payment</button>
    <table id="paymentTable">
        <thead>
            <tr>
                <th>Payment Date</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <div id="addPaymentFormContainer" style="display:none; margin-top: 20px;">
    <h4>Add New Payment</h4>
    <form id="addPaymentForm">
        <input type="hidden" id="addLoanId" name="loanid">
        <!-- In Payment Modal -->
        <input type="hidden" id="addPaymentEmpId" name="empID">


        <label for="paymentDate">Payment Date:</label>
        <input type="date" id="paymentDate" name="paymentDate" required><br>

        <label for="amount">Amount:</label>
        <input type="number" id="amount" name="amount" required><br>

        <label for="method">Method:</label>
        <input type="text" id="method" name="method" required><br>

        <button type="submit">Add Payment</button>
    </form>
</div>

</div>
<script>
function showPayments(buttonOrLoanId, empId = null) {
    let loanID, empID;

    if (typeof buttonOrLoanId === 'object') {
        loanID = buttonOrLoanId.dataset.loanid;
        empID = buttonOrLoanId.dataset.empid;
    } else {
        loanID = buttonOrLoanId;
        empID = empId || document.getElementById("addPaymentEmpId").value;
    }

    document.getElementById("paymentModal").style.display = "block";
    document.getElementById("addLoanId").value = loanID;
    document.getElementById("addPaymentEmpId").value = empID;

    fetch(`get_payments.php?loanid=${loanID}`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector("#paymentTable tbody");
            tbody.innerHTML = "";

            data.forEach(p => {
                tbody.innerHTML += `
                    <tr>
                        <td>${p.PaymentDate}</td>
                        <td>₱${parseInt(p.PaymentAmount).toLocaleString()}</td>
                        <td>${p.PaymentMethod}</td>
                        <td><button onclick="deletePayment(${p.PaymentID}, ${loanID})">Delete</button></td>
                    </tr>`;
            });
        });
}

function openAddPaymentForm() {
    document.getElementById("addPaymentFormContainer").style.display = "block";
}

document.getElementById("addPaymentForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const form = new FormData(this);
fetch("add_payment.php", {
    method: "POST",
    body: form
}).then(res => res.text())
.then(res => {
    if (res.trim() === 'success') {
        showPayments(document.getElementById("addLoanId").value, document.getElementById("addPaymentEmpId").value);

        this.reset();
        document.getElementById("addPaymentFormContainer").style.display = "none";
    } else {
        alert("Failed to add payment: " + res);  // Show server response if failed
    }
}).catch(error => {
    console.error('Error adding payment:', error);
    alert('Failed to add payment.');
});
});  


function closeModal() {
    document.getElementById("paymentModal").style.display = "none";
    document.getElementById("addPaymentFormContainer").style.display = "none";
}

document.getElementById("searchInput").addEventListener("input", function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll("tbody tr");
    
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
});
function openAddLoanModal() {
    document.getElementById("modalOverlay").style.display = "block";
    document.getElementById("addLoanModal").style.display = "block";
}

function closeAddLoanModal() {
    document.getElementById("modalOverlay").style.display = "none";
    document.getElementById("addLoanModal").style.display = "none";
}

function deletePayment(paymentID, loanID) {
    if (confirm("Are you sure you want to delete this payment?")) {
        fetch(`delete_payment.php?paymentid=${paymentID}`)
            .then(res => res.text())
            .then(res => {
                if (res.trim() === "success") {
                    showPayments(loanID); // Refresh payment list
                } else {
                    alert("Failed to delete payment.");
                }
            });
    }
}

function deleteLoan(loanID) {
    if (confirm("Are you sure you want to delete this loan and all related payments?")) {
        fetch(`delete_loan.php?loanid=${loanID}`)
            .then(res => res.text())
            .then(res => {
                if (res.trim() === "success") {
                    alert("Loan deleted successfully.");
                    location.reload(); // Refresh the page
                } else {
                    alert("Failed to delete loan.");
                }
            });
    }
}
    document.getElementById("addLoanForm").addEventListener("submit", function(e) {
    e.preventDefault();  // Prevent form from submitting the default way

    const form = new FormData(this);  // Collect form data, including empID
    fetch("add_loan.php", {
        method: "POST",
        body: form  // Send the form data, including empID, to the PHP script
    }).then(res => res.text())
      .then(res => {
        if (res.trim() === "success") {
            alert("Loan added successfully!");
            closeAddLoanModal();
            location.reload();  // Refresh page to show the new loan
        } else {
            alert("Failed to add loan.");
        }
    });
});


</script>


</body>
</html>
