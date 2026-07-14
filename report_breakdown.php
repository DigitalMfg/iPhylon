<?php
// session_start();
require 'function.php';

$bucket = $_GET['bucket'] ?? '';
$style_filter  = $_GET['style'] ?? '';
$item_filter   = $_GET['item'] ?? '';
$po_filter     = $_GET['po'] ?? '';
$po_item_filter = $_GET['po_item'] ?? '';
$line_filter   = $_GET['line'] ?? '';
$type_scan = $_GET['type_scan'] ?? 'OUT_PACKING';
$isSearch = isset($_GET['search']);
$dateList = [];

$whereDate = "";

if ($bucket != '')
    $whereDate .= " AND mb.bucket='$bucket'";
if ($style_filter != '')
    $whereDate .= " AND mb.style='$style_filter'";
if ($item_filter != '')
    $whereDate .= " AND mb.item='$item_filter'";
if ($po_filter != '')
    $whereDate .= " AND mb.po='$po_filter'";
if ($po_item_filter != '')
    $whereDate .= " AND mb.po_item='$po_item_filter'";
if ($line_filter != '')
    $whereDate .= " AND mb.line='$line_filter'";

$getDate = mysqli_query($conn, "
SELECT DISTINCT DATE(ts.date_scan) AS tanggal
FROM tbl_transaction_scan ts

INNER JOIN tbl_master_barcode mb
ON ts.qr_code = mb.qr_code

WHERE ts.type_scan='$type_scan'
$whereDate

ORDER BY DATE(ts.date_scan)
");
while ($d = mysqli_fetch_assoc($getDate)) {
    $dateList[] = $d['tanggal'];
}

$sizes = [
    '1',
    '1T',
    '2',
    '2T',
    '3',
    '3T',
    '4',
    '4T',
    '5',
    '5T',
    '6',
    '6T',
    '7',
    '7T',
    '8',
    '8T',
    '9',
    '9T',
    '10',
    '10T',
    '11',
    '11T',
    '12',
    '12T',
    '13',
    '13T',
    '14',
    '14T',
    '15'
];

$plan = [];

$qPlan = mysqli_query($conn, "
SELECT
    ssq.size,
    SUM(ssq.qty) AS qty
FROM tbl_spk_detail sd

INNER JOIN tbl_spk_size_qty ssq
    ON sd.id_detail = ssq.id_detail

WHERE 1=1

" . ($bucket != '' ? "AND sd.bucket = '$bucket'" : "") . "
" . ($style_filter != '' ? "AND sd.style = '$style_filter'" : "") . "
" . ($po_filter != '' ? "AND sd.po = '$po_filter'" : "") . "

" . ($po_item_filter != '' ? "AND EXISTS (
        SELECT 1
        FROM tbl_master_barcode mb
        WHERE mb.id_size_qty = ssq.id_size_qty
        AND mb.po_item = '$po_item_filter'
    )" : "") . "

" . ($item_filter != '' ? "AND EXISTS (
        SELECT 1
        FROM tbl_master_barcode mb
        WHERE mb.id_size_qty = ssq.id_size_qty
        AND mb.item = '$item_filter'
    )" : "") . "

" . ($line_filter != '' ? "AND EXISTS (
        SELECT 1
        FROM tbl_master_barcode mb
        WHERE mb.id_size_qty = ssq.id_size_qty
        AND mb.line = '$line_filter'
    )" : "") . "

GROUP BY ssq.size
ORDER BY ssq.size
");

while ($r = mysqli_fetch_assoc($qPlan)) {
    $plan[$r['size']] = $r['qty'];
}

$scanData = [];

$qScan = mysqli_query($conn, "
SELECT

    DATE(ts.date_scan) AS tanggal,

    mb.size,

    SUM(mb.qty) AS qty

FROM tbl_transaction_scan ts

INNER JOIN tbl_master_barcode mb
    ON ts.qr_code = mb.qr_code

WHERE

    ts.type_scan='$type_scan'

    " . ($bucket != '' ? "AND mb.bucket='$bucket'" : "") . "
    " . ($style_filter != '' ? "AND mb.style='$style_filter'" : "") . "
    " . ($item_filter != '' ? "AND mb.item='$item_filter'" : "") . "
    " . ($po_filter != '' ? "AND mb.po='$po_filter'" : "") . "
    " . ($po_item_filter != '' ? "AND mb.po_item='$po_item_filter'" : "") . "
    " . ($line_filter != '' ? "AND mb.line='$line_filter'" : "") . "

GROUP BY

    DATE(ts.date_scan),

    mb.size

ORDER BY

    DATE(ts.date_scan),
    mb.size

");

while ($r = mysqli_fetch_assoc($qScan)) {

    $scanData[$r['tanggal']][$r['size']] = $r['qty'];
}

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
                                        <label>Bucket <span class="text-danger">*</span></label>
                                        <select
                                            id="bucket"
                                            name="bucket"
                                            class="form-control select2bs4"
                                            required>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label>Style</label>
                                        <select
                                            id="style"
                                            name="style"
                                            class="form-control select2bs4">
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label>Item</label>
                                        <select
                                            id="item"
                                            name="item"
                                            class="form-control select2bs4">
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label>PO</label>
                                        <select
                                            id="po"
                                            name="po"
                                            class="form-control select2bs4">
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label>PO Item</label>
                                        <select
                                            id="po_item"
                                            name="po_item"
                                            class="form-control select2bs4">
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label>Line</label>
                                        <select
                                            id="line"
                                            name="line"
                                            class="form-control select2bs4">
                                        </select>
                                    </div>

                                </div>

                                <br>

                                <button
                                    type="submit"
                                    name="search"
                                    value="1"
                                    class="btn btn-primary"
                                    style="width:100px;">
                                    <i class="fas fa-search"></i>
                                    Search
                                </button>

                                <button
                                    type="button"
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
                                    href="?<?= http_build_query(array_merge($_GET, ['search' => 1, 'type_scan' => 'OUT_PACKING'])) ?>"
                                    class="btn <?= $type_scan == 'OUT_PACKING' ? 'btn-success' : 'btn-outline-success' ?>">
                                    Out Packing
                                </a>

                                <a
                                    href="?<?= http_build_query(array_merge($_GET, ['search' => 1, 'type_scan' => 'IN_SM'])) ?>"
                                    class="btn <?= $type_scan == 'IN_SM' ? 'btn-primary' : 'btn-outline-success' ?>">
                                    In Supermarket
                                </a>

                                <a
                                    href="?<?= http_build_query(array_merge($_GET, ['search' => 1, 'type_scan' => 'OUT_SM'])) ?>"
                                    class="btn <?= $type_scan == 'OUT_SM' ? 'btn-warning' : 'btn-outline-success' ?>">
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
                                            foreach ($sizes as $size):
                                            ?>
                                                <th><?= $size ?></th>
                                            <?php endforeach; ?>
                                            <th>Total</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php if ($isSearch && $bucket != ''): ?>

                                            <?php
                                            // Running Remaining
                                            $runningPlan = $plan;
                                            // Hitung total plan
                                            $planTotal = array_sum($plan);
                                            ?>
                                            <tr class="table">

                                                <td></td>
                                                <td><b>Plan</b></td>
                                                <?php foreach ($sizes as $size): ?>
                                                    <td class="text-right">
                                                        <?= number_format($plan[$size] ?? 0) ?>
                                                    </td>
                                                <?php endforeach; ?>
                                                <td class="text-right">
                                                    <b><?= number_format($planTotal) ?></b>
                                                </td>
                                            </tr>
                                            <?php foreach ($dateList as $tanggal): ?>
                                                <?php
                                                $totalOut = 0;
                                                $totalRemain = 0;
                                                ?>

                                                <tr>
                                                    <td><?= date('d-M-Y', strtotime($tanggal)); ?></td>
                                                    <td><b><?= str_replace('_', ' ', $type_scan); ?></b></td>
                                                    <?php foreach ($sizes as $size): ?>
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
                                                foreach ($sizes as $size) {
                                                    $runningPlan[$size] = ($runningPlan[$size] ?? 0) - ($scanData[$tanggal][$size] ?? 0);
                                                    $totalRemain += $runningPlan[$size];
                                                }
                                                ?>
                                                <tr class="table">
                                                    <td></td>
                                                    <td><b>Remaining</b></td>
                                                    <?php foreach ($sizes as $size): ?>
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
            $(function() {

                $('#ReportStock').DataTable({
                    responsive: false,
                    autoWidth: false,
                    paging: true,
                    searching: true,
                    ordering: false,
                    lengthMenu: [10, 25, 50, 100, 250, 500],

                    dom: "<'row mb-2'<'col-sm-6 d-flex align-items-center'l><'col-sm-6 d-flex justify-content-end gap-2'Bf>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row mt-2'<'col-sm-5'i><'col-sm-7'p>>",

                    buttons: [{
                        extend: 'excelHtml5',
                        text: 'Export Excel',
                        className: 'btn btn-success btn-sm',
                        title: 'Report Breakdown'
                    }]
                });

            });
        </script>

        <script>
            $(document).ready(function() {

                function initSelect2(id, action, placeholder) {

                    $(id).select2({
                        theme: "bootstrap4",
                        width: "100%",
                        placeholder: placeholder,
                        allowClear: true,
                        minimumInputLength: 1,
                        ajax: {
                            url: "get_filter_report_breakdown.php",
                            type: "POST",
                            dataType: "json",
                            delay: 250,
                            data: function(params) {
                                return {
                                    action: action,
                                    search: params.term,

                                    bucket: $("#bucket").val(),
                                    style: $("#style").val(),
                                    item: $("#item").val(),
                                    po: $("#po").val(),
                                    po_item: $("#po_item").val(),
                                    line: $("#line").val()
                                };
                            },
                            processResults: function(data) {
                                return {
                                    results: data.results || []
                                };
                            },
                            cache: true
                        }
                    });

                }

                // Inisialisasi semua filter
                initSelect2("#bucket", "searchBucket", "Pilih Bucket");
                initSelect2("#style", "searchStyle", "Pilih Style");
                initSelect2("#item", "searchItem", "Pilih Item");
                initSelect2("#po", "searchPo", "Pilih PO");
                initSelect2("#po_item", "searchPoItem", "Pilih PO Item");
                initSelect2("#line", "searchLine", "Pilih Line");

                $(document).on('select2:open', () => {
                    document.querySelector('.select2-container--open .select2-search__field').focus();
                });

            });

            function setSelected(id, value) {

                if (!value) return;

                var option = new Option(value, value, true, true);

                $(id).append(option).trigger("change");

            }

            setSelected("#bucket", "<?= $bucket ?>");
            setSelected("#style", "<?= $style_filter ?>");
            setSelected("#item", "<?= $item_filter ?>");
            setSelected("#po", "<?= $po_filter ?>");
            setSelected("#po_item", "<?= $po_item_filter ?>");
            setSelected("#line", "<?= $line_filter ?>");

            function resetFilters(ids) {
                ids.forEach(function(id) {
                    $(id).val(null).trigger("change");
                });
            }

            $("#bucket").on("change", function() {
                resetFilters([
                    "#style",
                    "#item",
                    "#po",
                    "#po_item",
                    "#line"
                ]);
            });
        </script>

</body>

</html>