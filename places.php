<?php
include "config.php";

$q = $_GET["q"] ?? "";
$country = $_GET["country"] ?? "";
$genre = $_GET["genre"] ?? "";
$cost = $_GET["cost"] ?? "";

$sql = "SELECT p.*, u.name AS scout_name
        FROM posts p
        LEFT JOIN users u ON p.scout_id=u.id
        WHERE p.status='approved'";

if ($q != "") {
    $q = $conn->real_escape_string($q);
    $sql .= " AND (p.title LIKE '%$q%' OR p.country LIKE '%$q%')";
}

if ($country != "") {
    $country = $conn->real_escape_string($country);
    $sql .= " AND p.country='$country'";
}

if ($genre != "") {
    $genre = $conn->real_escape_string($genre);
    $sql .= " AND p.genre='$genre'";
}

if ($cost != "") {
    $cost = $conn->real_escape_string($cost);
    $sql .= " AND p.cost_level='$cost'";
}

$sql .= " ORDER BY p.created_at DESC";

$result = $conn->query($sql);

$countries = $conn->query(
    "SELECT DISTINCT country FROM posts WHERE status='approved' ORDER BY country"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Places</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "header.php"; ?>

<div class="container">

<h2>Explore Places</h2>

<form method="get">

    Search:
    <input type="text" name="q" value="<?php echo e($q); ?>">

    Country:
    <select name="country">
        <option value="">All Countries</option>

        <?php while ($c = $countries->fetch_assoc()): ?>
            <option value="<?php echo e($c["country"]); ?>"
                <?php if ($country == $c["country"]) echo "selected"; ?>>
                <?php echo e($c["country"]); ?>
            </option>
        <?php endwhile; ?>
    </select>

    Genre:
    <select name="genre">
        <option value="">All</option>
        <option value="beach">Beach</option>
        <option value="mountain">Mountain</option>
        <option value="city">City</option>
        <option value="historical">Historical</option>
        <option value="nature">Nature</option>
        <option value="cultural">Cultural</option>
        <option value="adventure">Adventure</option>
        <option value="other">Other</option>
    </select>

    Cost:
    <select name="cost">
        <option value="">All</option>
        <option value="low">Low</option>
        <option value="medium">Medium</option>
        <option value="high">High</option>
    </select>

    <input type="submit" value="Search">

</form>

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

        <p><?php echo e($row["country"]); ?></p>
        <p><?php echo e($row["genre"]); ?> |
           <?php echo e($row["cost_level"]); ?></p>

        <a href="post.php?id=<?php echo $row["id"]; ?>">
            View Details
        </a>

    </div>

<?php endwhile; ?>

</div>

</div>

<script src="script.js"></script>
</body>
</html>