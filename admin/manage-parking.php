<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['vpmsaid'] == 0)) {
    header('location:logout.php');
    exit();
}

// Delete logic
if (isset($_GET['delid'])) {
    $id = intval($_GET['delid']);
    $query = mysqli_query($con, "DELETE FROM parking_space WHERE id='$id'");
    if ($query) {
        echo "<script>alert('Parking space deleted successfully');</script>";
        echo "<script>window.location.href='manage-parking.php';</script>";
    }
}

?>

<!doctype html>
<html lang="">
<head>
    
    <title>Manage parking spaces</title>
   

    <link rel="apple-touch-icon" href="https://i.imgur.com/QRAUqs9.png">
    <link rel="shortcut icon" href="https://i.imgur.com/QRAUqs9.png">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/normalize.css@8.0.0/normalize.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pixeden-stroke-7-icon@1.2.3/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.2.0/css/flag-icon.min.css">
    <link rel="stylesheet" href="assets/css/cs-skin-elastic.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>

    <!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/html5shiv/3.7.3/html5shiv.min.js"></script> -->

</head>
<body>
<?php include_once('includes/sidebar.php'); ?>
<?php include_once('includes/header.php'); ?>

<div class="container mt-5">
    <h3 class="mb-4">Manage Parking Spaces</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Parking Number</th>
                <th>Status</th>
                <th>Price per Hour</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
<?php
$ret = mysqli_query($con, "SELECT * FROM parking_space");
$cnt = 1;
while ($row = mysqli_fetch_array($ret)) {
?>
    <tr>
        <td><?php echo $cnt++; ?></td>
        <td><?php echo $row['parking_number']; ?></td>
        <td><?php echo $row['status']; ?></td>
        <td><?php echo $row['price_per_hour']; ?></td>
        <td>
            <a href="edit-parking.php?editid=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
            <a href="manage-parking.php?delid=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?');" class="btn btn-sm btn-danger">Delete</a>
        </td>
    </tr>
<?php } ?>
        </tbody>
    </table>
</div>

<?php include_once('includes/footer.php'); ?>
</body>
</html>
