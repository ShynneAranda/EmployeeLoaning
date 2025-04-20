// Function to update time and date
function updateTime() {
    const timeElement = document.getElementById('time');
    const now = new Date();
    
    // Format the time (hh:mm:ss)
    const timeString = now.toLocaleTimeString();
    
    // Format the date
    const dateString = now.toLocaleDateString('en-US', {
        month: 'short',
        day: '2-digit',
        year: 'numeric'
    });

    timeElement.textContent = `${timeString} | ${dateString}`;
}

// Function to update battery status
function updateBatteryStatus() {
    navigator.getBattery().then(function(battery) {
        const batteryElement = document.getElementById('battery');
        const batteryLevel = (battery.level * 100).toFixed(0);  // Battery level as a percentage
        const chargingStatus = battery.charging ? "Charging" : "Not Charging";
        
        batteryElement.innerHTML = `<i class="fas fa-battery-${getBatteryIconLevel(batteryLevel)}"></i> ${batteryLevel}%`;
    });
}

// Function to get the correct battery icon based on the level
function getBatteryIconLevel(level) {
    if (level >= 75) return 'full';
    if (level >= 50) return 'three-quarters';
    if (level >= 25) return 'half';
    if (level >= 10) return 'quarter';
    return 'empty';
}

// Update time every second
setInterval(updateTime, 1000);

// Update battery status every 30 seconds (to ensure real-time updates)
setInterval(updateBatteryStatus, 30000); // Update more frequently for better user experience

document.getElementById("sortSelect").addEventListener("change", function () {
    const selectedValue = this.value;
    const table = document.getElementById("employeeTable");
    const rows = Array.from(table.querySelectorAll("tbody tr"));
    let columnIndex;

    switch (selectedValue) {
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
        const aText = a.cells[columnIndex].textContent.trim();
        const bText = b.cells[columnIndex].textContent.trim();

        if (!isNaN(aText) && !isNaN(bText)) {
            return Number(aText) - Number(bText);
        } else {
            return aText.localeCompare(bText);
        }
    });

    const tbody = table.querySelector("tbody");
    tbody.innerHTML = "";
    rows.forEach(row => tbody.appendChild(row));
});
