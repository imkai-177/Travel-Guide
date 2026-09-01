<?php
include "config.php";
requireUser();

$uid = (int)$_SESSION["user_id"];
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $conn->real_escape_string($_POST["trip_name"]);
    $start = $_POST["start_date"];
    $end = $_POST["end_date"];

    if ($name != "") {
        $conn->query(
            "INSERT INTO trips(user_id,trip_name,start_date,end_date)
             VALUES($uid,'$name','$start','$end')"
        );
        $message = "Trip created successfully";
    }
}

$result = $conn->query(
    "SELECT * FROM trips WHERE user_id=$uid ORDER BY created_at DESC"
);
?>

<!DOCTYPE html>
<html>
<head><title>Plan Trip</title><link rel="stylesheet" href="style.css"></head>
<body>
<?php include "header.php"; ?>
<div class="container">
<h2>Plan a Trip</h2>
<p class="success"><?php echo e($message); ?></p>
<form method="post" onsubmit="return checkForm()">
    Trip Name:
    <input type="text" name="trip_name" required>
    Start Date:
    <input type="date" name="start_date">
    End Date:
    <input type="date" name="end_date">
    <input type="submit" value="Create Trip">
</form>

<h3>My Trips</h3>
<?php while ($row=$result->fetch_assoc()): ?>
<div class="box">
    <b><?php echo e($row["trip_name"]); ?></b>
    <p><?php echo e($row["start_date"]); ?> to <?php echo e($row["end_date"]); ?></p>
</div>
<?php endwhile; ?>
</div>
<script src="script.js"></script>
</body>
</html>