<?php
session_start();
include('includes/dbconnection.php');

if (!isset($_GET['booking_id'])) {
    die("Missing booking_id");
}

$bookingId = intval($_GET['booking_id']);
$userId = $_SESSION['vpmsuid'];

// Fetch booking, user, and parking price
$query = mysqli_query($con, "
    SELECT 
        b.id,
        b.parking_number,
        b.start_time,
        b.end_time,
        ps.price_per_hour,
        u.MobileNumber
    FROM bookings b
    JOIN parking_space ps ON b.parking_number = ps.parking_number
    JOIN tblregusers u ON b.user_id = u.id
    WHERE b.id = '$bookingId' AND b.user_id = '$userId'
");

if (!$row = mysqli_fetch_assoc($query)) {
    die("Invalid booking ID or permission denied.");
}

// Calculate duration and cost
$start = strtotime($row['start_time']);
$end = strtotime($row['end_time']);
$hours = max(1, ceil(($end - $start) / 3600)); // Minimum 1 hour
$rate = $row['price_per_hour'];
$amount = $rate * $hours;

$phone = $row['MobileNumber'];
$parkingNumberCode = $row['parking_number'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Bookings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
</head>
<body>

<h3>Payment Details</h3>
<div class="mb-4">
    <label>Debug Info:</label>
    <textarea class="form-control" rows="10" readonly><?php
        echo "User ID: $userId\n";
        echo "Booking ID: $bookingId\n";
        echo "Parking Number: $parkingNumberCode\n";
        echo "Start Time: " . $row['start_time'] . "\n";
        echo "End Time: " . $row['end_time'] . "\n";
        echo "Hours: $hours\n";
        echo "Rate per Hour: $rate\n";
        echo "Amount: $amount\n";
        echo "Phone: $phone\n";
    ?></textarea>
</div>

<ul class="list-group mb-4">
    <li class="list-group-item">Parking Number: <strong><?= htmlspecialchars($row['parking_number']) ?></strong></li>
    <li class="list-group-item">Start Time: <strong><?= $row['start_time'] ?></strong></li>
    <li class="list-group-item">End Time: <strong><?= $row['end_time'] ?></strong></li>
    <li class="list-group-item">Total Amount: <strong>Ksh<?= number_format($amount) ?></strong></li>
</ul>

<form method="post">
    <input type="hidden" name="booking_id" value="<?= $bookingId ?>">
    <input type="hidden" name="amount" value="<?= $amount ?>">
    <input type="hidden" name="phone" value="<?= $phone ?>">
    <input type="hidden" name="parking_number" value="<?= $parkingNumberCode ?>">
    <button type="submit" name="pay_now" class="btn btn-success btn-lg">Pay Now</button>
</form>

<?php
if (isset($_POST['pay_now'])) {
    $amount = intval($_POST['amount']);
    $phone = $_POST['phone'];
    $bookingId = intval($_POST['booking_id']);
    $parkingNumber = $_POST['parking_number'];

    // Get parking_space.id from parking_number
    $psQuery = mysqli_query($con, "SELECT id FROM parking_space WHERE parking_number = '$parkingNumber'");
    $psRow = mysqli_fetch_assoc($psQuery);
    if (!$psRow) {
        echo "<div class='alert alert-danger'>Parking space not found for number: " . htmlspecialchars($parkingNumber) . "</div>";
        exit;
    }
    $parkingSpaceId = intval($psRow['id']);

    // Insert payment as pending
    $insert = mysqli_prepare($con, "
        INSERT INTO payment (booking_id, parking_number, amount, status)
        VALUES (?, ?, ?, 'pending')
    ");
    mysqli_stmt_bind_param($insert, 'iid', $bookingId, $parkingSpaceId, $amount);
    mysqli_stmt_execute($insert);
    $paymentId = mysqli_insert_id($con);
// Get the inserted payment ID
    $paymentId = mysqli_insert_id($con);

    // Generate receipt URL
    $receiptUrl = "http://127.0.0.1:8080/users/receipt.php?pk=" . $paymentId;

    // Update the payment record with the receipt_url
    $update = mysqli_prepare($con, "
        UPDATE payment
        SET receipt_url = ?
        WHERE id = ?
    ");
    mysqli_stmt_bind_param($update, 'si', $receiptUrl, $paymentId);
    mysqli_stmt_execute($update);
    mysqli_stmt_close($update);
    // Send STK Push
    $postData = ['amount' => $amount, 'phone' => $phone];
    $ch = curl_init("http://localhost/users/stkpush.php");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    $response = curl_exec($ch);
    curl_close($ch);

    // Extract CheckoutRequestID if present
    $mpesa = json_decode($response, true);
    if (isset($mpesa['CheckoutRequestID'])) {
        $checkoutId = $mpesa['CheckoutRequestID'];
        $update = mysqli_prepare($con, "
            UPDATE payment SET mpesa_checkout_id = ? WHERE id = ?
        ");
        mysqli_stmt_bind_param($update, 'si', $checkoutId, $paymentId);
        mysqli_stmt_execute($update);
        mysqli_stmt_close($update);
        echo "<div class='alert alert-success mt-3'>STK Push initiated. Checkout ID saved.</div>";
    } else {
        echo "<div class='alert alert-warning mt-3'>STK push response did not return a CheckoutRequestID.</div>";
    }

    echo "<h4 class='mt-4'>Payment Response:</h4>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
}
?>

</body>
</html>
