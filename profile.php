<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: login.php");
    exit();
}

// Check if the cookie for session time exists, if not, set it
if (!isset($_COOKIE['session_start_time'])) {
    // Set the cookie with current time for 5 minutes (300 seconds)
    setcookie('session_start_time', time(), time() + 300, "/");
}

// Check if the session time (cookie) has expired (5 minutes)
if (isset($_COOKIE['session_start_time']) && (time() - $_COOKIE['session_start_time']) > 300) {
    // Session has expired, destroy session and logout
    session_unset();
    session_destroy();
    setcookie('session_start_time', '', time() - 3600, '/');  // Expire the cookie
    header("Location: login.php");
    exit();
}

// Database connection
$connection = new mysqli("localhost", "root", "", "phptest");

if (mysqli_connect_errno()) {
    die("Connection error: " . mysqli_connect_errno());
}

$user_id = $_SESSION['user_id'];  // Assuming the user ID is stored in session

// Query to fetch user details
$query = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($connection, $query);
$user = mysqli_fetch_assoc($result);

if ($user) {
    // Display user profile information
    echo "<h2>User Profile</h2>";
    echo "<p><strong>Username:</strong> " . htmlspecialchars($user['username']) . "</p>";
    echo "<p><strong>Name:</strong> " . htmlspecialchars($user['name']) . "</p>";
    echo "<p><strong>Email:</strong> " . htmlspecialchars($user['email']) . "</p>";
    echo "<p><strong>Gender:</strong> " . htmlspecialchars($user['gender']) . "</p>";
    echo "<p><strong>Description:</strong> " . htmlspecialchars($user['description']) . "</p>";
    echo "<p><strong>Role:</strong> " . htmlspecialchars($user['role']) . "</p>";

    // Display profile picture if exists
    if ($user['profile_picture']) {
        echo "<p><strong>Profile Picture:</strong><br>";
        echo "<img src='uploads/" . htmlspecialchars($user['profile_picture']) . "' alt='Profile Picture' width='150'><br></p>";
    }
} else {
    echo "User not found!";
}
?>
<!DOCTYPE html>
<html>
<body>

<form action="update.php" method="GET">
<fieldset>
<legend>Update Account Details</legend><br>
<button type="submit" name="update">Update Account</button>
</fieldset><br>
</form>
<form action="delete.php" method="GET">
<fieldset>
<legend>Delete Account</legend><br>
<button type="submit" name="delete">Delete Account</button>
</form>
</body>
</html>