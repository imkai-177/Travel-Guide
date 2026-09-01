<?php
include "config.php";
requireUser();

$uid = (int)$_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $postId = (int)$_POST["post_id"];
    $action = $_POST["action"];

    if ($action == "add") {

        $check = $conn->query(
            "SELECT * FROM wishlist WHERE user_id=$uid AND post_id=$postId"
        );

        if ($check->num_rows == 0) {
            $conn->query(
                "INSERT INTO wishlist(user_id,post_id,added_at)
                 VALUES($uid,$postId,NOW())"
            );
        }

    } elseif ($action == "remove") {

        $conn->query(
            "DELETE FROM wishlist
             WHERE user_id=$uid AND post_id=$postId"
        );
    }

    header("Location: wishlist.php");
    exit;
}

$sql = "SELECT w.*, p.title,p.country,p.genre,p.cost_level
        FROM wishlist w
        JOIN posts p ON w.post_id=p.id
        WHERE w.user_id=$uid
        ORDER BY w.added_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Wishlist</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "header.php"; ?>

<div class="container">

<h2>My Wishlist</h2>

<?php if ($result->num_rows == 0): ?>

    <div class="box">
        <p>Your wishlist is empty.</p>
        <a href="places.php">Browse Places</a>
    </div>

<?php else: ?>

    <?php while ($row = $result->fetch_assoc()): ?>

        <div class="box">

            <h3><?php echo e($row["title"]); ?></h3>

            <p>
                <?php echo e($row["country"]); ?> |
                <?php echo e($row["genre"]); ?> |
                <?php echo e($row["cost_level"]); ?>
            </p>

            <a href="post.php?id=<?php echo $row["post_id"]; ?>">
                View
            </a>

            <form method="post" style="padding:0;margin-top:10px;"
                  onsubmit="return confirmDelete()">

                <input type="hidden" name="post_id"
                       value="<?php echo $row["post_id"]; ?>">

                <input type="hidden" name="action" value="remove">

                <input type="submit" value="Remove">

            </form>

        </div>

    <?php endwhile; ?>

<?php endif; ?>

</div>

<script src="script.js"></script>
</body>
</html>