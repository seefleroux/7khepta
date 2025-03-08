<?php
// Include database connection
require_once 'db_config.php';

// Function to get existing responses
function getExistingResponses($conn, $questionnaire_id) {
    $responses = array();
    try {
        $stmt = $conn->prepare("
            SELECT q.field_name, r.response_text 
            FROM responses r
            JOIN questions q ON r.question_id = q.id
            WHERE r.questionnaire_id = :questionnaire_id
        ");
        $stmt->bindParam(':questionnaire_id', $questionnaire_id);
        $stmt->execute();
        
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $responses[$row['field_name']] = $row['response_text'];
        }
    } catch(PDOException $e) {
        // Silently handle error
    }
    return $responses;
}

// Get existing responses for this questionnaire
$responses = getExistingResponses($conn, 3);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vision & Mission Questionnaire - Track & Field Strategic Plan</title>
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
            <p class="mt-2">Vision & Mission Questionnaire</p>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-bold mb-4 text-primary">Vision & Mission</h2>
            <p class="mb-4">This questionnaire will help articulate the vision and mission for your athletics program. Your responses will shape the core direction of your strategic plan.</p>
            
            <div class="bg-gray-50 p-4 rounded-md mb-6">
                <h3 class="font-bold text-primary">Why this matters</h3>
                <p class="text-gray-600 text-sm">A compelling vision and clear mission are essential foundations for any successful strategic plan. They provide direction, inspire stakeholders, and create a framework for decision-making. Your responses will help craft vision and mission statements that are both aspirational and achievable.</p>
            </div>
            
            <?php if(!empty($responses)): ?>
            <div class="bg-blue-50 p-4 rounded-md mb-6 border border-blue-200">
                <p class="text-blue-800"><strong>Note:</strong> Your previous responses have been loaded. You can review and modify them as needed.</p>
            </div>
            <?php endif; ?>
        </div>

        <form action="process_questionnaire3.php" method="post" class="space-y-8">
            <!-- Strategic Vision -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4 text-primary border-b pb-2">Strategic Vision</h2>
                
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700 font-semibold">1. How would you articulate the ultimate aspiration for Paarl Gim Athletics in one sentence?</label>
                    <p class="text-gray-600 text-sm mb-2">What is the most ambitious yet achievable vision?</p>
                    <textarea name="ultimate_aspiration" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"><?php echo isset($responses['ultimate_aspiration']) ? htmlspecialchars($responses['ultimate_aspiration']) : ''; ?></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700 font-semibold">2. What specific position should athletics occupy within the school's overall sports program in 5 years?</label>
                    <p class="text-gray-600 text-sm mb-2">e.g., largest by participation, highest achieving, most recognized</p>
                    <textarea name="desired_position" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"><?php echo isset($responses['desired_position']) ? htmlspecialchars($responses['desired_position']) : ''; ?></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700 font-semibold">20. What aspirational language would resonate most with the Paarl Gim community?</label>
                    <p class="text-gray-600 text-sm mb-2">Consider language that remains authentic and achievable</p>
                    <textarea name="aspirational_language" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"><?php echo isset($responses['aspirational_language']) ? htmlspecialchars($responses['aspirational_language']) : ''; ?></textarea>
                </div>
            </div>
            
            <!-- Position and Growth -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4 text-primary border-b pb-2">Position and Growth</h2>
                
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700 font-semibold">3. What timeframe is realistic for athletics to become the largest sport at Paarl Gim?</label>
                    <p class="text-gray-600 text-sm mb-2">What metrics will define "largest"?</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2">
                        <div>
                            <label class="block mb-1 text-gray-600 text-sm">Realistic timeframe (years)</label>
                            <input type="number" name="timeframe_years" min="1" max="10" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" value="<?php echo isset($responses['timeframe_years']) ? htmlspecialchars($responses['timeframe_years']) : ''; ?>">
                        </div>
                    </div>
                    <textarea name="largest_metrics" rows="3" placeholder="Metrics that will define 'largest'" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"><?php echo isset($responses['largest_metrics']) ? htmlspecialchars($responses['largest_metrics']) : ''; ?></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700 font-semibold">7. What specific metrics would indicate that athletics has become the largest sport at the school?</label>
                    <p class="text-gray-600 text-sm mb-2">total participants, active athletes, competition entries, etc.</p>
                    <textarea name="success_metrics" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"><?php echo isset($responses['success_metrics']) ? htmlspecialchars($responses['success_metrics']) : ''; ?></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700 font-semibold">8. How many active participants would represent a successful achievement of the vision?</label>
                    <p class="text-gray-600 text-sm mb-2">What would be the ideal distribution across age groups and genders?</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2">
                        <div>
                            <label class="block mb-1 text-gray-600 text-sm">Target number of active participants</label>
                            <input type="number" name="target_participants" min="1" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" value="<?php echo isset($responses['target_participants']) ? htmlspecialchars($responses['target_participants']) : ''; ?>">
                        </div>
                    </div>
                    <textarea name="participant_distribution" rows="3" placeholder="Ideal distribution across age groups and genders" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"><?php echo isset($responses['participant_distribution']) ? htmlspecialchars($responses['participant_distribution']) : ''; ?></textarea>
                </div>
            </div>
            
            <!-- Performance and Resources -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4 text-primary border-b pb-2">Performance and Resources</h2>
                
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700 font-semibold">4. What level of financial support is realistically achievable for athletics in 5 years?</label>
                    <p class="text-gray-600 text-sm mb-2">What should be the funding sources mix (school budget, external funding, self-generated)?</p>
                    <textarea name="financial_support" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"><?php echo isset($responses['financial_support']) ? htmlspecialchars($responses['financial_support']) : ''; ?></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700 font-semibold">9. What international success level is realistically achievable within 5 years?</label>
                    <p class="text-gray-600 text-sm mb-2">What specific events or disciplines have the most potential?</p>
                    <textarea name="international_potential" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"><?php echo isset($responses['international_potential']) ? htmlspecialchars($responses['international_potential']) : ''; ?></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700 font-semibold">22. How should the vision address the long-term financial sustainability of the athletics program?</label>
                    <textarea name="financial_sustainability" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"><?php echo isset($responses['financial_sustainability']) ? htmlspecialchars($responses['financial_sustainability']) : ''; ?></textarea>
                </div>
            </div>
            
            <!-- Program Objectives -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4 text-primary border-b pb-2">Program Objectives</h2>
                
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700 font-semibold">5. What specific types of competitions would best serve the program's objectives?</label>
                    <p class="text-gray-600 text-sm mb-2">What competition formats would drive both participation and excellence?</p>
                    <textarea name="competition_types" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"><?php echo isset($responses['competition_types']) ? htmlspecialchars($responses['competition_types']) : ''; ?></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700 font-semibold">6. How do you define "inclusivity" in the context of Paarl Gim Athletics?</label>
                    <p class="text-gray-600 text-sm mb-2">What groups or demographics are currently underrepresented?</p>
                    <textarea name="inclusivity_definition" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"><?php echo isset($responses['inclusivity_definition']) ? htmlspecialchars($responses['inclusivity_definition']) : ''; ?></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700 font-semibold">13. How does becoming the "largest" sport affect the mission regarding elite performance?</label>
                    <p class="text-gray-600 text-sm mb-2">How will both mass participation and elite excellence be balanced?</p>
                    <textarea name="participation_excellence_balance" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"><?php echo isset($responses['participation_excellence_balance']) ? htmlspecialchars($responses['participation_excellence_balance']) : ''; ?></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700 font-semibold">17. What specific performance outcomes should be referenced in the vision or mission statements?</label>
                    <textarea name="performance_outcomes" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"><?php echo isset($responses['performance_outcomes']) ? htmlspecialchars($responses['performance_outcomes']) : ''; ?></textarea>
                </div>
            </div>
            
            <!-- Impact and Alignment -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4 text-primary border-b pb-2">Impact and Alignment</h2>
                
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700 font-semibold">10. How should the athletics program's purpose align with the school's broader educational mission?</label>
                    <p class="text-gray-600 text-sm mb-2">What educational outcomes should athletics support?</p>
                    <textarea name="educational_alignment" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"><?php echo isset($responses['educational_alignment']) ? htmlspecialchars($responses['educational_alignment']) : ''; ?></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700 font-semibold">11. What lasting impact should the athletics program have on participants after they leave school?</label>
                    <p class="text-gray-600 text-sm mb-2">What life skills or values should it develop?</p>
                    <textarea name="lasting_impact" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"><?php echo isset($responses['lasting_impact']) ? htmlspecialchars($responses['lasting_impact']) : ''; ?></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700 font-semibold">16. What transformative impact should the athletics program have on the school's overall identity and reputation?</label>
                    <textarea name="school_identity_impact" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"><?php echo isset($responses['school_identity_impact']) ? htmlspecialchars($responses['school_identity_impact']) : ''; ?></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700 font-semibold">18. What unique aspects of Paarl Gim should be reflected in the athletics program's vision and mission?</label>
                    <textarea name="unique_aspects" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"><?php echo isset($responses['unique_aspects']) ? htmlspecialchars($responses['unique_aspects']) : ''; ?></textarea>
                </div>
            </div>
            
            <!-- Stakeholder Engagement -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4 text-primary border-b pb-2">Stakeholder Engagement</h2>
                
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700 font-semibold">12. What elements of the athletics experience are most important to emphasize in the mission statement?</label>
                    <textarea name="important_elements" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"><?php echo isset($responses['important_elements']) ? htmlspecialchars($responses['important_elements']) : ''; ?></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700 font-semibold">14. What key stakeholders should be directly mentioned or addressed in the mission statement?</label>
                    <textarea name="key_stakeholders" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"><?php echo isset($responses['key_stakeholders']) ? htmlspecialchars($responses['key_stakeholders']) : ''; ?></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700 font-semibold">19. How should the relationship between athletics and other sports be characterized in the vision/mission?</label>
                    <textarea name="sport_relationships" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"><?php echo isset($responses['sport_relationships']) ? htmlspecialchars($responses['sport_relationships']) : ''; ?></textarea>
                </div>
            </div>
            
            <!-- Future Direction -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4 text-primary border-b pb-2">Future Direction</h2>
                
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700 font-semibold">15. What timeframe references should be included in the vision to create both urgency and sustainability?</label>
                    <textarea name="timeframe_references" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"><?php echo isset($responses['timeframe_references']) ? htmlspecialchars($responses['timeframe_references']) : ''; ?></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700 font-semibold">21. What elements of the current vision and mission are most important to preserve?</label>
                    <p class="text-gray-600 text-sm mb-2">What elements need significant revision?</p>
                    <textarea name="preserve_revise" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"><?php echo isset($responses['preserve_revise']) ? htmlspecialchars($responses['preserve_revise']) : ''; ?></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700 font-semibold">23. What role should innovation play in the vision for Paarl Gim Athletics?</label>
                    <textarea name="innovation_role" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"><?php echo isset($responses['innovation_role']) ? htmlspecialchars($responses['innovation_role']) : ''; ?></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700 font-semibold">24. How should the vision and mission address the development of coaches and officials, not just athletes?</label>
                    <textarea name="coach_development" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"><?php echo isset($responses['coach_development']) ? htmlspecialchars($responses['coach_development']) : ''; ?></textarea>
                </div>
            </div>
            
            <div class="flex justify-between">
                <a href="index.php" class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-opacity-90 transition">Back to Home</a>
                <button type="submit" class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-opacity-90 transition">Save & Continue</button>
            </div>
        </form>
    </main>

    <footer class="bg-primary text-white py-4 mt-8">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; <?php echo date('Y'); ?> Track & Field Strategic Planning System</p>
        </div>
    </footer>
</body>
</html> 