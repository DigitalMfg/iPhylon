<?php
require 'function.php';

$qr_code_filter = $_GET['qr_code'] ?? '';
$line_filter    = $_GET['line'] ?? '';
$model_filter   = $_GET['model'] ?? '';
$bucket_filter  = $_GET['bucket'] ?? '';
$style_filter   = $_GET['style'] ?? '';
$po_filter      = $_GET['po'] ?? '';
$po_item_filter = $_GET['po_item'] ?? '';
$size_filter    = $_GET['size'] ?? '';
$date_filter    = $_GET['date'] ?? '';

$reportData = [];
$sizeList = [];

$isSearch = false;

$where = "WHERE 1=1";

if($qr_code_filter!=''){
    $where .= " AND mb.qr_code LIKE '%$qr_code_filter%'";
}

if($line_filter!=''){
    $where .= " AND mb.line='$line_filter'";
}

if($model_filter!=''){
    $where .= " AND mb.item='$model_filter'";
}

if($bucket_filter!=''){
    $where .= " AND mb.bucket='$bucket_filter'";
}

if($style_filter!=''){
    $where .= " AND mb.style='$style_filter'";
}

if($po_filter!=''){
    $where .= " AND mb.po='$po_filter'";
}

if($po_item_filter!=''){
    $where .= " AND mb.po_item='$po_item_filter'";
}

if($size_filter!=''){
    $where .= " AND mb.size='$size_filter'";
}

if($date_filter!=''){
    $where .= " AND DATE(mb.created_at)='$date_filter'";
}

$isSearch = !empty($_GET);

if($isSearch){

    $sql = mysqli_query($conn,"
    SELECT

        mb.qr_code,
        mb.line,
        mb.item,
        mb.bucket,
        mb.style,
        mb.po,
        mb.po_item,
        mb.size,
        mb.qty,

        mb.created_at AS generate_time,
        mb.updated_by AS generate_by,

        mb.created_at AS print_time,
        mb.updated_by AS print_by,

        op.date_transaction AS out_packing_time,
        op.username AS out_packing_by,

        insm.date_transaction AS in_sm_time,
        insm.username AS in_sm_by,

        outsm.date_transaction AS out_sm_time,
        outsm.username AS out_sm_by

    FROM tbl_master_barcode mb

    LEFT JOIN tbl_transaction_scan op
        ON mb.qr_code = op.qr_code
        AND op.type_scan = 'OUT_PACKING'

    LEFT JOIN tbl_transaction_scan insm
        ON mb.qr_code = insm.qr_code
        AND insm.type_scan = 'IN_SM'

    LEFT JOIN tbl_transaction_scan outsm
        ON mb.qr_code = outsm.qr_code
        AND outsm.type_scan = 'OUT_SM'

    $where

    ORDER BY mb.created_at DESC
    ");

    while($row = mysqli_fetch_assoc($sql)){
        $reportData[] = $row;
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
// MASTER LINE
// =========================
$listLine = mysqli_query($conn,"
SELECT DISTINCT line
FROM tbl_master_barcode
ORDER BY line
");

// =========================
// MASTER PO
// =========================
$listPO = mysqli_query($conn,"
SELECT DISTINCT po
FROM tbl_master_barcode
ORDER BY po
");


// =========================
// MASTER PO
// =========================
$listPOItem = mysqli_query($conn,"
SELECT DISTINCT po_item
FROM tbl_master_barcode
ORDER BY po_item
");
?>

<style>
.dataTables_wrapper{
    width:100%;
}

.table-responsive{
    overflow-x:auto;
}

#reportMinus{
    width:100% !important;
}

#reportMinus th,
#reportMinus td{
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

<title>iPhylon | Report QR Viewer</title>

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
                Report QR Code Viewer
                </h1>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                    <a href="index.php">Home</a>
                    </li>
                    <li class="breadcrumb-item active">
                    Report QR Code Viewer
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
        Detail QR Code Viewer
        </h3>
    </div>

<div class="card-body">

    <form method="GET">

        <div class="row">

            <div class="col-md-3">
                <label>QR Code</label>
                <input type="text"
                    name="qr_code"
                    class="form-control"
                    value="<?= htmlspecialchars($qr_code_filter) ?>"
                    placeholder="Scan / Input QR Code">
            </div>

            <div class="col-md-3">
                <label>Line</label>

                <select class="form-control select2bs4" name="line">
                    <option value="">All Line</option>

                    <?php while($line = mysqli_fetch_assoc($listLine)): ?>
                    <option value="<?= $line['line']; ?>"
                        <?= ($line_filter == $line['line']) ? 'selected' : ''; ?>>
                        <?= $line['line']; ?>
                    </option>
                    <?php endwhile; ?>

                </select>
            </div>

            <div class="col-md-3">
                <label>Model</label>

                <select class="form-control select2bs4" name="model">
                    <option value="">All Model</option>

                    <?php while($item = mysqli_fetch_assoc($listItem)): ?>
                    <option value="<?= $item['item']; ?>"
                        <?= ($model_filter == $item['item']) ? 'selected' : ''; ?>>
                        <?= $item['item']; ?>
                    </option>
                    <?php endwhile; ?>

                </select>
            </div>

            <div class="col-md-3">
                <label>Date</label>
                <input type="date"
                    class="form-control"
                    name="date"
                    value="<?= $date_filter ?>">
            </div>

        </div>

        <div class="row mt-3">

            <div class="col-md-3">
                <label>Bucket</label>

                <select class="form-control select2bs4" name="bucket">
                    <option value="">All Bucket</option>

                    <?php while($bucket = mysqli_fetch_assoc($listBucket)): ?>
                    <option value="<?= $bucket['bucket']; ?>"
                        <?= ($bucket_filter == $bucket['bucket']) ? 'selected' : ''; ?>>
                        <?= $bucket['bucket']; ?>
                    </option>
                    <?php endwhile; ?>

                </select>
            </div>

            <div class="col-md-3">
                <label>Style</label>

                <select class="form-control select2bs4" name="style">
                    <option value="">All Style</option>

                    <?php while($style = mysqli_fetch_assoc($listStyle)): ?>
                    <option value="<?= $style['style']; ?>"
                        <?= ($style_filter == $style['style']) ? 'selected' : ''; ?>>
                        <?= $style['style']; ?>
                    </option>
                    <?php endwhile; ?>

                </select>
            </div>

            <div class="col-md-3">
                <label>PO</label>

                <select class="form-control select2bs4" name="po">
                    <option value="">All PO</option>

                    <?php while($po = mysqli_fetch_assoc($listPO)): ?>
                    <option value="<?= $po['po']; ?>"
                        <?= ($po_filter == $po['po']) ? 'selected' : ''; ?>>
                        <?= $po['po']; ?>
                    </option>
                    <?php endwhile; ?>

                </select>
            </div>

            <div class="col-md-3">
                <label>PO Item</label>

                <select class="form-control select2bs4" name="po_item">
                    <option value="">All PO Item</option>

                    <?php while($poitem = mysqli_fetch_assoc($listPOItem)): ?>
                    <option value="<?= $poitem['po_item']; ?>"
                        <?= ($po_item_filter == $poitem['po_item']) ? 'selected' : ''; ?>>
                        <?= $poitem['po_item']; ?>
                    </option>
                    <?php endwhile; ?>

                </select>
            </div>

        </div>

        <div class="row mt-3 align-items-end">

            <div class="col-md-3">

                <label>Size</label>

                <select class="form-control select2bs4" name="size">
                    <option value="">All Size</option>

                    <?php while($size = mysqli_fetch_assoc($listSize)): ?>
                    <option value="<?= $size['size']; ?>"
                        <?= ($size_filter == $size['size']) ? 'selected' : ''; ?>>
                        <?= $size['size']; ?>
                    </option>
                    <?php endwhile; ?>

                </select>

            </div>

            <div class="col-md-9 d-flex align-items-end">
                <div class="mb-1">

                    <button type="submit"
                            class="btn btn-primary"
                            style="width:120px;">
                        <i class="fas fa-search"></i>
                        Search
                    </button>

                    <button type="button"
                            class="btn btn-secondary ml-2"
                            style="width:120px;"
                            onclick="window.location.href='report_tracking_qrcode.php'">
                        <i class="fas fa-sync-alt"></i>
                        Reset
                    </button>

                </div>
            </div>
        </div>
    </form>
</div>
</div>
<div class="card card-outline card-success">

    <div class="card-header">

        <h3 class="card-title">
            Detail QR Code Viewer
        </h3>

    </div>

    <div class="card-body">
        

        <div class="table-responsive">

            <table id="reportMinus" class="table table-bordered table-striped" style="width:100%">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>QR Code</th>
                        <th>Line</th>
                        <th>Model</th>
                        <th>Bucket</th>
                        <th>Style</th>
                        <th>PO</th>
                        <th>PO Item</th>
                        <th>Size</th>
                        <th>Qty</th>
                        <th>Generate</th>
                        <th>PIC Generate</th>
                        <th>Print</th>
                        <th>PIC Print</th>
                        <th>Out Packing</th>
                        <th>PIC Out Packing</th>
                        <th>In SM</th>
                        <th>PIC In SM</th>
                        <th>Out SM</th>
                        <th>PIC Out SM</th>
                    </tr>
                </thead>    

                <tbody>
                    <?php $no=1; ?>
                    <?php foreach($reportData as $row): ?>
                    <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['qr_code'] ?></td>
                    <td><?= $row['line'] ?></td>
                    <td><?= $row['item'] ?></td>
                    <td><?= $row['bucket'] ?></td>
                    <td><?= $row['style'] ?></td>
                    <td><?= $row['po'] ?></td>
                    <td><?= $row['po_item'] ?></td>
                    <td><?= $row['size'] ?></td>
                    <td class="text-center"><?= $row['qty'] ?></td>
                    <td><?= $row['generate_time'] ?></td>
                    <td><?= $row['generate_by'] ?></td>
                    <td><?= $row['print_time'] ?></td>
                    <td><?= $row['print_by'] ?></td>
                    <td><?= $row['out_packing_time'] ?></td>
                    <td><?= $row['out_packing_by'] ?></td>
                    <td><?= $row['in_sm_time'] ?></td>
                    <td><?= $row['in_sm_by'] ?></td>
                    <td><?= $row['out_sm_time'] ?></td>
                    <td><?= $row['out_sm_by'] ?></td>
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

        responsive:false,
        scrollX:true,
        scrollCollapse:true,
        autoWidth:false,
        paging:true,
        searching:true,
        ordering:true,
        lengthMenu:[10,25,50,100,250,500],

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
</div>
</body>
</html>


