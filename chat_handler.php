<?php
header('Content-Type: application/json');

// Log incoming request
file_put_contents('scratch/chat_debug.txt', "Method: " . $_SERVER['REQUEST_METHOD'] . "\nInput: " . file_get_contents('php://input') . "\n", FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, TRUE);
    
    $message = isset($input['message']) ? $input['message'] : '';
    
    if (empty(trim($message))) {
        echo json_encode(['response' => "Hakerek buat ruma lai."]);
        exit;
    }
    
    // Sanitize the message to prevent command injection
    $escaped_message = escapeshellarg($message);
    
    // Call the Python script
    // Note: Assuming 'python' is in the system PATH.
    $command = "python chatbot_logic.py " . $escaped_message;
    $output = shell_exec($command);
    
    // Attempt to decode the output from Python
    $decoded = json_decode($output, true);
    
    if ($decoded && isset($decoded['response'])) {
        echo json_encode(['response' => $decoded['response']]);
    } else {
        // Fallback error
        echo json_encode(['response' => "Deskulpa, iha problema ho ligasaun ba Chatbot.\nDebug: $output"]);
    }
} else {
    echo json_encode(['response' => "Invalid request. Method was: " . $_SERVER['REQUEST_METHOD']]);
}
?>
