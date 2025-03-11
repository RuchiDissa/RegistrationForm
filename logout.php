<?php
session_start();
session_destroy(); // Destroy all session data
header("Location: auth.php"); // Redirect to the login/signup page
exit();
?>
