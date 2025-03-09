<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Qualifiers Analysis</title>
    <link href="https://fonts.cdnfonts.com/css/aptos" rel="stylesheet">
    <style>
        body {
            font-family: 'Aptos Display', sans-serif;
            margin: 0;
            padding: 0;
            width: 100vw;
            overflow-x: hidden;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
        .highlight {
            background-color: #FFFF00;
        }
        h1 {
            text-align: center;
            padding: 20px 0;
            background: black;
            color: white;
            margin: 0;
        }
        th.category {
            background-color: black;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }
        th.standard {
            background-color: #f2f2f2;
        }
        tr.event {
            font-weight: bold;
        }
        .small-name {
            font-size: 10px;
            font-weight: normal;
        }
    </style>
</head>
<body>

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

// Display Results Analysis
echo "<h1>Results Analysis</h1>";

// Get all results with standards achieved
$sql = "SELECT 
            r.athlete,
            r.team_abbr,
            r.gender,
            r.age,
            r.event,
            r.performance,
            r.standard,
            r.doubtful,
            r.criteria_not_met,
            r.wind_assisted
        FROM 
            results r
        WHERE 
            r.standard LIKE '%QS%'
        ORDER BY 
            r.athlete, r.event";

$result = $conn->query($sql);

// Process the results
$ageGroups = array(
    'Under 16' => array(),
    'Under 18' => array(),
    'Under 20' => array(),
    'Seniors' => array()
);

if ($result && $result->num_rows > 0) {
    $processedAthletes = array(); // To track which athletes we've already processed
    
    while($row = $result->fetch_assoc()) {
        $athlete = $row['athlete'];
        $event = $row['event'];
        $key = $athlete . '|' . $event;
        
        // Skip if this athlete is SMALL, ATUYAH
        if (strpos($athlete, 'SMALL, ATUYAH') !== false) {
            continue;
        }
        
        // Skip if any flag is set
        if ($row['doubtful'] == 1 || $row['criteria_not_met'] == 1 || $row['wind_assisted'] == 1) {
            continue;
        }
        
        // Skip if we've already processed this athlete for this event
        if (isset($processedAthletes[$key])) {
            continue;
        }
        
        // Mark as processed
        $processedAthletes[$key] = true;
        
        // Determine age group
        $age = (int)$row['age'];
        $ageGroup = 'Seniors';
        if ($age <= 16) {
            $ageGroup = 'Under 16';
        } elseif ($age <= 18) {
            $ageGroup = 'Under 18';
        } elseif ($age <= 20) {
            $ageGroup = 'Under 20';
        }
        
        // Determine standard
        $standard = null;
        if (strpos($row['standard'], 'A-QS') !== false) {
            $standard = 'A';
        } elseif (strpos($row['standard'], 'B-QS') !== false) {
            $standard = 'B';
        }
        
        // Skip if no standard achieved
        if ($standard === null) {
            continue;
        }
        
        // Add to appropriate age group
        if (!isset($ageGroups[$ageGroup][$row['gender']])) {
            $ageGroups[$ageGroup][$row['gender']] = array();
        }
        
        if (!isset($ageGroups[$ageGroup][$row['gender']][$event])) {
            $ageGroups[$ageGroup][$row['gender']][$event] = array(
                'A' => array(),
                'B' => array()
            );
        }
        
        // Add athlete to appropriate standard
        $athleteInfo = $athlete . '|' . $row['team_abbr'];
        $ageGroups[$ageGroup][$row['gender']][$event][$standard][] = $athleteInfo;
    }
}

// Display the results
foreach ($ageGroups as $ageGroup => $genders) {
    if (empty($genders)) continue;
    
    echo "<table>";
    echo "<tr><th colspan='3' class='category'>" . $ageGroup . "</th></tr>";
    
    foreach ($genders as $gender => $events) {
        if (empty($events)) continue;
        
        echo "<tr>";
        echo "<th class='category'>" . $gender . "</th>";
        echo "<th class='standard'>A</th>";
        echo "<th class='standard'>B</th>";
        echo "</tr>";
        
        foreach ($events as $event => $standards) {
            echo "<tr class='event'>";
            echo "<td>" . $event . "</td>";
            
            // A Standard
            echo "<td class='highlight'>";
            if (!empty($standards['A'])) {
                echo implode("<br>", array_map(function($athlete) {
                    $parts = explode('|', $athlete);
                    return $parts[0] . " <span class='small-name'>(" . $parts[1] . ")</span>";
                }, $standards['A']));
            } else {
                echo "-";
            }
            echo "</td>";
            
            // B Standard
            echo "<td>";
            if (!empty($standards['B'])) {
                echo implode("<br>", array_map(function($athlete) {
                    $parts = explode('|', $athlete);
                    return $parts[0] . " <span class='small-name'>(" . $parts[1] . ")</span>";
                }, $standards['B']));
            } else {
                echo "-";
            }
            echo "</td>";
            
            echo "</tr>";
        }
    }
    
    echo "</table><br>";
}

$conn->close();
?>
</body>
</html>