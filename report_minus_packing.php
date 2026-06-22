<?php
// session_start();
require 'function.php';

$bucket_from = $_GET['bucket_from'] ?? '';
$bucket_to   = $_GET['bucket_to'] ?? '';
$item_filter = $_GET['item'] ?? '';
$colour_filter = $_GET['colour'] ?? '';

$bucketList = [];

if($bucket_from != '' && $bucket_to != ''){

    $getBucket = mysqli_query($conn,"
        SELECT DISTINCT bucket
        FROM tbl_spk_detail
        WHERE bucket >= '$bucket_from'
        AND bucket <= '$bucket_to'
        ORDER BY bucket
    ");
    
    while($row = mysqli_fetch_assoc($getBucket)){
        $bucketList[] = $row['bucket'];
    }

}

$reportData = [];

if(count($bucketList) > 0){

    $where = "";

    if($item_filter != ''){
        $where .= " AND item='$item_filter'";
    }

    if($colour_filter != ''){
        $where .= " AND colour='$colour_filter'";
    }

    $sql = mysqli_query($conn,"

        SELECT

            p.item,
            p.colour,
            p.bucket,
            p.qty_planning,

            COALESCE(o.qty_out,0) AS qty_out

        FROM

        (
            SELECT

                item,
                colour,
                bucket,

                SUM(qty) AS qty_planning

            FROM tbl_master_barcode

            WHERE bucket BETWEEN '$bucket_from'
            AND '$bucket_to'
            $where
            GROUP BY
                item,
                colour,
                bucket

        ) p

        LEFT JOIN

        (
            SELECT

                mb.item,
                mb.colour,
                mb.bucket,

                SUM(mb.qty) AS qty_out

            FROM tbl_transaction_scan ts

            INNER JOIN tbl_master_barcode mb
                ON ts.qr_code = mb.qr_code

            WHERE ts.type_scan='OUT_PACKING'

            GROUP BY
                mb.item,
                mb.colour,
                mb.bucket

        ) o

        ON p.item = o.item
        AND p.colour = o.colour
        AND p.bucket = o.bucket

        ORDER BY
            p.item,
            p.colour,
            p.bucket

        ");

        

    while($row = mysqli_fetch_assoc($sql)){

        $item   = $row['item'];
        $colour = $row['colour'];

        $key = $item.'||'.$colour;

        if(!isset($reportData[$key])){

            $reportData[$key] = [
                'item' => $item,
                'colour' => $colour,
                'total' => 0
            ];
        }

        $minus = $row['qty_planning'] - $row['qty_out'];
        if($minus <= 0){continue;}

        $reportData[$key][$row['bucket']] = $minus;

        $reportData[$key]['total'] += $minus;

    }

}

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
$listItem = mysqli_query($conn,"
    SELECT DISTINCT item
    FROM tbl_master_barcode
    WHERE item IS NOT NULL
    AND item <> ''
    ORDER BY item
");

// =========================
// MASTER COLOUR
// =========================
$listColour = mysqli_query($conn,"
    SELECT DISTINCT colour
    FROM tbl_master_barcode
    WHERE colour IS NOT NULL
    AND colour <> ''
    ORDER BY colour
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

<title>iPhylon | Report Minus Packing</title>

<link rel="icon" href="assets/images/i.Phylon.png" type="image/x-icon">

<link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="dist/css/adminlte.min.css">

<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

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
                Report Minus Packing
                </h1>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                    <a href="index.php">Home</a>
                    </li>
                    <li class="breadcrumb-item active">
                    Report Minus Packing
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

<div class="col-md-3">
<label>Bucket From</label>

<select class="form-control select2bs4"
        name="bucket_from"
        style="width:100%;"
        required>

<option value="">Pilih Bucket</option>
<?php while($bucket = mysqli_fetch_assoc($listBucket)): ?>
<option
value="<?= $bucket['bucket']; ?>"
<?= (isset($_GET['bucket_from']) && $_GET['bucket_from']==$bucket['bucket']) ? 'selected' : ''; ?>
>
<?= $bucket['bucket']; ?>
</option>
<?php endwhile; ?>

</select>

</div>

<div class="col-md-3">
<label>Bucket To</label>

<select class="form-control select2bs4"
        name="bucket_to"
        style="width:100%;"
        required>
        <?php

$listBucket2 = mysqli_query($conn,"
SELECT DISTINCT bucket
FROM tbl_spk_detail
WHERE bucket IS NOT NULL
AND bucket <> ''
ORDER BY bucket
");

?>

<option value="">Select Bucket</option>

<?php while($bucket = mysqli_fetch_assoc($listBucket2)): ?>

<option
value="<?= $bucket['bucket']; ?>"
<?= (isset($_GET['bucket_to']) && $_GET['bucket_to']==$bucket['bucket']) ? 'selected' : ''; ?>
>
<?= $bucket['bucket']; ?>
</option>

<?php endwhile; ?>

</select>

</div>

<div class="col-md-3">
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

<div class="col-md-3">
<label>Colour</label>

<select class="form-control select2bs4"
        name="colour"
        style="width:100%;">

<option value="">Semua Colour</option>

<?php while($colour = mysqli_fetch_assoc($listColour)): ?>
<option
value="<?= $colour['colour']; ?>"
<?= (isset($_GET['colour']) && $_GET['colour']==$colour['colour']) ? 'selected' : ''; ?>
>
<?= $colour['colour']; ?>
</option>

<?php endwhile; ?>


</select>

</div>

</div>

<br>

<button type="submit"
        class="btn btn-primary"
        style="width:100px;">
    <i class="fas fa-search"></i>
    Search
</button>

<button type="button"
        class="btn btn-secondary"
        style="width:100px;"
        onclick="window.location='report_minus_packing.php'">
    <i class="fas fa-sync-alt"></i>
    Reset
</button>

</form>
</div>
</div>

<div class="card card-outline card-success">

    <div class="card-header">

        <h3 class="card-title">
            Detail Minus Packing
        </h3>

    </div>

    <div class="card-body">
        

        <div class="table-responsive">

            <table id="reportMinus"
                   class="table table-bordered table-striped">

                <thead>

                    <tr>
                        <th>Item</th>
                        <th>Colour</th>

                        <?php foreach($bucketList as $bucket): ?>
                        <th class="text-center"><?= $bucket ?></th>
                        <?php endforeach; ?>

                        <th>Total</th>
                    </tr>

                </thead>

                <tbody>
                    <?php foreach($reportData as $row): ?>
                    <tr>
                        <td><?= $row['item'] ?></td>
                        <td><?= $row['colour'] ?></td>
                        <?php foreach($bucketList as $bucket): ?>

                        <?php
                        $value = $row[$bucket] ?? 0;
                        ?>
                        <td class="<?= $value > 0 ? 'bg-danger' : '' ?>">
                            <?= $value ?>
                        </td>
                        <?php endforeach; ?>
                        <td class="font-weight-bold">
                        <?= $row['total'] ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>
        </div>
    </div>
</div>

</div>
</section>

</div>

<footer class="main-footer">
<strong>Mfg Project Officer</strong>
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

<script>

$(function(){

    $('.select2bs4').select2({
        theme: 'bootstrap4',
        placeholder: 'Pilih Data',
        allowClear: true
    });

    $('#reportMinus').DataTable({
        responsive: true,
        paging: true,
        searching: true,
        ordering: true,
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
                title: 'Report Minus Packing'
            }
        ]
    });

});

</script>

</body>
</html>


