<?php
// DB connection
$conn = mysqli_connect("mysql_db", "app", "app", "vpms");
if ($conn->connect_error) {
    http_response_code(500);
    echo "DB connection failed.";
    exit;
}

// 1. Get and decode callback JSON
$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

// 2. Extract important fields
$callback = $data['Body']['stkCallback'] ?? null;

if (!$callback) {
    http_response_code(400);
    echo "Invalid callback payload.";
    exit;
}

$checkoutId = $callback['CheckoutRequestID'];
$resultCode = $callback['ResultCode'];
$resultDesc = $callback['ResultDesc'];

$status = $resultCode === 0 ? 'paid' : 'failed';
$amount = null;
$receipt = null;
$phone = null;
$txn_date = null;

// Extract metadata (only if transaction succeeded)
if ($status === 'paid' && isset($callback['CallbackMetadata']['Item'])) {
    foreach ($callback['CallbackMetadata']['Item'] as $item) {
        switch ($item['Name']) {
            case 'Amount':
                $amount = $item['Value'];
                break;
            case 'MpesaReceiptNumber':
                $receipt = $item['Value'];
                break;
            case 'TransactionDate':
                $txn_date = $item['Value'];
                break;
            case 'PhoneNumber':
                $phone = $item['Value'];
                break;
        }
    }
}

// 3. Update existing payment with matching CheckoutRequestID
$stmt = $conn->prepare("
    UPDATE payment
    SET 
        status = ?, 
        amount = IFNULL(?, amount),
        remarks = ?, 
    WHERE mpesa_checkout_id = ?
");

$stmt->bind_param("sssss", $status, $amount, $resultDesc, $receipt, $checkoutId);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    http_response_code(200);
    echo json_encode([
        "ResultCode" => 0,
        "ResultDesc" => "Payment updated successfully"
    ]);
} else {
    http_response_code(200); // Still 200 to avoid retries
    echo json_encode([
        "ResultCode" => 0,
        "ResultDesc" => "Callback received but no matching payment found"
    ]);
}

$stmt->close();
$conn->close();
