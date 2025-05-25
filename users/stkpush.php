<?php
function getAccessToken()
{
    $consumerKey = "sFq4EsIoMma1uBxITeeJCHsF7d6MEGgyzdGRlWOJVWUkjovX";
    $consumerSecret = "w7tC5dFL1T4C4rvh2djJtXGxt4TkPkXvEa5vu63aOSL5XBb7cL3Cxyos8iHfFP4m";

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
        "Password" => "MTc0Mzc5YmZiMjc5ZjlhYTliZGJjZjE1OGU5N2RkNzFhNDY3Y2QyZTBjODkzMDU5YjEwZjc4ZTZiNzJhZGExZWQyYzkxOTIwMjUwNTI0MTYyMzQ4",
        "Timestamp" => "20250524162348",
        "TransactionType" => "CustomerPayBillOnline",
        "Amount" => 1,
        "PartyA" => 254741232714,
        "PartyB" => 174379,
        "PhoneNumber" => 254741232714,
        "CallBackURL" => "https://0c90-105-163-158-209.ngrok-free.app/users/call_back.php",
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
