<?php
include "../config.php";
requireAdmin();

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $action = $_POST["action"];
    $id = (int)($_POST["id"] ?? 0);

    if ($action == "add") {

        $name = $conn->real_escape_string($_POST["name"]);
        $email = $conn->real_escape_string($_POST["email"]);
        $password = $conn->real_escape_string($_POST["password"]);
        $role = $_POST["role"];

        $check = $conn->query(
            "SELECT * FROM users WHERE email='$email'"
        );

        if ($check->num_rows > 0) {

            $error = "Email already exists";

        } else {

            $conn->query(
                "INSERT INTO users(name,email,password,role,is_verified)
                 VALUES('$name','$email','$password','$role',1)"
            );

            $success = "User added successfully";
        }

    } elseif ($action == "verify") {

        $conn->query(
            "UPDATE users SET is_verified = 1-is_verified
             WHERE id=$id"
        );

    } elseif ($action == "role") {

        $role = $conn->real_escape_string($_POST["role"]);

        if ($id != $_SESSION["user_id"]) {

            $conn->query(
                "UPDATE users SET role='$role' WHERE id=$id"
            );
        }

    } elseif ($action == "delete") {

        if ($id != $_SESSION["user_id"]) {

            $conn->query("DELETE FROM wishlist WHERE user_id=$id");
            $conn->query("DELETE FROM comments WHERE user_id=$id");
            $conn->query("DELETE FROM post_requests WHERE scout_id=$id");

            $postResult = $conn->query(
                "SELECT id FROM posts WHERE scout_id=$id"
            );

            while ($p = $postResult->fetch_assoc()) {

                $pid = (int)$p["id"];

                $conn->query("DELETE FROM wishlist WHERE post_id=$pid");
                $conn->query("DELETE FROM comments WHERE post_id=$pid");
                $conn->query("DELETE FROM cost_estimates WHERE post_id=$pid");
                $conn->query("DELETE FROM posts WHERE id=$pid");
            }

            $conn->query("DELETE FROM users WHERE id=$id");
        }
    }
}

$result = $conn->query(
    "SELECT * FROM users ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<?php include "../header.php"; ?>

<div class="container">

<h2>Manage Users</h2>

<p class="success"><?php echo e($success); ?></p>
<p class="error"><?php echo e($error); ?></p>

<div class="box">

<h3>Add User</h3>

<form method="post">

    <input type="hidden" name="action" value="add">

    Name:
    <input type="text" name="name" required>

    Email:
    <input type="email" name="email" required>

    Password:
    <input type="text" name="password" required>

    Role:
    <select name="role">
        <option value="user">User</option>
        <option value="scout">Scout</option>
        <option value="admin">Admin</option>
    </select>

    <input type="submit" value="Add User">

</form>

</div>

<input type="text" id="userSearch"
       placeholder="Search users">

<table id="usersTable">

<tr>
    <th>Name</th>
    <th>Email</th>
    <th>Role</th>
    <th>Verified</th>
    <th>Action</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>

<tr>

    <td><?php echo e($row["name"]); ?></td>
    <td><?php echo e($row["email"]); ?></td>

    <td>

    <form method="post" style="padding:0;margin:0;">

        <input type="hidden" name="action" value="role">
        <input type="hidden" name="id"
               value="<?php echo $row["id"]; ?>">

        <select name="role" onchange="this.form.submit()">

            <option value="user"
                <?php if ($row["role"]=="user") echo "selected"; ?>>
                User
            </option>

            <option value="scout"
                <?php if ($row["role"]=="scout") echo "selected"; ?>>
                Scout
            </option>

            <option value="admin"
                <?php if ($row["role"]=="admin") echo "selected"; ?>>
                Admin
            </option>

        </select>

    </form>

    </td>

    <td>
        <?php echo $row["is_verified"] ? "Yes" : "No"; ?>
    </td>

    <td>

        <form method="post" style="padding:0;margin:3px;">

            <input type="hidden" name="action" value="verify">
            <input type="hidden" name="id"
                   value="<?php echo $row["id"]; ?>">

            <input type="submit"
                   value="Verify / Unverify">

        </form>

        <?php if ($row["id"] != $_SESSION["user_id"]): ?>

        <form method="post" style="padding:0;margin:3px;"
              onsubmit="return confirmDelete()">

            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id"
                   value="<?php echo $row["id"]; ?>">

            <input type="submit" value="Delete">

        </form>

        <?php endif; ?>

    </td>

</tr>

<?php endwhile; ?>

</table>

</div>

<script src="../script.js"></script>
<script>
searchTable("userSearch", "usersTable");
</script>

</body>
</html>