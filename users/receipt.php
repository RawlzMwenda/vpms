<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;

// 1. Get the parking ID
$pk = isset($_GET['pk']) ? (int)$_GET['pk'] : null;
if (!$pk) {
    http_response_code(400);
    echo "Missing pk.";
    exit;
}

// 2. DB connection
$con=mysqli_connect("localhost", "root", "", "vpmsdb");
if(mysqli_connect_errno()){
echo "Connection Fail".mysqli_connect_error();
}


// 3. Fetch receipt data
$stmt = $con->prepare("
    SELECT 
        p.id AS payment_id,
        p.amount,
        p.status AS payment_status,
        p.created_at AS payment_time,
        b.parking_number,
        b.start_time,
        b.end_time,
        CONCAT(u.firstname, ' ', u.lastname) AS user_name,
        u.MobileNumber AS user_phone,
        v.RegistrationNumber AS car_plate,
        v.VehicleCompanyname AS car_model
    FROM payment p
    JOIN bookings b ON p.booking_id = b.id
    JOIN tblregusers u ON b.user_id = u.id
    JOIN tblvehicle v ON b.vehicle_id = v.id
    WHERE p.id = ?
");

$stmt->bind_param("i", $pk);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo "Receipt not found.";
    exit;
}

$receipt = $result->fetch_assoc();
$stmt->close();
$con->close();

// 4. Set filename
$fileName = "Parking_Receipt_#{$receipt['payment_id']}.pdf";

// 5. Render HTML content
ob_start();
include 'parking-receipt-template.php'; // this should echo or use $receipt
$html = ob_get_clean();

// 6. Generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// 7. Output PDF in browser
$dompdf->stream($fileName, ['attachment' => 0]);
exit;
?>