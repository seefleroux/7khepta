<?php
// Include database connection
require_once 'db_config.php';

try {
    // Get all responses for all questionnaires
    $stmt = $conn->prepare("
        SELECT 
            q.questionnaire_id,
            q.section,
            q.question_text,
            r.response_text
        FROM questions q
        LEFT JOIN responses r ON q.id = r.question_id
        ORDER BY q.questionnaire_id, q.display_order
    ");
    $stmt->execute();
    $allResponses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Organize responses by questionnaire
    $organizedResponses = [];
    foreach ($allResponses as $response) {
        $qid = $response['questionnaire_id'];
        if (!isset($organizedResponses[$qid])) {
            $organizedResponses[$qid] = [];
        }
        if (!isset($organizedResponses[$qid][$response['section']])) {
            $organizedResponses[$qid][$response['section']] = [];
        }
        $organizedResponses[$qid][$response['section']][] = $response;
    }
    
    // Get the strategic plan content (in case there's any custom content)
    $stmt = $conn->prepare("SELECT content FROM strategic_plans WHERE id = 1");
    $stmt->execute();
    $customPlan = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch(Exception $e) {
    $error = $e->getMessage();
}

// Helper function to get questionnaire title
function getQuestionnaireTitle($id) {
    switch($id) {
        case 2: return "Executive Summary";
        case 3: return "Vision and Mission";
        case 4: return "Current State Assessment";
        case 5: return "SWOT Analysis";
        case 6: return "Core Values Alignment";
        case 7: return "High Performance";
        case 8: return "Participation Data";
        case 9: return "Competition and Events";
        default: return "";
    }
}

// Function to get responses for a specific questionnaire
function getResponses($conn, $questionnaire_id) {
    $responses = [];
    try {
        $stmt = $conn->prepare("
            SELECT q.field_name, q.question_text, r.response_text, q.section
            FROM responses r
            JOIN questions q ON r.question_id = q.id
            WHERE r.questionnaire_id = :questionnaire_id
            ORDER BY q.display_order ASC
        ");
        $stmt->bindParam(':questionnaire_id', $questionnaire_id);
        $stmt->execute();
        
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if(!isset($responses[$row['section']])) {
                $responses[$row['section']] = [];
            }
            $responses[$row['section']][] = [
                'field_name' => $row['field_name'],
                'question' => $row['question_text'],
                'response' => $row['response_text']
            ];
        }
    } catch(PDOException $e) {
        // Silently handle error
    }
    return $responses;
}

// Get responses for each questionnaire
$executiveSummary = getResponses($conn, 2);
$visionMission = getResponses($conn, 3);
$currentState = getResponses($conn, 4);
$swotAnalysis = getResponses($conn, 5);
$coreValues = getResponses($conn, 6);
$highPerformance = getResponses($conn, 7);
$participationData = getResponses($conn, 8);
$competitionEvents = getResponses($conn, 9);

// Check completion status of each questionnaire
function isQuestionnaireCompleted($conn, $questionnaireId) {
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM responses WHERE questionnaire_id = :id");
        $stmt->bindParam(':id', $questionnaireId);
        $stmt->execute();
        return ($stmt->fetchColumn() > 0);
    } catch(PDOException $e) {
        return false;
    }
}

$executiveSummaryCompleted = isQuestionnaireCompleted($conn, 2);
$visionMissionCompleted = isQuestionnaireCompleted($conn, 3);
$currentStateCompleted = isQuestionnaireCompleted($conn, 4);
$swotAnalysisCompleted = isQuestionnaireCompleted($conn, 5);
$coreValuesCompleted = isQuestionnaireCompleted($conn, 6);
$highPerformanceCompleted = isQuestionnaireCompleted($conn, 7);
$participationDataCompleted = isQuestionnaireCompleted($conn, 8);
$competitionEventsCompleted = isQuestionnaireCompleted($conn, 9);

// Calculate overall completion percentage
$totalQuestionnaires = 9;
$completedQuestionnaires = 0;
if($executiveSummaryCompleted) $completedQuestionnaires++;
if($visionMissionCompleted) $completedQuestionnaires++;
if($currentStateCompleted) $completedQuestionnaires++;
if($swotAnalysisCompleted) $completedQuestionnaires++;
if($coreValuesCompleted) $completedQuestionnaires++;
if($highPerformanceCompleted) $completedQuestionnaires++;
if($participationDataCompleted) $completedQuestionnaires++;
if($competitionEventsCompleted) $completedQuestionnaires++;

$completionPercentage = round(($completedQuestionnaires / $totalQuestionnaires) * 100);

// Organize responses for display
$organizedResponses = array();
$organizedResponses[2] = $executiveSummary;
$organizedResponses[3] = $visionMission;
$organizedResponses[4] = $currentState;
$organizedResponses[5] = $swotAnalysis;
$organizedResponses[6] = $coreValues;
$organizedResponses[7] = $highPerformance;
$organizedResponses[8] = $participationData;
$organizedResponses[9] = $competitionEvents;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strategic Plan - Track & Field Program</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Aptos:wght@400;500;600;700&display=swap">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#255e3e',
                        secondary: '#ffd700',
                    },
                    fontFamily: {
                        'aptos': ['Aptos', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Aptos', sans-serif; }
        @media print {
            .no-print { display: none; }
            .print-break-after { page-break-after: always; }
            body { font-size: 12pt; }
            .prose h2 { margin-top: 2em; }
            .prose h3 { margin-top: 1.5em; }
        }
    </style>
</head>
<body class="bg-gray-100">
    <header class="bg-primary text-white py-6 no-print">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold">Track & Field Strategic Plan</h1>
            <p class="mt-2">Complete Strategic Plan Document</p>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <?php if (isset($error)): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <div class="flex justify-between items-center mb-6 no-print">
                    <h2 class="text-2xl font-bold text-primary">Strategic Plan</h2>
                    <button onclick="window.print()" class="px-4 py-2 bg-primary text-white rounded hover:bg-opacity-90 transition">
                        Print Plan
                    </button>
                </div>

                <div class="prose max-w-none">
                    <!-- Cover Page -->
                    <div class="text-center mb-8 print-break-after">
                        <h1 class="text-3xl font-bold text-primary">Track & Field Program</h1>
                        <h2 class="text-xl text-gray-600">Strategic Plan <?php echo date('Y'); ?></h2>
                        <p class="mt-4">Last Updated: <?php echo date('F j, Y'); ?></p>
                    </div>

                    <!-- Table of Contents -->
                    <div class="mb-8 print-break-after">
                        <h2 class="text-2xl font-bold mb-4">Table of Contents</h2>
                        <ul class="space-y-2">
                            <li><a href="#executive-summary" class="text-primary hover:underline">1. Executive Summary</a></li>
                            <li><a href="#vision-mission" class="text-primary hover:underline">2. Vision and Mission</a></li>
                            <li><a href="#current-state" class="text-primary hover:underline">3. Current State Assessment</a></li>
                            <li><a href="#swot" class="text-primary hover:underline">4. SWOT Analysis</a></li>
                            <li><a href="#core-values" class="text-primary hover:underline">5. Core Values Alignment</a></li>
                            <li><a href="#high-performance" class="text-primary hover:underline">6. High Performance</a></li>
                            <li><a href="#participation-data" class="text-primary hover:underline">7. Participation Data</a></li>
                            <li><a href="#competition-and-events" class="text-primary hover:underline">8. Competition and Events</a></li>
                        </ul>
                    </div>

                    <!-- Plan Content -->
                    <div class="space-y-8">
                        <?php
                        // Display each questionnaire's responses
                        for ($i = 2; $i <= 9; $i++) {
                            if (isset($organizedResponses[$i]) && !empty($organizedResponses[$i])) {
                                $title = getQuestionnaireTitle($i);
                                $anchorId = strtolower(str_replace(' ', '-', $title));
                                $anchorId = str_replace(' and ', '-and-', $anchorId);
                                echo "<div id='$anchorId' class='print-break-after'>";
                                echo "<h2 class='text-2xl font-bold text-primary mb-6'>" . htmlspecialchars($title) . "</h2>";
                                
                                foreach ($organizedResponses[$i] as $section => $responses) {
                                    if ($section) {
                                        echo "<h3 class='text-xl font-bold text-gray-700 mt-6 mb-4'>" . htmlspecialchars($section) . "</h3>";
                                    }
                                    
                                    echo "<div class='space-y-4'>";
                                    foreach ($responses as $response) {
                                        if (isset($response['response']) && $response['response']) {
                                            echo "<div class='mb-4'>";
                                            echo "<p class='font-semibold text-gray-700'>" . htmlspecialchars($response['question']) . "</p>";
                                            echo "<p class='mt-1 text-gray-600'>" . nl2br(htmlspecialchars($response['response'])) . "</p>";
                                            echo "</div>";
                                        }
                                    }
                                    echo "</div>";
                                }
                                echo "</div>";
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            
            <!-- Update completion status to include questionnaire9 -->
            <section class="mb-12">
                <h2 class="text-2xl font-bold mb-6 text-primary border-b pb-2">Strategic Plan Completion Status</h2>
                
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-700">Overall Completion</span>
                            <span class="font-bold text-primary"><?php echo $completionPercentage; ?>%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-primary h-2.5 rounded-full" style="width: <?php echo $completionPercentage; ?>%"></div>
                        </div>
                    </div>
                    
                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th class="text-left py-2 px-4 border-b">Section</th>
                                <th class="text-center py-2 px-4 border-b">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="py-2 px-4 border-b">Executive Summary</td>
                                <td class="py-2 px-4 border-b text-center">
                                    <?php if($executiveSummaryCompleted): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Completed</span>
                                    <?php else: ?>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 px-4 border-b">Vision & Mission</td>
                                <td class="py-2 px-4 border-b text-center">
                                    <?php if($visionMissionCompleted): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Completed</span>
                                    <?php else: ?>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 px-4 border-b">Current State Assessment</td>
                                <td class="py-2 px-4 border-b text-center">
                                    <?php if($currentStateCompleted): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Completed</span>
                                    <?php else: ?>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 px-4 border-b">SWOT Analysis</td>
                                <td class="py-2 px-4 border-b text-center">
                                    <?php if($swotAnalysisCompleted): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Completed</span>
                                    <?php else: ?>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 px-4 border-b">Core Values Alignment</td>
                                <td class="py-2 px-4 border-b text-center">
                                    <?php if($coreValuesCompleted): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Completed</span>
                                    <?php else: ?>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 px-4 border-b">High Performance</td>
                                <td class="py-2 px-4 border-b text-center">
                                    <?php if($highPerformanceCompleted): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Completed</span>
                                    <?php else: ?>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 px-4 border-b">Participation Data</td>
                                <td class="py-2 px-4 border-b text-center">
                                    <?php if($participationDataCompleted): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Completed</span>
                                    <?php else: ?>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 px-4 border-b">Competition and Events</td>
                                <td class="py-2 px-4 border-b text-center">
                                    <?php if($competitionEventsCompleted): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Completed</span>
                                    <?php else: ?>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
            
            <div class="flex justify-between mt-8 no-print">
                <a href="index.php" class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-opacity-90 transition">Back to Home</a>
                <button onclick="window.print()" class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-opacity-90 transition">Print Plan</button>
            </div>
        <?php endif; ?>
    </main>

    <footer class="bg-primary text-white py-4 mt-8 no-print">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; <?php echo date('Y'); ?> Track & Field Strategic Planning System</p>
        </div>
    </footer>
</body>
</html> 