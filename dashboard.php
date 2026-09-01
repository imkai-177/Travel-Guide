<?php
include "config.php";
requireLogin();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "header.php"; ?>

<div class="container">

<h2>Welcome, <?php echo e($_SESSION["name"]); ?></h2>

<p>Your role:
    <b><?php echo e($_SESSION["role"]); ?></b>
</p>

<?php if (isUser()): ?>

    <div class="box">
        <h3>General User</h3>
        <p><a href="places.php">Browse Travel Posts</a></p>
        <p><a href="wishlist.php">My Wishlist</a></p>
        <p><a href="trip.php">Plan a Trip</a></p>
        <p><a href="profile.php">My Profile</a></p>
    </div>

<?php elseif (isScout()): ?>

    <div class="box">
        <h3>Scout</h3>
        <p><a href="scout/create.php">Submit Travel Destination</a></p>
        <p><a href="scout/requests.php">Manage My Requests</a></p>
        <p><a href="scout/approved.php">Approved Destinations</a></p>
    </div>

<?php elseif (isAdmin()): ?>

    <div class="box">
        <h3>Administrator</h3>
        <p><a href="admin/dashboard.php">Admin Dashboard</a></p>
        <p><a href="admin/users.php">Manage Users</a></p>
        <p><a href="admin/posts.php">Manage Posts</a></p>
        <p><a href="admin/comments.php">Manage Comments</a></p>
    </div>

<?php endif; ?>

</div>

</body>
</html>