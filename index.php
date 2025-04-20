<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Loaning System</title>
    <link rel="stylesheet" href="css/css1.css"> <!-- Link to your CSS file -->
    <script src="script.js" defer></script> <!-- Link to your JavaScript file -->
</head>
<body>
    <h1 class="title">Welcome to Employee <br> Loaning System</h1>
    <div class="container">
        <div class="card">
            <h2>Login</h2>
            <form id="login_Form" action="Dashboard.php" method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" href="">Login</button>
            </form>
          

        <script>
            document.getElementById('login_Form').addEventListener('submit', function(event) {
                const username = document.getElementById('username').value;
                const password = document.getElementById('password').value;

                if (username === 'admin' && password === 'admin') {
                    // Login is valid
                } else {
                    alert('Invalid username or password');
                    event.preventDefault(); // Prevent form submission
                }
            });
        </script>
        </div>
</body>
</html>