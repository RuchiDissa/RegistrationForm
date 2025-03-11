<!DOCTYPE HTML>
<html>
<head>
    <title>Login & Signup</title>
</head>
<body>
    <h2>User Authentication</h2>

    <!-- Signup Form -->
    <h3>Signup</h3>
    <form method="POST" action="">
        <label for="signup_username">Username:</label>
        <input type="text" id="signup_username" name="signup_username" required><br><br>
        <label for="signup_password">Password:</label>
        <input type="password" id="signup_password" name="signup_password" required><br><br>
        <button type="submit" name="signup">Signup</button>
    </form>

    <hr>

    <!-- Login Form -->
    <h3>Login</h3>
    <form method="POST" action="">
        <label for="login_username">Username:</label>
        <input type="text" id="login_username" name="login_username" required><br><br>
        <label for="login_password">Password:</label>
        <input type="password" id="login_password" name="login_password" required><br><br>
        <button type="submit" name="login">Login</button>
    </form>

<?php
session_start(); // Start the session

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login_system";

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Signup
if (isset($_POST['signup'])) {
    $signupUsername = $_POST['signup_username'];
    $signupPassword = $_POST['signup_password'];

    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $signupUsername);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<p style='color: red;'>Username already exists. Please choose another one.</p>";
    } else {
        $hashedPassword = password_hash($signupPassword, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $signupUsername, $hashedPassword);

        if ($stmt->execute()) {
            echo "<p style='color: green;'>Signup successful! You can now log in.</p>";
        } else {
            echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
        }
    }

    $stmt->close();
}

// Handle Login
if (isset($_POST['login'])) {
    $loginUsername = $_POST['login_username'];
    $loginPassword = $_POST['login_password'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $loginUsername);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($loginPassword, $row['password'])) {
            $_SESSION['username'] = $loginUsername; // Store username in session
            header("Location: welcome.php"); // Redirect to the welcome page
            exit();
        } else {
            echo "<p style='color: red;'>Invalid password.</p>";
        }
    } else {
        echo "<p style='color: red;'>Invalid username.</p>";
    }

    $stmt->close();
}

$conn->close();
?>

</body>
</html>
