<?php
include "../config.php";
requireAdmin();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = (int)$_POST["id"];

    $conn->query(
        "DELETE FROM comments WHERE id=$id"
    );
}

$result = $conn->query(
    "SELECT c.*,u.name,p.title
     FROM comments c
     LEFT JOIN users u ON c.user_id=u.id
     LEFT JOIN posts p ON c.post_id=p.id
     ORDER BY c.created_at DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Comments</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<?php include "../header.php"; ?>

<div class="container">

<h2>Manage Comments</h2>

<table id="commentsTable">

<tr>
    <th>User</th>
    <th>Post</th>
    <th>Comment</th>
    <th>Action</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>

<tr>

    <td><?php echo e($row["name"]); ?></td>
    <td><?php echo e($row["title"]); ?></td>
    <td><?php echo e($row["content"]); ?></td>

    <td>

        <form method="post" style="padding:0;margin:0;"
              onsubmit="return confirmDelete()">

            <input type="hidden" name="id"
                   value="<?php echo $row["id"]; ?>">

            <input type="submit" value="Delete">

        </form>

    </td>

</tr>

<?php endwhile; ?>

</table>

</div>

<script src="../script.js"></script>
</body>
</html>