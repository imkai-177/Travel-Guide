<?php
include "../config.php";
requireAdmin();

$id = (int)($_GET["id"] ?? 0);

$result = $conn->query(
    "SELECT * FROM posts WHERE id=$id"
);

if ($result->num_rows == 0) {
    die("Post not found");
}

$post = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = $conn->real_escape_string($_POST["title"]);
    $country = $conn->real_escape_string($_POST["country"]);
    $genre = $conn->real_escape_string($_POST["genre"]);
    $cost = $conn->real_escape_string($_POST["cost_level"]);
    $history = $conn->real_escape_string($_POST["short_history"]);
    $travel = $conn->real_escape_string($_POST["travel_medium_info"]);

    $conn->query(
        "UPDATE posts SET
         title='$title',
         country='$country',
         genre='$genre',
         cost_level='$cost',
         short_history='$history',
         travel_medium_info='$travel',
         updated_at=NOW()
         WHERE id=$id"
    );

    header("Location: posts.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Post</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<?php include "../header.php"; ?>

<div class="container">

<form method="post" onsubmit="return checkForm()">

    Title:
    <input type="text" name="title" required
           value="<?php echo e($post["title"]); ?>">

    Country:
    <input type="text" name="country" required
           value="<?php echo e($post["country"]); ?>">

    Genre:
    <input type="text" name="genre" required
           value="<?php echo e($post["genre"]); ?>">

    Cost:
    <select name="cost_level">
        <option value="low">Low</option>
        <option value="medium">Medium</option>
        <option value="high">High</option>
    </select>

    Travel Medium:
    <input type="text" name="travel_medium_info" required
           value="<?php echo e($post["travel_medium_info"]); ?>">

    Description:
    <textarea name="short_history" required><?php
        echo e($post["short_history"]);
    ?></textarea>

    <input type="submit" value="Update">

</form>

</div>

<script src="../script.js"></script>
</body>
</html>