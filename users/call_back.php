<?php
// Get raw POST data from the callback
$rawData = file_get_contents('php://input');

// Decode it into a PHP array
$data = json_decode($rawData, true);

// Print to browser (or log if needed)
echo "Received M-Pesa Callback Payload:<br><pre>";
print_r($data);
echo "</pre>";

// Respond with success so Safaricom doesn't retry
echo json_encode([
    "ResultCode" => 0,
    "ResultDesc" => "Callback received successfully"
]);
