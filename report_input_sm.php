<?php
require 'function.php';

$bucket_from = $_GET['bucket_from'] ?? '';
$bucket_to   = $_GET['bucket_to'] ?? '';
$item_filter = $_GET['item'] ?? '';
$colour_filter = $_GET['colour'] ?? '';
$style_filter  = $_GET['style'] ?? '';
$gender_filter = $_GET['gender'] ?? '';
$size_filter   = $_GET['size'] ?? '';
$type_scan    = $_GET['type_scan'] ?? '';

$reportData = [];
$sizeList = [];

$isSearch = false;

/* =========================
   BUILD WHERE
========================= */
$where = "WHERE 1=1";

if($bucket_from != '' && $bucket_to != ''){
    $where .= " AND mb.bucket BETWEEN '$bucket_from' AND '$bucket_to'";
    $isSearch = true;
}

if($type_scan != ''){
    $where .= " AND ts.type_scan = '$type_scan'";
    $isSearch = true;
}

if($item_filter != ''){
    $where .= " AND mb.item = '$item_filter'";
    $isSearch = true;
}

if($colour_filter != ''){
    $where .= " AND mb.colour = '$colour_filter'";
    $isSearch = true;
}

if($style_filter != ''){
    $where .= " AND mb.style = '$style_filter'";
    $isSearch = true;
}

if($gender_filter != ''){
    $where .= " AND mb.gender = '$gender_filter'";
    $isSearch = true;
}

if($size_filter != ''){
    $where .= " AND mb.size = '$size_filter'";
    $isSearch = true;
}

/* =========================
   QUERY ONLY IF SEARCH
========================= */
if($isSearch){

    $sql = mysqli_query($conn, "
        SELECT
            mb.style,
            mb.item,
            mb.gender,
            mb.colour,
            mb.bucket,
            mb.size,
            COALESCE(SUM(mb.qty),0) AS qty
        FROM tbl_transaction_scan ts
        LEFT JOIN tbl_master_barcode mb
            ON ts.qr_code = mb.qr_code
        $where
        GROUP BY
            mb.style,
            mb.item,
            mb.gender,
            mb.colour,
            mb.bucket,
            mb.size
        ORDER BY
            mb.style,
            mb.item,
            mb.colour,
            mb.bucket,
            mb.size
    ");

    while($row = mysqli_fetch_assoc($sql)){

        if(!in_array($row['size'], $sizeList)){
            $sizeList[] = $row['size'];
        }

        $key = $row['style'].'||'.$row['item'].'||'.$row['colour'].'||'.$row['bucket'];

        if(!isset($reportData[$key])){
            $reportData[$key] = [
                'style'  => $row['style'],
                'item'   => $row['item'],
                'gender' => $row['gender'],
                'colour' => $row['colour'],
                'bucket' => $row['bucket'],
                'total'  => 0
            ];
        }

        $reportData[$key][$row['size']] = $row['qty'];
        $reportData[$key]['total'] += $row['qty'];
    }

}

/* =========================
   SORT SIZE
========================= */
usort($sizeList, function($a,$b){
    $na = intval(str_replace('T','',$a));
    $nb = intval(str_replace('T','',$b));
    return $na <=> $nb;
});



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

// =========================
// MASTER STYLE
// =========================
$listStyle = mysqli_query($conn,"
    SELECT DISTINCT style
    FROM tbl_master_barcode
    WHERE style IS NOT NULL
    AND style <> ''
    ORDER BY style
");

// =========================
// MASTER GENDER
// =========================
$listGender = mysqli_query($conn,"
    SELECT DISTINCT gender
    FROM tbl_master_barcode
    WHERE gender IS NOT NULL
    AND gender <> ''
    ORDER BY gender
");

// =========================
// MASTER SIZE
// =========================
$listSize = mysqli_query($conn,"
    SELECT DISTINCT size
    FROM tbl_master_barcode
    WHERE size IS NOT NULL
    AND size <> ''
    ORDER BY
        CAST(REPLACE(size,'T','') AS UNSIGNED),
        size
");
// =========================
// MASTER TYPE SCAN
// =========================
$listTypeScan = mysqli_query($conn,"
    SELECT DISTINCT type_scan
    FROM tbl_transaction_scan
    WHERE type_scan IS NOT NULL
    AND type_scan <> ''
    ORDER BY type_scan
");
?>
<style>
.dataTables_wrapper {
    width: 100% !important;
}

.table-responsive {
    overflow-x: auto;
}

#reportMinus {
    width: 100% !important;
}

#reportMinus th,
#reportMinus td {
    white-space: nowrap;
    vertical-align: middle;
}

.content-wrapper {
    overflow-x: hidden;
}

.dataTables_length {
    float: left;
}

.dataTables_filter {
    float: right;
}

.dataTables_info {
    float: left;
    margin-top: 10px;
}

.dataTables_paginate {
    float: right;
    margin-top: 10px;
}

</style>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>iPhylon | Report Output by Size</title>

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
                Report Output by Size
                </h1>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                    <a href="index.php">Home</a>
                    </li>
                    <li class="breadcrumb-item active">
                    Report Output by Size
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
<label>Bucket From</label> <label class="text-danger">*</label>

<select class="form-control select2bs4"
        name="bucket_from"
        style="width:100%;"
        required>

<option value="">Select Bucket</option>
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
<label>Bucket To</label> <label class="text-danger">*</label>

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

<div class="col-md-3">
<label>Style</label>

<select class="form-control select2bs4"
        name="style"
        style="width:100%;">

<option value="">Semua Style</option>

<?php while($style = mysqli_fetch_assoc($listStyle)): ?>
<option
value="<?= $style['style']; ?>"
<?= (isset($_GET['style']) && $_GET['style']==$style['style']) ? 'selected' : ''; ?>
>
<?= $style['style']; ?>
</option>

<?php endwhile; ?>


</select>

</div>

<div class="col-md-3">
<label>Gender</label>

<select class="form-control select2bs4"
        name="gender"
        style="width:100%;">

<option value="">Semua Gender</option>

<?php while($gender = mysqli_fetch_assoc($listGender)): ?>
<option
value="<?= $gender['gender']; ?>"
<?= (isset($_GET['gender']) && $_GET['gender']==$gender['gender']) ? 'selected' : ''; ?>
>
<?= $gender['gender']; ?>
</option>

<?php endwhile; ?>


</select>

</div>

<div class="col-md-3">
<label>Size</label>

<select class="form-control select2bs4"
        name="size"
        style="width:100%;">

<option value="">Semua Size</option>

<?php while($size = mysqli_fetch_assoc($listSize)): ?>
<option
value="<?= $size['size']; ?>"
<?= (isset($_GET['size']) && $_GET['size']==$size['size']) ? 'selected' : ''; ?>
>
<?= $size['size']; ?>
</option>

<?php endwhile; ?>


</select>

</div>

<div class="col-md-3">
<label>Type Scan  </label> <label class="text-danger">*</label>

<select class="form-control select2bs4"
        name="type_scan"
        style="width:100%;"
        required>

<option value="">Semua Type Scan</option>

<?php while($typeScan = mysqli_fetch_assoc($listTypeScan)): ?>
<option
value="<?= $typeScan['type_scan']; ?>"
<?= (isset($_GET['type_scan']) && $_GET['type_scan']==$typeScan['type_scan']) ? 'selected' : ''; ?>
>
<?php
$val = trim($typeScan['type_scan'] ?? '');

switch ($val) {
    case 'IN_SM':
        echo 'Scan In Supermarket';
        break;
    case 'OUT_SM':
        echo 'Scan Out Supermarket';
        break;
    case 'OUT_PACKING':
        echo 'Scan Out Packing';
        break;
    default:
        echo $val;
}
?>
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
        onclick="window.location.href = window.location.pathname;">
    <i class="fas fa-sync-alt"></i>
    Reset
</button>

</form>
</div>
</div>

<div class="card card-outline card-success">

    <div class="card-header">

        <h3 class="card-title">
            Detail Input SM by Size
        </h3>

    </div>

    <div class="card-body">
        

        <div class="table-responsive">

            <table id="reportMinus"
                   class="table table-bordered table-striped">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Style</th>
                        <th>Item</th>
                        <th>Gender</th>
                        <th>Colour</th>
                        <th>Bucket</th>
                        <?php foreach($sizeList as $size): ?>
                            <th><?= $size ?></th>
                        <?php endforeach; ?>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $no=1; ?>
                    <?php foreach($reportData as $row): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['style'] ?></td>
                        <td><?= $row['item'] ?></td>
                        <td><?= $row['gender'] ?></td>                                  
                        <td><?= $row['colour'] ?></td>
                        <td><?= $row['bucket'] ?></td>
                        <?php foreach($sizeList as $size): ?>
                            <td class="text-end">
                                <?= isset($row[$size]) ? number_format($row[$size]) : 0 ?>
                            </td>
                        <?php endforeach; ?>
                        <td class="text-end font-weight-bold">
                            <?= number_format($row['total']) ?>
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

<script>

$(function(){

    $('.select2bs4').select2({
        theme: 'bootstrap4',
        placeholder: 'Select Data',
        allowClear: true
    });

        $('#reportMinus').DataTable({
        responsive: false,
        scrollX: true,
        scrollCollapse: true,
        autoWidth: false,

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
                title: 'Report Input SM by Size'
            }
        ]
    });

});

</script>

</body>
</html>


