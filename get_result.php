<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "boland";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM results WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode([]);
    }
}

if (isset($_POST['submit'])) {
    $doubtful = isset($_POST['doubtful']) ? 1 : 0;
    $criteria_not_met = isset($_POST['criteria_not_met']) ? 1 : 0;
    $wind_assisted = isset($_POST['wind_assisted']) ? 1 : 0;

    if (isset($_POST['id'])) {
        // Edit
        $sql = "UPDATE results SET 
                athlete = ?, team_abbr = ?, team = ?, gender = ?, 
                age = ?, event = ?, performance = ?, standard = ?, meet = ?,
                doubtful = ?, criteria_not_met = ?, wind_assisted = ?
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssii", 
            $_POST['athlete'], $_POST['team_abbr'], $_POST['team'], 
            $_POST['gender'], $_POST['age'], $_POST['event'], 
            $_POST['performance'], $_POST['standard'], $_POST['meet'], 
            $doubtful, $criteria_not_met, $wind_assisted, $_POST['id']);
        $stmt->execute();
    } else {
        // Add
        $sql = "INSERT INTO results 
                (athlete, team_abbr, team, gender, age, event, performance, standard, meet, doubtful, criteria_not_met, wind_assisted) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssss", 
            $_POST['athlete'], $_POST['team_abbr'], $_POST['team'], 
            $_POST['gender'], $_POST['age'], $_POST['event'], 
            $_POST['performance'], $_POST['standard'], $_POST['meet'], 
            $doubtful, $criteria_not_met, $wind_assisted);
        $stmt->execute();
    }
    // Use JavaScript for redirect here too
    echo "<script>window.location.href = '" . $_SERVER['PHP_SELF'] . "?scroll=true';</script>";
    exit();
}

$conn->close();
?> 