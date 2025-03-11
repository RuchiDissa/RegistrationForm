<?php
$connection = new mysqli("localhost", "root", "", "phptest");

if (mysqli_connect_errno()) {
    die("Connection error: " . mysqli_connect_errno());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update'])) {
		$id = mysqli_real_escape_string($connection,$_SESSION['user_id']);
        $username = mysqli_real_escape_string($connection, $_POST['username']);
        $name = mysqli_real_escape_string($connection, $_POST['name']);
        $email = mysqli_real_escape_string($connection, $_POST['email']);
        $password = mysqli_real_escape_string($connection, $_POST['password']);
        $gender = mysqli_real_escape_string($connection, $_POST['gender']);
        $description = mysqli_real_escape_string($connection, $_POST['description']);
        $role = mysqli_real_escape_string($connection, $_POST['role']);
        
        // Handle file upload
        $profilePicture = null;
        if (!empty($_FILES['file']['name'])) {
            $targetDir = "uploads/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true); // Create the directory if it doesn't exist
            }
            $profilePicture = $targetDir . basename($_FILES["file"]["name"]);
            if (!move_uploaded_file($_FILES["file"]["tmp_name"], $profilePicture)) {
                die("Error uploading file.");
            }
        }

        // Hash password
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);

        // Update query with a WHERE clause to update only the current user's data
        $updatesql = "UPDATE users SET username='$username', name='$name', email='$email', password='$password_hashed', gender='$gender', description='$description', role='$role', profile_picture='$profilePicture' WHERE id ='$id'";
        
        $result = mysqli_query($connection, $updatesql);

        // Check if the update was successful
        if ($result) {
            echo "<p>Account updated successfully.</p>";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            printf("Error updating account: %s", mysqli_error($connection));
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Account</title>
</head>
<body>
    <h1>Fill in all the Information</h1>

    <!-- Update Account Form -->
    <form method="POST" enctype="multipart/form-data" id="update-form" action="new_prac.php">
        <fieldset>
            <legend>Upload Picture</legend>
            <input type="file" name="file" required><br><br>
        </fieldset>

        <fieldset>
            <legend>User Information</legend>
            <label for="username">Username: </label>
            <input type="text" id="username" name="username" required><br><br>

            <label for="name">Name: </label>
            <input type="text" id="name" name="name" required><br><br>

            <label for="email">E-mail: </label>
            <input type="email" id="email" name="email" required><br><br>

            <label for="password">Password: </label>
            <input type="password" id="password" name="password" required><br><br>
        </fieldset>

        <fieldset>
            <legend>Gender</legend>
            <label for="gender_male">Male:</label>
            <input type="radio" id="gender_male" name="gender" value="Male" required><br><br>

            <label for="gender_female">Female:</label>
            <input type="radio" id="gender_female" name="gender" value="Female" required><br><br>

            <label for="gender_other">Others:</label>
            <input type="radio" id="gender_other" name="gender" value="Others" required><br><br>
        </fieldset>

        <fieldset>
            <legend>Description</legend>
            <textarea name="description" rows="4" cols="50"></textarea><br><br>
        </fieldset>

        <fieldset>
            <legend>Role</legend>
            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="Admin">Admin</option>
                <option value="Employee">Employee</option>
                <option value="Customer">Customer</option>
            </select><br><br>
        </fieldset>

        <button type="submit" name="update">Update</button>
    </form>

    <!-- Separate Login Form -->
    <form action="profile.php" method="GET">
        <fieldset>
            <legend>If you don't want to update account</legend><br>
            <button type="submit" name="profile">Profile</button>
        </fieldset>
    </form>
</body>
</html>
