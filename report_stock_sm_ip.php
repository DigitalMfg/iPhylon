<?php
// session_start();
require 'function.php';

$bucket_from   = $_GET['bucket_from'] ?? '';
$bucket_to     = $_GET['bucket_to'] ?? '';
$item_filter   = $_GET['item'] ?? '';
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

    $sql = mysqli_query($conn, "

        SELECT
            p.style,
            p.item,
            p.colour,
            p.gender,
            p.bucket,

            p.qty_order,

            COALESCE(i.qty_in,0) AS qty_in,
            COALESCE(o.qty_out,0) AS qty_out,

            (
                COALESCE(i.qty_in,0) -
                COALESCE(o.qty_out,0)
            ) AS balance

        FROM
        (
            SELECT
                style,
                item,
                colour,
                gender,
                bucket,

                SUM(qty) AS qty_order

            FROM tbl_master_barcode

            WHERE bucket BETWEEN '$bucket_from' AND '$bucket_to'
            $where

            GROUP BY
                style,
                item,
                colour,
                gender,
                bucket

        ) p


        LEFT JOIN
        (
            SELECT
                mb.style,
                mb.item,
                mb.gender,
                mb.colour,
                mb.bucket,

                SUM(mb.qty) AS qty_in

            FROM tbl_transaction_scan ts

            INNER JOIN tbl_master_barcode mb
                ON ts.qr_code = mb.qr_code

            WHERE ts.type_scan = 'IN_SM'

            GROUP BY
                mb.style,
                mb.item,
                mb.gender,
                mb.colour,
                mb.bucket

        ) i
            ON p.style  = i.style
            AND p.item   = i.item
            AND p.gender = i.gender
            AND p.colour = i.colour
            AND p.bucket = i.bucket


        LEFT JOIN
        (
            SELECT
                mb.style,
                mb.item,
                mb.gender,
                mb.colour,
                mb.bucket,

                SUM(mb.qty) AS qty_out

            FROM tbl_transaction_scan ts

            INNER JOIN tbl_master_barcode mb
                ON ts.qr_code = mb.qr_code

            WHERE ts.type_scan = 'OUT_SM'

            GROUP BY
                mb.style,
                mb.item,
                mb.gender,
                mb.colour,
                mb.bucket

        ) o
            ON p.style  = o.style
            AND p.item   = o.item
            AND p.gender = o.gender
            AND p.colour = o.colour
            AND p.bucket = o.bucket


        ORDER BY
            p.item,
            p.colour,
            p.bucket,
            p.style

    ");

    while($row = mysqli_fetch_assoc($sql)){

        /*
        |--------------------------------------------------------------------------
        | KEY WAJIB PAKAI STYLE
        |--------------------------------------------------------------------------
        */

        $key =
            $row['style'].'||'.
            $row['item'].'||'.
            $row['gender'].'||'.
            $row['colour'].'||'.
            $row['bucket'];

        $reportData[$key] = [

            'style'      => $row['style'],
            'item'       => $row['item'],
            'gender'     => $row['gender'],
            'colour'     => $row['colour'],
            'bucket'     => $row['bucket'],

            'qty_order'  => $row['qty_order'],
            'qty_in'     => $row['qty_in'],
            'qty_out'    => $row['qty_out'],
            'balance'    => $row['balance']

        ];
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

.dataTables_wrapper{
    width:100%;
}

.table-responsive{
    overflow-x:auto;
}

#ReportStock{
    width:100% !important;
}

#ReportStock th,
#ReportStock td{
    white-space:nowrap;
    vertical-align:middle;
}

.content-wrapper{
    overflow-x:hidden;
}

.dataTables_length{
    float:left;
}

.dataTables_filter{
    float:right;
}

.dataTables_info{
    float:left;
    margin-top:10px;
}

.dataTables_paginate{
    float:right;
    margin-top:10px;
}

</style>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>iPhylon | Report Stock SM IP</title>

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
            Report Stock SM IP
        </h1>

    </div>

    <div class="col-sm-6">

        <ol class="breadcrumb float-sm-right">

            <li class="breadcrumb-item">
                <a href="index.php">Home</a>
            </li>

            <li class="breadcrumb-item active">
                Report Stock SM IP
            </li>

        </ol>

    </div>

</div>

</div>

</section>


<section class="content">

<div class="container-fluid">


<!-- FILTER -->
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
<label class="text-danger">*</label>

<select
    class="form-control select2bs4"
    name="bucket_from"
    style="width:100%;"
    required
>

<option value="">Pilih Bucket</option>

<?php while($bucket = mysqli_fetch_assoc($listBucket)): ?>

<option
    value="<?= $bucket['bucket']; ?>"
    <?= (
        isset($_GET['bucket_from']) &&
        $_GET['bucket_from'] == $bucket['bucket']
    ) ? 'selected' : ''; ?>
>
    <?= $bucket['bucket']; ?>
</option>

<?php endwhile; ?>

</select>

</div>


<div class="col-md-3">

<label>Bucket To</label>
<label class="text-danger">*</label>

<?php

$listBucket2 = mysqli_query($conn,"
    SELECT DISTINCT bucket
    FROM tbl_spk_detail
    WHERE bucket IS NOT NULL
    AND bucket <> ''
    ORDER BY bucket
");

?>

<select
    class="form-control select2bs4"
    name="bucket_to"
    style="width:100%;"
    required
>

<option value="">Select Bucket</option>

<?php while($bucket = mysqli_fetch_assoc($listBucket2)): ?>

<option
    value="<?= $bucket['bucket']; ?>"
    <?= (
        isset($_GET['bucket_to']) &&
        $_GET['bucket_to'] == $bucket['bucket']
    ) ? 'selected' : ''; ?>
>
    <?= $bucket['bucket']; ?>
</option>

<?php endwhile; ?>

</select>

</div>


<div class="col-md-3">

<label>Item</label>

<select
    class="form-control select2bs4"
    name="item"
    style="width:100%;"
>

<option value="">Semua Item</option>

<?php while($item = mysqli_fetch_assoc($listItem)): ?>

<option
    value="<?= $item['item']; ?>"
    <?= (
        isset($_GET['item']) &&
        $_GET['item'] == $item['item']
    ) ? 'selected' : ''; ?>
>
    <?= $item['item']; ?>
</option>

<?php endwhile; ?>

</select>

</div>


<div class="col-md-3">

<label>Colour</label>

<select
    class="form-control select2bs4"
    name="colour"
    style="width:100%;"
>

<option value="">Semua Colour</option>

<?php while($colour = mysqli_fetch_assoc($listColour)): ?>

<option
    value="<?= $colour['colour']; ?>"
    <?= (
        isset($_GET['colour']) &&
        $_GET['colour'] == $colour['colour']
    ) ? 'selected' : ''; ?>
>
    <?= $colour['colour']; ?>
</option>

<?php endwhile; ?>

</select>

</div>


</div>

<br>

<button
    type="submit"
    class="btn btn-primary"
    style="width:100px;"
>
    <i class="fas fa-search"></i>
    Search
</button>


<button
    type="button"
    class="btn btn-secondary"
    style="width:100px;"
    onclick="window.location='report_stock_sm_ip.php'"
>
    <i class="fas fa-sync-alt"></i>
    Reset
</button>

</form>

</div>

</div>


<!-- TABLE -->
<div class="card card-outline card-success">

<div class="card-header">

<h3 class="card-title">
Detail Stock SM IP
</h3>

</div>


<div class="card-body">

<div class="table-responsive">

<table
    id="ReportStock"
    class="table table-bordered table-striped"
>

<thead>

<tr>

    <th>No</th>
    <th>Style</th>
    <th>Item</th>
    <th>Gender</th>
    <th>Colour</th>
    <th>Bucket</th>
    <th>Qty Order</th>
    <th>Scan In</th>
    <th>Scan Out</th>
    <th>Stock</th>

</tr>

</thead>


<tbody>

<?php

$total_order   = 0;
$total_in      = 0;
$total_out     = 0;
$total_balance = 0;

$no = 1;

?>

<?php foreach($reportData as $row): ?>

<?php

$total_order   += $row['qty_order'];
$total_in      += $row['qty_in'];
$total_out     += $row['qty_out'];
$total_balance += $row['balance'];

?>

<tr>

<td>
    <?= $no++ ?>
</td>

<td>
    <?= $row['style'] ?>
</td>

<td>
    <?= $row['item'] ?>
</td>

<td>
    <?= $row['gender'] ?>
</td>

<td>
    <?= $row['colour'] ?>
</td>

<td>
    <?= $row['bucket'] ?>
</td>

<td>
    <?= number_format($row['qty_order']) ?>
</td>


<!-- SCAN IN -->
<td class="text-center">

<a
    href="#"
    class="detail-stock"

    data-type="IN_SM"

    data-style="<?= $row['style'] ?>"
    data-item="<?= $row['item'] ?>"
    data-gender="<?= $row['gender'] ?>"
    data-colour="<?= $row['colour'] ?>"
    data-bucket="<?= $row['bucket'] ?>"
>
    <?= number_format($row['qty_in']) ?>
</a>

</td>


<!-- SCAN OUT -->
<td class="text-center">

<a
    href="#"
    class="detail-stock"

    data-type="OUT_SM"

    data-style="<?= $row['style'] ?>"
    data-item="<?= $row['item'] ?>"
    data-gender="<?= $row['gender'] ?>"
    data-colour="<?= $row['colour'] ?>"
    data-bucket="<?= $row['bucket'] ?>"
>
    <?= number_format($row['qty_out']) ?>
</a>

</td>


<!-- STOCK -->
<td class="text-center">

<a
    href="#"
    class="detail-stock"

    data-type="STOCK"

    data-style="<?= $row['style'] ?>"
    data-item="<?= $row['item'] ?>"
    data-gender="<?= $row['gender'] ?>"
    data-colour="<?= $row['colour'] ?>"
    data-bucket="<?= $row['bucket'] ?>"
>
    <?= number_format($row['balance']) ?>
</a>

</td>

</tr>

<?php endforeach; ?>

</tbody>


<tfoot>

<tr class="font-weight-bold bg-light">

<td colspan="6" class="text-center">
TOTAL
</td>


<td class="text-right">
<?= number_format($total_order) ?>
</td>


<td class="text-center">

<a
    href="#"
    class="detail-stock"

    data-type="IN_SM"

    data-style=""
    data-item=""
    data-gender=""
    data-colour=""
    data-bucket=""

    data-bucket-from="<?= $bucket_from ?>"
    data-bucket-to="<?= $bucket_to ?>"
    data-item-filter="<?= $item_filter ?>"
    data-colour-filter="<?= $colour_filter ?>"
>
    <?= number_format($total_in) ?>
</a>

</td>


<td class="text-center">

<a
    href="#"
    class="detail-stock"

    data-type="OUT_SM"

    data-style=""
    data-item=""
    data-gender=""
    data-colour=""
    data-bucket=""

    data-bucket-from="<?= $bucket_from ?>"
    data-bucket-to="<?= $bucket_to ?>"
    data-item-filter="<?= $item_filter ?>"
    data-colour-filter="<?= $colour_filter ?>"
>
    <?= number_format($total_out) ?>
</a>

</td>


<td class="text-center">

<a
    href="#"
    class="detail-stock"

    data-type="STOCK"

    data-style=""
    data-item=""
    data-gender=""
    data-colour=""
    data-bucket=""

    data-bucket-from="<?= $bucket_from ?>"
    data-bucket-to="<?= $bucket_to ?>"
    data-item-filter="<?= $item_filter ?>"
    data-colour-filter="<?= $colour_filter ?>"
>
    <?= number_format($total_balance) ?>
</a>

</td>

</tr>

</tfoot>

</table>

</div>

</div>

</div>


</div>

</section>

</div>


<?php include 'modal_stock_sm_ip.php'; ?>


<footer class="main-footer">

<div class="float-right d-none d-sm-block">
<b>Version</b> 1.0.0
</div>

2024

<strong>
<a href="#">Mfg Project Officer</a>.
</strong>

All rights reserved.

</footer>

</div>


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

$(function () {

    $('.select2bs4').select2({
        theme: 'bootstrap4',
        placeholder: 'Select Data',
        allowClear: true
    });


    $('#ReportStock').DataTable({

        responsive: false,
        scrollX: true,
        scrollCollapse: true,
        autoWidth: false,

        paging: true,
        searching: true,
        ordering: true,

        lengthMenu: [
            10,
            25,
            50,
            100,
            250,
            500
        ],

        dom:
            "<'row mb-2'<'col-sm-6 d-flex align-items-center'l><'col-sm-6 d-flex justify-content-end gap-2'Bf>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row mt-2'<'col-sm-5'i><'col-sm-7'p>>",

        buttons: [

            {
                extend: 'excelHtml5',
                text: 'Export Excel',
                className: 'btn btn-success btn-sm',
                title: 'Report Stock SM IP'
            }

        ]

    });


    /*
    |--------------------------------------------------------------------------
    | DETAIL MODAL
    |--------------------------------------------------------------------------
    */

    $(document).on("click", ".detail-stock", function (e) {

        e.preventDefault();


        var type   = $(this).data("type");

        var style  = $(this).data("style") || '';
        var item   = $(this).data("item") || '';
        var gender = $(this).data("gender") || '';
        var colour = $(this).data("colour") || '';
        var bucket = $(this).data("bucket") || '';


        var bucket_from   = $(this).data("bucket-from") || '';
        var bucket_to     = $(this).data("bucket-to") || '';

        var item_filter   = $(this).data("item-filter") || '';
        var colour_filter = $(this).data("colour-filter") || '';


        /*
        |--------------------------------------------------------------------------
        | EXPORT DETAIL
        |--------------------------------------------------------------------------
        */

        var exportUrl =

            "export_stock_sm_ip_detail.php?" +

            "type=" + encodeURIComponent(type) +

            "&style=" + encodeURIComponent(style) +

            "&item=" + encodeURIComponent(item) +

            "&gender=" + encodeURIComponent(gender) +

            "&colour=" + encodeURIComponent(colour) +

            "&bucket=" + encodeURIComponent(bucket) +

            "&bucket_from=" + encodeURIComponent(bucket_from) +

            "&bucket_to=" + encodeURIComponent(bucket_to) +

            "&item_filter=" + encodeURIComponent(item_filter) +

            "&colour_filter=" + encodeURIComponent(colour_filter);


        $("#btnExportDetail").attr(
            "href",
            exportUrl
        );


        /*
        |--------------------------------------------------------------------------
        | SHOW MODAL
        |--------------------------------------------------------------------------
        */

        $("#modalDetailStock").modal("show");

        $("#modalDetailBody").html(
            "<div class='text-center p-3'>Loading...</div>"
        );


        /*
        |--------------------------------------------------------------------------
        | AJAX
        |--------------------------------------------------------------------------
        */

        $.ajax({

            url: "ajax_stock_sm_ip_detail.php",

            type: "POST",

            data: {

                type: type,

                style: style,
                item: item,
                gender: gender,
                colour: colour,
                bucket: bucket,

                bucket_from: bucket_from,
                bucket_to: bucket_to,

                item_filter: item_filter,
                colour_filter: colour_filter

            },

            success: function (html) {

                $("#modalDetailBody").html(html);

            }

        });

    });


});

</script>

</body>
</html>