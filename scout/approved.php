<?php
include "../config.php";
requireScout();

$uid = (int)$_SESSION["user_id"];

$result = $conn->query(
    "SELECT * FROM posts
     WHERE scout_id=$uid AND status='approved'
     ORDER BY created_at DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approved Posts</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<?php include "../header.php"; ?>

<div class="container">

<h2>My Approved Destinations</h2>

<?php while ($row = $result->fetch_assoc()): ?>

<div class="box">

    <h3><?php echo e($row["title"]); ?></h3>

    <p>
        <?php echo e($row["country"]); ?> |
        <?php echo e($row["genre"]); ?>
    </p>

    <p><?php echo e($row["short_history"]); ?></p>

    <a href="change.php?id=<?php echo $row["id"]; ?>">
        Request Changes
    </a>

</div>

<?php endwhile; ?>

</div>

</body>
</html>