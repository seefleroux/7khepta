<?php
// Include database connection
require_once 'db_config.php';

// Check if the strategic plan exists
$planExists = false;
try {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM strategic_plans WHERE id = 1");
    $stmt->execute();
    $planExists = ($stmt->fetchColumn() > 0);
} catch(PDOException $e) {
    // Silently handle error
}

// Get questionnaire completion status
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
$commercialFinancialCompleted = isQuestionnaireCompleted($conn, 10);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track & Field Strategic Plan</title>
    <!-- Tailwind CSS from CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Aptos Display font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Aptos:wght@400;500;600;700&display=swap">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#255e3e',
                        secondary: '#ffd700', // Gold color
                    },
                    fontFamily: {
                        'aptos': ['Aptos', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Aptos', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <header class="bg-primary text-white py-6">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold">Track & Field Strategic Plan</h1>
            <p class="mt-2">Building excellence through strategic planning</p>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-bold mb-4 text-primary">Welcome to the Strategic Planning System</h2>
            <p class="mb-4">This system helps build a comprehensive strategic plan for your high school track and field program. Complete the questionnaires below to customize your strategic plan.</p>
            <?php if($planExists): ?>
                <div class="mt-4 p-4 bg-green-50 text-green-800 rounded-md border border-green-200">
                    <p class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Your strategic plan has been created and is ready to view!
                    </p>
                </div>
            <?php endif; ?>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4 text-primary">Questionnaires</h2>
                
                <!-- Strategic Foundation -->
                <h3 class="font-bold text-gray-700 mb-2 mt-4">Strategic Foundation</h3>
                <div class="space-y-3 mb-6">
                    <a href="questionnaire2.php" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-secondary transition <?php echo $executiveSummaryCompleted ? 'border-green-300 bg-green-50' : ''; ?>">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-primary">Executive Summary</h3>
                            <?php if($executiveSummaryCompleted): ?>
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Completed</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-gray-600 text-sm">Overview of the strategic plan</p>
                    </a>
                    
                    <a href="questionnaire3.php" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-secondary transition <?php echo $visionMissionCompleted ? 'border-green-300 bg-green-50' : ''; ?>">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-primary">Vision & Mission</h3>
                            <?php if($visionMissionCompleted): ?>
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Completed</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">Available</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-gray-600 text-sm">Define program vision and mission</p>
                    </a>
                    
                    <a href="questionnaire4.php" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-secondary transition <?php echo $currentStateCompleted ? 'border-green-300 bg-green-50' : ''; ?>">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-primary">Current State Assessment</h3>
                            <?php if($currentStateCompleted): ?>
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Completed</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">Available</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-gray-600 text-sm">Analyze your current program state</p>
                    </a>
                    
                    <a href="questionnaire5.php" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-secondary transition <?php echo $swotAnalysisCompleted ? 'border-green-300 bg-green-50' : ''; ?>">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-primary">SWOT Analysis</h3>
                            <?php if($swotAnalysisCompleted): ?>
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Completed</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">Available</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-gray-600 text-sm">Evaluate position</p>
                    </a>
                    
                    <a href="questionnaire6.php" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-secondary transition <?php echo $coreValuesCompleted ? 'border-green-300 bg-green-50' : ''; ?>">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-primary">Core Values Alignment</h3>
                            <?php if($coreValuesCompleted): ?>
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Completed</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">Available</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-gray-600 text-sm">Define and align with core values</p>
                    </a>
                </div>
                
                <!-- Strategic Pillars -->
                <h3 class="font-bold text-gray-700 mb-2 mt-4">Strategic Pillars</h3>
                <div class="space-y-3 mb-6">
                    <div class="block p-4 border border-gray-200 rounded-lg opacity-70">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-primary">Strategic Objectives</h3>
                            <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">Coming Soon</span>
                        </div>
                        <p class="text-gray-600 text-sm">Define key program objectives</p>
                    </div>
                    
                    <a href="questionnaire7.php" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-secondary transition <?php echo $highPerformanceCompleted ? 'border-green-300 bg-green-50' : ''; ?>">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-primary">High Performance</h3>
                            <?php if($highPerformanceCompleted): ?>
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Completed</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">Available</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-gray-600 text-sm">Elite athlete development</p>
                    </a>
                    
                    <a href="questionnaire8.php" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-secondary transition <?php echo $participationDataCompleted ? 'border-green-300 bg-green-50' : ''; ?>">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-primary">Participation Data</h3>
                            <?php if($participationDataCompleted): ?>
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Completed</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">Available</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-gray-600 text-sm">Analyze participation trends</p>
                    </a>
                    
                    <a href="questionnaire9.php" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-secondary transition <?php echo $competitionEventsCompleted ? 'border-green-300 bg-green-50' : ''; ?>">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-primary">Competition & Events</h3>
                            <?php if($competitionEventsCompleted): ?>
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Completed</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">Available</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-gray-600 text-sm">Assess competition and event strategies</p>
                    </a>
                    
                    <!-- New Questionnaire Link -->
                    <a href="questionnaire10.php" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-secondary transition <?php echo $commercialFinancialCompleted ? 'border-green-300 bg-green-50' : ''; ?>">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-primary">Commercial & Financial</h3>
                            <?php if($commercialFinancialCompleted): ?>
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Completed</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">Available</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-gray-600 text-sm">Evaluate commercial and financial aspects</p>
                    </a>
                </div>
                
                <!-- Resources & Implementation -->
                <h3 class="font-bold text-gray-700 mb-2 mt-4">Resources & Implementation</h3>
                <div class="space-y-3">
                    <div class="block p-4 border border-gray-200 rounded-lg opacity-70">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-primary">Governance Structure</h3>
                            <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">Coming Soon</span>
                        </div>
                        <p class="text-gray-600 text-sm">Program leadership and organization</p>
                    </div>
                    
                    <div class="block p-4 border border-gray-200 rounded-lg opacity-70">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-primary">Budget & Resources</h3>
                            <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">Coming Soon</span>
                        </div>
                        <p class="text-gray-600 text-sm">Financial planning and allocation</p>
                    </div>
                    
                    <div class="block p-4 border border-gray-200 rounded-lg opacity-70">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-primary">Facilities & Equipment</h3>
                            <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">Coming Soon</span>
                        </div>
                        <p class="text-gray-600 text-sm">Infrastructure assessment and needs</p>
                    </div>
                    
                    <div class="block p-4 border border-gray-200 rounded-lg opacity-70">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-primary">Coaching & Officials</h3>
                            <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">Coming Soon</span>
                        </div>
                        <p class="text-gray-600 text-sm">Personnel development strategies</p>
                    </div>
                    
                    <div class="block p-4 border border-gray-200 rounded-lg opacity-70">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-primary">Implementation Plan</h3>
                            <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">Coming Soon</span>
                        </div>
                        <p class="text-gray-600 text-sm">Year 1 action plan and priorities</p>
                    </div>
                    
                    <div class="block p-4 border border-gray-200 rounded-lg opacity-70">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-primary">Roles & Responsibilities</h3>
                            <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">Coming Soon</span>
                        </div>
                        <p class="text-gray-600 text-sm">Implementation accountability matrix</p>
                    </div>
                </div>
                
                <!-- Measurement & Success -->
                <h3 class="font-bold text-gray-700 mb-2 mt-4">Measurement & Success</h3>
                <div class="space-y-3">
                    <div class="block p-4 border border-gray-200 rounded-lg opacity-70">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-primary">Key Performance Indicators</h3>
                            <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">Coming Soon</span>
                        </div>
                        <p class="text-gray-600 text-sm">Success metrics and evaluation</p>
                    </div>
                    
                    <div class="block p-4 border border-gray-200 rounded-lg opacity-70">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-primary">Risk Assessment</h3>
                            <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">Coming Soon</span>
                        </div>
                        <p class="text-gray-600 text-sm">Risk management framework</p>
                    </div>
                    
                    <div class="block p-4 border border-gray-200 rounded-lg opacity-70">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-primary">Long-term Success Factors</h3>
                            <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">Coming Soon</span>
                        </div>
                        <p class="text-gray-600 text-sm">Path to sustainable excellence</p>
                    </div>
                    
                    <div class="block p-4 border border-gray-200 rounded-lg opacity-70">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-primary">Stakeholder Expectations</h3>
                            <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">Coming Soon</span>
                        </div>
                        <p class="text-gray-600 text-sm">Engagement and alignment of key groups</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4 text-primary">Strategic Plan Output</h2>
                <?php if($planExists): ?>
                    <p class="mb-4">Your strategic plan is available for viewing. You can continue to refine it by completing additional questionnaires.</p>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="view_plan.php" class="inline-block px-6 py-3 bg-primary text-white rounded-lg hover:bg-opacity-90 transition text-center">View Current Plan</a>
                        <button onclick="window.print()" class="inline-block px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-center">Print Plan</button>
                    </div>
                    <div class="mt-6 p-4 bg-yellow-50 rounded-md border border-yellow-200">
                        <h3 class="text-sm font-bold text-yellow-800 mb-2">Completion Status:</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-sm">Current State Assessment</span>
                                <?php if($currentStateCompleted): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Completed</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">Pending</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm">Strategic Pillars (5)</span>
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">0 of 5 Complete</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm">Implementation Elements (6)</span>
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">0 of 6 Complete</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm">Overall Completion</span>
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">5%</span>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="mb-4">After completing the questionnaires, your strategic plan will be available here.</p>
                    <div class="p-6 bg-gray-50 rounded-md border border-gray-200 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-gray-600">Complete at least one questionnaire to generate your strategic plan</p>
                        <a href="questionnaire1.php" class="mt-4 inline-block px-6 py-2 bg-primary text-white rounded-lg hover:bg-opacity-90 transition">Start Current State Assessment</a>
                    </div>
                <?php endif; ?>
                
                <div class="mt-8">
                    <h3 class="font-bold text-primary mb-3">Strategic Plan Framework</h3>
                    <img src="https://via.placeholder.com/600x300?text=Strategic+Plan+Framework" alt="Strategic Plan Framework" class="w-full rounded-lg shadow-sm">
                    <p class="text-sm text-gray-600 mt-2">The framework will be populated as you complete questionnaires.</p>
                </div>
            </div>
        </div>
        
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4 text-primary">Strategic Planning Process</h2>
            <div class="grid md:grid-cols-4 gap-4">
                <div class="p-4 border border-gray-200 rounded-lg text-center">
                    <div class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-primary bg-opacity-10 text-primary mb-4">1</div>
                    <h3 class="font-bold">Assessment</h3>
                    <p class="text-sm text-gray-600">Evaluate your current program state</p>
                </div>
                <div class="p-4 border border-gray-200 rounded-lg text-center">
                    <div class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-primary bg-opacity-10 text-primary mb-4">2</div>
                    <h3 class="font-bold">Strategic Direction</h3>
                    <p class="text-sm text-gray-600">Define vision, mission & pillars</p>
                </div>
                <div class="p-4 border border-gray-200 rounded-lg text-center">
                    <div class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-primary bg-opacity-10 text-primary mb-4">3</div>
                    <h3 class="font-bold">Implementation Plan</h3>
                    <p class="text-sm text-gray-600">Create actionable roadmap</p>
                </div>
                <div class="p-4 border border-gray-200 rounded-lg text-center">
                    <div class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-primary bg-opacity-10 text-primary mb-4">4</div>
                    <h3 class="font-bold">Evaluation Framework</h3>
                    <p class="text-sm text-gray-600">Track progress & measure success</p>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-primary text-white py-4 mt-8">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; <?php echo date('Y'); ?> Track & Field Strategic Planning System</p>
        </div>
    </footer>
</body>
</html>
