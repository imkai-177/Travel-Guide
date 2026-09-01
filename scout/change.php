<?php
include "../config.php";
requireScout();

$uid = (int)$_SESSION["user_id"];
$id = (int)($_GET["id"] ?? 0);

$result = $conn->query(
    "SELECT * FROM posts
     WHERE id=$id AND scout_id=$uid AND status='approved'"
);

if ($result->num_rows == 0) {
    die("Post not found");
}

$post = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $data = [
        "title" => trim($_POST["title"]),
        "country" => trim($_POST["country"]),
        "genre" => $_POST["genre"],
        "cost_level" => $_POST["cost_level"],
        "short_history" => trim($_POST["short_history"]),
        "travel_medium_info" => trim($_POST["travel_medium_info"]),
        "images" => $post["images"]
    ];

    $json = $conn->real_escape_string(json_encode($data));

    $sql = "INSERT INTO post_requests
            (scout_id,post_data,original_post_id,is_change_request,requested_at,status)
            VALUES($uid,'$json',$id,1,NOW(),'pending')";

    if ($conn->query($sql)) {
        header("Location: approved.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Request Changes</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<?php include "../header.php"; ?>

<div class="container">

<h2>Request Changes</h2>

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
    <input type="text" name="cost_level" required
           value="<?php echo e($post["cost_level"]); ?>">

    Travel Medium:
    <input type="text" name="travel_medium_info" required
           value="<?php echo e($post["travel_medium_info"]); ?>">

    Description:
    <textarea name="short_history" required><?php
        echo e($post["short_history"]);
    ?></textarea>

    <input type="submit" value="Send Change Request">

</form>

</div>

<script src="../script.js"></script>
</body>
</html>