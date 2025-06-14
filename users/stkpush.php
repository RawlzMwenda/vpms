<?php
function getAccessToken()
{
    $consumerKey ="8RYoo7OBBVjLvvAv4PlD7Y1GNi28AiyyM3j5LNleTc8HgERt" ;
    $consumerSecret = "35K8uApMVB7Gz0Xgu6QhzSvEHDRgUAa6pFpuIen7GABjhmNnY3kSAAGKsbhxW9ib";

    $credentials = base64_encode("$consumerKey:$consumerSecret");

    $url = "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";

    $headers = [
        "Authorization: Basic $credentials"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        die("Curl error: " . curl_error($ch));
    }

    curl_close($ch);

    $json = json_decode($response, true);
    return $json['access_token'] ?? null;
}

function initiateSTKPush()
{
    $accessToken = getAccessToken();
    if (!$accessToken) {
        return ["error" => "Failed to get access token"];
    }

    $timestamp = date('YmdHis');
    $shortCode = "174379";
    $passkey = ""; // Use your actual passkey here
    $password = base64_encode($shortCode . $passkey . $timestamp);

    $payload = [
        "BusinessShortCode" => 174379,
        "Password" => "MTc0Mzc5YmZiMjc5ZjlhYTliZGJjZjE1OGU5N2RkNzFhNDY3Y2QyZTBjODkzMDU5YjEwZjc4ZTZiNzJhZGExZWQyYzkxOTIwMjUwNjE0MTIwMzMy",
        "Timestamp" => "20250614120332",
        "TransactionType" => "CustomerPayBillOnline",
        "Amount" => 1,
        "PartyA" => 254757172576,
        "PartyB" => 174379,
        "PhoneNumber" => 254757172576,
        "CallBackURL" => "https://43bc-2c0f-6300-e06-d00-bdce-e778-611-12ae.ngrok-free.app/vpms/users/call-back.php",
        "AccountReference" => "CompanyXLTD",
        "TransactionDesc" => "Payment of X"
    ];

    $url = "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest";
    $headers = [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        die("Curl error: " . curl_error($ch));
    }

    curl_close($ch);
    return json_decode($response, true);
}

// Trigger if run directly
if (php_sapi_name() !== 'cli') {
    header('Content-Type: application/json');
    echo json_encode(initiateSTKPush());
}
?>