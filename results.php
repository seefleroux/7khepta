<?php
session_start();
// Add error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Results</title>
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
      width: 100vw;
      border-collapse: collapse;
      margin: 0;
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
    .actions {
      display: flex;
      gap: 10px;
    }
    .btn {
      padding: 5px 10px;
      border: none;
      border-radius: 3px;
      cursor: pointer;
    }
    .btn-edit {
      background-color: #4CAF50;
      color: white;
    }
    .btn-delete {
      background-color: #f44336;
      color: white;
    }
    .btn-add {
      background-color: #008CBA;
      color: white;
      margin-bottom: 20px;
    }
    form {
      max-width: 500px;
      margin: 20px 0;
    }
    .form-group {
      margin-bottom: 15px;
    }
    label {
      display: block;
      margin-bottom: 5px;
    }
    input, select {
      width: 100%;
      padding: 8px;
      border: 1px solid #ddd;
      border-radius: 3px;
    }
    .modal {
      display: none;
      position: fixed;
      z-index: 1;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.4);
    }
    .modal-content {
      background-color: #fefefe;
      margin: 5% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 80%;
      max-width: 600px;
      border-radius: 5px;
    }
    .modal-top {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 20px;
    }
    .modal-top h2 {
      margin: 0 0 10px 0;
    }
    .modal-top .checkboxes {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      align-items: center;
    }
    .modal-top .checkboxes label {
      margin: 0;
      font-weight: bold;
    }
    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
    }
    .close:hover,
    .close:focus {
      color: black;
      text-decoration: none;
      cursor: pointer;
    }
    .strikeout {
      text-decoration: line-through;
      color: red;
    }
    .header-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px;
      width: 100vw;
      box-sizing: border-box;
    }
  </style>
</head>
<body>
  <div class="header-container">
    <h1>Manage Results</h1>
    <button class="btn btn-add" onclick="openModal()">Add Result</button>
  </div>

  <!-- The Modal -->
  <div id="addModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <!-- The form now wraps the entire content including the top section -->
      <form method="post" id="resultForm">
        <div class="modal-top">
          <h2 id="modalTitle">Add New Result</h2>
          <div class="checkboxes">
            <label><input type="checkbox" name="doubtful" id="doubtfulCheckbox"> Doubtful</label>
            <label><input type="checkbox" name="criteria_not_met" id="criteriaCheckbox"> Criteria not Met</label>
            <label><input type="checkbox" name="wind_assisted" id="windCheckbox"> Wind Assisted</label>
          </div>
          <button type="submit" name="submit" class="btn btn-add">Save Result</button>
        </div>
        <input type="hidden" name="id" id="athleteId">
        <!-- Hidden field to store scroll position -->
        <input type="hidden" name="scrollPosition" id="scrollPosition">
        <div class="form-group">
          <label>Athlete:</label>
          <input type="text" name="athlete" id="athleteName" required>
        </div>
        <div class="form-group">
          <label>Team Abbreviation:</label>
          <input type="text" name="team_abbr" id="teamAbbr" required>
        </div>
        <div class="form-group">
          <label>Team:</label>
          <input type="text" name="team" id="teamName" required>
        </div>
        <div class="form-group">
          <label>Gender:</label>
          <select name="gender" id="genderSelect" required>
            <option value="Boys">Boys</option>
            <option value="Girls">Girls</option>
            <option value="Men">Men</option>
            <option value="Women">Women</option>
          </select>
        </div>
        <div class="form-group">
          <label>Age:</label>
          <input type="number" name="age" id="athleteAge" required>
        </div>
        <div class="form-group">
          <label>Event:</label>
          <input type="text" name="event" id="eventName" required>
        </div>
        <div class="form-group">
          <label>Performance:</label>
          <input type="text" name="performance" id="performanceValue" required>
        </div>
        <div class="form-group">
          <label>Standard:</label>
          <input type="text" name="standard" id="standardValue" required>
        </div>
        <div class="form-group">
          <label>Meet:</label>
          <input type="text" name="meet" id="meetName" required>
        </div>
      </form>
    </div>
  </div>

  <?php
  // Store the scroll position when form is submitted
  if (isset($_POST['scrollPosition'])) {
      $_SESSION['scrollPosition'] = $_POST['scrollPosition'];
  }

  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "boland";

  $conn = new mysqli($servername, $username, $password, $dbname);

  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }

  // Handle Add/Edit
  if (isset($_POST['submit'])) {
      $doubtful = isset($_POST['doubtful']) ? 1 : 0;
      $criteria_not_met = isset($_POST['criteria_not_met']) ? 1 : 0;
      $wind_assisted = isset($_POST['wind_assisted']) ? 1 : 0;

      if (!empty($_POST['id'])) {
          // Edit
          $sql = "UPDATE results SET 
                  athlete = ?, team_abbr = ?, team = ?, gender = ?, 
                  age = ?, event = ?, performance = ?, standard = ?, meet = ?,
                  doubtful = ?, criteria_not_met = ?, wind_assisted = ?
                  WHERE id = ?";
          $stmt = $conn->prepare($sql);
          $stmt->bind_param("ssssissssiiis", 
              $_POST['athlete'], $_POST['team_abbr'], $_POST['team'], 
              $_POST['gender'], $_POST['age'], $_POST['event'], 
              $_POST['performance'], $_POST['standard'], $_POST['meet'], 
              $doubtful, $criteria_not_met, $wind_assisted, $_POST['id']);
          
          if (!$stmt->execute()) {
              echo "Error updating record: " . $stmt->error;
          }
      } else {
          // Add
          $sql = "INSERT INTO results 
                  (athlete, team_abbr, team, gender, age, event, performance, standard, meet, doubtful, criteria_not_met, wind_assisted) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
          $stmt = $conn->prepare($sql);
          $stmt->bind_param("ssssissssiii", 
              $_POST['athlete'], $_POST['team_abbr'], $_POST['team'], 
              $_POST['gender'], $_POST['age'], $_POST['event'], 
              $_POST['performance'], $_POST['standard'], $_POST['meet'], 
              $doubtful, $criteria_not_met, $wind_assisted);
          
          if (!$stmt->execute()) {
              echo "Error adding record: " . $stmt->error;
          }
      }
      // Redirect preserving the scroll query so that we know to scroll on page load
      echo "<script>window.location.href = '" . $_SERVER['PHP_SELF'] . "?scroll=true';</script>";
      exit();
  }

  // Display Results Table
  $sql = "SELECT * FROM results 
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

  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
      echo "<div class='container'>";
      echo "<table id='resultsTable'>";
      echo "<tr>
              <th>Athlete</th>
              <th>Team</th>
              <th>Gender</th>
              <th>Age</th>
              <th>Event</th>
              <th>Performance</th>
              <th>Standard</th>
              <th>Meet</th>
              <th>Actions</th>
            </tr>";
      
      while($row = $result->fetch_assoc()) {
          $rowClass = '';
          if ($row['doubtful'] || $row['criteria_not_met'] || $row['wind_assisted']) {
              $rowClass = 'strikeout'; // Apply strikeout class if any checkbox is checked
          }
          echo "<tr class='$rowClass'>";
          echo "<td>" . $row['athlete'] . "</td>";
          echo "<td>" . $row['team_abbr'] . " - " . $row['team'] . "</td>";
          echo "<td>" . $row['gender'] . "</td>";
          echo "<td>" . $row['age'] . "</td>";
          echo "<td>" . $row['event'] . "</td>";
          echo "<td>" . $row['performance'] . "</td>";
          echo "<td>" . $row['standard'] . "</td>";
          echo "<td>" . $row['meet'] . "</td>";
          echo "<td class='actions'>";
          echo "<button class='btn btn-edit' onclick='editResult(" . $row['id'] . ")'>Edit</button>";
          echo "</td>";
          echo "</tr>";
      }
      echo "</table>";
      echo "</div>";
  } else {
      echo "No results found";
  }

  $conn->close();
  ?>

  <script>
    // Global variable to store the scroll position while the modal is open
    var lastScrollPosition = 0;
    var modal = document.getElementById("addModal");
    var scrollPositionField = document.getElementById("scrollPosition");

    function openModal() {
      // Store the current scroll position
      lastScrollPosition = window.scrollY;
      modal.style.display = "block";
      document.getElementById("modalTitle").innerText = "Add New Result";
      document.getElementById("resultForm").reset(); // Reset the form
      document.getElementById("athleteId").value = ""; // Clear hidden ID
      // Clear checkboxes
      document.getElementById("doubtfulCheckbox").checked = false;
      document.getElementById("criteriaCheckbox").checked = false;
      document.getElementById("windCheckbox").checked = false;
      // Update the hidden scroll position field
      if(scrollPositionField) {
          scrollPositionField.value = lastScrollPosition;
      }
    }

    function closeModal() {
      modal.style.display = "none";
      // Restore the previous scroll position smoothly
      window.scrollTo({ top: lastScrollPosition, behavior: "smooth" });
    }

    // Close modal when clicking outside of it
    window.onclick = function(event) {
      if (event.target == modal) {
          closeModal();
      }
    }

    function editResult(id) {
      // Store current scroll before editing
      lastScrollPosition = window.scrollY;
      if(scrollPositionField) {
          scrollPositionField.value = lastScrollPosition;
      }
      // Fetch the athlete's data using AJAX or a form submission
      fetch('get_result.php?id=' + id)
          .then(response => response.json())
          .then(data => {
              // Populate the form with the fetched data
              document.getElementById("athleteId").value = data.id;
              document.getElementById("athleteName").value = data.athlete;
              document.getElementById("teamAbbr").value = data.team_abbr;
              document.getElementById("teamName").value = data.team;
              document.getElementById("genderSelect").value = data.gender;
              document.getElementById("athleteAge").value = data.age;
              document.getElementById("eventName").value = data.event;
              document.getElementById("performanceValue").value = data.performance;
              document.getElementById("standardValue").value = data.standard;
              document.getElementById("meetName").value = data.meet;
              document.getElementById("doubtfulCheckbox").checked = data.doubtful == 1;
              document.getElementById("criteriaCheckbox").checked = data.criteria_not_met == 1;
              document.getElementById("windCheckbox").checked = data.wind_assisted == 1;

              // Change modal title and show modal
              document.getElementById("modalTitle").innerText = "Edit Result";
              modal.style.display = "block";
          });
    }

    // Add an event listener on form submission to store current scroll position
    document.getElementById("resultForm").addEventListener("submit", function(e) {
      // Update the hidden field with the current scroll position
      if(scrollPositionField) {
          scrollPositionField.value = window.scrollY;
      }
    });
  </script>
  
  <?php
  // If the redirect includes a scroll param and a scroll position was stored in the session,
  // append a script that scrolls the page to the stored position on load.
  if (isset($_GET['scroll']) && isset($_SESSION['scrollPosition'])) {
      $scroll = (int) $_SESSION['scrollPosition'];
      unset($_SESSION['scrollPosition']);
      echo "<script>
          window.addEventListener('load', function() {
              window.scrollTo({ top: " . $scroll . ", behavior: 'smooth' });
          });
      </script>";
  }
  ?>

</body>
</html>