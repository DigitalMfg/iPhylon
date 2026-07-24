<?php
// session_start();
require 'function.php';
require 'shift_helper.php';

$current = getCurrentShift($conn);

$currentDate  = $current['date'];
$currentShift = $current['shift'];

$listLine = mysqli_query($conn,"
SELECT DISTINCT line
FROM tbl_master_barcode
WHERE line <> ''
ORDER BY CAST(line AS UNSIGNED)
");

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| SHIFT AKTIF
|--------------------------------------------------------------------------
*/

$shiftNow = 1;

$getShift = mysqli_query($conn,"
SELECT shift
FROM tbl_master_time
WHERE date = CURDATE()
AND CURTIME() BETWEEN time_start AND time_end
ORDER BY id_time DESC
LIMIT 1
");

if(mysqli_num_rows($getShift)>0){
    $shiftNow = mysqli_fetch_assoc($getShift)['shift'];
}

/*
|--------------------------------------------------------------------------
| SHIFT SEBELUMNYA
|--------------------------------------------------------------------------
*/

$today = date('Y-m-d');

if($shiftNow == 1){

    $lastShift = 3;
    $lastDate = date('Y-m-d', strtotime('-1 day'));

}else{

    $lastShift = $shiftNow - 1;
    $lastDate = $today;

}

/*
|--------------------------------------------------------------------------
| LIST LINE
|--------------------------------------------------------------------------
*/

$listLine = mysqli_query($conn,"
SELECT DISTINCT line
FROM tbl_master_barcode
WHERE line IS NOT NULL
AND line <> ''
ORDER BY CAST(line AS UNSIGNED)
");

?>

<style>
  .stat-card{
      border:none;
      border-radius:16px;
      transition:.3s;
  }

  .stat-card:hover{
      transform:translateY(-5px);
  }

  .stat-icon{
      width:60px;
      height:60px;
      border-radius:15px;
      color:white;
      display:flex;
      align-items:center;
      justify-content:center;
      font-size:22px;
  }

  .card{
      border-radius:16px;
  }

  .card-header{
      background:white;
      border-bottom:1px solid #f1f1f1;
  }

  
</style>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>iPhylon | Dashboard MES</title>

    <link rel="icon" href="assets/images/i.Phylon.png" type="image/x-icon">
    
    <!-- Google Font -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700">

    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="plugins/fontawesome-free/css/all.min.css">

    <!-- AdminLTE -->
    <link rel="stylesheet"
        href="dist/css/adminlte.min.css">


    

</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- NAVBAR -->
    <?php include 'header.php'; ?>

    <!-- CONTENT -->
    <div class="content-wrapper">

        <!-- HEADER -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>
                            Dashboard
                        </h1>
                    </div>

                    <div class="col-sm-6 text-right">
                        <h5>
                            <?= date('d F Y '); ?> | Shift : <?= $shiftNow; ?>
                        </h5>
                    </div>
                </div>
            </div>
        </section>

        <!-- CONTENT -->
        <section class="content">
            <div class="container-fluid">

                <!-- Stat Cards -->
                <!-- <div class="row mt-12">
                    <div class="col-lg">
                        <div class="card stat-card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">
                                            Total Orders
                                        </p>
                                        <h2><?= number_format($totalOrder); ?></h2>
                                        <span class="text-info">
                                            Of Lot Basis
                                        </span>
                                    </div>

                                    <div class="stat-icon bg-info">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg col-md-6">
                        <div class="card stat-card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">
                                            Total Planning
                                        </p>
                                        <h2><?= number_format($totalPlanning) ?></h2>
                                        <span class="text-warning">
                                            <?= number_format($planningOrderPercent,1); ?> % of Order
                                        </span>
                                    </div>

                                    <div class="stat-icon bg-warning">
                                        <i class="fas fa-clipboard-list"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg col-md-6">
                        <div class="card stat-card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">
                                            Total Production
                                        </p>
                                        <h2><?= number_format($totalProduction) ?></h2>
                                        <span class="text-primary">
                                            <?= number_format($planningPercent,1) ?> % of Total Plan
                                        </span>
                                    </div>

                                    <div class="stat-icon bg-primary">
                                        <i class="fas fa-box"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg col-md-6">
                        <div class="card stat-card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">
                                        Stock SM IP
                                        </p>
                                        <h2><?= number_format($totalStockSM); ?></h2>
                                        <span class="text-success">
                                            IN SM - OUT SM
                                        </span>
                                    </div>

                                    <div class="stat-icon bg-success">
                                        <i class="fas fa-store"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg col-md-6">
                        <div class="card stat-card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">
                                            Remaining
                                        </p>
                                        <h2><?= number_format($remainingPlanning) ?></h2>
                                        <span class="text-danger">
                                            <?= number_format($remainingPercent,1); ?>% of Remaining
                                        </span>
                                    </div>

                                    <div class="stat-icon bg-danger">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->

                <div class="card mt-3">

                    <div class="card-header bg-primary">

                        <h3 class="card-title">
                            Production & Supermarket Monitoring
                        </h3>

                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mb-0">
                                <thead class="text-center">

                                    <tr>
                                        <th rowspan="2" class="text-center align-middle">Line</th>
                                        <th colspan="3">PRODUCTION</th>
                                        <th colspan="4">SUPERMARKET</th>
                                    </tr>

                                    <tr>

                                        <th>Last WIP</th>
                                        <th>Scan Out Packing</th>
                                        <th>WIP Production</th>
                                        <th>Last WIP</th>
                                        <th>Scan In</th>
                                        <th>Scan Out</th>
                                        <th>WIP Supermarket</th>
                                    </tr>
                                </thead>

                            <tbody>
                                    <?php while($line=mysqli_fetch_assoc($listLine)): ?>
                                    <?php
                                    $lineNo = $line['line'];
                                    $sqlWip = mysqli_query($conn,"
                                        SELECT *
                                        FROM tbl_shift_wip
                                        WHERE
                                        tanggal = CURDATE()
                                        AND shift = '$shiftNow'
                                        AND line = '$lineNo'
                                        LIMIT 1
                                        ");

                                        if(mysqli_num_rows($sqlWip))
                                        {
                                            $row = mysqli_fetch_assoc($sqlWip);

                                            $productionLast    = $row['production_last_wip'];
                                            $productionScanOut = $row['production_scan_out'];
                                            $productionWIP     = $row['production_wip'];

                                            $smLast            = $row['sm_last_wip'];
                                            $smScanIn          = $row['sm_scan_in'];
                                            $smScanOut         = $row['sm_scan_out'];
                                            $smWIP             = $row['sm_wip'];
                                        }
                                        else
                                        {
                                            $productionLast    = 0;
                                            $productionScanOut = 0;
                                            $productionWIP     = 0;

                                            $smLast            = 0;
                                            $smScanIn          = 0;
                                            $smScanOut         = 0;
                                            $smWIP             = 0;
                                        }
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= $lineNo ?></td>
                                        <!-- Production -->
                                        <td class="text-center">
                                            <?= number_format($productionLast) ?>
                                        </td>
                                        <td class="text-center">
                                            <?= number_format($productionScanOut) ?>
                                        </td>
                                        <td class="text-center">
                                            <?= number_format($productionWIP) ?>
                                        </td>
                                        <!-- Supermarket -->
                                        <td class="text-center">
                                            <?= number_format($smLast) ?>
                                        </td>
                                        <td class="text-center">
                                            <?= number_format($smScanIn) ?>
                                        </td>
                                        <td class="text-center">
                                            <?= number_format($smScanOut) ?>
                                        </td>
                                        <td class="text-center">
                                            <?= number_format($smWIP) ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        </section>      
    </div>

    <!-- FOOTER -->
  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 1.0.0
    </div>2024 
    <strong><a href="#">Mfg Project Officer</a>.</strong> All rights reserved.
  </footer>

</div>

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>

<!-- Bootstrap -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- AdminLTE -->
<script src="dist/js/adminlte.min.js"></script>

<script>

setInterval(function(){
    location.reload();
},120000);

</script>


</body>
</html>