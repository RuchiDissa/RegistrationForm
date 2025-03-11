<?php
// Establish database connection
$connection = new mysqli("localhost", "root", "", "phptest");
if (mysqli_connect_errno()) {
    die("Connection error: " . mysqli_connect_error());
}
// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['submit'])) {
        // Sanitize input data
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

        // Insert data into the database
        $subsql = "
            INSERT INTO users (username, name, email, password, gender, description, role, profile_picture)
            VALUES ('$username', '$name', '$email', '$password_hashed', '$gender', '$description', '$role', '$profilePicture')
        ";
        $result = mysqli_query($connection, $subsql);

        // Check if the insertion was successful
        if ($result === TRUE) {
            // Redirect to the same page after successful submission
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            printf("Error adding user: %s", mysqli_error($connection));
        }
    }
}

$s_sql = "SELECT * FROM users";
$data = mysqli_query($connection,$s_sql);

if(mysqli_num_rows($data)>0){
	echo "<h2> User Information</h2>";
	echo "<ul>";
	
	while($users= mysqli_fetch_array($data,MYSQLI_ASSOC)){
		echo "<li>ID : ".htmlspecialchars($users['id']).",";
		echo "Username : ".htmlspecialchars($users['username']).",";
		echo "Name : ".htmlspecialchars($users['name']).",";
		echo "Email : ".htmlspecialchars($users['email']).",";
		echo "Gender : ".htmlspecialchars($users['gender'])."</li>";
	}
	echo "</ul>";
	
	mysqli_data_seek($data, 0);
	
	$roleCount = 0;
	$role = [];
	
	while($users = mysqli_fetch_array($data,MYSQLI_ASSOC)){
		if(!empty($users['role'])){
			$role[] = htmlspecialchars($users['role']);
			$roleCount++;
		}
	}
	if($roleCount > 0)
	{
		echo "<table border = 1>";
		echo "<thead>";
		echo "<th>roles</th>";
		echo "</thead>";
		echo "<tbody>";
		
		foreach($role as $role)
		{
			echo "<tr><td>$role</td></td>";
		}
		echo "</tbody>";
		echo "</table>";
		echo "<p><strong>Total roles :</strong>$roleCount</p>";
	}else 
	{
		echo "<p><strong>No roles selected</strong></p>";
	}
}else {
	echo "No user found";
}


// Close database connection
mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
</head>
<body>
    <h1>Fill in all the Information</h1>

    <!-- Registration Form -->
    <form method="POST" enctype="multipart/form-data" id="register-form" action="new_prac.php">
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

        <button type="submit" name="submit">Register</button>
    </form>

    <!-- Separate Login Form -->
    <form action="userlogin.php" method="GET">
        <fieldset>
            <legend>If you have an account</legend><br>
            <button type="submit" name="login">Login</button>
        </fieldset>
    </form>
</body>
</html>

