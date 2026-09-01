<?php
include "../config.php";
requireScout();

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST["title"]);
    $country = trim($_POST["country"]);
    $genre = $_POST["genre"];
    $cost = $_POST["cost_level"];
    $history = trim($_POST["short_history"]);
    $travel = trim($_POST["travel_medium_info"]);

    if (empty($title) || empty($country) || empty($genre) ||
        empty($cost) || empty($history) || empty($travel)) {

        $error = "Please fill all fields";

    } elseif (strlen($history) < 20) {

        $error = "Description must be at least 20 characters";

    } else {

        $images = uploadImages($_FILES["images"] ?? []);
        $imageText = $conn->real_escape_string(implode(",", $images));

        $data = [
            "title"=>$title,
            "country"=>$country,
            "genre"=>$genre,
            "cost_level"=>$cost,
            "short_history"=>$history,
            "travel_medium_info"=>$travel,
            "images"=>implode(",", $images)
        ];

        $json = $conn->real_escape_string(json_encode($data));
        $uid = (int)$_SESSION["user_id"];

        $sql = "INSERT INTO post_requests
                (scout_id,post_data,requested_at,status)
                VALUES($uid,'$json',NOW(),'pending')";

        if ($conn->query($sql)) {
            $success = "Travel destination submitted successfully";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Destination</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<?php include "../header.php"; ?>

<div class="container">

<h2>Submit Travel Destination</h2>

<p class="success"><?php echo e($success); ?></p>
<p class="error"><?php echo e($error); ?></p>

<form method="post" enctype="multipart/form-data"
      onsubmit="return checkForm()">

    Title:
    <input type="text" name="title" required id="sc_title">

    Country:
    <input type="text" name="country" required id="sc_country">

    Genre:
    <select name="genre" required id="sc_genre">
        <option value="">Select</option>
        <option value="beach">Beach</option>
        <option value="mountain">Mountain</option>
        <option value="city">City</option>
        <option value="historical">Historical</option>
        <option value="nature">Nature</option>
        <option value="cultural">Cultural</option>
        <option value="adventure">Adventure</option>
        <option value="other">Other</option>
    </select>

    Cost Level:
    <select name="cost_level" required id="sc_cost">
        <option value="">Select</option>
        <option value="low">Low</option>
        <option value="medium">Medium</option>
        <option value="high">High</option>
    </select>

    Travel Medium:
    <input type="text" name="travel_medium_info" required id="sc_travel">

    Description:
    <textarea name="short_history" required id="sc_history"></textarea>

    Pictures:
    <input type="file" name="images[]" multiple
           accept="image/*" onchange="previewImages(this)">

    <div id="preview"></div>

    <input type="submit" value="Submit">

</form>

</div>

<script src="../script.js"></script>
</body>
</html>