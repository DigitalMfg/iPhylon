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
| DASHBOARD SUMMARY
|--------------------------------------------------------------------------
*/

// Total Order
$sqlOrder = mysqli_query($conn,"
SELECT COALESCE(SUM(total_order),0) total
FROM tbl_spk_detail
");
$totalOrder = mysqli_fetch_assoc($sqlOrder)['total'];

// =========================
// TOTAL PLANNING
// =========================
$sqlPlanning = mysqli_query($conn,"
SELECT COALESCE(SUM(total_qty),0) AS total
FROM tbl_spk_detail
");

$totalPlanning = mysqli_fetch_assoc($sqlPlanning)['total'];


// Total Production (OUT_PACKING)
$sqlProduction = mysqli_query($conn,"
    SELECT COALESCE(SUM(CAST(mb.qty AS UNSIGNED)),0) AS total
    FROM tbl_transaction_scan ts
    INNER JOIN tbl_master_barcode mb
        ON ts.qr_code = mb.qr_code
    WHERE ts.type_scan = 'OUT_PACKING'
");
$totalProduction = mysqli_fetch_assoc($sqlProduction)['total'];


// Total Stock Supermarket
$sqlStockSM = mysqli_query($conn,"
    SELECT
    (
        SELECT COALESCE(SUM(CAST(mb.qty AS UNSIGNED)),0)
        FROM tbl_transaction_scan ts
        INNER JOIN tbl_master_barcode mb
            ON ts.qr_code = mb.qr_code
        WHERE ts.type_scan='IN_SM'
    )
    -
    (
        SELECT COALESCE(SUM(CAST(mb.qty AS UNSIGNED)),0)
        FROM tbl_transaction_scan ts
        INNER JOIN tbl_master_barcode mb
            ON ts.qr_code = mb.qr_code
        WHERE ts.type_scan='OUT_SM'
    ) AS total
");
$totalStockSM = mysqli_fetch_assoc($sqlStockSM)['total'];


// Progress Production
$remainingPlanning = $totalPlanning - $totalProduction;

// Progress %
$planningPercent = 0;
$orderPercent = 0;
$planningOrderPercent = 0;
$stockSupermarketPercent = 0;
$remainingPercent = 0;

if($totalPlanning > 0){
    $planningPercent = ($totalProduction / $totalPlanning) * 100;
}

if($totalOrder > 0){
    $orderPercent = ($totalProduction / $totalOrder) * 100;

    // Planning dibanding Order
    $planningOrderPercent = ($totalPlanning / $totalOrder) * 100;
}

if($totalPlanning > 0){
    $planningPercent = ($totalProduction/$totalPlanning)*100;
}
if($totalStockSM  > 0){
    $stockSupermarketPercent = ($totalStockSM / $totalPlanning) * 100;
}

if($remainingPlanning > 0){
    $remainingPercent = ($remainingPlanning / $totalPlanning) * 100;
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Data Label -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    

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
                <div class="row mt-12">
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
                                            <?= number_format($planningPercent,1) ?> % of Total Planning
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
                                            Total Stock SM IP
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

                </div>

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

    plugins: [ChartDataLabels],

    type: 'bar',

    data: {

        labels: <?= json_encode($labels); ?>,

        datasets: [
           {
    label: 'Plan',
    data: <?= json_encode($planData); ?>,
    backgroundColor: '#ffc107',
    borderColor: '#ffc107',
    borderWidth: 1
},

{
    label: 'Packing',
    data: <?= json_encode($packingData); ?>,
    backgroundColor: '#28a745',
    borderColor: '#28a745',
    borderWidth: 1
}
        ]
    },

    options: {

    onClick: function(evt, elements)
    {
        if(elements.length > 0)
        {
            let index = elements[0].index;
            let datasetIndex = elements[0].datasetIndex;

            let line =
                this.data.labels[index]
                    .replace('Line ','');

            let type =
                datasetIndex == 0
                ? 'PLAN'
                : 'PACKING';

            loadDetail(line,type);
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
}, 120000);

function loadDetail(line,type)
{
    $('#detailChartModal').modal('show');

    $('#detailChartBody').html('Loading...');

    $.ajax({

        url : 'ajax_dashboard_detail.php',

        type : 'POST',

        data : {
            line : line,
            type : type,
            shift : <?= $shiftNow ?>
        },

        success:function(result)
        {
            $('#detailChartBody').html(result);
        }

    });
}

function loadOutputPerHour(line,shift)
{
    $('#hourlyModal').modal('show');

    $('#hourlyBody').html('Loading...');

    $.ajax({

        url : 'ajax_output_per_hour.php',

        type : 'POST',

        data : {
            line : line,
            shift : shift
        },

        success:function(result)
        {
            $('#hourlyBody').html(result);
        }

    });
}

</script>

    <div class="modal fade" id="detailChartModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h4 class="modal-title">
                        Production Detail
                    </h4>

                    <button type="button"
                            class="close"
                            data-dismiss="modal">
                        &times;
                    </button>
                </div>

                <div class="modal-body" id="detailChartBody">
                    Loading...
                

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="hourlyModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h4 class="modal-title">
                        Output Per Hour
                    </h4>

                    <button type="button"
                            class="close"
                            data-dismiss="modal">
                        &times;
                    </button>
                </div>

                <div class="modal-body"
                    id="hourlyBody">
                    Loading...
                </div>
            </div>
        </div>
    </div>

</body>
</html>