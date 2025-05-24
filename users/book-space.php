<?php
session_start();
error_reporting(1);
include('includes/dbconnection.php');
if (strlen($_SESSION['vpmsuid']==0)) {
    header('location:logout.php');
    } else{ 
// $currentPage = basename($_SERVER['PHP_SELF']);
if (isset($_POST['submit'])) {
    $parkingnumber = $_POST['parking_number'];
    $catename = $_POST['catename'];
    $vehcomp = $_POST['vehcomp'];
    $vehreno = $_POST['vehreno'];
    $ownername = $_POST['ownername'];
    $ownercontno = $_POST['ownercontno'];
    $selected_parking_number = $_POST['parking_number'];
    $userid = $_SESSION['vpmsuid'];
    $start_time = date('Y-m-d H:i:s');

    // Insert into tblvehicle
    $vehicle_query = mysqli_query(
        $con,
        "INSERT INTO tblvehicle 
            (ParkingNumber, VehicleCategory, VehicleCompanyname, RegistrationNumber, OwnerName, OwnerContactNumber)
         VALUES 
            ('$parkingnumber','$catename','$vehcomp','$vehreno','$ownername','$ownercontno')
         ON DUPLICATE KEY UPDATE 
            ParkingNumber = VALUES(ParkingNumber),
            VehicleCategory = VALUES(VehicleCategory),
            VehicleCompanyname = VALUES(VehicleCompanyname),
            OwnerName = VALUES(OwnerName),
            OwnerContactNumber = VALUES(OwnerContactNumber)"
    );
    if ($vehicle_query) {
        $vehicle_id = mysqli_insert_id($con); // Last inserted vehicle ID

        // Insert into bookings table
        $booking_query = mysqli_query(
            $con,
            "INSERT INTO bookings (user_id, parking_number, vehicle_id, start_time, `status`,`created_at`)
             VALUES ('$userid', '$selected_parking_number', '$vehicle_id', '$start_time', 'active', NOW())"
        );
        
        if (!$booking_query) {
            die("Booking insert failed: " . mysqli_error($con));
        }
        // Update parking space status
        mysqli_query($con, "UPDATE parking_space SET status='booked' WHERE parking_number='$selected_parking_number'");

        echo "<script>alert('Vehicle and booking recorded successfully');</script>";
        echo "<script>window.location.href ='book-space.php'</script>";
    } else {
        echo "<script>alert('Something went wrong. Please try again.');</script>";
    }
}
?>

<!doctype html>
<html lang="">
<head>
    <title>Book Parking Space</title>
    <link rel="apple-touch-icon" href="https://i.imgur.com/QRAUqs9.png">
    <link rel="shortcut icon" href="https://i.imgur.com/QRAUqs9.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../admin/assets/css/style.css">
</head>
<body>

<?php include_once('includes/sidebar.php'); ?>
<?php include_once('includes/header.php'); ?>

<div class="content">
    <div class="animated fadeIn">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-header"><strong>Book</strong> Parking Space</div>
                    <div class="card-body card-block">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Vehicle Category</label>
                                <div class="col-md-9">
                                    <select name="catename" class="form-control" required>
                                        <option value="">Select Category</option>
                                        <?php
                                        $query = mysqli_query($con, "SELECT * FROM tblcategory");
                                        while ($row = mysqli_fetch_array($query)) {
                                            echo '<option value="' . $row['VehicleCat'] . '">' . $row['VehicleCat'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Vehicle Company</label>
                                <div class="col-md-9">
                                    <input type="text" name="vehcomp" class="form-control" required placeholder="e.g., Toyota">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Registration Number</label>
                                <div class="col-md-9">
                                    <input type="text" name="vehreno" class="form-control" required placeholder="e.g., KA-01-1234">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Owner Name</label>
                                <div class="col-md-9">
                                    <input type="text" name="ownername" class="form-control" required placeholder="Owner's full name">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Owner Contact</label>
                                <div class="col-md-9">
                                    <input type="text" name="ownercontno" class="form-control" required maxlength="10" pattern="[0-9]+" placeholder="10-digit mobile number">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Select Parking Space</label>
                                <div class="col-md-9">
                                    <select name="parking_number" class="form-control" required>
                                        <option value="">-- Select Available Space --</option>
                                        <?php
                                        $result = mysqli_query($con, "SELECT * FROM parking_space WHERE status='available'");
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo '<option value="' . $row['parking_number'] . '">' . $row['parking_number'] . ' (ksh' . $row['price_per_hour'] . '/hr)</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" name="submit" class="btn btn-primary">Book Space</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>


    </div><!-- .animated -->
</div><!-- .content -->

    <div class="clearfix"></div>

   <?php include_once('includes/footer.php');?>

</div><!-- /#right-panel -->

<!-- Right Panel -->

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.4/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-match-height@0.7.2/dist/jquery.matchHeight.min.js"></script>
<script src="assets/js/main.js"></script>


</body>
</html>
<?php }  ?>