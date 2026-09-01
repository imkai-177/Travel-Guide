<?php
include "config.php";

$email = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {

        $error = "Please fill the form";

    } else {

        $email = $conn->real_escape_string($email);
        $password = $conn->real_escape_string($password);

        $sql = "SELECT * FROM users
                WHERE email='$email' AND password='$password'";

        $result = $conn->query($sql);

        if ($result->num_rows == 1) {

            $row = $result->fetch_assoc();

            $_SESSION["user_id"] = $row["id"];
            $_SESSION["name"] = $row["name"];
            $_SESSION["role"] = $row["role"];
            $_SESSION["is_verified"] = $row["is_verified"];

            header("Location: dashboard.php");
            exit;

        } else {

            $error = "Wrong email or password";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "header.php"; ?>

<div class="container">

<h2>Login</h2>

<form method="post" action="" onsubmit="return checkForm()">

    Email:
    <input type="email" name="email" required
           value="<?php echo e($email); ?>">

    Password:
    <input type="password" name="password" required>

    <input type="submit" value="Login">

</form>

<p class="error"><?php echo e($error); ?></p>

</div>

<script src="script.js"></script>
</body>
</html>