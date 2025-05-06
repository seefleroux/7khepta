<?php
// Connect to database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "boland";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if columns exist
$checkSql = "SHOW COLUMNS FROM results LIKE 'doubtful'";
$result = $conn->query($checkSql);
$doubtfulExists = ($result && $result->num_rows > 0);

$checkSql = "SHOW COLUMNS FROM results LIKE 'criteria_not_met'";
$result = $conn->query($checkSql);
$criteriaNotMetExists = ($result && $result->num_rows > 0);

$checkSql = "SHOW COLUMNS FROM results LIKE 'wind_assisted'";
$result = $conn->query($checkSql);
$windAssistedExists = ($result && $result->num_rows > 0);

echo "<h1>Database Field Check</h1>";
echo "<p>Checking if required fields exist in the database:</p>";
echo "<ul>";
echo "<li>doubtful field: " . ($doubtfulExists ? "EXISTS" : "MISSING") . "</li>";
echo "<li>criteria_not_met field: " . ($criteriaNotMetExists ? "EXISTS" : "MISSING") . "</li>";
echo "<li>wind_assisted field: " . ($windAssistedExists ? "EXISTS" : "MISSING") . "</li>";
echo "</ul>";

// Add missing columns if needed
if (!$doubtfulExists || !$criteriaNotMetExists || !$windAssistedExists) {
    echo "<p>Adding missing columns to the database...</p>";
    
    $alterSql = "ALTER TABLE results ";
    $alterParts = [];
    
    if (!$doubtfulExists) {
        $alterParts[] = "ADD COLUMN doubtful TINYINT(1) DEFAULT 0";
    }
    
    if (!$criteriaNotMetExists) {
        $alterParts[] = "ADD COLUMN criteria_not_met TINYINT(1) DEFAULT 0";
    }
    
    if (!$windAssistedExists) {
        $alterParts[] = "ADD COLUMN wind_assisted TINYINT(1) DEFAULT 0";
    }
    
    $alterSql .= implode(", ", $alterParts);
    
    if ($conn->query($alterSql) === TRUE) {
        echo "<p>Columns added successfully!</p>";
    } else {
        echo "<p>Error adding columns: " . $conn->error . "</p>";
    }
}

// Check if the specific athlete has the flags set correctly
$checkAthleteSql = "SELECT id, athlete, team_abbr, event, 
                    doubtful, criteria_not_met, wind_assisted 
                    FROM results 
                    WHERE athlete LIKE '%SMALL%'";
$athleteResult = $conn->query($checkAthleteSql);

echo "<h2>Athlete Flag Check</h2>";
if ($athleteResult && $athleteResult->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Athlete</th><th>Team</th><th>Event</th><th>Doubtful</th><th>Criteria Not Met</th><th>Wind Assisted</th></tr>";
    
    while($row = $athleteResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['athlete'] . "</td>";
        echo "<td>" . $row['team_abbr'] . "</td>";
        echo "<td>" . $row['event'] . "</td>";
        echo "<td>" . $row['doubtful'] . "</td>";
        echo "<td>" . $row['criteria_not_met'] . "</td>";
        echo "<td>" . $row['wind_assisted'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Update the flags for this athlete if they're not set
    $updateSql = "UPDATE results 
                 SET doubtful = 1, criteria_not_met = 1, wind_assisted = 1 
                 WHERE athlete LIKE '%SMALL%' AND doubtful = 0 AND criteria_not_met = 0 AND wind_assisted = 0";
    
    if ($conn->query($updateSql) === TRUE) {
        $affected = $conn->affected_rows;
        if ($affected > 0) {
            echo "<p>Updated flags for " . $affected . " records.</p>";
            echo "<p>Please refresh the results.php and index.php pages to see the changes.</p>";
        } else {
            echo "<p>No records needed updating.</p>";
        }
    } else {
        echo "<p>Error updating records: " . $conn->error . "</p>";
    }
} else {
    echo "<p>No records found for athlete 'SMALL'.</p>";
}

$conn->close();
?> 