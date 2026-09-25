<?php
// session_start();
require 'function.php';

/*
|--------------------------------------------------------------------------
| AJAX FILTER SELECT2
|--------------------------------------------------------------------------
*/

if (isset($_POST['action'])) {
    header('Content-Type: application/json; charset=utf-8');
    $action    = $_POST['action'] ?? '';
    $search    = trim($_POST['search'] ?? '');
    $bucket    = trim($_POST['bucket'] ?? '');
    $bucket_to = trim($_POST['bucket_to'] ?? '');
    $item      = trim($_POST['item'] ?? '');
    $style     = trim($_POST['style'] ?? '');
    $colour    = trim($_POST['colour'] ?? '');
    $gender    = trim($_POST['gender'] ?? '');

    /*
    |--------------------------------------------------------------------------
    | MINIMUM SEARCH 1 CHARACTER
    |--------------------------------------------------------------------------
    */

    if (strlen($search) < 1) {
        echo json_encode([
            'results' => []
        ]);
        exit;
    }

    $searchEscaped = mysqli_real_escape_string(
        $conn,
        $search
    );

    /*
    |--------------------------------------------------------------------------
    | BASE WHERE
    |--------------------------------------------------------------------------
    */
    $where = "WHERE 1=1";

    /*
    |--------------------------------------------------------------------------
    | BUCKET RANGE
    |--------------------------------------------------------------------------
    */

    if (
        $bucket !== '' &&
        $bucket_to !== ''
    ) {

        $bucketFromNum = (int) substr(
            preg_replace('/[^0-9]/', '', $bucket),
            0,
            6
        );

        $bucketToNum = (int) substr(
            preg_replace('/[^0-9]/', '', $bucket_to),
            0,
            6
        );

        /*
        |----------------------------------------------------------------------
        | Jika From > To, tukar
        |----------------------------------------------------------------------
        */

        if ($bucketFromNum > $bucketToNum) {
            $temp = $bucketFromNum;
            $bucketFromNum = $bucketToNum;
            $bucketToNum = $temp;
        }

        $where .= "
            AND CAST(
                LEFT(mb.bucket, 6)
                AS UNSIGNED
            )
            BETWEEN
                $bucketFromNum
            AND
                $bucketToNum
        ";
    }


    /*
    |--------------------------------------------------------------------------
    | SEARCH ITEM
    |--------------------------------------------------------------------------
    */

    if ($action === 'searchItem') {
        $where .= "
            AND mb.item LIKE '%$searchEscaped%'
        ";

        $sql = "
            SELECT DISTINCT
                mb.item AS value
            FROM tbl_master_barcode mb
            $where
            AND mb.item IS NOT NULL
            AND mb.item <> ''
            ORDER BY mb.item
            LIMIT 50
        ";
    }


    /*
    |--------------------------------------------------------------------------
    | SEARCH STYLE
    |--------------------------------------------------------------------------
    */

    elseif ($action === 'searchStyle') {

        /*
        |----------------------------------------------------------------------
        | Style mengikuti Item
        |----------------------------------------------------------------------
        */

        if ($item !== '') {

            $itemEscaped = mysqli_real_escape_string(
                $conn,
                $item
            );

            $where .= "
                AND mb.item = '$itemEscaped'
            ";
        }

        /*
        |----------------------------------------------------------------------
        | Style menggunakan LIKE
        |----------------------------------------------------------------------
        */

        $where .= "
            AND mb.style LIKE '%$searchEscaped%'
        ";

        $sql = "
            SELECT DISTINCT
                mb.style AS value
            FROM tbl_master_barcode mb
            $where
            AND mb.style IS NOT NULL
            AND mb.style <> ''
            ORDER BY mb.style
            LIMIT 50
        ";
    }


    /*
    |--------------------------------------------------------------------------
    | SEARCH COLOUR
    |--------------------------------------------------------------------------
    */

    elseif ($action === 'searchColour') {

        /*
        |----------------------------------------------------------------------
        | Colour mengikuti Item
        |----------------------------------------------------------------------
        */

        if ($item !== '') {
            $itemEscaped = mysqli_real_escape_string(
                $conn,
                $item
            );
            $where .= "
                AND mb.item = '$itemEscaped'
            ";
        }

        /*
        |----------------------------------------------------------------------
        | Colour mengikuti Style
        |----------------------------------------------------------------------
        */

        if ($style !== '') {

            $styleEscaped = mysqli_real_escape_string(
                $conn,
                $style
            );

            $where .= "
                AND mb.style = '$styleEscaped'
            ";
        }

        /*
        |----------------------------------------------------------------------
        | Colour menggunakan LIKE
        |----------------------------------------------------------------------
        */

        $where .= "
            AND mb.colour LIKE '%$searchEscaped%'
        ";

        $sql = "
            SELECT DISTINCT
                mb.colour AS value

            FROM tbl_master_barcode mb
            $where
            AND mb.colour IS NOT NULL
            AND mb.colour <> ''
            ORDER BY mb.colour
            LIMIT 50
        ";
    }


    /*
    |--------------------------------------------------------------------------
    | SEARCH GENDER
    |--------------------------------------------------------------------------
    */

    elseif ($action === 'searchGender') {
        /*
        |----------------------------------------------------------------------
        | Gender mengikuti Item
        |----------------------------------------------------------------------
        */

        if ($item !== '') {
            $itemEscaped = mysqli_real_escape_string(
                $conn,
                $item
            );

            $where .= "
                AND mb.item = '$itemEscaped'
            ";
        }

        /*
        |----------------------------------------------------------------------
        | Gender mengikuti Style
        |----------------------------------------------------------------------
        */

        if ($style !== '') {

            $styleEscaped = mysqli_real_escape_string(
                $conn,
                $style
            );
            $where .= "
                AND mb.style = '$styleEscaped'
            ";
        }

        /*
        |----------------------------------------------------------------------
        | Gender mengikuti Colour
        |----------------------------------------------------------------------
        */

        if ($colour !== '') {
            $colourEscaped = mysqli_real_escape_string(
                $conn,
                $colour
            );

            $where .= "
                AND mb.colour = '$colourEscaped'
            ";
        }

        /*
        |----------------------------------------------------------------------
        | Gender menggunakan LIKE
        |----------------------------------------------------------------------
        */

        $where .= "
            AND mb.gender LIKE '%$searchEscaped%'
        ";

        $sql = "
            SELECT DISTINCT
                mb.gender AS value
            FROM tbl_master_barcode mb

            $where

            AND mb.gender IS NOT NULL
            AND mb.gender <> ''
            ORDER BY mb.gender

            LIMIT 50
        ";
    }


    /*
    |--------------------------------------------------------------------------
    | SEARCH BUCKET
    |--------------------------------------------------------------------------
    */

    elseif ($action === 'searchBucket') {
        $sql = "
            SELECT DISTINCT
                mb.bucket AS value
            FROM tbl_master_barcode mb
            WHERE mb.bucket LIKE '%$searchEscaped%'
            AND mb.bucket IS NOT NULL
            AND mb.bucket <> ''
            ORDER BY mb.bucket
            LIMIT 50
        ";
    }


    /*
    |--------------------------------------------------------------------------
    | ACTION TIDAK DIKENAL
    |--------------------------------------------------------------------------
    */

    else {
        echo json_encode([
            'results' => []
        ]);
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | EXECUTE QUERY
    |--------------------------------------------------------------------------
    */

    $query = mysqli_query(
        $conn,
        $sql
    );


    if (!$query) {
        echo json_encode([
            'results' => [],
            'error' => mysqli_error($conn)
        ]);
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | FORMAT SELECT2
    |--------------------------------------------------------------------------
    */

    $results = [];

    while ($row = mysqli_fetch_assoc($query)) {

        $value = trim(
            $row['value'] ?? ''
        );

        if ($value === '') {
            continue;
        }

        $results[] = [
            'id'   => $value,
            'text' => $value
        ];
    }


    echo json_encode([
        'results' => $results
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| FILTER
|--------------------------------------------------------------------------
*/

$bucket         = $_GET['bucket'] ?? '';
$bucket_to      = $_GET['bucket_to'] ?? '';
$style_filter   = $_GET['style'] ?? '';
$item_filter    = $_GET['item'] ?? '';
$colour_filter  = $_GET['colour'] ?? '';
$gender_filter  = $_GET['gender'] ?? '';
$po_filter      = $_GET['po'] ?? '';
$po_item_filter = $_GET['po_item'] ?? '';
$line_filter    = $_GET['line'] ?? '';
$type_scan      = $_GET['type_scan'] ?? 'OUT_PACKING';
$status_filter  = $_GET['status'] ?? '';
$isSearch       = isset($_GET['search']);

/*
|--------------------------------------------------------------------------
| SIZE
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| TOTAL COLUMN
|--------------------------------------------------------------------------
|
| Item       = 1
| Style      = 2
| Colour     = 3
| Gender     = 4
| Bucket     = 5
| QTY Order  = 6
| Status     = 7
| Size       = 29
| Total      = 1
|
| TOTAL = 37 COLUMN
|--------------------------------------------------------------------------
*/

$totalColumns = 7 + count($sizes) + 1;

/*
|--------------------------------------------------------------------------
| VALID TYPE SCAN
|--------------------------------------------------------------------------
*/

$allowedTypeScan = [
    'OUT_PACKING',
    'IN_SM',
    'OUT_SM'
];

if (!in_array($type_scan, $allowedTypeScan, true)) {
    $type_scan = 'OUT_PACKING';
}

/*
|--------------------------------------------------------------------------
| REPORT DATA
|--------------------------------------------------------------------------
*/

$reportData = [];

/*
|--------------------------------------------------------------------------
| GRAND TOTAL
|--------------------------------------------------------------------------
*/

$grandOrderTotal     = 0;
$grandActualTotal    = 0;
$grandRemainingTotal = 0;

$grandOrder     = [];
$grandActual    = [];
$grandRemaining = [];

foreach ($sizes as $size) {
    $grandOrder[$size]     = 0;
    $grandActual[$size]    = 0;
    $grandRemaining[$size] = 0;
}

/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

if (
    $isSearch &&
    $bucket !== '' &&
    $bucket_to !== ''
) {

    $bucketFrom = mysqli_real_escape_string(
        $conn,
        $bucket
    );

    $bucketTo = mysqli_real_escape_string(
        $conn,
        $bucket_to
    );

    $bucketFromNum = (int) substr(
        preg_replace('/[^0-9]/', '', $bucketFrom),
        0,
        6
    );

    $bucketToNum = (int) substr(
        preg_replace('/[^0-9]/', '', $bucketTo),
        0,
        6
    );

    if ($bucketFromNum > $bucketToNum) {
        $temp = $bucketFromNum;
        $bucketFromNum = $bucketToNum;
        $bucketToNum = $temp;
    }

    /*
    |--------------------------------------------------------------------------
    | MASTER FILTER
    |--------------------------------------------------------------------------
    */

    $whereMaster = "";

    $whereMaster .= "
        AND CAST(LEFT(mb.bucket, 6) AS UNSIGNED)
        BETWEEN
            $bucketFromNum
        AND
            $bucketToNum
    ";

    if ($style_filter !== '') {

        $style = mysqli_real_escape_string(
            $conn,
            $style_filter
        );

        $whereMaster .= "
            AND mb.style = '$style'
        ";
    }

    if ($item_filter !== '') {

        $item = mysqli_real_escape_string(
            $conn,
            $item_filter
        );

        $whereMaster .= "
            AND mb.item = '$item'
        ";
    }

    if ($colour_filter !== '') {

        $colour = mysqli_real_escape_string(
            $conn,
            $colour_filter
        );

        $whereMaster .= "
            AND mb.colour = '$colour'
        ";
    }

    if ($gender_filter !== '') {

        $gender = mysqli_real_escape_string(
            $conn,
            $gender_filter
        );

        $whereMaster .= "
            AND mb.gender = '$gender'
        ";
    }

    if ($po_filter !== '') {

        $po = mysqli_real_escape_string(
            $conn,
            $po_filter
        );

        $whereMaster .= "
            AND mb.po = '$po'
        ";
    }

    if ($po_item_filter !== '') {

        $po_item = mysqli_real_escape_string(
            $conn,
            $po_item_filter
        );

        $whereMaster .= "
            AND mb.po_item = '$po_item'
        ";
    }

    if ($line_filter !== '') {

        $line = mysqli_real_escape_string(
            $conn,
            $line_filter
        );

        $whereMaster .= "
            AND mb.line = '$line'
        ";
    }

    /*
    |--------------------------------------------------------------------------
    | MASTER BARCODE
    |--------------------------------------------------------------------------
    */

    $qMaster = mysqli_query(
        $conn,
        "
        SELECT
            mb.bucket,
            mb.item,
            mb.style,
            mb.colour,
            mb.gender,
            mb.size,
            SUM(mb.qty) AS qty_order
        FROM tbl_master_barcode mb
        WHERE 1=1
            $whereMaster
        GROUP BY
            mb.bucket,
            mb.item,
            mb.style,
            mb.colour,
            mb.gender,
            mb.size
        ORDER BY
            mb.bucket,
            mb.item,
            mb.style,
            mb.colour,
            mb.gender
        "
    );

    if (!$qMaster) {
        die(
            "Error Query Master Barcode : " .
            mysqli_error($conn)
        );
    }

    while ($r = mysqli_fetch_assoc($qMaster)) {

        $key =
            $r['bucket'] . '|' .
            $r['item'] . '|' .
            $r['style'] . '|' .
            $r['colour'] . '|' .
            $r['gender'];

        if (!isset($reportData[$key])) {

            $reportData[$key] = [
                'bucket' => $r['bucket'],
                'item'   => $r['item'],
                'style'  => $r['style'],
                'colour' => $r['colour'],
                'gender' => $r['gender'],
                'order'  => [],
                'actual' => []
            ];

            foreach ($sizes as $size) {

                $reportData[$key]['order'][$size]  = 0;
                $reportData[$key]['actual'][$size] = 0;
            }
        }

        $size = $r['size'];

        if (
            isset(
                $reportData[$key]['order'][$size]
            )
        ) {

            $reportData[$key]['order'][$size] +=
                (int) $r['qty_order'];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ACTUAL PRODUCTION
    |--------------------------------------------------------------------------
    */

    $typeScanEscaped = mysqli_real_escape_string(
        $conn,
        $type_scan
    );

    $qActual = mysqli_query(
        $conn,
        "
        SELECT
            mb.bucket,
            mb.item,
            mb.style,
            mb.colour,
            mb.gender,
            mb.size,
            SUM(mb.qty) AS qty_actual
        FROM tbl_transaction_scan ts
        INNER JOIN tbl_master_barcode mb
            ON ts.qr_code = mb.qr_code
        WHERE
            ts.type_scan = '$typeScanEscaped'
            $whereMaster
        GROUP BY
            mb.bucket,
            mb.item,
            mb.style,
            mb.colour,
            mb.gender,
            mb.size
        ORDER BY
            mb.bucket,
            mb.item,
            mb.style,
            mb.colour,
            mb.gender
        "
    );

    if (!$qActual) {

        die(
            "Error Query Transaction Scan : " .
            mysqli_error($conn)
        );
    }

    while ($r = mysqli_fetch_assoc($qActual)) {

        $key =
            $r['bucket'] . '|' .
            $r['item'] . '|' .
            $r['style'] . '|' .
            $r['colour'] . '|' .
            $r['gender'];

        if (isset($reportData[$key])) {

            $size = $r['size'];

            if (
                isset(
                    $reportData[$key]['actual'][$size]
                )
            ) {

                $reportData[$key]['actual'][$size] +=
                    (int) $r['qty_actual'];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <title>
        iPhylon | Report Minus Produksi IP
    </title>

    <link
        rel="icon"
        href="assets/images/i.Phylon.png"
        type="image/x-icon">

    <!-- AdminLTE -->
    <link
        rel="stylesheet"
        href="plugins/fontawesome-free/css/all.min.css">

    <link
        rel="stylesheet"
        href="dist/css/adminlte.min.css">

    <!-- DataTables -->
    <link
        rel="stylesheet"
        href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">

    <link
        rel="stylesheet"
        href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

    <link
        rel="stylesheet"
        href="plugins/select2/css/select2.min.css">

    <link
        rel="stylesheet"
        href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

    <link
        rel="stylesheet"
        href="https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.dataTables.min.css">

    <link
        rel="stylesheet"
        href="https://cdn.datatables.net/fixedheader/3.4.1/css/fixedHeader.dataTables.min.css">

    <style>

        /*
        |--------------------------------------------------------------------------
        | DATATABLES
        |--------------------------------------------------------------------------
        */

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

        #ReportStock {
            width: 100% !important;
            white-space: nowrap;
        }

        #ReportStock th,
        #ReportStock td {
            white-space: nowrap;
            vertical-align: middle;
        }

        /* Sembunyikan scrollbar horizontal bawaan DataTables */
        #ReportStock_wrapper .dataTables_scrollBody {
            overflow-x: auto !important;
            overflow-y: auto !important;

            /* Aktifkan swipe horizontal pada touchscreen */
            touch-action: pan-x pan-y;

            /* Sembunyikan scrollbar bawaan */
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        #ReportStock_wrapper .dataTables_scrollBody::-webkit-scrollbar {
            display: none;
        }

        /*
        |--------------------------------------------------------------------------
        | SELECT2
        |--------------------------------------------------------------------------
        */

        .select2-container {
            width: 100% !important;
        }

        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        .status-actual,
        .status-remaining {
            font-weight: bold;
            text-align: left !important;
        }

        .minus-value {
            font-weight: bold;
        }

        .row-remaining {
            background-color: #f8f9fa;
        }

        /*
        |--------------------------------------------------------------------------
        | COMPARE
        |--------------------------------------------------------------------------
        */

        .compare-actual {
            background-color: #ffffff;
        }

        .compare-remaining {
            background-color: #f8f9fa;
        }

        /*
        |--------------------------------------------------------------------------
        | FIXED COLUMNS
        |--------------------------------------------------------------------------
        */

        .DTFC_LeftBodyLiner {
            overflow-y: hidden !important;
        }

        /*
        |--------------------------------------------------------------------------
        | BUTTON EXPORT
        |--------------------------------------------------------------------------
        */

        .dt-buttons {
            display: inline-flex;
            align-items: center;
        }

        .dt-button {
            margin-left: 5px !important;
        }

        html,
        body {
            overflow-x: hidden !important;
        }

        .select2-container {
            max-width: 100%;
        }

        .select2-dropdown {
            max-width: 100vw;
        }

        /* ==========================================================
        CUSTOM HORIZONTAL SCROLLBAR
        Posisi scrollbar di bawah TOTAL
        ========================================================== */

        .report-scrollbar-wrapper {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            height: 18px;
            margin-top: 2px;
        }

        .report-scrollbar {
            height: 1px;
        }

    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">

<div class="wrapper">
    <?php include 'header.php'; ?>
    <div class="content-wrapper">

        <!-- CONTENT HEADER -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>
                            <i class="fas fa-chart-bar"></i>
                            Report Minus Produksi IP
                        </h1>
                    </div>

                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="index.php">
                                    Home
                                </a>
                            </li>

                            <li class="breadcrumb-item active">
                                Report Minus Produksi IP
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <!-- CONTENT -->

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

                                <!-- BUCKET FROM -->

                                <div class="col-md-2">
                                    <label>
                                        Bucket From
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        id="bucket"
                                        name="bucket"
                                        class="form-control"
                                        required>
                                    </select>
                                </div>

                                <!-- BUCKET TO -->

                                <div class="col-md-2">
                                    <label>
                                        Bucket To
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        id="bucket_to"
                                        name="bucket_to"
                                        class="form-control"
                                        required>
                                    </select>
                                </div>

                                <!-- STYLE -->

                                <div class="col-md-4">
                                    <label>
                                        Style
                                    </label>

                                    <select
                                        id="style"
                                        name="style"
                                        class="form-control">
                                    </select>
                                </div>

                                <!-- ITEM -->

                                <div class="col-md-4">
                                    <label>
                                        Item
                                    </label>

                                    <select
                                        id="item"
                                        name="item"
                                        class="form-control">
                                    </select>
                                </div>
                                <!-- COLOUR -->

                                <div class="col-md-4">
                                    <label>
                                        Colour
                                    </label>

                                    <select
                                        id="colour"
                                        name="colour"
                                        class="form-control">
                                    </select>
                                </div>
                                <!-- GENDER -->

                                <div class="col-md-4">
                                    <label>
                                        Gender
                                    </label>

                                    <select
                                        id="gender"
                                        name="gender"
                                        class="form-control">
                                    </select>
                                </div>

                                <!-- STATUS -->

                                <div class="col-md-4">
                                    <label>
                                        Status
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        id="status"
                                        name="status"
                                        class="form-control"
                                        required>

                                        <option
                                            value=""
                                            <?= $status_filter == '' ? 'selected' : '' ?>>
                                            --Pilih Status--
                                        </option>

                                        <option
                                            value="Actual Prod"
                                            <?= $status_filter == 'Actual Prod' ? 'selected' : '' ?>>
                                            Actual Prod
                                        </option>

                                        <option
                                            value="Remaining"
                                            <?= $status_filter == 'Remaining' ? 'selected' : '' ?>>
                                            Remaining
                                        </option>

                                        <option
                                            value="Compare"
                                            <?= $status_filter == 'Compare' ? 'selected' : '' ?>>
                                            Compare
                                        </option>
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
                                onclick="window.location='report_minus_produksi_ip.php'">
                                <i class="fas fa-sync-alt"></i>
                                Reset
                            </button>
                        </form>
                    </div>
                </div>

                <!-- REPORT -->

                <div class="card card-outline card-success">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            Report Minus Produksi IP
                        </h3>

                        <div
                            class="btn-group"
                            role="group"
                            aria-label="Type Scan">

                            <a
                                href="?<?= http_build_query(
                                    array_merge(
                                        $_GET,
                                        [
                                            'search' => 1,
                                            'type_scan' => 'OUT_PACKING'
                                        ]
                                    )
                                ) ?>"
                                class="btn <?= $type_scan == 'OUT_PACKING'
                                    ? 'btn-success'
                                    : 'btn-outline-success' ?>">
                                Out Packing
                            </a>

                            <a
                                href="?<?= http_build_query(
                                    array_merge(
                                        $_GET,
                                        [
                                            'search' => 1,
                                            'type_scan' => 'IN_SM'
                                        ]
                                    )
                                ) ?>"
                                class="btn <?= $type_scan == 'IN_SM'
                                    ? 'btn-success'
                                    : 'btn-outline-success' ?>">
                                In Supermarket
                            </a>

                            <a
                                href="?<?= http_build_query(
                                    array_merge(
                                        $_GET,
                                        [
                                            'search' => 1,
                                            'type_scan' => 'OUT_SM'
                                        ]
                                    )
                                ) ?>"
                                class="btn <?= $type_scan == 'OUT_SM'
                                    ? 'btn-success'
                                    : 'btn-outline-success' ?>">
                                Out Supermarket
                            </a>
                        </div>
                    </div>
                    <div class="card-body">

                        <!--
                        IMPORTANT:
                        Jangan gunakan .table-responsive di sini.
                        DataTables scrollX yang menangani horizontal scroll.
                        -->

                        <table
                            id="ReportStock"
                            class="table table-bordered table-striped table-sm"
                            style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Style</th>
                                    <th>Colour</th>
                                    <th>Gender</th>
                                    <th>Bucket</th>
                                    <th style="text-align:center;">
                                        QTY Order
                                    </th>

                                    <th>
                                        Status
                                    </th>
                                    <?php foreach ($sizes as $size): ?>
                                        <th style="text-align:center;">
                                            <?= htmlspecialchars($size) ?>
                                        </th>
                                    <?php endforeach; ?>
                                    <th style="text-align:center;">
                                        Total
                                    </th>
                                </tr>
                            </thead>

                            <tbody>

                            <?php

                            /*
                            |--------------------------------------------------------------------------
                            | DISPLAY REPORT
                            |--------------------------------------------------------------------------
                            */

                            if (
                                $isSearch &&
                                $bucket !== '' &&
                                $bucket_to !== ''
                            ):
                                $grandOrderTotal = 0;
                                $grandActualTotal = 0;
                                $grandRemainingTotal = 0;

                                foreach ($sizes as $size) {
                                    $grandOrder[$size] = 0;
                                    $grandActual[$size] = 0;
                                    $grandRemaining[$size] = 0;
                                }

                                foreach ($reportData as $data):
                                    $actual = [];
                                    $remaining = [];
                                    $orderTotal = 0;
                                    $actualTotal = 0;
                                    $remainingTotal = 0;
                                    foreach ($sizes as $size):
                                        $orderQty =
                                            $data['order'][$size] ?? 0;

                                        $actualQty =
                                            $data['actual'][$size] ?? 0;

                                        $remainingQty =
                                            $orderQty - $actualQty;

                                        $actual[$size] =
                                            $actualQty;

                                        $remaining[$size] =
                                            $remainingQty;

                                        $orderTotal +=
                                            $orderQty;

                                        $actualTotal +=
                                            $actualQty;

                                        $remainingTotal +=
                                            $remainingQty;

                                        $grandOrder[$size] +=
                                            $orderQty;

                                        $grandActual[$size] +=
                                            $actualQty;

                                        $grandRemaining[$size] +=
                                            $remainingQty;

                                    endforeach;

                                    $grandOrderTotal +=
                                        $orderTotal;

                                    $grandActualTotal +=
                                        $actualTotal;

                                    $grandRemainingTotal +=
                                        $remainingTotal;


                                    /*
                                    |--------------------------------------------------------------------------
                                    | COMPARE
                                    |--------------------------------------------------------------------------
                                    |
                                    | PENTING:
                                    | Tidak menggunakan rowspan.
                                    | Kedua TR mempunyai tepat 37 TD.
                                    |
                                    */
                                    if ($status_filter == 'Compare'):
                            ?>

                                        <!-- COMPARE - ACTUAL -->

                                        <tr class="compare-actual">
                                            <td>
                                                <?= htmlspecialchars(
                                                    $data['item']
                                                ) ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars(
                                                    $data['style']
                                                ) ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars(
                                                    $data['colour']
                                                ) ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars(
                                                    $data['gender']
                                                ) ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars(
                                                    $data['bucket']
                                                ) ?>
                                            </td>

                                            <td class="text-center">
                                                <?= number_format(
                                                    $orderTotal
                                                ) ?>
                                            </td>

                                            <td class="status-actual">
                                                Actual Prod
                                            </td>

                                            <?php foreach ($sizes as $size): ?>
                                                <td class="text-center">
                                                    <?= number_format(
                                                        $actual[$size]
                                                    ) ?>
                                                </td>

                                            <?php endforeach; ?>

                                            <td class="text-center">
                                                <b>
                                                    <?= number_format(
                                                        $actualTotal
                                                    ) ?>
                                                </b>
                                            </td>
                                        </tr>

                                        <!-- COMPARE - REMAINING -->
                                            <tr class="compare-remaining">
                                                <!-- 1. ITEM -->
                                                <td></td>
                                                <!-- 2. STYLE -->
                                                <td></td>
                                                <!-- 3. COLOUR -->
                                                <td></td>
                                                <!-- 4. GENDER -->
                                                <td></td>
                                                <!-- 5. BUCKET -->
                                                <td></td>
                                                <!-- 6. QTY ORDER -->
                                                <td></td>
                                                <!-- 7. STATUS -->
                                                <td class="status-remaining">
                                                    Remaining
                                                </td>
                                                <?php foreach ($sizes as $size): ?>
                                                    <td class="text-center">
                                                        <?php if (
                                                            $remaining[$size] < 0
                                                        ): ?>
                                                            <span
                                                                class="text-danger minus-value">
                                                                <?= number_format(
                                                                    $remaining[$size]
                                                                ) ?>
                                                            </span>
                                                        <?php else: ?>
                                                            <?= number_format(
                                                                $remaining[$size]
                                                            ) ?>
                                                        <?php endif; ?>
                                                    </td>

                                                <?php endforeach; ?>

                                                <!-- 37. TOTAL -->
                                                <td class="text-center">
                                                    <b>
                                                        <?php if (
                                                            $remainingTotal < 0
                                                        ): ?>
                                                            <span class="text-danger">
                                                                <?= number_format(
                                                                    $remainingTotal
                                                                ) ?>
                                                            </span>
                                                        <?php else: ?>

                                                            <?= number_format(
                                                                $remainingTotal
                                                            ) ?>

                                                        <?php endif; ?>
                                                    </b>
                                                </td>
                                            </tr>

                                <?php

                                    /*
                                    |--------------------------------------------------------------------------
                                    | ACTUAL PROD / DEFAULT
                                    |--------------------------------------------------------------------------
                                    */

                                    elseif (
                                        $status_filter == '' ||
                                        $status_filter == 'Actual Prod'
                                    ):

                                ?>

                                        <tr>
                                            <td>
                                                <?= htmlspecialchars(
                                                    $data['item']
                                                ) ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars(
                                                    $data['style']
                                                ) ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars(
                                                    $data['colour']
                                                ) ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars(
                                                    $data['gender']
                                                ) ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars(
                                                    $data['bucket']
                                                ) ?>
                                            </td>

                                            <td class="text-center">
                                                <?= number_format(
                                                    $orderTotal
                                                ) ?>
                                            </td>

                                            <td class="status-actual">
                                                Actual Prod
                                            </td>

                                            <?php foreach ($sizes as $size): ?>

                                                <td class="text-center">
                                                    <?= number_format(
                                                        $actual[$size]
                                                    ) ?>
                                                </td>

                                            <?php endforeach; ?>

                                            <td class="text-center">
                                                <b>
                                                    <?= number_format(
                                                        $actualTotal
                                                    ) ?>
                                                </b>
                                            </td>
                                        </tr>

                            <?php

                                    /*
                                    |--------------------------------------------------------------------------
                                    | REMAINING
                                    |--------------------------------------------------------------------------
                                    */

                                    elseif (
                                        $status_filter == 'Remaining'
                                    ):

                            ?>

                                        <tr class="row-remaining">
                                            <td>
                                                <?= htmlspecialchars(
                                                    $data['item']
                                                ) ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars(
                                                    $data['style']
                                                ) ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars(
                                                    $data['colour']
                                                ) ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars(
                                                    $data['gender']
                                                ) ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars(
                                                    $data['bucket']
                                                ) ?>
                                            </td>

                                            <td class="text-center">
                                                <?= number_format(
                                                    $orderTotal
                                                ) ?>
                                            </td>

                                            <td class="status-remaining">

                                                Remaining

                                            </td>

                                            <?php foreach ($sizes as $size): ?>
                                                <td class="text-center">
                                                    <?php if (
                                                        $remaining[$size] < 0
                                                    ): ?>
                                                        <span
                                                            class="text-danger minus-value">

                                                            <?= number_format(
                                                                $remaining[$size]
                                                            ) ?>
                                                        </span>

                                                    <?php else: ?>
                                                        <?= number_format(
                                                            $remaining[$size]
                                                        ) ?>
                                                    <?php endif; ?>
                                                </td>

                                            <?php endforeach; ?>

                                            <td class="text-center">
                                                <b>
                                                    <?php if (
                                                        $remainingTotal < 0
                                                    ): ?>

                                                        <span class="text-danger">
                                                            <?= number_format(
                                                                $remainingTotal
                                                            ) ?>
                                                        </span>

                                                    <?php else: ?>
                                                        <?= number_format(
                                                            $remainingTotal
                                                        ) ?>
                                                    <?php endif; ?>
                                                </b>
                                            </td>
                                        </tr>

                            <?php

                                    endif;

                                endforeach;                             

                                ?>

                                </tbody>

                                <tfoot>
                                <?php

                                /*
                                |--------------------------------------------------------------------------
                                | GRAND TOTAL - ACTUAL
                                |--------------------------------------------------------------------------
                                */

                                if (
                                    $status_filter == '' ||
                                    $status_filter == 'Actual Prod' ||
                                    $status_filter == 'Compare'
                                ):

                            ?>

                                    <tr class="grand-total">
                                        <!-- 1 -->
                                        <td></td>
                                        <!-- 2 -->
                                        <td></td>
                                        <!-- 3 -->
                                        <td></td>
                                        <!-- 4 -->
                                        <td></td>
                                        <!-- 5 -->
                                        <td class="text-center">
                                            <b>
                                                TOTAL
                                            </b>
                                        </td>

                                        <!-- 6 -->
                                        <td class="text-center">

                                            <b>
                                                <?= number_format(
                                                    $grandOrderTotal
                                                ) ?>
                                            </b>
                                        </td>

                                        <!-- 7 -->
                                        <td>
                                            <b>
                                                Actual Prod
                                            </b>
                                        </td>

                                        <?php

                                        $grandActualTotal = 0;

                                        foreach ($sizes as $size):

                                            $grandActualTotal +=
                                                $grandActual[$size];

                                        ?>

                                            <td class="text-center">
                                                <b>
                                                    <?= number_format(
                                                        $grandActual[$size]
                                                    ) ?>
                                                </b>
                                            </td>

                                        <?php endforeach; ?>

                                        <!-- 37 -->
                                        <td class="text-center">
                                            <b>
                                                <?= number_format(
                                                    $grandActualTotal
                                                ) ?>
                                            </b>
                                        </td>
                                    </tr>

                            <?php

                                endif;


                                /*
                                |--------------------------------------------------------------------------
                                | GRAND TOTAL - REMAINING
                                |--------------------------------------------------------------------------
                                */

                                if (
                                    $status_filter == 'Remaining' ||
                                    $status_filter == 'Compare'
                                ):

                            ?>

                                    <tr class="grand-total row-remaining">
                                        <!-- 1 -->
                                        <td></td>
                                        <!-- 2 -->
                                        <td></td>
                                        <!-- 3 -->
                                        <td></td>
                                        <!-- 4 -->
                                        <td></td>
                                        <!-- 5 -->
                                        <td class="text-center">
                                            <b>
                                                TOTAL
                                            </b>
                                        </td>

                                        <!-- 6 -->
                                        <td class="text-center">

                                            <b>
                                                <?= number_format(
                                                    $grandOrderTotal
                                                ) ?>
                                            </b>
                                        </td>

                                        <!-- 7 -->
                                        <td>
                                            <b>
                                                Remaining
                                            </b>
                                        </td>

                                        <?php foreach ($sizes as $size): ?>

                                            <td class="text-center">
                                                <b>
                                                    <?= number_format(
                                                        $grandRemaining[$size] ?? 0
                                                    ) ?>
                                                </b>
                                            </td>
                                        <?php endforeach; ?>

                                        <!-- 37 -->
                                        <td class="text-center">
                                            <b>
                                                <?= number_format(
                                                    $grandRemainingTotal
                                                ) ?>
                                            </b>
                                        </td>
                                    </tr>

                            <?php
                                 endif;
                                ?>
                            </tfoot>

                            <?php
                            endif;
                            ?>

                            </table>
                            <!-- CUSTOM HORIZONTAL SCROLLBAR -->
                            <div class="report-scrollbar-wrapper">
                                <div class="report-scrollbar"></div>
                            </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- FOOTER -->

    <footer class="main-footer">
        <div class="float-right d-none d-sm-block">
            <b>
                Version
            </b>
            1.0.0
        </div>
        2024
        <strong>

            <a href="#">
                Mfg Project Officer
            </a>.
        </strong>
        All rights reserved.
    </footer>
</div>


<!--
|--------------------------------------------------------------------------
| JAVASCRIPT
|--------------------------------------------------------------------------
| Hanya load jQuery dan DataTables satu kali.
|--------------------------------------------------------------------------
-->

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="plugins/select2/js/select2.full.min.js"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.4.1/js/dataTables.fixedHeader.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>


<script>

$(document).ready(function () {

    /*
    |--------------------------------------------------------------------------
    | DATA FILTER DARI PHP
    |--------------------------------------------------------------------------
    */

    var bucketValue =
        <?= json_encode($bucket) ?>;

    var bucketToValue =
        <?= json_encode($bucket_to) ?>;

    var styleValue =
        <?= json_encode($style_filter) ?>;

    var itemValue =
        <?= json_encode($item_filter) ?>;

    var colourValue =
        <?= json_encode($colour_filter) ?>;

    var genderValue =
        <?= json_encode($gender_filter) ?>;

    var statusValue =
        <?= json_encode($status_filter) ?>;


    /*
    |--------------------------------------------------------------------------
    | FLAG RESTORE FILTER
    |--------------------------------------------------------------------------
    */

    var restoringFilter = true;


    /*
    |--------------------------------------------------------------------------
    | SELECT2 AJAX
    |--------------------------------------------------------------------------
    */

    /*
|--------------------------------------------------------------------------
| SELECT2 AJAX
|--------------------------------------------------------------------------
*/

    function initSelect2(
        selector,
        action,
        placeholder,
        selectedValue
    ) {

        $(selector).select2({

            theme: "bootstrap4",
            width: "100%",
            placeholder: placeholder,
            allowClear: true,

            /*
            |--------------------------------------------------------------------------
            | MINIMUM 1 CHARACTER
            |--------------------------------------------------------------------------
            */

            minimumInputLength: 1,
            ajax: {

                /*
                |--------------------------------------------------------------------------
                | PENTING
                |--------------------------------------------------------------------------
                | AJAX sekarang langsung ke file ini.
                */

                url: "report_minus_produksi_ip.php",
                type: "POST",
                dataType: "json",
                delay: 250,

                data: function (params) {
                    return {
                        action: action,
                        search:
                            params.term || "",

                        /*
                        |--------------------------------------------------------------------------
                        | BUCKET
                        |--------------------------------------------------------------------------
                        */

                        bucket:
                            $("#bucket").val() || "",

                        bucket_to:
                            $("#bucket_to").val() || "",

                        /*
                        |--------------------------------------------------------------------------
                        | DEPENDENCY
                        |--------------------------------------------------------------------------
                        */

                        item:
                            $("#item").val() || "",

                        style:
                            $("#style").val() || "",

                        colour:
                            $("#colour").val() || "",

                        gender:
                            $("#gender").val() || ""
                    };

                },

                processResults: function (data) {
                    return {
                        results:
                            data.results || []
                    };

                },

                cache: false

            }

        });


        /*
        |--------------------------------------------------------------------------
        | RESTORE VALUE SETELAH SEARCH
        |--------------------------------------------------------------------------
        */

        if (
            selectedValue !== null &&
            selectedValue !== undefined &&
            selectedValue !== ""
        ) {

            var option = new Option(
                selectedValue,
                selectedValue,
                true,
                true
            );

            $(selector)
                .append(option)
                .trigger("change");

        }

    }


    /*
    |--------------------------------------------------------------------------
    | INIT SELECT2
    |--------------------------------------------------------------------------
    */

    initSelect2(
        "#bucket",
        "searchBucket",
        "Pilih Bucket",
        bucketValue
    );

    initSelect2(
        "#bucket_to",
        "searchBucket",
        "Pilih Bucket",
        bucketToValue
    );

    initSelect2(
        "#style",
        "searchStyle",
        "Pilih Style",
        styleValue
    );

    initSelect2(
        "#item",
        "searchItem",
        "Pilih Item",
        itemValue
    );

    initSelect2(
        "#colour",
        "searchColour",
        "Pilih Colour",
        colourValue
    );

    initSelect2(
        "#gender",
        "searchGender",
        "Pilih Gender",
        genderValue
    );


    /*
    |--------------------------------------------------------------------------
    | STATUS SELECT2
    |--------------------------------------------------------------------------
    */

    $("#status").select2({
        theme: "bootstrap4",
        width: "100%",
        placeholder: "Pilih Status",
        allowClear: true

    });


    if (
        statusValue !== null &&
        statusValue !== undefined &&
        statusValue !== ""
    ) {

        $("#status")
            .val(statusValue)
            .trigger("change");

    }


    /*
    |--------------------------------------------------------------------------
    | SELESAI RESTORE FILTER
    |--------------------------------------------------------------------------
    */
    restoringFilter = false;

    /*
    |--------------------------------------------------------------------------
    | BUCKET CHANGE
    |--------------------------------------------------------------------------
    |
    | Bucket berubah
    | ↓
    | Item, Style, Colour, Gender di-reset
    |
    */

    $("#bucket, #bucket_to").on(
        "change",
        function () {

            if (restoringFilter) {
                return;
            }

            $("#item")
                .val(null)
                .trigger("change");

            $("#style")
                .val(null)
                .trigger("change");

            $("#colour")
                .val(null)
                .trigger("change");

            $("#gender")
                .val(null)
                .trigger("change");

        }
    );


    /*
    |--------------------------------------------------------------------------
    | ITEM CHANGE
    |--------------------------------------------------------------------------
    |
    | Item berubah
    | ↓
    | Style, Colour, Gender di-reset
    |
    */

    $("#item").on(
        "change",
        function () {

            if (restoringFilter) {
                return;
            }

            $("#style")
                .val(null)
                .trigger("change");

            $("#colour")
                .val(null)
                .trigger("change");

            $("#gender")
                .val(null)
                .trigger("change");

        }
    );


    /*
    |--------------------------------------------------------------------------
    | STYLE CHANGE
    |--------------------------------------------------------------------------
    |
    | Style berubah
    | ↓
    | Colour, Gender di-reset
    |
    */

    $("#style").on(
        "change",
        function () {

            if (restoringFilter) {
                return;
            }

            $("#colour")
                .val(null)
                .trigger("change");

            $("#gender")
                .val(null)
                .trigger("change");

        }
    );


    /*
    |--------------------------------------------------------------------------
    | COLOUR CHANGE
    |--------------------------------------------------------------------------
    |
    | Colour berubah
    | ↓
    | Gender di-reset
    |
    */

    $("#colour").on(
        "change",
        function () {

            if (restoringFilter) {
                return;
            }

            $("#gender")
                .val(null)
                .trigger("change");

        }
    );


    /*
    |--------------------------------------------------------------------------
    | SELECT2 AUTO FOCUS
    |--------------------------------------------------------------------------
    */

    $(document).on(
        "select2:open",
        function () {
            var searchField =
                document.querySelector(
                    ".select2-container--open .select2-search__field"
                );

            if (searchField) {
                searchField.focus();

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | DATATABLE
    |--------------------------------------------------------------------------
    |
    | PENTING:
    |
    | Setiap TR pada TBODY sekarang mempunyai
    | tepat 37 TD.
    |
    | Tidak ada rowspan / colspan.
    |
    |--------------------------------------------------------------------------
    */

    var reportTable = $('#ReportStock').DataTable({

        responsive: false,
        scrollX: true,
        scrollCollapse: true,
        fixedHeader: true,
        fixedColumns: {
            leftColumns: 0
        },

        autoWidth: false,
        paging: true,
        searching: true,
        ordering: false,
        info: true,
        pageLength: 10,

        lengthMenu: [
            [10, 25, 50, 100, 250, 500],
            [10, 25, 50, 100, 250, 500]
        ],

        dom:
            "<'row mb-2'" +
                "<'col-sm-6 d-flex align-items-center'l>" +
                "<'col-sm-6 d-flex justify-content-end align-items-center'Bf>" +
            ">" +

            "<'row'" +
                "<'col-sm-12'tr>" +
            ">" +

            "<'row mt-2'" +
                "<'col-sm-5'i>" +
                "<'col-sm-7'p>" +
            ">",

        buttons: [

            {
                extend: 'excelHtml5',
                text:
                    '<i class="fas fa-file-excel"></i> Export Excel',
                className:
                    'btn btn-success btn-sm',
                title:
                    'Report Minus Produksi IP',
                filename:
                    'Report_Minus_Produksi_IP',

                exportOptions: {
                    columns: ':visible',
                    modifier: {
                        search: 'applied',
                        order: 'applied'
                    }
                }
            }
        ],

        columnDefs: [
            {
                targets: '_all',
                defaultContent: '',
                className: 'text-center'
            }

        ],

        initComplete: function () {
            console.log(
                'DATATABLE BERHASIL INITIALIZE'
            );

            console.log(
                'ROWS:',
                this.api().rows().count()
            );

            console.log(
                'COLUMNS:',
                this.api().columns().count()
            );

            console.log(
                'EXPORT BUTTON:',
                $('#ReportStock_wrapper .dt-buttons').length
            );

            console.log(
                'SHOW ENTRIES:',
                $('#ReportStock_wrapper .dataTables_length').length
            );
        }
    });


    /*
    |--------------------------------------------------------------------------
    | COLUMN ADJUST
    |--------------------------------------------------------------------------
    */

    reportTable.columns.adjust();

    /*
    |--------------------------------------------------------------------------
    | CUSTOM HORIZONTAL SCROLLBAR
    |--------------------------------------------------------------------------
    */

    function syncReportScrollbar() {
        var scrollBody =
            $('#ReportStock_wrapper .dataTables_scrollBody');
        var scrollbar =
            $('.report-scrollbar-wrapper');
        var scrollbarInner =
            $('.report-scrollbar');
        if (
            scrollBody.length &&
            scrollbar.length &&
            scrollbarInner.length
        ) {

            /*
            |--------------------------------------------------------------
            | Samakan lebar custom scrollbar dengan lebar tabel
            |--------------------------------------------------------------
            */

            scrollbarInner.width(
                scrollBody[0].scrollWidth
            );

            /*
            |--------------------------------------------------------------
            | Scroll DataTables -> Custom Scrollbar
            |--------------------------------------------------------------
            */

            scrollbar.on(
                'scroll',
                function () {

                    scrollBody.scrollLeft(
                        $(this).scrollLeft()
                    );
                }
            );

            /*
            |--------------------------------------------------------------
            | Custom Scrollbar -> DataTables
            |--------------------------------------------------------------
            */

            scrollBody.on(
                'scroll',
                function () {

                    scrollbar.scrollLeft(
                        $(this).scrollLeft()
                    );
                }
            );
        }
    }
    syncReportScrollbar();

    /*
    |--------------------------------------------------------------------------
    | WINDOW RESIZE
    |--------------------------------------------------------------------------
    */

    $(window).on(
        'resize',
        function () {

            reportTable.columns.adjust();
        }
    );
});

</script>
</body>
</html>