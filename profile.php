<?php
include "config.php";
requireLogin();

$uid = (int)$_SESSION["user_id"];
$error = "";
$success = "";

$userResult = $conn->query("SELECT * FROM users WHERE id=$uid");
$user = $userResult->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);

    if (empty($name) || empty($email)) {

        $error = "Name and email are required";

    } else {

        $nameSql = $conn->real_escape_string($name);
        $emailSql = $conn->real_escape_string($email);

        $sql = "UPDATE users
                SET name='$nameSql', email='$emailSql'
                WHERE id=$uid";

        if ($conn->query($sql)) {

            if (!empty($_FILES["profile_picture"]["name"])) {

                $picture = uploadProfilePicture($_FILES["profile_picture"]);

                if ($picture != "") {

                    $pictureSql = $conn->real_escape_string($picture);

                    $conn->query(
                        "UPDATE users SET profile_picture='$pictureSql'
                         WHERE id=$uid"
                    );
                } else {
                    $error = "Profile picture could not be uploaded";
                }
            }

            if (!empty($_POST["current_password"]) &&
                !empty($_POST["new_password"])) {

                $current = $conn->real_escape_string($_POST["current_password"]);
                $new = $conn->real_escape_string($_POST["new_password"]);

                if ($current == $user["password"]) {

                    $conn->query(
                        "UPDATE users SET password='$new' WHERE id=$uid"
                    );

                } else {

                    $error = "Current password is wrong";
                }
            }

            if ($error == "") {
                $_SESSION["name"] = $name;
                $success = "Profile updated successfully";
            }
        }
    }

    $userResult = $conn->query("SELECT * FROM users WHERE id=$uid");
    $user = $userResult->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "header.php"; ?>

<div class="container">

<h2>My Profile</h2>

<p class="success"><?php echo e($success); ?></p>
<p class="error"><?php echo e($error); ?></p>

<div class="box">

<?php if (!empty($user["profile_picture"])): ?>

    <img src="uploads/profiles/<?php echo e($user["profile_picture"]); ?>"
         style="width:120px;height:120px;object-fit:cover;">

<?php endif; ?>

<form method="post" enctype="multipart/form-data"
      onsubmit="return checkForm()">

    Name:
    <input type="text" name="name" required
           value="<?php echo e($user["name"]); ?>">

    Email:
    <input type="email" name="email" required
           value="<?php echo e($user["email"]); ?>">

    Profile Picture:
    <input type="file" name="profile_picture"
           accept="image/*">

    <h3>Change Password</h3>

    Current Password:
    <input type="password" name="current_password">

    New Password:
    <input type="password" name="new_password">

    <input type="submit" value="Save Changes">

</form>

</div>

</div>

<script src="script.js"></script>
</body>
</html>