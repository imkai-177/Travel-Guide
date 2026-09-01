<?php
include "../config.php";
requireScout();

$uid = (int)$_SESSION["user_id"];
$id = (int)($_GET["id"] ?? 0);

$result = $conn->query(
    "SELECT * FROM post_requests
     WHERE id=$id AND scout_id=$uid AND status='pending'"
);

if ($result->num_rows == 0) {
    die("Request not found");
}

$row = $result->fetch_assoc();
$data = json_decode($row["post_data"], true);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $data["title"] = trim($_POST["title"]);
    $data["country"] = trim($_POST["country"]);
    $data["genre"] = $_POST["genre"];
    $data["cost_level"] = $_POST["cost_level"];
    $data["short_history"] = trim($_POST["short_history"]);
    $data["travel_medium_info"] = trim($_POST["travel_medium_info"]);

    if (empty($data["title"]) || empty($data["country"]) ||
        empty($data["short_history"])) {

        $error = "Please fill all fields";

    } else {

        $json = $conn->real_escape_string(json_encode($data));

        $conn->query(
            "UPDATE post_requests
             SET post_data='$json'
             WHERE id=$id AND scout_id=$uid AND status='pending'"
        );

        header("Location: requests.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Request</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<?php include "../header.php"; ?>

<div class="container">

<h2>Edit Request</h2>

<form method="post" onsubmit="return checkForm()">

    Title:
    <input type="text" name="title" required
           value="<?php echo e($data["title"]); ?>">

    Country:
    <input type="text" name="country" required
           value="<?php echo e($data["country"]); ?>">

    Genre:
    <select name="genre">
        <?php
        $genres = ["beach","mountain","city","historical",
                   "nature","cultural","adventure","other"];
        foreach ($genres as $g):
        ?>
            <option value="<?php echo $g; ?>"
                <?php if ($data["genre"] == $g) echo "selected"; ?>>
                <?php echo ucfirst($g); ?>
            </option>
        <?php endforeach; ?>
    </select>

    Cost:
    <select name="cost_level">
        <option value="low" <?php if ($data["cost_level"]=="low") echo "selected"; ?>>Low</option>
        <option value="medium" <?php if ($data["cost_level"]=="medium") echo "selected"; ?>>Medium</option>
        <option value="high" <?php if ($data["cost_level"]=="high") echo "selected"; ?>>High</option>
    </select>

    Travel Medium:
    <input type="text" name="travel_medium_info" required
           value="<?php echo e($data["travel_medium_info"]); ?>">

    Description:
    <textarea name="short_history" required><?php
        echo e($data["short_history"]);
    ?></textarea>

    <input type="submit" value="Update">

</form>

</div>

<script src="../script.js"></script>
</body>
</html>