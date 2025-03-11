<?php
$connect = new mysqli("localhost","root","","prac");
if(mysqli_connect_errno()){
	die("connection error : ".mysqli_connect_errno());
}

if($_SERVER['REQUEST_METHOD']=='post')
{
	if(isset($_POST['submit'])){
		$username = mysqli_real_escape_string($connect,$_POST['username']);
		$surname = mysqli_real_escape_string($connect,$_POST['surname']);
		$password = mysqli_real_escape_string($connect,$_POST['password']);
		$role = mysqli_real_escape_string($connect,$_POST['role']);
		
		$P_hash = password_hash($password,PASSWORD_DEFAULT);
		
		$sql = "INSERT INTO users (username,surname,password,role)
				VALUES('$username','$surname','$p_hash','$role')";
		$data = mysqli_data($connect,$sql);
		if($data === TRUE){
			echo "<p>Signup successful!</p>";
		}else{
			printf("Error : %s",mysqli_error($connect));
		}
	}
}

mysqli_close($connect);
?>

<!DOCTYPE html>
<html>
<head>User login</head>
<body>
	<form id="userinput" method="POST">
    <fieldset>
        <legend>Fill the form</legend>
        <label>Username</label><br>
        <input type="text" id="username" name="username" required><br>
        <label>Surname</label><br>
        <input type="text" id="surname" name="surname" required><br>
        <label>Password</label><br>
        <input type="password" id="password" name="password" required><br>
        <label>Role</label><br>
        <select id="role" name="role">
            <option value="Admin">Admin</option>
            <option value="Employee">Employee</option>
            <option value="Customer">Customer</option>
        </select><br>
    </fieldset>
    <fieldset>
        <legend>Submit Information</legend>
        <button type="submit" name="submit">Submit</button>
    </fieldset>
</form>
</body>
</html>