<?php
// session_start();
require 'function.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

/* =========================
   SAMPLE KPI QUERY
========================= */
// $totalPlanning = mysqli_fetch_assoc(mysqli_query($conn,"
//     SELECT COUNT(*) total
//     FROM tbl_jo_spk
// "))['total'];

// $totalScanIn = mysqli_fetch_assoc(mysqli_query($conn,"
//     SELECT COUNT(*) total
//     FROM tbl_scan_in
// "))['total'] ?? 0;

// $totalScanOut = mysqli_fetch_assoc(mysqli_query($conn,"
//     SELECT COUNT(*) total
//     FROM tbl_scan_out
// "))['total'] ?? 0;

// $totalPrinted = mysqli_fetch_assoc(mysqli_query($conn,"
//     SELECT COUNT(*) total
//     FROM tbl_spk_size_qty
//     WHERE status_print='YES'
// "))['total'] ?? 0;

// $totalNotPrinted = mysqli_fetch_assoc(mysqli_query($conn,"
//     SELECT COUNT(*) total
//     FROM tbl_spk_size_qty
//     WHERE status_print='NO'
// "))['total'] ?? 0;


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

    <style>

        body{
        background: #f4f6f9;
        }

        .content-wrapper{
            background: #f4f6f9;
        }

        .small-box{
            border-radius: 12px;
        }

        .card{
            border-radius: 12px;
        }

        .table td,
        .table th{
            white-space: nowrap;
        }

        .line-card{
            border-left: 5px solid #28a745;
        }

        .line-stop{
            border-left: 5px solid #dc3545;
        }

        .live-box{
            height: 350px;
            overflow-y: auto;
        }

    </style>

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
                            Dashboard MES
                        </h1>
                    </div>

                    <div class="col-sm-6 text-right">

                        <h5>
                            <?= date('d F Y H:i:s'); ?>
                        </h5>

                    </div>

                </div>

            </div>

        </section>

        <!-- CONTENT -->
        <section class="content">

            <div class="container-fluid">

                <!-- KPI -->
                <div class="row">

                    <!-- Planning -->
                    <div class="col-lg-3 col-6">

                        <div class="small-box bg-info">

                            <div class="inner">

                                <h3>
                                    <?= $totalPlanning; ?>
                                </h3>

                                <p>
                                    Total Planning
                                </p>

                            </div>

                            <div class="icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>

                        </div>

                    </div>

                    <!-- Scan IN -->
                    <div class="col-lg-3 col-6">

                        <div class="small-box bg-success">

                            <div class="inner">

                                <h3>
                                    <?= $totalScanIn; ?>
                                </h3>

                                <p>
                                    Total Scan IN
                                </p>

                            </div>

                            <div class="icon">
                                <i class="fas fa-sign-in-alt"></i>
                            </div>

                        </div>

                    </div>

                    <!-- Scan OUT -->
                    <div class="col-lg-3 col-6">

                        <div class="small-box bg-warning">

                            <div class="inner">

                                <h3>
                                    <?= $totalScanOut; ?>
                                </h3>

                                <p>
                                    Total Scan OUT
                                </p>

                            </div>

                            <div class="icon">
                                <i class="fas fa-sign-out-alt"></i>
                            </div>

                        </div>

                    </div>

                    <!-- QR Printed -->
                    <div class="col-lg-3 col-6">

                        <div class="small-box bg-danger">

                            <div class="inner">

                                <h3>
                                    <?= $totalPrinted; ?>
                                </h3>

                                <p>
                                    QR Printed
                                </p>

                            </div>

                            <div class="icon">
                                <i class="fas fa-qrcode"></i>
                            </div>

                        </div>

                    </div>

                </div>

                <!-- CHART + LIVE -->
                <div class="row">

                    <!-- CHART -->
                    <div class="col-md-8">

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

                    <!-- LIVE ACTIVITY -->
                    <div class="col-md-4">

                        <div class="card card-success">

                            <div class="card-header">

                                <h3 class="card-title">
                                    Live Scan Activity
                                </h3>

                            </div>

                            <div class="card-body live-box">

                                <ul class="list-group">

                                    <li class="list-group-item">
                                        08:20 - Line 1 - IN - Bucket 260518
                                    </li>

                                    <li class="list-group-item">
                                        08:22 - Line 2 - OUT - Bucket 260520
                                    </li>

                                    <li class="list-group-item">
                                        08:25 - Line 4 - IN - Bucket 260521
                                    </li>

                                    <li class="list-group-item">
                                        08:27 - Line 1 - OUT - Bucket 260510
                                    </li>

                                </ul>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- LINE MONITOR -->
                <div class="row">

                    <!-- LINE 1 -->
                    <div class="col-md-3">

                        <div class="card line-card">

                            <div class="card-body">

                                <h4>
                                    Line 1
                                </h4>

                                <h5 class="text-success">
                                    ● RUNNING
                                </h5>

                                <hr>

                                <p>
                                    Target : 2000
                                </p>

                                <p>
                                    Actual : 1850
                                </p>

                                <p>
                                    Achievement : 92%
                                </p>

                                <div class="progress">

                                    <div class="progress-bar bg-success"
                                        style="width:92%">
                                        92%
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- LINE 2 -->
                    <div class="col-md-3">

                        <div class="card line-stop">

                            <div class="card-body">

                                <h4>
                                    Line 2
                                </h4>

                                <h5 class="text-danger">
                                    ● STOP
                                </h5>

                                <hr>

                                <p>
                                    Target : 1800
                                </p>

                                <p>
                                    Actual : 900
                                </p>

                                <p>
                                    Problem : Material Delay
                                </p>

                                <div class="progress">

                                    <div class="progress-bar bg-danger"
                                        style="width:50%">
                                        50%
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- ALERT -->
                <div class="row">

                    <div class="col-md-12">

                        <div class="card card-danger">

                            <div class="card-header">

                                <h3 class="card-title">
                                    Alert & Problem Monitoring
                                </h3>

                            </div>

                            <div class="card-body p-0">

                                <table class="table table-bordered">

                                    <thead>

                                        <tr>

                                            <th>Problem</th>
                                            <th>Total</th>
                                            <th>Status</th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <tr>

                                            <td>
                                                QR Belum Print
                                            </td>

                                            <td>
                                                <?= $totalNotPrinted; ?>
                                            </td>

                                            <td>
                                                <span class="badge badge-danger">
                                                    Critical
                                                </span>
                                            </td>

                                        </tr>

                                        <tr>

                                            <td>
                                                Scan Mismatch
                                            </td>

                                            <td>
                                                5
                                            </td>

                                            <td>
                                                <span class="badge badge-warning">
                                                    Warning
                                                </span>
                                            </td>

                                        </tr>

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>

    </div>

    <!-- FOOTER -->
    <footer class="main-footer">

        <strong>
            Mfg Project Officer
        </strong>

        <div class="float-right d-none d-sm-inline-block">

            <b>Version</b> 1.0.0

        </div>

    </footer>

</div>

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>

<!-- Bootstrap -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- AdminLTE -->
<script src="dist/js/adminlte.min.js"></script>

<script>

    /* CHART */

    const ctx = document.getElementById('productionChart');

    new Chart(ctx, {

        type: 'bar',

        data: {

            labels: [
                '08:00',
                '09:00',
                '10:00',
                '11:00',
                '12:00',
                '13:00'
            ],

            datasets: [{

                label: 'Output Production',

                data: [
                    120,
                    190,
                    300,
                    500,
                    200,
                    350
                ]

            }]

        },

        options: {

            responsive: true,

            plugins: {

                legend: {
                    labels: {
                        color: 'white'
                    }
                }

            },

            scales: {

                x: {
                    ticks: {
                        color: 'white'
                    }
                },

                y: {
                    ticks: {
                        color: 'white'
                    }
                }

            }

        }

    });

    /* AUTO REFRESH */

    setInterval(function(){

        location.reload();

    }, 60000);

</script>

</body>
</html>