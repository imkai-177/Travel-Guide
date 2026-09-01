<?php
include "../config.php";
requireAdmin();

$users = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()["total"];
$posts = $conn->query("SELECT COUNT(*) AS total FROM posts")->fetch_assoc()["total"];
$comments = $conn->query("SELECT COUNT(*) AS total FROM comments")->fetch_assoc()["total"];
$pending = $conn->query(
    "SELECT COUNT(*) AS total FROM post_requests WHERE status='pending'"
)->fetch_assoc()["total"];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<?php include "../header.php"; ?>

<div class="container">

<h2>Admin Dashboard</h2>

<div class="stats">

    <div class="stat">
        <h2><?php echo $users; ?></h2>
        <p>Total Users</p>
    </div>

    <div class="stat">
        <h2><?php echo $posts; ?></h2>
        <p>Total Posts</p>
    </div>

    <div class="stat">
        <h2><?php echo $comments; ?></h2>
        <p>Total Comments</p>
    </div>

    <div class="stat">
        <h2><?php echo $pending; ?></h2>
        <p>Pending Requests</p>
    </div>

</div>

<div class="box">

    <h3>Admin Options</h3>

    <p><a href="users.php">Manage Users</a></p>
    <p><a href="posts.php">Manage Posts</a></p>
    <p><a href="comments.php">Manage Comments</a></p>

</div>

</div>

</body>
</html>