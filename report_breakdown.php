<?php
// session_start();
require 'function.php';

$bucket = $_GET['bucket'] ?? '';
$style_filter  = $_GET['style'] ?? '';
$item_filter   = $_GET['item'] ?? '';
$po_filter     = $_GET['po'] ?? '';
$po_item_filter= $_GET['po_item'] ?? '';
$line_filter   = $_GET['line'] ?? '';
$type_scan = $_GET['type_scan'] ?? 'OUT_PACKING';
$isSearch = isset($_GET['search']);
$dateList = [];

$whereDate = "";

if($bucket != '')
    $whereDate .= " AND mb.bucket='$bucket'";
if($style_filter != '')
    $whereDate .= " AND mb.style='$style_filter'";
if($item_filter != '')
    $whereDate .= " AND mb.item='$item_filter'";
if($po_filter != '')
    $whereDate .= " AND mb.po='$po_filter'";
if($po_item_filter != '')
    $whereDate .= " AND mb.po_item='$po_item_filter'";
if($line_filter != '')
    $whereDate .= " AND mb.line='$line_filter'";

$getDate = mysqli_query($conn,"
SELECT DISTINCT DATE(ts.date_scan) AS tanggal
FROM tbl_transaction_scan ts

INNER JOIN tbl_master_barcode mb
ON ts.qr_code = mb.qr_code

WHERE ts.type_scan='$type_scan'
$whereDate

ORDER BY DATE(ts.date_scan)
");
while($d=mysqli_fetch_assoc($getDate)){
    $dateList[] = $d['tanggal'];
}

$sizes = [
    '1','1T','2','2T','3','3T',
    '4','4T','5','5T','6','6T',
    '7','7T','8','8T','9','9T',
    '10','10T','11','11T','12','12T',
    '13','13T','14','14T','15'
];

$plan = [];

$wherePlan = "";

if($bucket != '')
    $wherePlan .= " AND bucket='$bucket'";
if($style_filter != '')
    $wherePlan .= " AND style='$style_filter'";
if($item_filter != '')
    $wherePlan .= " AND item='$item_filter'";
if($po_filter != '')
    $wherePlan .= " AND po='$po_filter'";
if($po_item_filter != '')
    $wherePlan .= " AND po_item='$po_item_filter'";
if($line_filter != '')
    $wherePlan .= " AND line='$line_filter'";

$qPlan = mysqli_query($conn,"
SELECT
    ssq.size,
    SUM(ssq.qty) AS qty
FROM tbl_spk_detail sd
INNER JOIN tbl_spk_size_qty ssq
    ON sd.id_detail = ssq.id_detail
WHERE 1=1
$wherePlan
GROUP BY ssq.size
ORDER BY ssq.size
");

while($r=mysqli_fetch_assoc($qPlan)){
    $plan[$r['size']] = $r['qty'];
}

$scanData = [];

$qScan = mysqli_query($conn,"
SELECT

    DATE(ts.date_scan) AS tanggal,

    mb.size,

    SUM(mb.qty) AS qty

FROM tbl_transaction_scan ts

INNER JOIN tbl_master_barcode mb
    ON ts.qr_code = mb.qr_code

WHERE

    ts.type_scan='$type_scan'

    ".($bucket != '' ? "AND mb.bucket='$bucket'" : "")."
    ".($style_filter != '' ? "AND mb.style='$style_filter'" : "")."
    ".($item_filter != '' ? "AND mb.item='$item_filter'" : "")."
    ".($po_filter != '' ? "AND mb.po='$po_filter'" : "")."
    ".($po_item_filter != '' ? "AND mb.po_item='$po_item_filter'" : "")."
    ".($line_filter != '' ? "AND mb.line='$line_filter'" : "")."

GROUP BY

    DATE(ts.date_scan),

    mb.size

ORDER BY

    DATE(ts.date_scan),
    mb.size

");

while($r=mysqli_fetch_assoc($qScan))
{

    $scanData[
        $r['tanggal']
    ][
        $r['size']
    ] = $r['qty'];

}

$filterMaster = "";

if($bucket != '')
    $filterMaster .= " AND bucket='$bucket'";

if($style_filter != '')
    $filterMaster .= " AND style='$style_filter'";

if($item_filter != '')
    $filterMaster .= " AND item='$item_filter'";

if($po_filter != '')
    $filterMaster .= " AND po='$po_filter'";

if($po_item_filter != '')
    $filterMaster .= " AND po_item='$po_item_filter'";

if($line_filter != '')
    $filterMaster .= " AND line='$line_filter'";

// =========================
// MASTER BUCKET
// =========================
$listBucket = mysqli_query($conn,"
    SELECT DISTINCT bucket
    FROM tbl_spk_detail
    WHERE bucket IS NOT NULL
    AND bucket <> ''
    ORDER BY bucket
");

// =========================
// MASTER ITEM
// =========================
$whereItem = "";

$listItem = mysqli_query($conn,"
SELECT DISTINCT item
FROM tbl_master_barcode
WHERE item IS NOT NULL
AND item <> ''
$filterMaster
ORDER BY item
");

// =========================
// MASTER STYLE
// =========================
$whereStyle = "";

$listStyle = mysqli_query($conn,"
SELECT DISTINCT style
FROM tbl_master_barcode
WHERE style IS NOT NULL
AND style <> ''
$filterMaster
ORDER BY style
");

// =========================
// MASTER PO
// =========================
$wherePO = "";
$listPO = mysqli_query($conn,"
SELECT DISTINCT po
FROM tbl_master_barcode
WHERE po IS NOT NULL
AND po <> ''
$filterMaster
ORDER BY po
");

// =========================
// MASTER PO ITEM
// =========================
$listPOItem = mysqli_query($conn,"
SELECT DISTINCT po_item
FROM tbl_master_barcode
WHERE po_item IS NOT NULL
AND po_item <> ''
$filterMaster
ORDER BY po_item
");

// =========================
// MASTER LINE
// =========================
$listLine = mysqli_query($conn,"
SELECT DISTINCT line
FROM tbl_master_barcode
WHERE line IS NOT NULL
AND line <> ''
$filterMaster
ORDER BY line
");

?>
<style>
.dataTables_wrapper {
    width: 100% !important;
}

.dataTables_length {
    float: left !important;
}

.dataTables_filter {
    float: right !important;
}

.dataTables_info {
    float: left !important;
    margin-top: 10px;
}

.dataTables_paginate {
    float: right !important;
    margin-top: 10px;
}
</style>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>iPhylon | Report Breakdown</title>

<link rel="icon" href="assets/images/i.Phylon.png" type="image/x-icon">
<link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="dist/css/adminlte.min.css">
<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.dataTables.min.css">

</head>

<body class="hold-transition sidebar-mini">

<div class="wrapper">

<?php include 'header.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <h1>
                <i class="fas fa-chart-bar"></i>
                Report Breakdown
                </h1>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                    <a href="index.php">Home</a>
                    </li>
                    <li class="breadcrumb-item active">
                    Report Report Breakdown
                    </li>
                </ol>
            </div>

        </div>

        </div>
    </section>

    <section class="content">
<div class="container-fluid">

<div class="card card-outline card-primary">

<div class="card-header">
<h3 class="card-title">
Filter Report
</h3>
</div>

<div class="card-body">
<form method="GET">
<div class="row">

<div class="col-md-4">
    <label>Bucket</label> <label class="text-danger">*</label>
        <select
            class="form-control select2bs4"
            name="bucket"
            required>

            <option value="">
            Pilih Bucket
            </option>

            <?php while($b=mysqli_fetch_assoc($listBucket)): ?>

            <option
            value="<?= $b['bucket'] ?>"
            <?= ($bucket==$b['bucket'])?'selected':'' ?>>

            <?= $b['bucket'] ?>

            </option>

            <?php endwhile; ?>

        </select>
</div>

<div class="col-md-4">
    <label>Style</label>

    <select class="form-control select2bs4"
            name="style"
            style="width:100%;">
        <option value="">Semua Style</option>
        <?php while($style_filter = mysqli_fetch_assoc($listStyle)): ?>

        <option
            value="<?= $style_filter['style']; ?>"
            <?= (isset($_GET['style']) && $_GET['style']==$style_filter['style']) ? 'selected' : ''; ?>
            >
            <?= $style_filter['style']; ?>
        </option>
        <?php endwhile; ?>

    </select>
</div>

<div class="col-md-4">
    <label>Item</label>

    <select class="form-control select2bs4"
            name="item"
            style="width:100%;">
        <option value="">Semua Item</option>
        <?php while($item = mysqli_fetch_assoc($listItem)): ?>

        <option
            value="<?= $item['item']; ?>"
            <?= (isset($_GET['item']) && $_GET['item']==$item['item']) ? 'selected' : ''; ?>
            >
            <?= $item['item']; ?>
        </option>
        <?php endwhile; ?>

    </select>
</div>

<div class="col-md-4">
    <label>PO</label>

    <select class="form-control select2bs4"
            name="po"
            style="width:100%;">
        <option value="">Semua PO</option>
        <?php while($po = mysqli_fetch_assoc($listPO)): ?>

        <option
            value="<?= $po['po']; ?>"
            <?= (isset($_GET['po']) && $_GET['po']==$po['po']) ? 'selected' : ''; ?>
            >
            <?= $po['po']; ?>
        </option>
        <?php endwhile; ?>
    </select>
</div>

<div class="col-md-4">
    <label>Po-Item</label>

    <select class="form-control select2bs4"
            name="po_item"
            style="width:100%;">
        <option value="">Semua PO Item</option>
        <?php while($poItem = mysqli_fetch_assoc($listPOItem)): ?>

        <option
            value="<?= $poItem['po_item']; ?>"
            <?= (isset($_GET['po_item']) && $_GET['po_item']==$poItem['po_item']) ? 'selected' : ''; ?>
            >
            <?= $poItem['po_item']; ?>
        </option>
        <?php endwhile; ?>
    </select>
</div>

<div class="col-md-4">
    <label>Line</label>

    <select class="form-control select2bs4"
            name="line"
            style="width:100%;">
        <option value="">Semua Line</option>
        <?php while($line = mysqli_fetch_assoc($listLine)): ?>

        <option
            value="<?= $line['line']; ?>"
            <?= (isset($_GET['line']) && $_GET['line']==$line['line']) ? 'selected' : ''; ?>
            >
            <?= $line['line']; ?>
        </option>
        <?php endwhile; ?>
    </select>
</div>
</div>

<br>
<button type="submit"
        name="search"
        value="1"
        class="btn btn-primary"
        style="width:100px;">
    <i class="fas fa-search"></i>
    Search
</button>

<button type="button"
        class="btn btn-secondary"
        style="width:100px;"
        onclick="window.location='report_breakdown.php'">
    <i class="fas fa-sync-alt"></i>
    Reset
</button>

</form>
</div>
</div>

<div class="card card-outline card-success">

    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">
        Report Breakdown
        </h3>

        <div class="btn-group" role="group" aria-label="Type Scan">
            <a
            href="?<?= http_build_query(array_merge($_GET,['search'=>1,'type_scan'=>'OUT_PACKING'])) ?>"
            class="btn <?= $type_scan=='OUT_PACKING'?'btn-success':'btn-outline-success' ?>">
            Out Packing
            </a>

            <a
            href="?<?= http_build_query(array_merge($_GET,['search'=>1,'type_scan'=>'IN_SM'])) ?>"
            class="btn <?= $type_scan=='IN_SM'?'btn-primary':'btn-outline-success' ?>">
            In Supermarket
            </a>

            <a
            href="?<?= http_build_query(array_merge($_GET,['search'=>1,'type_scan'=>'OUT_SM'])) ?>"
            class="btn <?= $type_scan=='OUT_SM'?'btn-warning':'btn-outline-success' ?>">
            Out Supermarket
            </a>

        </div>

    </div>

    <div class="card-body">
        

        <div class="table-responsive report-table">

            <table id="ReportStock"
                   class="table table-bordered table-striped">

                <thead>

                    <tr>
                        <th>Date</th>
                        <th>Desc</th>
                        <?php
                        $grandPlan = 0;
                        foreach($sizes as $size):
                        ?>
                            <th><?= $size ?></th>
                        <?php endforeach; ?>
                        <th>Total</th>
                    </tr>
                    </thead>

            <tbody>
                    <?php if($isSearch && $bucket != ''): ?>

                        <?php
                            // Running Remaining
                            $runningPlan = $plan;
                            // Hitung total plan
                            $planTotal = array_sum($plan);
                            ?>
                        <tr class="table">

                            <td></td>
                            <td><b>Plan</b></td>
                            <?php foreach($sizes as $size): ?>
                                <td class="text-right">
                                    <?= number_format($plan[$size] ?? 0) ?>
                                </td>
                            <?php endforeach; ?>
                            <td class="text-right">
                                <b><?= number_format($planTotal) ?></b>
                            </td>
                        </tr>
                        <?php foreach($dateList as $tanggal): ?>
                            <?php
                                $totalOut = 0;
                                $totalRemain = 0;
                            ?>

                            <tr>
                                <td><?= date('d-M-Y', strtotime($tanggal)); ?></td>
                                <td><b><?= str_replace('_',' ', $type_scan); ?></b></td>
                                <?php foreach($sizes as $size): ?>
                                    <?php
                                        $qty = $scanData[$tanggal][$size] ?? 0;
                                        $totalOut += $qty;
                                    ?>
                                    <td class="text-right">
                                        <?= number_format($qty) ?>
                                    </td>
                                <?php endforeach; ?>

                                <td class="text-right">
                                    <b><?= number_format($totalOut) ?></b>
                                </td>

                            </tr>

                            <?php
                                foreach($sizes as $size){
                                    $runningPlan[$size] -= ($scanData[$tanggal][$size] ?? 0);
                                    $totalRemain += $runningPlan[$size];
                                }
                                ?>
                                <tr class="table">
                                    <td></td>
                                    <td><b>Remaining</b></td>
                                    <?php foreach($sizes as $size): ?>
                                        <td class="text-right">
                                            <?= number_format($runningPlan[$size]) ?>
                                        </td>
                                    <?php endforeach; ?>
                                    <td class="text-right">
                                        <b><?= number_format($totalRemain) ?></b>
                                    </td>
                                </tr>

                            <?php endforeach; ?>
                        <?php endif; ?>
            </tbody>

                
            </table>
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

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="plugins/select2/js/select2.full.min.js"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>

<script>

$(function(){

    $('.select2bs4').select2({
        theme: 'bootstrap4',
        placeholder: 'Select Data',
        allowClear: true
    });

    $('#ReportStock').DataTable({
        responsive:false,
    autoWidth:false,
    paging:true,
    searching:true,
    ordering:false,
        lengthMenu: [10, 25, 50, 100, 250, 500],

        dom:
            "<'row mb-2'<'col-sm-6 d-flex align-items-center'l><'col-sm-6 d-flex justify-content-end gap-2'Bf>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row mt-2'<'col-sm-5'i><'col-sm-7'p>>",

        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Export Excel',
                className: 'btn btn-success btn-sm',
                title: 'Report Breakdown'
            }
        ]
    });

});

</script>

</body>
</html>


