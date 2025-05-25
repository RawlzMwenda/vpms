<?php
session_start();
error_reporting(1);
include('includes/dbconnection.php');

if (strlen($_SESSION['vpmsuid']) == 0) {
    header('location:logout.php');
    exit();
}
?>

<!doctype html>
<html lang="">
<head>
    <title>My Payments</title>
    <link rel="apple-touch-icon" href="https://i.imgur.com/QRAUqs9.png">
    <link rel="shortcut icon" href="https://i.imgur.com/QRAUqs9.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../admin/assets/css/style.css">
</head>

<body>
<?php include_once('includes/sidebar.php'); ?>
<?php include_once('includes/header.php'); ?>

<div class="container mt-5">
    <h3 class="mb-4">My Payment Records</h3>

    <!-- Debug Info -->
    <div class="mb-4">
        <label for="debugBox">Debug Info:</label>
        <textarea id="debugBox" class="form-control" rows="3" readonly><?php
            echo "User ID: " . $_SESSION['vpmsuid'] . "\n";
            echo "Date: " . date('Y-m-d H:i:s') . "\n";
            echo "IP: " . $_SERVER['REMOTE_ADDR'];
        ?></textarea>
    </div>

    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Payment ID</th>
                <th>Booking ID</th>
                <th>Parking Number</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Receipt</th>
                <th>Remarks</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
<?php
$cnt = 1;
$query = "
    SELECT 
        p.id AS payment_id,
        p.booking_id,
        ps.parking_number,
        p.amount,
        p.status,
        p.receipt_url,
        p.remarks,
        p.created_at
    FROM payment p
    JOIN bookings b ON p.booking_id = b.id
    JOIN parking_space ps ON b.parking_number = ps.parking_number
    WHERE b.user_id = ?
    ORDER BY p.created_at DESC
";

$stmt = $con->prepare($query);
$stmt->bind_param("i", $_SESSION['vpmsuid']);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
?>
    <tr>
        <td><?php echo $cnt++; ?></td>
        <td><?php echo htmlspecialchars($row['payment_id']); ?></td>
        <td><?php echo htmlspecialchars($row['booking_id']); ?></td>
        <td><?php echo htmlspecialchars($row['parking_number']); ?></td>
        <td>KES <?php echo number_format($row['amount'], 2); ?></td>
        <td><?php echo htmlspecialchars($row['status']); ?></td>
        <td>
            <?php if (!empty($row['receipt_url'])) { ?>
                <a href="<?php echo htmlspecialchars($row['receipt_url']); ?>" target="_blank">View</a>
            <?php } else { ?>
                <span class="text-muted">N/A</span>
            <?php } ?>
        </td>
        <td><?php echo htmlspecialchars($row['remarks']); ?></td>
        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
    </tr>
<?php } ?>
        </tbody>
    </table>
</div>

<?php include_once('includes/footer.php'); ?>
</body>
</html>
