<?php
include "../config.php";
requireAdmin();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $action = $_POST["action"];
    $id = (int)($_POST["id"] ?? 0);

    if ($action == "approve") {

        $r = $conn->query(
            "SELECT * FROM post_requests
             WHERE id=$id AND status='pending'"
        );

        if ($r->num_rows > 0) {

            $request = $r->fetch_assoc();
            $data = json_decode($request["post_data"], true);

            $title = $conn->real_escape_string($data["title"]);
            $history = $conn->real_escape_string($data["short_history"]);
            $country = $conn->real_escape_string($data["country"]);
            $genre = $conn->real_escape_string($data["genre"]);
            $cost = $conn->real_escape_string($data["cost_level"]);
            $travel = $conn->real_escape_string($data["travel_medium_info"]);
            $images = $conn->real_escape_string($data["images"] ?? "");
            $scout = (int)$request["scout_id"];

            $conn->query(
                "INSERT INTO posts
                (scout_id,title,short_history,country,genre,cost_level,
                 travel_medium_info,images,status,created_at,updated_at)
                VALUES
                ($scout,'$title','$history','$country','$genre','$cost',
                 '$travel','$images','approved',NOW(),NOW())"
            );

            $postId = $conn->insert_id;

            $base = 500;
            if ($cost == "medium") $base = 1500;
            if ($cost == "high") $base = 3000;

            $conn->query(
                "INSERT INTO cost_estimates(post_id,base_cost,currency,last_updated)
                 VALUES($postId,$base,'BDT',NOW())"
            );

            $conn->query(
                "UPDATE post_requests SET status='approved' WHERE id=$id"
            );
        }

    } elseif ($action == "reject") {

        $conn->query(
            "UPDATE post_requests SET status='rejected'
             WHERE id=$id"
        );

    } elseif ($action == "delete") {

        $conn->query("DELETE FROM wishlist WHERE post_id=$id");
        $conn->query("DELETE FROM comments WHERE post_id=$id");
        $conn->query("DELETE FROM cost_estimates WHERE post_id=$id");
        $conn->query("DELETE FROM posts WHERE id=$id");

    } elseif ($action == "edit") {

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
    }
}

$requests = $conn->query(
    "SELECT pr.*,u.name
     FROM post_requests pr
     LEFT JOIN users u ON pr.scout_id=u.id
     ORDER BY pr.requested_at DESC"
);

$posts = $conn->query(
    "SELECT p.*,u.name
     FROM posts p
     LEFT JOIN users u ON p.scout_id=u.id
     ORDER BY p.created_at DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Posts</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<?php include "../header.php"; ?>

<div class="container">

<h2>Post Requests</h2>

<?php while ($row = $requests->fetch_assoc()): ?>

<?php $data = json_decode($row["post_data"], true); ?>

<div class="box">

    <h3><?php echo e($data["title"] ?? ""); ?></h3>

    <p>
        Scout: <?php echo e($row["name"]); ?><br>
        Status: <?php echo e($row["status"]); ?>
    </p>

    <?php if ($row["status"] == "pending"): ?>

        <form method="post" style="padding:0;margin:5px;">

            <input type="hidden" name="action" value="approve">
            <input type="hidden" name="id"
                   value="<?php echo $row["id"]; ?>">

            <input type="submit" value="Approve">

        </form>

        <form method="post" style="padding:0;margin:5px;"
              onsubmit="return confirmDelete()">

            <input type="hidden" name="action" value="reject">
            <input type="hidden" name="id"
                   value="<?php echo $row["id"]; ?>">

            <input type="submit" value="Reject">

        </form>

    <?php endif; ?>

</div>

<?php endwhile; ?>

<h2>Approved Posts</h2>

<table>

<tr>
    <th>Title</th>
    <th>Country</th>
    <th>Scout</th>
    <th>Action</th>
</tr>

<?php while ($row = $posts->fetch_assoc()): ?>

<tr>

    <td><?php echo e($row["title"]); ?></td>
    <td><?php echo e($row["country"]); ?></td>
    <td><?php echo e($row["name"]); ?></td>

    <td>

        <a href="edit_post.php?id=<?php echo $row["id"]; ?>">
            Edit
        </a>

        <form method="post" style="padding:0;margin:5px;"
              onsubmit="return confirmDelete()">

            <input type="hidden" name="action" value="delete">
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