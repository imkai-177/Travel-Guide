<?php
include "config.php";

$id = (int)($_GET["id"] ?? 0);

$sql = "SELECT p.*, u.name AS scout_name
        FROM posts p
        LEFT JOIN users u ON p.scout_id=u.id
        WHERE p.id=$id AND p.status='approved'";

$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Post not found");
}

$post = $result->fetch_assoc();

$images = imageList($post["images"]);

$comments = $conn->query(
    "SELECT c.*, u.name
     FROM comments c
     LEFT JOIN users u ON c.user_id=u.id
     WHERE c.post_id=$id
     ORDER BY c.created_at DESC"
);

$base = 500;

if ($post["cost_level"] == "medium") {
    $base = 1500;
} elseif ($post["cost_level"] == "high") {
    $base = 3000;
}

$costResult = $conn->query(
    "SELECT * FROM cost_estimates WHERE post_id=$id"
);

if ($costResult && $costResult->num_rows > 0) {
    $costRow = $costResult->fetch_assoc();
    $base = $costRow["base_cost"];
}

$inWishlist = false;

if (isUser()) {
    $uid = (int)$_SESSION["user_id"];

    $wish = $conn->query(
        "SELECT * FROM wishlist WHERE user_id=$uid AND post_id=$id"
    );

    if ($wish->num_rows > 0) {
        $inWishlist = true;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["delete_comment"])) {

        requireLogin();

        $commentId = (int)$_POST["delete_comment"];

        $check = $conn->query(
            "SELECT * FROM comments WHERE id=$commentId"
        );

        if ($check->num_rows > 0) {

            $comment = $check->fetch_assoc();

            if ((int)$comment["user_id"] == (int)$_SESSION["user_id"] || isAdmin()) {
                $conn->query("DELETE FROM comments WHERE id=$commentId");
            }
        }

        header("Location: post.php?id=$id");
        exit;
    }

    if (isset($_POST["content"]) && isUser()) {

        $content = $conn->real_escape_string(trim($_POST["content"]));
        $uid = (int)$_SESSION["user_id"];

        if ($content != "") {
            $conn->query(
                "INSERT INTO comments(user_id,post_id,content,created_at)
                 VALUES($uid,$id,'$content',NOW())"
            );
        }

        header("Location: post.php?id=$id");
        exit;
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo e($post["title"]); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "header.php"; ?>

<div class="container">

<div class="box">

<h1><?php echo e($post["title"]); ?></h1>

<p>
    Country: <?php echo e($post["country"]); ?><br>
    Genre: <?php echo e($post["genre"]); ?><br>
    Cost: <?php echo e($post["cost_level"]); ?><br>
    Travel By: <?php echo e($post["travel_medium_info"]); ?><br>
    Scout: <?php echo e($post["scout_name"]); ?>
</p>

<div class="image-row">

<?php foreach ($images as $image): ?>

    <img src="uploads/posts/<?php echo e($image); ?>">

<?php endforeach; ?>

</div>

<h3>About This Destination</h3>

<p><?php echo nl2br(e($post["short_history"])); ?></p>

</div>

<?php if (isUser()): ?>

<div class="box">

<h3>Wishlist</h3>

<?php if ($inWishlist): ?>

    <form method="post" action="wishlist.php">
        <input type="hidden" name="post_id" value="<?php echo $id; ?>">
        <input type="hidden" name="action" value="remove">
        <input type="submit" value="Remove from Wishlist">
    </form>

<?php else: ?>

    <form method="post" action="wishlist.php">
        <input type="hidden" name="post_id" value="<?php echo $id; ?>">
        <input type="hidden" name="action" value="add">
        <input type="submit" value="Add to Wishlist">
    </form>

<?php endif; ?>

</div>

<div class="box">

<h3>Cost Estimator</h3>

<p>Base cost: ৳<?php echo number_format($base); ?> per person/week</p>

<input type="number" id="travelers" min="1" value="1">
<input type="number" id="days" min="1" value="7">

<button onclick="calculateCost()">Calculate</button>

<p id="cost"></p>

</div>

<?php endif; ?>

<div class="box">

<h3>Comments</h3>

<?php while ($comment = $comments->fetch_assoc()): ?>

    <div class="comment">

        <b><?php echo e($comment["name"]); ?></b>

        <p><?php echo e($comment["content"]); ?></p>

        <small><?php echo e($comment["created_at"]); ?></small>

        <?php if (loggedIn() &&
            ((int)$comment["user_id"] == (int)$_SESSION["user_id"] || isAdmin())): ?>

            <form method="post" action="post.php?id=<?php echo $id; ?>"
                  style="padding:0;margin:5px 0;">
                <input type="hidden" name="delete_comment"
                       value="<?php echo $comment["id"]; ?>">
                <input type="submit" value="Delete">
            </form>

        <?php endif; ?>

    </div>

<?php endwhile; ?>

<?php if (isUser()): ?>

<form method="post" action="post.php?id=<?php echo $id; ?>"
      onsubmit="return checkForm()">

    <textarea name="content" required
              placeholder="Write your comment"></textarea>

    <input type="submit" value="Post Comment">

</form>

<?php elseif (!loggedIn()): ?>

<p>Please <a href="login.php">login</a> to comment.</p>

<?php endif; ?>

</div>

</div>

<script>
function calculateCost() {

    var travelers = document.getElementById("travelers").value;
    var days = document.getElementById("days").value;
    var weekly = <?php echo $base; ?>;

    var total = weekly * travelers * (days / 7);

    document.getElementById("cost").innerHTML =
        "Estimated Total: ৳" + total.toFixed(2);
}
</script>

</body>
</html>
