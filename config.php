<?php
session_start();

$host = "localhost";
$user = "root";
$password = "";
$dbname = "travelGuide";
define("BASE", "/travel");

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function syncDestinationPosts() {
    global $conn;

    $admin = $conn->query("SELECT id FROM users WHERE email='admin@travel.com' LIMIT 1");
    if (!$admin || $admin->num_rows == 0) {
        return;
    }

    $adminId = (int)$admin->fetch_assoc()["id"];

    $destinations = [
        [
            "find" => "Coxs Bazar",
            "title" => "Coxs Bazar",
            "history" => "A popular sea beach destination in Bangladesh. It is known for its long natural sandy beach and beautiful sunset.",
            "genre" => "beach",
            "cost" => "medium",
            "travel" => "Bus, train or flight",
            "images" => "coxs_bazar_1.jpg,coxs_bazar_2.jpg,coxs_bazar_3.jpg"
        ],
        [
            "find" => "Bandarban",
            "title" => "Bandarban",
            "history" => "A beautiful hill destination in Bangladesh known for green mountains, winding roads and peaceful natural surroundings.",
            "genre" => "mountain",
            "cost" => "low",
            "travel" => "Bus",
            "images" => "bandarban_1.jpg,bandarban_2.jpg,bandarban_3.jpg"
        ],
        [
            "find" => "Sundarban",
            "title" => "Sundarban",
            "history" => "The Sundarban is a unique mangrove forest in Bangladesh, famous for its rivers, wildlife and Royal Bengal Tigers.",
            "genre" => "nature",
            "cost" => "medium",
            "travel" => "Bus and boat",
            "images" => "sundarban_1.jpg,sundarban_2.jpg,sundarban_3.jpg"
        ]
    ];

    foreach ($destinations as $d) {
        $find = $conn->real_escape_string($d["find"]);
        $title = $conn->real_escape_string($d["title"]);
        $history = $conn->real_escape_string($d["history"]);
        $genre = $conn->real_escape_string($d["genre"]);
        $cost = $conn->real_escape_string($d["cost"]);
        $travel = $conn->real_escape_string($d["travel"]);
        $images = $conn->real_escape_string($d["images"]);

        $lookup = ($d["title"] == "Bandarban")
            ? "(title='Bandarban' OR title='Mountain Escape')"
            : "title='$find'";

        $existing = $conn->query(
            "SELECT id FROM posts
             WHERE $lookup
             ORDER BY id ASC LIMIT 1"
        );

        if ($existing && $existing->num_rows > 0) {
            $row = $existing->fetch_assoc();
            $postId = (int)$row["id"];

            $conn->query(
                "UPDATE posts SET
                    scout_id=$adminId,
                    title='$title',
                    short_history='$history',
                    country='Bangladesh',
                    genre='$genre',
                    cost_level='$cost',
                    travel_medium_info='$travel',
                    images='$images',
                    status='approved',
                    updated_at=NOW()
                 WHERE id=$postId"
            );
        } else {
            $conn->query(
                "INSERT INTO posts
                    (scout_id,title,short_history,country,genre,cost_level,travel_medium_info,images,status)
                 VALUES
                    ($adminId,'$title','$history','Bangladesh','$genre','$cost','$travel','$images','approved')"
            );
            $postId = (int)$conn->insert_id;
        }

        if ($postId > 0) {
            $base = ($d["cost"] == "low") ? 500 : (($d["cost"] == "high") ? 3000 : 1500);
            $estimate = $conn->query("SELECT id FROM cost_estimates WHERE post_id=$postId LIMIT 1");

            if ($estimate && $estimate->num_rows > 0) {
                $conn->query("UPDATE cost_estimates SET base_cost=$base, currency='BDT', last_updated=NOW() WHERE post_id=$postId");
            } else {
                $conn->query("INSERT INTO cost_estimates(post_id,base_cost,currency,last_updated) VALUES($postId,$base,'BDT',NOW())");
            }
        }
    }
}

syncDestinationPosts();

function e($text) {
    return htmlspecialchars($text ?? "", ENT_QUOTES, "UTF-8");
}

function loggedIn() {
    return isset($_SESSION["user_id"]);
}

function isUser() {
    return loggedIn() && ($_SESSION["role"] == "user" || $_SESSION["role"] == "traveler");
}

function isScout() {
    return loggedIn() && ($_SESSION["role"] == "scout" || $_SESSION["role"] == "guide");
}

function isAdmin() {
    return loggedIn() && $_SESSION["role"] == "admin";
}

function requireLogin() {
    if (!loggedIn()) {
        header("Location: login.php");
        exit;
    }
}

function requireUser() {
    requireLogin();
    if (!isUser()) {
        header("Location: dashboard.php");
        exit;
    }
}

function requireScout() {
    requireLogin();
    if (!isScout()) {
        header("Location: dashboard.php");
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header("Location: dashboard.php");
        exit;
    }
}

function imageList($value) {
    if (empty($value)) return [];
    return array_filter(array_map("trim", explode(",", $value)));
}

function uploadImages($files) {
    $names = [];

    if (!isset($files["name"]) || !is_array($files["name"])) {
        return $names;
    }

    $count = count($files["name"]);

    if ($count > 5) {
        $count = 5;
    }

    for ($i = 0; $i < $count; $i++) {

        if ($files["error"][$i] != 0) {
            continue;
        }

        $type = $files["type"][$i];

        if ($type != "image/jpeg" && $type != "image/png" &&
            $type != "image/gif" && $type != "image/webp") {
            continue;
        }

        if ($files["size"][$i] > 3 * 1024 * 1024) {
            continue;
        }

        $ext = pathinfo($files["name"][$i], PATHINFO_EXTENSION);
        $newName = time() . "_" . rand(1000,9999) . "." . $ext;

        move_uploaded_file(
            $files["tmp_name"][$i],
            "uploads/posts/" . $newName
        );

        $names[] = $newName;
    }

    return $names;
}

function uploadProfilePicture($file) {

    if (!isset($file["name"]) || $file["error"] != 0) {
        return "";
    }

    $type = $file["type"];

    if ($type != "image/jpeg" && $type != "image/png" &&
        $type != "image/gif" && $type != "image/webp") {
        return "";
    }

    if ($file["size"] > 2 * 1024 * 1024) {
        return "";
    }

    $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $name = time() . "_" . rand(1000,9999) . "." . $ext;

    $uploadDir = __DIR__ . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . "profiles";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $destination = $uploadDir . DIRECTORY_SEPARATOR . $name;

    if (!move_uploaded_file($file["tmp_name"], $destination)) {
        return "";
    }

    return $name;
}
?>