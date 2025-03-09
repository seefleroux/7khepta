<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Qualifying Standards</title>
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
            margin-bottom: 30px;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
            overflow: visible;
        }
        th {
            background-color: #f2f2f2;
            white-space: nowrap;
        }
        .header {
            background-color: black;
            color: white;
            font-weight: bold;
        }
        .event {
            text-align: left;
            font-weight: bold;
            width: 180px; /* Increased width for event column */
            white-space: nowrap;
        }
        .standard-a {
            background-color: #FFFF00;
        }
        .standard-column {
            width: calc((100% - 180px) / 6); /* Adjusted calculation for wider event column */
        }
        h1 {
            text-align: center;
            padding: 20px 0;
            background: black;
            color: white;
            margin: 0;
        }
        .dash {
            font-weight: bold;
        }
        .debug {
            margin-top: 20px;
            padding: 10px;
            background: #f8f8f8;
            border: 1px solid #ddd;
            display: none; /* Hide debug by default */
        }
        .athlete {
            display: block;
            font-size: 11px;
            text-align: center;
            margin-top: 4px;
            white-space: normal;
            line-height: 1.3;
        }
        .performance {
            font-weight: bold;
        }
        .cell-content {
            display: flex;
            flex-direction: column;
        }
        .standard-value {
            font-weight: bold;
        }
    </style>
</head>
<body>

<h1>2025 SELECTION CRITERIA FOR BOLAND TEAMS TO THE ASA NAT. T&F CHAMPS</h1>

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

// Define the order of events - using exact names from the database
$eventOrder = [
    // Track events
    '100m', '200m', '400m', '800m', '1500m', '3000m', '5000m', '10000m',
    // Steeplechase events
    '1500m SteepleChase', '2000m SteepleChase', '3000m SteepleChase',
    // Hurdles events
    '90m Hurdles', '100m Hurdles', '110m Hurdles', '300m Hurdles', '400m Hurdles',
    // Jumps
    'HighJump', 'Polevault', 'LongJump', 'TripleJump',
    // Throws
    'Shotput', 'Discus', 'Hammer', 'Javelin',
    // Walks
    '5000m Walk', '10000m Walk', '20km Walk',
    // Combined events
    'Pentathlon', 'Heptathlon', 'Decathlon'
];

// Function to determine if an event is a track event (times are better if lower)
function isTrackEvent($event) {
    $fieldEvents = ['HighJump', 'Polevault', 'LongJump', 'TripleJump', 'Shotput', 'Discus', 'Hammer', 'Javelin',
                 'Pentathlon', 'Heptathlon', 'Decathlon'];
    
    return !in_array($event, $fieldEvents);
}

// Function to convert performance string to a comparable value
function parsePerformance($performance, $isTrack) {
    // Remove any non-numeric characters except for decimal points and colons
    $performance = trim($performance);
    
    // Handle time format (h:m:s.ms)
    if (strpos($performance, ':') !== false) {
        $parts = explode(':', $performance);
        $seconds = 0;
        $multiplier = 1;
        
        for ($i = count($parts) - 1; $i >= 0; $i--) {
            $seconds += floatval($parts[$i]) * $multiplier;
            $multiplier *= 60;
        }
        
        return $seconds;
    }
    
    // Handle numeric format (for field events or simple seconds)
    return floatval($performance);
}

// Function to compare performances based on event type
function comparePerformances($a, $b, $isTrack) {
    $aValue = $a['raw_performance'];
    $bValue = $b['raw_performance'];
    
    if ($isTrack) {
        // Track events: lower is better
        return $aValue - $bValue;
    } else {
        // Field events: higher is better
        return $bValue - $aValue;
    }
}

// Function to get age group based on age
function getAgeGroup($age) {
    if ($age < 16) {
        return "Under 16";
    } elseif ($age < 18) {
        return "Under 18";
    } elseif ($age < 20) {
        return "Under 20";
    } else {
        return "Seniors";
    }
}

function displayStandardsTable($conn, $genderPrefix, $displayGender, $eventOrder) {
    // Define age groups - removed Seniors
    $ageGroups = ["Under 16", "Under 18", "Under 20"];
    
    // Collect standards from database
    $standards = [];
    $events = [];
    
    // Fetch qualifying standards
    $sql = "SELECT * FROM selection_standards WHERE gender LIKE '$genderPrefix%' ORDER BY event";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $event = $row['event'];
            $ageGroup = $row['age_group'];
            $standardType = $row['standard_type'];
            $electronic = $row['electronic_time'];
            $hand = $row['hand_time'];
            
            // Skip seniors
            if ($ageGroup == "Seniors") {
                continue;
            }
            
            // Check if this event is already in our list
            if (!in_array($event, $events) && in_array($event, $eventOrder)) {
                $events[] = $event;
            }
            
            // Add standard to our array
            if (!isset($standards[$event])) {
                $standards[$event] = [];
            }
            if (!isset($standards[$event][$ageGroup])) {
                $standards[$event][$ageGroup] = [];
            }
            
            $standards[$event][$ageGroup][$standardType] = [
                'electronic' => $electronic,
                'hand' => $hand,
                'athletes' => []
            ];
        }
    }
    
    // Sort events according to the defined order
    usort($events, function($a, $b) use ($eventOrder) {
        return array_search($a, $eventOrder) - array_search($b, $eventOrder);
    });
    
    // Fetch athletes who've met the standards
    $sql = "SELECT * FROM results WHERE gender = '$genderPrefix' AND standard LIKE '%QS%' 
            AND doubtful != 1 AND criteria_not_met != 1 AND wind_assisted != 1";
    $result = $conn->query($sql);
    
    // Temporary array to keep track of best performances
    $tempAthletes = [];
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $athleteName = $row['athlete'];
            $team = $row['team_abbr'];
            $age = $row['age'];
            $event = $row['event'];
            $performance = $row['performance'];
            $meet = $row['meet'];
            $standard = $row['standard'];
            
            // Skip if event not in our list
            if (!in_array($event, $events)) {
                continue;
            }
            
            // Determine age group
            $ageGroup = getAgeGroup($age);
            
            // Skip seniors
            if ($ageGroup == "Seniors") {
                continue;
            }
            
            // Determine standard type (A or B)
            $standardType = "";
            if (strpos($standard, "A-QS") !== false) {
                $standardType = "A";
            } elseif (strpos($standard, "B-QS") !== false) {
                $standardType = "B";
            } else {
                continue; // Skip if not a qualifying standard
            }
            
            // Skip if this standard doesn't exist in our array
            if (!isset($standards[$event][$ageGroup][$standardType])) {
                continue;
            }
            
            // Determine if track or field event
            $isTrack = isTrackEvent($event);
            
            // Create a unique key for this athlete/event/ageGroup/standard combination
            $key = $athleteName . "_" . $event . "_" . $ageGroup . "_" . $standardType;
            
            // Parse performance for comparison
            $rawPerformance = parsePerformance($performance, $isTrack);
            
            // Check if we've already seen this athlete
            if (isset($tempAthletes[$key])) {
                $existingPerformance = $tempAthletes[$key]['raw_performance'];
                
                // For track events, keep lower time; for field events, keep higher distance
                if ($isTrack && $rawPerformance < $existingPerformance) {
                    // New performance is better for track (lower time)
                    $tempAthletes[$key] = [
                        'name' => $athleteName,
                        'team' => $team,
                        'performance' => $performance,
                        'raw_performance' => $rawPerformance,
                        'meet' => $meet
                    ];
                } elseif (!$isTrack && $rawPerformance > $existingPerformance) {
                    // New performance is better for field (higher distance/height)
                    $tempAthletes[$key] = [
                        'name' => $athleteName,
                        'team' => $team,
                        'performance' => $performance,
                        'raw_performance' => $rawPerformance,
                        'meet' => $meet
                    ];
                }
            } else {
                // First time seeing this athlete for this event/ageGroup/standard
                $tempAthletes[$key] = [
                    'name' => $athleteName,
                    'team' => $team,
                    'performance' => $performance,
                    'raw_performance' => $rawPerformance,
                    'meet' => $meet
                ];
            }
        }
    }
    
    // Add best performances to standards array
    foreach ($tempAthletes as $key => $athleteData) {
        $keyParts = explode("_", $key);
        $event = $keyParts[1];
        $ageGroup = $keyParts[2];
        $standardType = $keyParts[3];
        
        // Add athlete to the standards array
        $standards[$event][$ageGroup][$standardType]['athletes'][] = $athleteData;
    }
    
    // Sort athletes by performance
    foreach ($events as $event) {
        $isTrack = isTrackEvent($event);
        
        foreach ($ageGroups as $ageGroup) {
            foreach (['A', 'B'] as $standardType) {
                if (isset($standards[$event][$ageGroup][$standardType])) {
                    $athletes = &$standards[$event][$ageGroup][$standardType]['athletes'];
                    if (!empty($athletes)) {
                        usort($athletes, function($a, $b) use ($isTrack) {
                            if ($isTrack) {
                                // Track events: lower is better (sort ascending)
                                return $a['raw_performance'] <=> $b['raw_performance'];
                            } else {
                                // Field events: higher is better (sort descending)
                                return $b['raw_performance'] <=> $a['raw_performance'];
                            }
                        });
                    }
                }
            }
        }
    }

    // Display standards table
    echo "<table>";
    echo "<tr>";
    echo "<th rowspan='2' class='header event'>$displayGender</th>";

    // Headers for age groups
    foreach ($ageGroups as $ageGroup) {
        echo "<th colspan='2' class='header'>$ageGroup</th>";
    }
    echo "</tr>";

    // Headers for standard types
    echo "<tr>";
    foreach ($ageGroups as $ageGroup) {
        echo "<th class='header standard-column'>A</th>";
        echo "<th class='header standard-column'>B</th>";
    }
    echo "</tr>";

    // Display each event
    foreach ($events as $event) {
        echo "<tr>";
        echo "<td class='event'>$event</td>";
        
        // Display standards for each age group
        foreach ($ageGroups as $ageGroup) {
            // A standard
            echo "<td class='standard-a standard-column'>";
            if (isset($standards[$event][$ageGroup]['A'])) {
                echo "<div class='cell-content'>";
                echo "<div class='standard-value'>";
                $electronic = $standards[$event][$ageGroup]['A']['electronic'];
                $hand = $standards[$event][$ageGroup]['A']['hand'];
                
                if ($electronic && $hand) {
                    echo "$electronic/$hand";
                } elseif ($electronic) {
                    echo $electronic;
                } elseif ($hand) {
                    echo $hand;
                } else {
                    echo "-";
                }
                echo "</div>"; // End standard-value
                
                // Display athletes who achieved this standard
                $athletes = $standards[$event][$ageGroup]['A']['athletes'];
                if (!empty($athletes)) {
                    foreach ($athletes as $athlete) {
                        echo "<div class='athlete'>";
                        echo "{$athlete['name']} ({$athlete['team']}) {$athlete['performance']} [{$athlete['meet']}]";
                        echo "</div>";
                    }
                }
                
                echo "</div>"; // End cell-content
            } else {
                echo "-";
            }
            echo "</td>";
            
            // B standard
            echo "<td class='standard-column'>";
            if (isset($standards[$event][$ageGroup]['B'])) {
                echo "<div class='cell-content'>";
                echo "<div class='standard-value'>";
                $electronic = $standards[$event][$ageGroup]['B']['electronic'];
                $hand = $standards[$event][$ageGroup]['B']['hand'];
                
                if ($electronic && $hand) {
                    echo "$electronic/$hand";
                } elseif ($electronic) {
                    echo $electronic;
                } elseif ($hand) {
                    echo $hand;
                } else {
                    echo "-";
                }
                echo "</div>"; // End standard-value
                
                // Display athletes who achieved this standard
                $athletes = $standards[$event][$ageGroup]['B']['athletes'];
                if (!empty($athletes)) {
                    foreach ($athletes as $athlete) {
                        echo "<div class='athlete'>";
                        echo "{$athlete['name']} ({$athlete['team']}) {$athlete['performance']} [{$athlete['meet']}]";
                        echo "</div>";
                    }
                }
                
                echo "</div>"; // End cell-content
            } else {
                echo "-";
            }
            echo "</td>";
        }
        
        echo "</tr>";
    }

    echo "</table>";
}

// Display women's standards
displayStandardsTable($conn, 'Girls', 'WOMEN', $eventOrder);

// Display men's standards
displayStandardsTable($conn, 'Boys', 'MEN', $eventOrder);

// Close the database connection
$conn->close();
?>

<div class="debug">
<h3>Database Table Structure</h3>
<p>Make sure your selection_standards table has the following structure:</p>
<pre>
id - int (Primary Key)
gender - varchar (Example: 'Girls' or 'Boys')
age_group - varchar (Example: 'Under 16', 'Under 18', 'Under 20')
event - varchar (Example: '100m', '200m', etc.)
standard_type - varchar (Either 'A' or 'B')
electronic_time - varchar (Example: '12.49' or '02:17.81')
hand_time - varchar (Example: '12.2' or NULL)
</pre>

<h3>Sample Records</h3>
<p>Here are some sample records that should be in your database:</p>
<pre>
INSERT INTO selection_standards VALUES (NULL, 'Girls', 'Under 16', '100m', 'A', '12.49', '12.2');
INSERT INTO selection_standards VALUES (NULL, 'Girls', 'Under 16', '100m', 'B', '12.78', '12.5');
</pre>
</div>

</body>
</html> 