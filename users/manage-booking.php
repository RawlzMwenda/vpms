<?php
session_start();
error_reporting(1);
include('includes/dbconnection.php');

if (strlen($_SESSION['vpmsuid']) == 0) {
    header('location:logout.php');
    exit();
}

// Handle checkout logic here
$checkoutMessage = '';
if (isset($_GET['checkout'])) {
    $bookingId = intval($_GET['checkout']);
    $endTime = date('Y-m-d H:i:s');

    // Fetch the parking number linked to this booking
    $bookingFetch = mysqli_query($con, "SELECT parking_number FROM bookings WHERE id = $bookingId AND user_id = " . $_SESSION['vpmsuid']);
    $row = mysqli_fetch_assoc($bookingFetch);

    if ($row) {
        $parkingNumber = $row['parking_number'];

        // Update booking to checked_out
        $updateBooking = mysqli_query($con, "
            UPDATE bookings 
            SET end_time = '$endTime', status = 'completed' 
            WHERE id = $bookingId
        ");

        // Update parking_space status to available
        $updateSpace = mysqli_query($con, "
            UPDATE parking_space 
            SET status = 'available' 
            WHERE parking_number = '$parkingNumber'
        ");

    if ($updateBooking && $updateSpace) {
        // Redirect to payment page with booking ID
        header("Location: payment.php?booking_id=" . $bookingId);
        exit();
    } else {
        $checkoutMessage = '<div class="alert alert-danger">Failed to complete checkout. Please try again.</div>';
    }
    } else {
        $checkoutMessage = '<div class="alert alert-warning">Invalid booking ID or permission denied.</div>';
    }
}
?>

<!doctype html>
<html lang="">
<head>
    <title>Manage Bookings</title>
    <link rel="apple-touch-icon" href="https://i.imgur.com/QRAUqs9.png">
    <link rel="shortcut icon" href="https://i.imgur.com/QRAUqs9.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../admin/assets/css/style.css">
</head>

<body>
<?php include_once('includes/sidebar.php'); ?>
<?php include_once('includes/header.php'); ?>

<div class="container mt-5">
    <h3 class="mb-4">Manage Bookings</h3>

    <!-- Debug Info -->
    <div class="mb-4">
        <label for="debugBox">Debug Info:</label>
        <textarea id="debugBox" class="form-control" rows="3" readonly><?php
            echo "User ID: " . $_SESSION['vpmsuid'] . "\n";
            echo "Date: " . date('Y-m-d H:i:s') . "\n";
            echo "IP: " . $_SERVER['REMOTE_ADDR'];
        ?></textarea>
    </div>

    <!-- Checkout Message -->
    <?php echo $checkoutMessage; ?>

    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Booking ID</th>
                <th>Parking Number</th>
                <th>Vehicle</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
<?php
$cnt = 1;
$query = "
    SELECT 
        b.id AS booking_id,
        ps.parking_number,
        v.RegistrationNumber,
        v.VehicleCompanyname,
        b.start_time,
        b.end_time,
        b.status
    FROM bookings b
    JOIN parking_space ps ON b.parking_number = ps.parking_number
    JOIN tblvehicle v ON b.vehicle_id = v.id
    WHERE b.user_id = ?
    ORDER BY b.start_time DESC
";

$stmt = $con->prepare($query);
$stmt->bind_param("i", $_SESSION['vpmsuid']);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
?>
    <tr>
        <td><?php echo $cnt++; ?></td>
        <td><?php echo htmlspecialchars($row['booking_id']); ?></td>
        <td><?php echo htmlspecialchars($row['parking_number']); ?></td>
        <td><?php echo htmlspecialchars($row['VehicleCompanyname'] . ' - ' . $row['RegistrationNumber']); ?></td>
        <td><?php echo htmlspecialchars($row['start_time']); ?></td>
        <td><?php echo htmlspecialchars($row['end_time']); ?></td>
        <td><?php echo htmlspecialchars($row['status']); ?></td>
        <td>
            <?php if ($row['status'] !== 'checked_out') { ?>
                <a href="?checkout=<?php echo $row['booking_id']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to checkout this booking?')">Checkout</a>
            <?php } else { ?>
                <span class="badge badge-secondary">Completed</span>
            <?php } ?>
        </td>
    </tr>
<?php } ?>
        </tbody>
    </table>
</div>

<?php include_once('includes/footer.php'); ?>
</body>
</html>