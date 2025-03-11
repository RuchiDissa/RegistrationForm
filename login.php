<?php
session_start();  // Start session for session management

$connection = new mysqli("localhost", "root", "", "phpdb");

if (mysqli_connect_errno()) {
    die("Connection error: " . mysqli_connect_errno());
}

// Login functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    // Get data from login form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Sanitize user inputs
    $username = mysqli_real_escape_string($connection, $username);
    $password = mysqli_real_escape_string($connection, $password);

    // Query the database to get user information
    $sql = "SELECT * FROM users WHERE username='$username' OR email='$username'";
    $result = mysqli_query($connection, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Check if password matches
        if (password_verify($password, $user['password'])) {
            // Store user session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header("Location: dashboard.php");  // Redirect to protected page
            exit();
        } else {
            echo "Invalid username or password.";
        }
    } else {
        echo "Error: " . mysqli_error($connection);
    }
}

// Logout functionality
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");  // Redirect to index page after logout
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>

<?php
// If user is already logged in, show the dashboard link
if (isset($_SESSION['user_id'])) {
    echo "Welcome, " . $_SESSION['username'] . "!";
    echo "<br><a href='index.php?logout=true'>Logout</a>";
} else {
    ?>

    <!-- Login Form -->
    <h2>Login</h2>
    <form method="POST" action="">
        <fieldset>
            <legend>Login to your account</legend>
            <label>Username or Email:</label><br>
            <input type="text" name="username" required><br>
            <label>Password:</label><br>
            <input type="password" name="password" required><br>
        </fieldset>
        <button type="submit" name="login">Login</button>
    </form>

    <?php
}
?>

</body>
</html>
