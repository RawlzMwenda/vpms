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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">

<h3>Payment Details</h3>
<ul class="list-group mb-4">
    <li class="list-group-item">Parking Number: <strong><?= htmlspecialchars($row['parking_number']) ?></strong></li>
    <li class="list-group-item">Start Time: <strong><?= $row['start_time'] ?></strong></li>
    <li class="list-group-item">End Time: <strong><?= $row['end_time'] ?></strong></li>
    <li class="list-group-item">Hours Parked: <strong><?= $hours ?></strong></li>
    <li class="list-group-item">Rate: <strong>Ksh<?= number_format($rate) ?>/hr</strong></li>
    <li class="list-group-item">Total Amount: <strong>Ksh<?= number_format($amount) ?></strong></li>
</ul>

<form method="post">
    <input type="hidden" name="booking_id" value="<?= $bookingId ?>">
    <input type="hidden" name="amount" value="<?= $amount ?>">
    <input type="hidden" name="phone" value="<?= $phone ?>">
    <button type="submit" name="pay_now" class="btn btn-success btn-lg">Pay Now</button>
</form>

<?php
if (isset($_POST['pay_now'])) {
    $amount = intval($_POST['amount']);
    $phone = $_POST['phone'];

    // Prepare POST request to stkpush.php
    $postData = [
        'amount' => $amount,
        'phone' => $phone
    ];

    $ch = curl_init("http://localhost/vpms/users/stkpush.php");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo "<div class='alert alert-danger mt-4'>Curl error: " . htmlspecialchars(curl_error($ch)) . "</div>";
    } else {
        echo "<h4 class='mt-4'>Payment Response:</h4>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
    curl_close($ch);
}
?>

</body>
</html>