<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results Per Team</title>
    <link href="https://fonts.cdnfonts.com/css/aptos" rel="stylesheet">
    <style>
        body {
            font-family: 'Aptos Display', sans-serif;
            margin: 0;
            padding: 0;
            width: 100vw;
            overflow-x: hidden;
        }
        
        .team-section {
            margin: 20px 0;
            width: 100vw;
        }
        
        .team-header {
            background-color: #f8f9fa;
            padding: 10px 20px;
            border-bottom: 2px solid #000;
        }
        
        table {
            width: 100vw;
            border-collapse: collapse;
            margin: 0 0 40px 0;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: black;
            color: white;
        }
        
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            width: 100vw;
            box-sizing: border-box;
        }
        
        .number-column,
        .rank-column {
            width: 40px;
            text-align: right;
            padding-right: 15px;
        }
    </style>
</head>
<body>
    <div class="header-container">
        <h1>Results Per Team</h1>
    </div>

    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "boland";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get unique team names from results
    $teamSql = "SELECT DISTINCT team FROM results ORDER BY team";
    $teamResult = $conn->query($teamSql);

    // Prepare SQL statement for team results filtering out disabled records
    $resultsSql = "SELECT * FROM results 
                   WHERE team = ? 
                     AND doubtful = 0 
                     AND criteria_not_met = 0 
                     AND wind_assisted = 0
                   ORDER BY 
                       athlete ASC,
                       event ASC,
                       CASE 
                           WHEN event LIKE '%m' OR event LIKE '%Walk' OR event LIKE '%Chase' 
                           THEN CAST(
                               CASE 
                                   WHEN performance LIKE '%:%' 
                                   THEN TIME_TO_SEC(performance)
                                   ELSE performance 
                               END AS DECIMAL(10,2)
                           )
                           ELSE -CAST(performance AS DECIMAL(10,2))
                       END ASC";

    while ($teamRow = $teamResult->fetch_assoc()) {
        $stmt = $conn->prepare($resultsSql);
        $stmt->bind_param("s", $teamRow['team']);
        $stmt->execute();
        $results = $stmt->get_result();

        // Only output a team section if there are valid results
        if ($results->num_rows > 0) {
            echo "<div class='team-section'>";
            echo "<h2 class='team-header'>" . $teamRow['team'] . "</h2>";

            echo "<table>";
            echo "<tr>
                    <th class='number-column'>#</th>
                    <th>Athlete</th>
                    <th>Gender</th>
                    <th>Age</th>
                    <th>Event</th>
                    <th>Performance</th>
                    <th>Standard</th>
                    <th>Meet</th>
                  </tr>";

            $currentAthlete = '';
            $athleteNumber = 0;
            while ($row = $results->fetch_assoc()) {
                echo "<tr>";
                // Only increment number for a new athlete
                if ($currentAthlete != $row['athlete']) {
                    $athleteNumber++;
                    $currentAthlete = $row['athlete'];
                    echo "<td class='number-column'>" . $athleteNumber . ".</td>";
                } else {
                    echo "<td class='number-column'></td>";
                }
                echo "<td>" . $row['athlete'] . "</td>";
                echo "<td>" . $row['gender'] . "</td>";
                echo "<td>" . $row['age'] . "</td>";
                echo "<td>" . $row['event'] . "</td>";
                echo "<td>" . $row['performance'] . "</td>";
                echo "<td>" . $row['standard'] . "</td>";
                echo "<td>" . $row['meet'] . "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
            echo "</div>";
        }
    }

    // Display summary table: total number of athletes per team
    $summarySql = "SELECT team, COUNT(DISTINCT athlete) AS total_athletes 
                   FROM results 
                   WHERE doubtful = 0 
                     AND criteria_not_met = 0 
                     AND wind_assisted = 0
                   GROUP BY team
                   ORDER BY total_athletes DESC";
    $summaryResult = $conn->query($summarySql);

    if ($summaryResult->num_rows > 0) {
        echo "<div class='team-section'>";
        echo "<h2 class='team-header'>Athlete Totals Per Team</h2>";
        echo "<table>";
        echo "<tr>
                <th class='rank-column'>Rank</th>
                <th>Team</th>
                <th>Total Athletes</th>
              </tr>";

        $rank = 0;
        while ($row = $summaryResult->fetch_assoc()) {
            $rank++;
            echo "<tr>";
            echo "<td class='rank-column'>" . $rank . ".</td>";
            echo "<td>" . $row['team'] . "</td>";
            echo "<td>" . $row['total_athletes'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
    }

    $conn->close();
    ?>

</body>
</html>