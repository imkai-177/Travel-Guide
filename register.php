<?php
include "config.php";

$name = "";
$email = "";
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $role = $_POST["role"];

    if (empty($name) || empty($email) || empty($password)) {

        $error = "Please fill the form";

    } else {

        if ($role == "admin") {
            $role = "user";
        }

        $name = $conn->real_escape_string($name);
        $email = $conn->real_escape_string($email);
        $password = $conn->real_escape_string($password);

        $check = "SELECT * FROM users WHERE email='$email'";
        $result = $conn->query($check);

        if ($result->num_rows > 0) {

            $error = "Email already exists";

        } else {

            $sql = "INSERT INTO users(name,email,password,role,is_verified)
                    VALUES('$name','$email','$password','$role',0)";

            if ($conn->query($sql) == TRUE) {
                $success = "Registration is complete";
            } else {
                $error = "Error: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "header.php"; ?>

<div class="container">

<h2>Register</h2>

<form method="post" action="" onsubmit="return checkForm()">

    Name:
    <input type="text" name="name" required
           value="<?php echo e($name); ?>">

    Email:
    <input type="email" name="email" required
           value="<?php echo e($email); ?>">

    Password:
    <input type="password" name="password" required>

    User Type:
    <select name="role">
        <option value="user">General User</option>
        <option value="scout">Scout</option>
    </select>

    <input type="submit" value="Register">

</form>

<p class="success"><?php echo e($success); ?></p>
<p class="error"><?php echo e($error); ?></p>

</div>

<script src="script.js"></script>
</body>
</html>