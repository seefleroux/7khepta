<?php
// Include database connection
require_once 'db_config.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get the questionnaire ID
    $questionnaire_id = 3; // Vision & Mission questionnaire
    
    try {
        // Start transaction
        $conn->beginTransaction();
        
        // First, clear any existing responses for this questionnaire
        $stmt = $conn->prepare("DELETE FROM responses WHERE questionnaire_id = :questionnaire_id");
        $stmt->bindParam(':questionnaire_id', $questionnaire_id);
        $stmt->execute();
        
        // Process each form field
        foreach ($_POST as $field_name => $value) {
            // Skip any non-relevant fields
            if ($field_name == 'submit') continue;
            
            // Get the question ID based on the field name
            $stmt = $conn->prepare("SELECT id FROM questions WHERE field_name = :field_name AND questionnaire_id = :questionnaire_id");
            $stmt->bindParam(':field_name', $field_name);
            $stmt->bindParam(':questionnaire_id', $questionnaire_id);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $question = $stmt->fetch(PDO::FETCH_ASSOC);
                $question_id = $question['id'];
                
                // Insert the response
                $stmt = $conn->prepare("INSERT INTO responses (questionnaire_id, question_id, response_text) VALUES (:questionnaire_id, :question_id, :response_text)");
                $stmt->bindParam(':questionnaire_id', $questionnaire_id);
                $stmt->bindParam(':question_id', $question_id);
                $stmt->bindParam(':response_text', $value);
                $stmt->execute();
            }
        }
        
        // Update the strategic plan content
        updateStrategicPlan($conn);
        
        // Commit the transaction
        $conn->commit();
        
        // Redirect to the success page
        header("Location: questionnaire_success.php");
        exit();
        
    } catch (PDOException $e) {
        // Roll back the transaction if something failed
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
}

/**
 * Updates the strategic plan based on questionnaire responses
 */
function updateStrategicPlan($conn) {
    // Get all responses from questionnaire 3
    $stmt = $conn->prepare("SELECT q.section, q.question_text, r.response_text 
                           FROM responses r 
                           JOIN questions q ON r.question_id = q.id 
                           WHERE r.questionnaire_id = 3 
                           ORDER BY q.display_order");
    $stmt->execute();
    $responses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Build the vision and mission content
    $visionMissionContent = "<h2>Vision and Mission</h2>\n";
    
    // Extract key responses to craft vision and mission statements
    $vision = "";
    $mission = "";
    
    foreach ($responses as $response) {
        if ($response['question_text'] == "How would you articulate the ultimate aspiration for Paarl Gim Athletics in one sentence?") {
            $vision = $response['response_text'];
        }
        
        // Add additional mission-related content based on responses
        if (strpos($response['question_text'], "lasting impact") !== false || 
            strpos($response['question_text'], "athletics experience") !== false ||
            strpos($response['question_text'], "program's purpose") !== false) {
            $mission .= $response['response_text'] . " ";
        }
    }
    
    // Format the vision and mission statements
    $visionMissionContent .= "<h3>Vision Statement</h3>\n";
    $visionMissionContent .= "<p>" . htmlspecialchars($vision) . "</p>\n";
    
    $visionMissionContent .= "<h3>Mission Statement</h3>\n";
    $visionMissionContent .= "<p>" . htmlspecialchars(trim($mission)) . "</p>\n";
    
    // Add the detailed responses
    $visionMissionContent .= "<h3>Strategic Direction Details</h3>\n";
    
    $currentSection = "";
    
    foreach ($responses as $response) {
        if ($response['section'] != $currentSection) {
            $currentSection = $response['section'];
            $visionMissionContent .= "<h4>" . htmlspecialchars($currentSection) . "</h4>\n";
        }
        
        $visionMissionContent .= "<p><strong>" . htmlspecialchars($response['question_text']) . ":</strong> " . 
                   htmlspecialchars($response['response_text']) . "</p>\n";
    }
    
    // Check if a strategic plan already exists
    $stmt = $conn->prepare("SELECT COUNT(*), content FROM strategic_plans WHERE id = 1");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $count = $result['COUNT(*)'];
    
    if ($count > 0) {
        $existingContent = $result['content'];
        
        // Add the vision and mission content to the existing plan
        // Check if there's already a Vision and Mission section
        if (strpos($existingContent, "<h2>Vision and Mission</h2>") !== false) {
            // Replace the existing section
            $pattern = "/<h2>Vision and Mission<\/h2>.*?(?=<h2>|$)/s";
            $updatedContent = preg_replace($pattern, $visionMissionContent, $existingContent);
        } else {
            // Add after Executive Summary or at the beginning if no Executive Summary
            if (strpos($existingContent, "<h2>Executive Summary</h2>") !== false) {
                $pattern = "/(<h2>Executive Summary<\/h2>.*?)(?=<h2>|$)/s";
                $updatedContent = preg_replace($pattern, "$1" . $visionMissionContent, $existingContent);
            } else {
                $updatedContent = $visionMissionContent . "\n" . $existingContent;
            }
        }
        
        // Update existing plan
        $stmt = $conn->prepare("UPDATE strategic_plans SET content = :content, updated_at = NOW() WHERE id = 1");
        $stmt->bindParam(':content', $updatedContent);
        $stmt->execute();
    } else {
        // Create new plan
        $stmt = $conn->prepare("INSERT INTO strategic_plans (title, content) VALUES ('Track & Field Strategic Plan', :content)");
        $stmt->bindParam(':content', $visionMissionContent);
        $stmt->execute();
    }
}
?> 