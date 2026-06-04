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

if(mysqli_num_rows($getShift) > 0)
{
    $shift = mysqli_fetch_assoc($getShift);
    $shiftNow = $shift['shift'];
}

/*
|--------------------------------------------------------------------------
| PLAN VS PACKING
|--------------------------------------------------------------------------
*/

$chartData = mysqli_query($conn,"

SELECT

    p.line_produksi,

    p.total_plan,

    IFNULL(k.total_packing,0) AS total_packing

FROM

(
    SELECT
        h.line_produksi,
        SUM(d.qty) AS total_plan

    FROM tbl_daily_plan_header h

    INNER JOIN tbl_daily_plan_detail d
        ON h.id_daily_header = d.id_daily_header

    WHERE h.tanggal_plan = CURDATE()
    AND d.type = 'PLAN'
    AND d.shift = '$shiftNow'

    GROUP BY h.line_produksi

) p

LEFT JOIN

(
    SELECT
        t.cost_center,
        SUM(m.qty) AS total_packing

    FROM tbl_transaction_scan t

    INNER JOIN tbl_master_barcode m
        ON m.qr_code = t.qr_code

    WHERE t.type_scan = 'OUT_PACKING'
    AND t.shift = '$shiftNow'
    AND DATE(t.date_scan) = CURDATE()

    GROUP BY t.cost_center

) k

ON CONCAT('Line ', p.line_produksi) = k.cost_center

ORDER BY p.line_produksi

");

$labels = [];
$planData = [];
$packingData = [];

while($row = mysqli_fetch_assoc($chartData))
{
    $labels[] = "Line ".$row['line_produksi'];

    $planData[] = (int)$row['total_plan'];

    $packingData[] = (int)$row['total_packing'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>iPhylon | Dashboard MES</title>

    <!-- Google Font -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700">

    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="plugins/fontawesome-free/css/all.min.css">

    <!-- AdminLTE -->
    <link rel="stylesheet"
        href="dist/css/adminlte.min.css">

    <!-- ChartJS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
                            <?= date('d F Y '); ?> | <strong>Shift :</strong><?= $shiftNow; ?>
                        </h5>
                    </div>
                </div>
            </div>

        </section>

        <!-- CONTENT -->
        <section class="content">
            <div class="container-fluid">
                <!-- CHART + LIVE -->
                <div class="row">

                    <!-- CHART -->
                    <div class="col-md-12">
                        <div class="card card-info">

                            <div class="card-header">
                                <h3 class="card-title">
                                    Production Output
                                </h3>
                            </div>

                            <div class="card-body">
                                <canvas id="productionChart"
                                    height="100">
                                </canvas>
                            </div>
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
const ctx = document.getElementById('productionChart');
new Chart(ctx, {

    type: 'bar',

    data: {

        labels: <?= json_encode($labels); ?>,

        datasets: [

            {
                label: 'Plan',
                data: <?= json_encode($planData); ?>
            },

            {
                label: 'Packing',
                data: <?= json_encode($packingData); ?>
            }

        ]

    },

    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true
            }
        },

        scales: {

            y: {
                beginAtZero: true
            }
        }
    }

});

setInterval(function(){

    location.reload();

}, 60000);

</script>

</body>
</html>