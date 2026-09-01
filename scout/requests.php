<?php
include "../config.php";
requireScout();

$uid = (int)$_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST" &&
    isset($_POST["delete_id"])) {

    $id = (int)$_POST["delete_id"];

    $conn->query(
        "DELETE FROM post_requests
         WHERE id=$id AND scout_id=$uid AND status='pending'"
    );

    header("Location: requests.php");
    exit;
}

$result = $conn->query(
    "SELECT * FROM post_requests
     WHERE scout_id=$uid
     ORDER BY requested_at DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Requests</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<?php include "../header.php"; ?>

<div class="container">

<h2>My Requests</h2>

<table>

<tr>
    <th>Title</th>
    <th>Status</th>
    <th>Date</th>
    <th>Action</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>

<?php $data = json_decode($row["post_data"], true); ?>

<tr>

    <td><?php echo e($data["title"] ?? ""); ?></td>

    <td><?php echo e($row["status"]); ?></td>

    <td><?php echo e($row["requested_at"]); ?></td>

    <td>

    <?php if ($row["status"] == "pending"): ?>

        <a href="edit.php?id=<?php echo $row["id"]; ?>">Edit</a>

        <form method="post" style="padding:0;margin:5px;"
              onsubmit="return confirmDelete()">

            <input type="hidden" name="delete_id"
                   value="<?php echo $row["id"]; ?>">

            <input type="submit" value="Delete">

        </form>

    <?php endif; ?>

    </td>

</tr>

<?php endwhile; ?>

</table>

</div>

<script src="../script.js"></script>
</body>
</html>