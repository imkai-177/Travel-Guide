<?php
include "config.php";

$sql = "SELECT p.*, u.name AS scout_name
        FROM posts p
        LEFT JOIN users u ON p.scout_id = u.id
        WHERE p.status='approved'
        ORDER BY p.created_at DESC
        LIMIT 6";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Travel Guide</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "header.php"; ?>

<div class="container">

    <div class="hero">
        <h1>Travel Guide</h1>
        <p>Find places, read travel posts and discover beautiful destinations.</p>
        <a class="button" href="places.php">Explore Places</a>
    </div>

    <h2>Latest Travel Posts</h2>

    <div class="cards">

    <?php while ($row = $result->fetch_assoc()): ?>

        <div class="card">

            <?php
            $images = imageList($row["images"]);
            if (!empty($images)):
            ?>
                <img src="uploads/posts/<?php echo e($images[0]); ?>">
            <?php endif; ?>

            <h3><?php echo e($row["title"]); ?></h3>

            <p>
                <?php echo e($row["country"]); ?>
                |
                <?php echo e($row["genre"]); ?>
            </p>

            <p><?php echo e(substr($row["short_history"], 0, 120)); ?>...</p>

            <a href="post.php?id=<?php echo $row["id"]; ?>">View Details</a>

        </div>

    <?php endwhile; ?>

    </div>

</div>

<script src="script.js"></script>
</body>
</html>