<?php
// Connect to database
include('database.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Departments</title>
    <link rel="stylesheet" href="css/departments.css">
    <!-- Optional: Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

    <!-- Back to Dashboard Button -->
    <a href="dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>

    <h1>Departments</h1>

    <!-- Department Buttons -->
    <div class="dept-buttons">
        <button onclick="loadDepartment('Production')">PRODUCTION</button>
        <button onclick="loadDepartment('Marketing')">MARKETING</button>
        <button onclick="loadDepartment('Finance')">FINANCE</button>
    </div>

    <!-- Search bar -->
    <div class="controls-row">
    <!-- Search Bar Container -->
    <div class="search-container">
        <input type="text" class="search-bar" id="searchInput" placeholder="Search..." onkeyup="filterTable()">
        <i class="fas fa-search search-icon"></i> <!-- Search Icon -->
    </div>

    <!-- Sort Button Container -->
    <select id="sortSelect" class="sort-dropdown" onchange="sortTable()">
    <option value="FullName">Name</option>
    <option value="EmpID">ID</option>
    <option value="Position">Position</option>
    <option value="Address">Address</option>
    <option value="Age">Age</option>
</select>

    </div>
</div>

    <!-- Data Grid -->
    <div class="data-grid">
        <table id="employeeTable">
        <thead>
    <tr>
        <th>ID</th> <!-- 👈 Added this -->
        <th>Name</th>
        <th>Age</th>
        <th>Position</th>
        <th>Address</th>
        <th>Actions</th>
    </tr>
</thead>

            <tbody id="employeeData">
                <!-- Data loaded via JS -->
            </tbody>
        </table>
    </div>

    <script>
    // Function to load the department data
   // Function to load the department data
function loadDepartment(dept = '') {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", "fetch_departments.php?dept=" + encodeURIComponent(dept), true);
    xhr.onload = function () {
    if (xhr.status === 200) {
        console.log(xhr.responseText); // Debugging output
        const employees = JSON.parse(xhr.responseText);
        const tableBody = document.getElementById("employeeData");
        tableBody.innerHTML = ''; // Clear table before appending new rows

        employees.forEach(emp => {
            tableBody.innerHTML += `
               <tr id="emp-${emp.EmpID}">
                            <td><span class="empid">${emp.EmpID}</span></td> <!-- 👈 NEW COLUMN -->

                    <td><span class="name">${emp.FullName}</span><input type="text" class="edit-name" value="${emp.FullName}" style="display:none;"></td>
                    <td><span class="age">${emp.Age}</span><input type="number" class="edit-age" value="${emp.Age}" style="display:none;"></td>
                    <td><span class="position">${emp.Position}</span><input type="text" class="edit-position" value="${emp.Position}" style="display:none;"></td>
                    <td><span class="address">${emp.Address}</span><input type="text" class="edit-address" value="${emp.Address}" style="display:none;"></td>
                    <td>
                        <button class="action-btn edit-btn"><i class="fas fa-edit"></i></button>
                        <button class="action-btn delete-btn" data-id="${emp.EmpID}"><i class="fas fa-trash-alt"></i></button>

                        <button class="action-btn save-btn" style="display:none;">Save</button>
                    </td>
                </tr>
                
            `;sortTable(); // Auto-sort after loading

        });

        bindEventListeners();
    }
};

    xhr.send();
}

// Function to bind event listeners dynamically to the new buttons
function bindEventListeners() {
    const editButtons = document.querySelectorAll('.edit-btn');
    const deleteButtons = document.querySelectorAll('.delete-btn');
    const saveButtons = document.querySelectorAll('.save-btn');

    // Bind the 'editRow' function to all edit buttons
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const rowId = this.closest('tr').id.replace('emp-', '');
            editRow(rowId);
        });
    });

    // Bind the 'deleteRow' function to all delete buttons
    deleteButtons.forEach(button => {
    button.addEventListener('click', function() {
        const empID = this.getAttribute('data-id');
        console.log("Clicked delete for ID:", empID); // ✅ Keep for debugging
        deleteRow(empID);
    });
});

    // Bind the 'saveRow' function to all save buttons
    saveButtons.forEach(button => {
        button.addEventListener('click', function() {
            const rowId = this.closest('tr').id.replace('emp-', '');
            saveRow(rowId);
        });
    });
}

// Edit the selected row
function editRow(id) {
    const row = document.getElementById(`emp-${id}`);
    row.querySelector('.name').style.display = 'none';
    row.querySelector('.edit-name').style.display = 'inline-block';

    row.querySelector('.age').style.display = 'none';
    row.querySelector('.edit-age').style.display = 'inline-block';

    row.querySelector('.position').style.display = 'none';
    row.querySelector('.edit-position').style.display = 'inline-block';

    row.querySelector('.address').style.display = 'none';
    row.querySelector('.edit-address').style.display = 'inline-block';

    row.querySelector('.edit-btn').style.display = 'none';
    row.querySelector('.save-btn').style.display = 'inline-block';
}

// Save the edited row data
function saveRow(id) {
    const row = document.getElementById(`emp-${id}`);
    const fullName = row.querySelector('.edit-name').value.trim();
    const age = row.querySelector('.edit-age').value;
    const position = row.querySelector('.edit-position').value;
    const fullAddress = row.querySelector('.edit-address').value.trim();

    // Assume name format is: First MI. Last
    const nameParts = fullName.split(' ');
    const FN = nameParts[0] || '';
    const MI = (nameParts.length === 3 && nameParts[1].includes('.')) ? nameParts[1].replace('.', '') : '';
    const LN = nameParts[nameParts.length - 1] || '';

    // Assume address format is: Brgy, Municipality, Province, Region
    const addressParts = fullAddress.split(',').map(part => part.trim());
    const Brgy = addressParts[0] || '';
    const Municipality = addressParts[1] || '';
    const Province = addressParts[2] || '';
    const Region = addressParts[3] || '';

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "update_employee.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function () {
        if (xhr.status === 200) {
            row.querySelector('.name').textContent = fullName;
            row.querySelector('.age').textContent = age;
            row.querySelector('.position').textContent = position;
            row.querySelector('.address').textContent = fullAddress;

            row.querySelector('.name').style.display = 'inline-block';
            row.querySelector('.edit-name').style.display = 'none';

            row.querySelector('.age').style.display = 'inline-block';
            row.querySelector('.edit-age').style.display = 'none';

            row.querySelector('.position').style.display = 'inline-block';
            row.querySelector('.edit-position').style.display = 'none';

            row.querySelector('.address').style.display = 'inline-block';
            row.querySelector('.edit-address').style.display = 'none';

            row.querySelector('.edit-btn').style.display = 'inline-block';
            row.querySelector('.save-btn').style.display = 'none';
        }
    };

    xhr.send(`id=${id}&FN=${FN}&MI=${MI}&LN=${LN}&age=${age}&position=${position}&Brgy=${Brgy}&Municipality=${Municipality}&Province=${Province}&Region=${Region}`);
}
</script>
<!-- Modal for confirmation -->
<div id="confirmationModal" class="modal" style="display: none;">
    <div class="modal-content">
        <p>Are you sure you want to delete this employee?</p>
        <button id="confirmDelete" class="action-btn">Yes</button>
        <button id="cancelDelete" class="action-btn">Cancel</button>
    </div>
</div>

<script>function deleteRow(empID) {
    // Show the confirmation modal
    document.getElementById('confirmationModal').style.display = 'block'; // Show modal

    const confirmButton = document.getElementById('confirmDelete');
    const cancelButton = document.getElementById('cancelDelete');

    confirmButton.onclick = function() {
        console.log("Deleting Employee ID:", empID); // Debugging log

        // Make the AJAX request to delete the employee
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "delete_employee.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onload = function () {
            if (xhr.status === 200) {
                console.log(xhr.responseText); // Debugging output
                const response = xhr.responseText.trim();
                if (response === "Employee deleted successfully!") {
                    const row = document.getElementById(`emp-${empID}`);
                    row.remove(); // Remove row from table
                    alert("Employee deleted successfully!");
                } else {
                    alert(response); // Show error message from backend
                }
            } else {
                alert("Failed to delete employee.");
            }
            // Hide the modal after the action (successful or failed)
            document.getElementById('confirmationModal').style.display = 'none';
        };

        xhr.send("id=" + encodeURIComponent(empID));
    };

    cancelButton.onclick = function() {
        // Close the modal when "Cancel" button is clicked
        document.getElementById('confirmationModal').style.display = 'none';
    };
}


</script>

<script>


function filterTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toUpperCase();
    const rows = document.querySelectorAll('#employeeTable tbody tr');

    rows.forEach(row => {
        const text = row.textContent.toUpperCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
}
function sortTable() {
    const table = document.getElementById("employeeTable").getElementsByTagName("tbody")[0];
    const rows = Array.from(table.getElementsByTagName("tr"));
    const sortBy = document.getElementById("sortSelect").value;

    rows.sort((a, b) => {
        let aText, bText;

        switch (sortBy) {
            case "FullName":
                aText = a.querySelector('.name').textContent.trim().toUpperCase();
                bText = b.querySelector('.name').textContent.trim().toUpperCase();
                break;
            case "EmpID":
                aText = parseInt(a.id.replace("emp-", ""));
                bText = parseInt(b.id.replace("emp-", ""));
                break;
            case "Position":
                aText = a.querySelector('.position').textContent.trim().toUpperCase();
                bText = b.querySelector('.position').textContent.trim().toUpperCase();
                break;
            case "Address":
                aText = a.querySelector('.address').textContent.trim().toUpperCase();
                bText = b.querySelector('.address').textContent.trim().toUpperCase();
                break;
            case "Age":
                aText = parseInt(a.querySelector('.age').textContent.trim());
                bText = parseInt(b.querySelector('.age').textContent.trim());
                break;
            default:
                return 0;
        }

        if (aText < bText) return -1;
        if (aText > bText) return 1;
        return 0;
    });

    // Re-append sorted rows to the table
    rows.forEach(row => table.appendChild(row));
}
// Load all employees by default on page load
window.onload = function () {
    loadDepartment('');
};

    </script>

</body>
</html>
