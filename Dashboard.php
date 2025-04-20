<?php
// Connect to database
include('database.php'); // Adjust the path if needed
$conn = new mysqli("localhost", "root", "", "employee_management_system");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL Query to fetch required data
$sql = "SELECT 
            EmpID,
            LN,
            FN,
            MI,
            EX,
            Age,
            DeptCode,
            Position,
            Region,
            Province,
            Municipality,
            Brgy
        FROM employeeinfo
        ORDER BY EmpID ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Employee List</title>
    <link rel="stylesheet" href="css/styles.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="header">
    <div class="header-right">
        <div class="battery" id="battery">
            <i class="fas fa-battery-half"></i> 75% <!-- This part is back! -->
        </div>
        <div class="time" id="time"></div> <!-- Time display -->
    </div>

<script>
// Function to update time and date in short format
function updateTime() {
    const timeElement = document.getElementById('time');
    const now = new Date();
    
    // Format the time (hh:mm:ss)
    const timeString = now.toLocaleTimeString();
    
    // Format the date (e.g., Apr 18, 2025)
    const dateString = now.toLocaleDateString('en-US', {
        month: 'short',
        day: '2-digit',
        year: 'numeric'
    });

    // Display both time and date
    timeElement.textContent = `${timeString} | ${dateString}`;
}

// Update time every second
setInterval(updateTime, 1000);

// Call once initially to set time immediately
updateTime();
</script>

</div>
<div class="page-layout">
    <div class="left-section">
        <div class="top-bar">
            <h1 class="title2">Employee Loaning System</h1>
            <div class="controls-row">
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="searchInput" placeholder="Search ID, Name, Dept Code, Position, and Address..." class="search-bar">
                </div>

                <!-- Sort Button Container -->
                <div class="sort-container">
                    <select class="sort-dropdown" id="sortSelect">
                        <option disabled selected>Sort</option>
                        <option value="EmpID">ID</option>
                        <option value="LN">Name</option>
                        <option value="DeptCode">Department Code</option>
                        <option value="Position">Position</option>
                        <option value="Address">Address</option>
                    </select>
                </div>
                <button class="add-btn" onclick="openAddEmployeeModal()">+ Add</button>
                <!-- Add Employee Modal -->
                <div id="addEmployeeModal" class="modal">
                    <div class="modal-content">
                        <span class="close-btn" onclick="closeAddEmployeeModal()">&times;</span>
                        <h2>Add Employee</h2>
                        <form action="add_employee.php" method="POST">
    <label for="empID">Employee ID</label>
    <input type="text" id="empID" name="EmpID" required>

    <label for="ln">Last Name</label>
    <input type="text" id="ln" name="LN" required>

    <label for="fn">First Name</label>
    <input type="text" id="fn" name="FN" required>

    <label for="mi">Middle Initial</label>
    <input type="text" id="mi" name="MI">

    <label for="ex">Extension</label>
    <input type="text" id="ex" name="EX">

    <label for="age">Age</label>
    <input type="number" id="age" name="Age" required>

    <label for="deptCode">Department Code</label>
    <select id="deptCode" name="DeptCode" required>
        <option value="FINA">Finance</option>
        <option value="PROD">Production</option>
        <option value="MARK">Marketing</option>
    </select>

    <label for="position">Position</label>
    <input type="text" id="position" name="Position">

    <label for="region">Region</label>
    <input type="text" id="region" name="Region">

    <label for="province">Province</label>
    <input type="text" id="province" name="Province">

    <label for="municipality">Municipality</label>
    <input type="text" id="municipality" name="Municipality">

    <label for="brgy">Barangay</label>
    <input type="text" id="brgy" name="Brgy">

    <button type="submit">Add Employee</button>
</form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FLEX CONTAINER: table on left, overview on right -->
    <div class="content-area">
        <!-- Left column for table -->
        <div class="left-column">
            <div class="datagrid-box">
                <table class="datagrid" id="employeeTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Age</th>
                            <th>Dept Code</th>
                            <th>Position</th>
                            <th>Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row["EmpID"] ?></td>
                                    <td>
                                        <?= $row["LN"] . ", " . $row["FN"] . " " . 
                                            ($row["MI"] ? $row["MI"] . "." : "") . 
                                            ($row["EX"] ? ", " . $row["EX"] : "") ?>
                                    </td>
                                    <td><?= $row["Age"] ?></td>
                                    <td><?= $row["DeptCode"] ?></td>
                                    <td><?= $row["Position"] ?></td>
                                    <td>
                                        <?= $row["Brgy"] . ", " . $row["Municipality"] . ", " . 
                                            $row["Province"] . ", " . $row["Region"] ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6">No employee data found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right column for overview box -->
        <div class="right-column">
            <div class="overview-box">
                <div class="widget employee-count">
                    <i class="fas fa-users icon"></i>
                    <h3 class="total">Total <br>Employees</h3>
                    <?php
                    // Include the database connection file
                    include('database.php');

                    // SQL query to count the total number of employees
                    $sql = "SELECT COUNT(*) AS total_employees FROM employeeinfo";
                    $result = $conn->query($sql);

                    // Fetch the result
                    $row = $result->fetch_assoc();

                    // Output the total number of employees
                    echo $row['total_employees'];

                    // Close the database connection
                    $conn->close();
                    ?>
                </div>

                <div class="widget donut-chart">
                    <canvas id="deptChart" width="200" height="200"></canvas>
                    <div class="chart-legend" id="deptLegend"></div>
                </div>

                <div class="widget active-loans" onclick="window.location.href='loans.php'">
                    <i class="fas fa-hand-holding-usd icon"></i>
                    <div>
                        <div class="number" id="activeLoanCount">0</div>
                        <div class="label">Active Loans <i class="fas fa-angle-right more-icon"></i></div>
                    </div>
                </div>

                <button class="dept-btn" onclick="window.location.href='departments.php'">View Departments</button>
            </div>
        </div>
    </div>
</div>

<script>
function fetchActiveLoans() {
    fetch('get_active_loans.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('activeLoanCount').textContent = data;
        })
        .catch(err => {
            console.error("Failed to fetch active loans:", err);
        });
}

// Initial fetch on load
fetchActiveLoans();

// Optional: Refresh every 30 seconds
setInterval(fetchActiveLoans, 30000);
</script>

<script>
// Function to open the Add Employee Modal
function openAddEmployeeModal() {
    document.getElementById("addEmployeeModal").style.display = "block";
}

// Function to close the Add Employee Modal
function closeAddEmployeeModal() {
    document.getElementById("addEmployeeModal").style.display = "none";
}

// Close modal if clicked outside
window.onclick = function(event) {
    if (event.target == document.getElementById("addEmployeeModal")) {
        closeAddEmployeeModal();
    }
}
</script>

<script>
let deptChart; // global chart instance

function fetchDonutChartData() {
    fetch('chart_data.php')
        .then(response => response.json())
        .then(data => {
            const labels = data.map(item => item.Department); // <-- changed from DeptCode to Department
            const counts = data.map(item => item.count);
            const colors = ['#D00F0F', '#F0552B', '#4C77E4', '#8BC34A', '#FF9800']; // Add more if needed

            // Initialize or update the chart
            if (deptChart) {
                deptChart.data.labels = labels;
                deptChart.data.datasets[0].data = counts;
                deptChart.update();
            } else {
                const ctx = document.getElementById('deptChart').getContext('2d');
                deptChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: counts,
                            backgroundColor: colors.slice(0, labels.length),
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: false,
                        plugins: {
                            legend: { display: false }
                        },
                        cutout: '65%'
                    }
                });
            }

            // Rebuild legend
            const legendContainer = document.getElementById('deptLegend');
            legendContainer.innerHTML = ''; // Clear existing legend
            labels.forEach((label, index) => {
                const legendItem = document.createElement('div');
                legendItem.innerHTML = `
                    <span class="legend-color" style="background:${colors[index]}"></span>
                    ${label}: ${counts[index]} employees
                `;
                legendContainer.appendChild(legendItem);
            });
        })
        .catch(error => console.error('Error fetching chart data:', error));
}


// Initial load
fetchDonutChartData();

// Update every 5 seconds
setInterval(fetchDonutChartData, 5000);

</script>

<script>
document.getElementById("searchInput").addEventListener("keyup", function () {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll("#employeeTable tbody tr");

    rows.forEach(row => {
        const cells = row.querySelectorAll("td");
        const rowText = Array.from(cells).map(cell => cell.textContent.toLowerCase()).join(" ");

        if (rowText.includes(filter)) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
});
</script>
<script>
document.getElementById("sortSelect").addEventListener("change", function () {
    const value = this.value;
    const table = document.getElementById("employeeTable");
    const rows = Array.from(table.querySelectorAll("tbody tr"));

    // Determine the column index based on selected sort value
    let columnIndex = 0;
    switch (value) {
        case "EmpID":
            columnIndex = 0;
            break;
        case "LN":
            columnIndex = 1;
            break;
        case "DeptCode":
            columnIndex = 3;
            break;
        case "Position":
            columnIndex = 4;
            break;
        case "Address":
            columnIndex = 5;
            break;
    }

    rows.sort((a, b) => {
        const cellA = a.cells[columnIndex].textContent.trim().toLowerCase();
        const cellB = b.cells[columnIndex].textContent.trim().toLowerCase();
        return cellA.localeCompare(cellB);
    });

    rows.forEach(row => table.appendChild(row));
});
</script>

</body>
</html>
