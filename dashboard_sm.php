<?php
// session_start();
require 'function.php';

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
WHERE CURDATE() = date
AND CURTIME() BETWEEN time_start AND time_end
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
| AUTO CLOSE SHIFT SEBELUMNYA
|--------------------------------------------------------------------------
*/

mysqli_query($conn,"
UPDATE tbl_shift_wip

SET status='CLOSE'

WHERE tanggal='$lastDate'
AND shift='$lastShift'
AND status='OPEN'
");

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

    function getLastWIP($conn, $line, $shift)
{
    if($shift == 1){

        $lastShift = 3;
        $tanggal = date('Y-m-d',strtotime('-1 day'));
        }
        elseif($shift == 2){

            $lastShift = 1;
            $tanggal = date('Y-m-d');
        }
        else{
            // Shift 3 mengambil Last WIP dari Shift 2 HARI SEBELUMNYA
            $lastShift = 2;
            $tanggal = date('Y-m-d',strtotime('-1 day'));
        }

        $sql = mysqli_query($conn,"
        SELECT
            production_wip,
            sm_wip
        FROM tbl_shift_wip
        WHERE tanggal='$tanggal'
        AND shift='$lastShift'
        AND line='$line'
        AND status='CLOSE'
        LIMIT 1
        ");

    if(mysqli_num_rows($sql) > 0)
    {
        $row = mysqli_fetch_assoc($sql);

        return [
            'production'=>$row['production_wip'],
            'sm'=>$row['sm_wip']
        ];
    }

    return [
        'production'=>0,
        'sm'=>0
    ];
}
// ==================================================
// SAVE SHIFT WIP
// ==================================================
    function saveShiftWIP(
        $conn,
        $tanggal,
        $shift,
        $line,
        $productionLast,
        $productionScanOut,
        $productionWIP,
        $smLast,
        $smScanIn,
        $smScanOut,
        $smWIP
    ){

        // Cek apakah shift sudah CLOSE
        $cek = mysqli_query($conn,"
        SELECT status
        FROM tbl_shift_wip
        WHERE tanggal='$tanggal'
        AND shift='$shift'
        AND line='$line'
        LIMIT 1
        ");

    if(mysqli_num_rows($cek) > 0){
        $row = mysqli_fetch_assoc($cek);
        if($row['status'] == 'CLOSE'){
            return;
        }
    }
            mysqli_query($conn,"
            INSERT INTO tbl_shift_wip(

            tanggal,
            shift,
            line,

            production_last_wip,
            production_scan_out,
            production_wip,

            sm_last_wip,
            sm_scan_in,
            sm_scan_out,
            sm_wip,

            status

            )

            VALUES(

            '$tanggal',
            '$shift',
            '$line',

            '$productionLast',
            '$productionScanOut',
            '$productionWIP',

            '$smLast',
            '$smScanIn',
            '$smScanOut',
            '$smWIP',

            'OPEN'

            )

            ON DUPLICATE KEY UPDATE

            production_last_wip = VALUES(production_last_wip),
            production_scan_out = VALUES(production_scan_out),
            production_wip      = VALUES(production_wip),

            sm_last_wip         = VALUES(sm_last_wip),
            sm_scan_in          = VALUES(sm_scan_in),
            sm_scan_out         = VALUES(sm_scan_out),
            sm_wip              = VALUES(sm_wip),

            status='OPEN'
            ");

}
    $today = date('Y-m-d');

    if($shiftNow == 1){

    $lastShift = 3;
    $lastDate = date('Y-m-d',strtotime('-1 day'));

    }
    elseif($shiftNow == 2){

        $lastShift = 1;
        $lastDate = date('Y-m-d');

    }
    else{

        // Shift 3 harus menutup Shift 2 kemarin
        $lastShift = 2;
        $lastDate = date('Y-m-d',strtotime('-1 day'));

}

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
                                    $last = getLastWIP($conn,$lineNo,$shiftNow);
                                    $productionLast = $last['production'];
                                    $smLast         = $last['sm'];

                                    // ==========================
                                    // Scan Out Packing
                                    // ==========================
                                    $sqlPacking = mysqli_query($conn,"
                                    SELECT COALESCE(SUM(mb.qty),0) total
                                    FROM tbl_transaction_scan ts
                                    INNER JOIN tbl_master_barcode mb
                                    ON ts.qr_code = mb.qr_code
                                    WHERE ts.type_scan='OUT_PACKING'
                                    AND ts.shift='$shiftNow'
                                    AND DATE(ts.date_scan)=CURDATE()
                                    AND mb.line='$lineNo'
                                    ");

                                    $productionScanOut = mysqli_fetch_assoc($sqlPacking)['total'];

                                    // ==========================
                                    // Scan In Supermarket
                                    // ==========================
                                    $sqlInSM = mysqli_query($conn,"
                                    SELECT COALESCE(SUM(mb.qty),0) total
                                    FROM tbl_transaction_scan ts
                                    INNER JOIN tbl_master_barcode mb
                                    ON ts.qr_code = mb.qr_code
                                    WHERE ts.type_scan='IN_SM'
                                    AND ts.shift='$shiftNow'
                                    AND DATE(ts.date_scan)=CURDATE()
                                    AND mb.line='$lineNo'
                                    ");
                                    $smScanIn = mysqli_fetch_assoc($sqlInSM)['total'];
                                    // ==========================
                                    // Scan Out Supermarket
                                    // ==========================

                                    $sqlOutSM = mysqli_query($conn,"
                                    SELECT COALESCE(SUM(mb.qty),0) total
                                    FROM tbl_transaction_scan ts
                                    INNER JOIN tbl_master_barcode mb
                                    ON ts.qr_code = mb.qr_code
                                    WHERE ts.type_scan='OUT_SM'
                                    AND ts.shift='$shiftNow'
                                    AND DATE(ts.date_scan)=CURDATE()
                                    AND mb.line='$lineNo'
                                    ");

                                    $smScanOut = mysqli_fetch_assoc($sqlOutSM)['total'];

                                    // ==========================
                                    // HITUNG WIP
                                    // ==========================
                                    $productionWIP = $productionLast + $productionScanOut - $smScanIn;
                                    $smWIP = $smLast + $smScanIn - $smScanOut;
                                    $saveDate = ($shiftNow == 3)
                                        ? date('Y-m-d')
                                        : date('Y-m-d');

                                    saveShiftWIP(
                                        $conn,
                                        $saveDate,
                                        $shiftNow,
                                        $lineNo,
                                        $productionLast,
                                        $productionScanOut,
                                        $productionWIP,
                                        $smLast,
                                        $smScanIn,
                                        $smScanOut,
                                        $smWIP
                                    );
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