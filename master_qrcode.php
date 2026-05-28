<?php
// session_start();
require 'function.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>iPhylon | Master QR Code</title>

    <!-- Google Font -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="plugins/fontawesome-free/css/all.min.css">

    <!-- DataTables -->
    <link rel="stylesheet"
        href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">

    <link rel="stylesheet"
        href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">

    <!-- Select2 -->
    <link rel="stylesheet"
        href="plugins/select2/css/select2.min.css">

    <link rel="stylesheet"
        href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

    <!-- Theme style -->
    <link rel="stylesheet"
        href="dist/css/adminlte.min.css">

    <style>

        .table-responsive{
            max-height: 70vh;
            overflow: auto;
        }

        /* FREEZE HEADER */
        #example1 thead th{
            position: sticky;
            top: 0;
            z-index: 100;
            background: #17a2b8 !important;
            color: white;
            white-space: nowrap;
        }

        /* TABLE */
        #example1 td,
        #example1 th{
            white-space: nowrap;
            vertical-align: middle;
        }

        /* CHECKBOX */
        .checkItem,
        #checkAll{
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        #example1 td:first-child,
        #example1 td:nth-child(2),
        #example1 th:first-child,
        #example1 th:nth-child(2){
            text-align: center;
            vertical-align: middle;
        }

    </style>

</head>

<body class="hold-transition sidebar-mini">

<div class="wrapper">

    <!-- HEADER -->
    <?php include 'header.php'; ?>

    <!-- CONTENT -->
    <div class="content-wrapper">

        <!-- PAGE HEADER -->
        <section class="content-header">

            <div class="container-fluid">

                <div class="row mb-2">

                    <div class="col-sm-6">
                        <h1>Master QR Code</h1>
                    </div>

                    <div class="col-sm-6">

                        <ol class="breadcrumb float-sm-right">

                            <li class="breadcrumb-item">
                                <a href="index.php">Home</a>
                            </li>

                            <li class="breadcrumb-item active">
                                Master QR Code
                            </li>

                        </ol>

                    </div>

                </div>

            </div>

        </section>

        <!-- MAIN CONTENT -->
        <section class="content">

            <div class="container-fluid">

                <!-- FILTER -->
                <div class="card card-info">

                    <div class="card-header">

                        <h3 class="card-title">
                            Filter QR Code
                        </h3>

                    </div>

                    <div class="card-body">

                        <form method="GET">

                            <div class="row">

                                <!-- QR CODE -->
                                <div class="col-md-3 mb-2">

                                    <select
                                        name="qr_code"
                                        class="form-control select2bs4"
                                        style="width:100%;">

                                        <option value="">
                                            QR Code
                                        </option>

                                        <?php
                                        $getQR = mysqli_query($conn,"
                                            SELECT DISTINCT qr_code
                                            FROM tbl_master_barcode
                                            ORDER BY qr_code ASC
                                        ");

                                        foreach($getQR as $qr){
                                        ?>

                                            <option
                                                value="<?= $qr['qr_code']; ?>"
                                                <?= (@$_GET['qr_code'] == $qr['qr_code']) ? 'selected' : ''; ?>>

                                                <?= $qr['qr_code']; ?>

                                            </option>

                                        <?php } ?>

                                    </select>

                                </div>

                                <!-- JO -->
                                <div class="col-md-3 mb-2">

                                    <select
                                        name="no_jo"
                                        class="form-control select2bs4"
                                        style="width:100%;">

                                        <option value="">
                                            JO
                                        </option>

                                        <?php
                                        $getJO = mysqli_query($conn,"
                                            SELECT DISTINCT no_jo
                                            FROM tbl_jo_spk
                                            ORDER BY no_jo ASC
                                        ");

                                        foreach($getJO as $jo){
                                        ?>

                                            <option
                                                value="<?= $jo['no_jo']; ?>"
                                                <?= (@$_GET['no_jo'] == $jo['no_jo']) ? 'selected' : ''; ?>>

                                                <?= $jo['no_jo']; ?>

                                            </option>

                                        <?php } ?>

                                    </select>

                                </div>

                                <!-- LINE -->
                                <div class="col-md-3 mb-2">

                                    <select
                                        name="line"
                                        class="form-control select2bs4"
                                        style="width:100%;">

                                        <option value="">
                                            Line
                                        </option>

                                        <option value="Line 1">Line 1</option>
                                        <option value="Line 2">Line 2</option>
                                        <option value="Line 3">Line 3</option>
                                        <option value="Line 4">Line 4</option>
                                        <option value="Line 5">Line 5</option>
                                        <option value="Line 6">Line 6</option>
                                        <option value="Line 7">Line 7</option>
                                        <option value="Line 8">Line 8</option>

                                    </select>

                                </div>

                                <!-- BUCKET -->
                                <div class="col-md-3 mb-2">

                                    <select
                                        name="bucket"
                                        class="form-control select2bs4"
                                        style="width:100%;">

                                        <option value="">
                                            Bucket
                                        </option>

                                        <?php
                                        $getBucket = mysqli_query($conn,"
                                            SELECT DISTINCT bucket
                                            FROM tbl_spk_detail
                                            ORDER BY bucket ASC
                                        ");

                                        foreach($getBucket as $bucket){
                                        ?>

                                            <option
                                                value="<?= $bucket['bucket']; ?>"
                                                <?= (@$_GET['bucket'] == $bucket['bucket']) ? 'selected' : ''; ?>>

                                                <?= $bucket['bucket']; ?>

                                            </option>

                                        <?php } ?>

                                    </select>

                                </div>

                                <!-- STYLE -->
                                <div class="col-md-3 mb-2">

                                    <select
                                        name="style"
                                        class="form-control select2bs4"
                                        style="width:100%;">

                                        <option value="">
                                            Style
                                        </option>

                                        <?php
                                        $getStyle = mysqli_query($conn,"
                                            SELECT DISTINCT style
                                            FROM tbl_spk_detail
                                            ORDER BY style ASC
                                        ");

                                        foreach($getStyle as $style){
                                        ?>

                                            <option
                                                value="<?= $style['style']; ?>"
                                                <?= (@$_GET['style'] == $style['style']) ? 'selected' : ''; ?>>

                                                <?= $style['style']; ?>

                                            </option>

                                        <?php } ?>

                                    </select>

                                </div>

                                <!-- PO -->
                                <div class="col-md-3 mb-2">

                                    <select
                                        name="po"
                                        class="form-control select2bs4"
                                        style="width:100%;">

                                        <option value="">
                                            PO
                                        </option>

                                        <?php
                                        $getPO = mysqli_query($conn,"
                                            SELECT DISTINCT po
                                            FROM tbl_spk_detail
                                            ORDER BY po ASC
                                        ");

                                        foreach($getPO as $po){
                                        ?>

                                            <option
                                                value="<?= $po['po']; ?>"
                                                <?= (@$_GET['po'] == $po['po']) ? 'selected' : ''; ?>>

                                                <?= $po['po']; ?>

                                            </option>

                                        <?php } ?>

                                    </select>

                                </div>

                                <!-- PO ITEM -->
                                <div class="col-md-3 mb-2">

                                    <select
                                        name="po_item"
                                        class="form-control select2bs4"
                                        style="width:100%;">

                                        <option value="">
                                            PO Item
                                        </option>

                                        <?php
                                        $getPOItem = mysqli_query($conn,"
                                            SELECT DISTINCT po_item
                                            FROM tbl_spk_detail
                                            ORDER BY po_item ASC
                                        ");

                                        foreach($getPOItem as $poitem){
                                        ?>

                                            <option
                                                value="<?= $poitem['po_item']; ?>"
                                                <?= (@$_GET['po_item'] == $poitem['po_item']) ? 'selected' : ''; ?>>

                                                <?= $poitem['po_item']; ?>

                                            </option>

                                        <?php } ?>

                                    </select>

                                </div>

                                <!-- ITEM -->
                                <div class="col-md-3 mb-2">

                                    <select
                                        name="item"
                                        class="form-control select2bs4"
                                        style="width:100%;">

                                        <option value="">
                                            Item
                                        </option>

                                        <?php
                                        $getItem = mysqli_query($conn,"
                                            SELECT DISTINCT item
                                            FROM tbl_jo_spk
                                            ORDER BY item ASC
                                        ");

                                        foreach($getItem as $item){
                                        ?>

                                            <option
                                                value="<?= $item['item']; ?>"
                                                <?= (@$_GET['item'] == $item['item']) ? 'selected' : ''; ?>>

                                                <?= $item['item']; ?>

                                            </option>

                                        <?php } ?>

                                    </select>

                                </div>

                                <!-- GENDER -->
                                <div class="col-md-3 mb-2">

                                    <select
                                        name="gender"
                                        class="form-control select2bs4"
                                        style="width:100%;">

                                        <option value="">
                                            Gender
                                        </option>

                                        <?php
                                        $getGender = mysqli_query($conn,"
                                            SELECT DISTINCT gender
                                            FROM tbl_spk_detail
                                            ORDER BY gender ASC
                                        ");

                                        foreach($getGender as $gender){
                                        ?>

                                            <option
                                                value="<?= $gender['gender']; ?>"
                                                <?= (@$_GET['gender'] == $gender['gender']) ? 'selected' : ''; ?>>

                                                <?= $gender['gender']; ?>

                                            </option>

                                        <?php } ?>

                                    </select>

                                </div>

                                <!-- COLOUR -->
                                <div class="col-md-3 mb-2">

                                    <select
                                        name="colour"
                                        class="form-control select2bs4"
                                        style="width:100%;">

                                        <option value="">
                                            Colour
                                        </option>

                                        <?php
                                        $getColour = mysqli_query($conn,"
                                            SELECT DISTINCT colour
                                            FROM tbl_spk_detail
                                            ORDER BY colour ASC
                                        ");

                                        foreach($getColour as $colour){
                                        ?>

                                            <option
                                                value="<?= $colour['colour']; ?>"
                                                <?= (@$_GET['colour'] == $colour['colour']) ? 'selected' : ''; ?>>

                                                <?= $colour['colour']; ?>

                                            </option>

                                        <?php } ?>

                                    </select>

                                </div>

                                <!-- SIZE -->
                                <div class="col-md-3 mb-2">

                                    <select
                                        name="size"
                                        class="form-control select2bs4"
                                        style="width:100%;">

                                        <option value="">
                                            Size
                                        </option>

                                        <?php
                                        $getSize = mysqli_query($conn,"
                                            SELECT DISTINCT size
                                            FROM tbl_spk_size_qty
                                            ORDER BY size ASC
                                        ");

                                        foreach($getSize as $size){
                                        ?>

                                            <option
                                                value="<?= $size['size']; ?>"
                                                <?= (@$_GET['size'] == $size['size']) ? 'selected' : ''; ?>>

                                                <?= $size['size']; ?>

                                            </option>

                                        <?php } ?>

                                    </select>

                                </div>

                                <!-- STATUS -->
                                <div class="col-md-3 mb-2">

                                    <select
                                        name="status"
                                        class="form-control">

                                        <option value="">
                                            -- Status --
                                        </option>

                                        <option
                                            value="PRINT"
                                            <?= (@$_GET['status'] == 'YES') ? 'selected' : ''; ?>>

                                            🟢 PRINTED

                                        </option>

                                        <option
                                            value="NOT PRINT"
                                            <?= (@$_GET['status'] == 'NO') ? 'selected' : ''; ?>>

                                            🔴 NOT PRINT

                                        </option>

                                    </select>

                                </div>

                                <!-- SEARCH -->
                                <div class="col-md-1 mb-2">

                                    <button
                                        type="submit"
                                        name="search"
                                        class="btn btn-primary btn-block">

                                        <i class="fas fa-search"></i>
                                        Search

                                    </button>

                                </div>

                                <!-- RESET -->
                                <div class="col-md-1 mb-2">

                                    <a
                                        href="master_qrcode.php"
                                        class="btn btn-secondary btn-block">

                                        <i class="fas fa-sync"></i>
                                        Reset

                                    </a>

                                </div>

                            </div>

                        </form>

                    </div>

                </div>

                <!-- TABLE -->
                <div class="card card-success">

                    <div class="card-header">

                        <h3 class="card-title">
                            Data QR Code
                        </h3>

                    </div>

                    <form
                        method="POST"
                        action="print_multiple_qrcode.php">

                        <div class="card-body table-responsive">

                            <table
                                id="example1"
                                class="table table-bordered table-striped">

                                <thead>

                                    <tr>

                                        <th width="50">No</th>

                                        <th width="50">
                                            <input
                                                type="checkbox"
                                                id="checkAll">
                                        </th>

                                        <th>QR Code</th>
                                        <th>JO</th>
                                        <th>Line</th>
                                        <th>Bucket</th>
                                        <th>Style</th>
                                        <th>PO</th>
                                        <th>PO Item</th>
                                        <th>Item</th>
                                        <th>Gender</th>
                                        <th>Colour</th>
                                        <th>Size</th>
                                        <th>Qty</th>
                                        <th>Status</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php

                                    $no = 1;

                                    $query = "
                                        SELECT *
                                        FROM tbl_spk_size_qty sq

                                        JOIN tbl_spk_detail d
                                        ON sq.id_detail = d.id_detail

                                        JOIN tbl_jo_spk j
                                        ON d.id_jo_spk = j.id_jo_spk

                                        INNER JOIN tbl_master_barcode mb
                                        ON sq.id_size_qty = mb.id_size_qty

                                        WHERE 1=1
                                    ";

                                    if(isset($_GET['search'])){

                                        if($_GET['qr_code'] != ''){
                                            $query .= " AND mb.qr_code = '".$_GET['qr_code']."'";
                                        }

                                        if($_GET['no_jo'] != ''){
                                            $query .= " AND j.no_jo LIKE '%".$_GET['no_jo']."%'";
                                        }

                                        if($_GET['line'] != ''){
                                            $query .= " AND j.line_produksi LIKE '%".$_GET['line']."%'";
                                        }

                                        if($_GET['bucket'] != ''){
                                            $query .= " AND d.bucket LIKE '%".$_GET['bucket']."%'";
                                        }

                                        if($_GET['style'] != ''){
                                            $query .= " AND d.style LIKE '%".$_GET['style']."%'";
                                        }

                                        if($_GET['po'] != ''){
                                            $query .= " AND d.po LIKE '%".$_GET['po']."%'";
                                        }

                                        if($_GET['po_item'] != ''){
                                            $query .= " AND d.po_item LIKE '%".$_GET['po_item']."%'";
                                        }

                                        if($_GET['item'] != ''){
                                            $query .= " AND j.item LIKE '%".$_GET['item']."%'";
                                        }

                                        if($_GET['gender'] != ''){
                                            $query .= " AND d.gender LIKE '%".$_GET['gender']."%'";
                                        }

                                        if($_GET['colour'] != ''){
                                            $query .= " AND d.colour LIKE '%".$_GET['colour']."%'";
                                        }

                                        if($_GET['size'] != ''){
                                            $query .= " AND sq.size = '".$_GET['size']."'";
                                        }

                                        if($_GET['status'] != ''){
                                            $query .= " AND sq.status_print = '".$_GET['status']."'";
                                        }

                                    }

                                    $data = [];

                                    if(isset($_GET['search'])){

                                        $query .= " ORDER BY sq.id_size_qty DESC";

                                        $data = mysqli_query($conn, $query);

                                    }

                                    ?>

                                    <?php if(isset($_GET['search'])) : ?>

                                        <?php foreach($data as $d) : ?>

                                            <tr>

                                                <td>
                                                    <?= $no++; ?>
                                                </td>

                                                <td>

                                                        <input
                                                            type="checkbox"
                                                            value="<?= $d['id_barcode']; ?>"
                                                            class="checkItem">
                                                </td>

                                                <td><?= $d['qr_code']; ?></td>
                                                <td><?= $d['no_jo']; ?></td>
                                                <td><?= $d['line_produksi']; ?></td>
                                                <td><?= $d['bucket']; ?></td>
                                                <td><?= $d['style']; ?></td>
                                                <td><?= $d['po']; ?></td>
                                                <td><?= $d['po_item']; ?></td>
                                                <td><?= $d['item']; ?></td>
                                                <td><?= $d['gender']; ?></td>
                                                <td><?= $d['colour']; ?></td>
                                                <td><?= $d['size']; ?></td>
                                                <td><?= $d['qty']; ?></td>

                                                <td>

                                                    <?php if($d['status_print'] == "YES") : ?>

                                                        <span class="badge badge-success">
                                                            PRINTED
                                                        </span>

                                                    <?php else : ?>

                                                        <span class="badge badge-danger">
                                                            NOT PRINT
                                                        </span>

                                                    <?php endif; ?>

                                                </td>

                                            </tr>

                                        <?php endforeach; ?>

                                    <?php else : ?>

                                        <tr>

                                            <td
                                                colspan="15"
                                                class="text-center text-muted">

                                                Silakan lakukan filter terlebih dahulu

                                            </td>

                                        </tr>

                                    <?php endif; ?>

                                </tbody>

                            </table>

                        </div>

                        <div class="card-footer">
                            <button
                                type="button"
                                class="btn btn-warning"
                                id="btnPrintSelected">

                                <i class="fas fa-print"></i>
                                Print Selected
                            </button>
                        </div>

                    </form>

                </div>

            </div>

        </section>

    </div>

    <!-- FOOTER -->
    <footer class="main-footer">

        <div class="float-right d-none d-sm-block">
            <b>Version</b> 1.0.0
        </div>

        2026
        <strong>
            <a href="#">Mfg Project Officer</a>.
        </strong>

        All rights reserved.

    </footer>

</div>

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>

<!-- Bootstrap -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

<!-- Select2 -->
<script src="plugins/select2/js/select2.full.min.js"></script>

<!-- AdminLTE -->
<script src="dist/js/adminlte.min.js"></script>

<!-- QR Code -->
<script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>

<script>

    /* DATATABLE */
    $(document).ready(function () {

        $('#example1').DataTable({

            "paging": true,
            "pageLength": 10,

            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],

            "lengthChange": true,
            "searching": false,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": false

        });

    });

    /* SELECT2 */
    $('.select2bs4').select2({

        theme: 'bootstrap4',
        width: '100%',
        minimumInputLength: 1

    });

    /* CHECK ALL */
    $('#checkAll').click(function(){

        $('.checkItem').prop(
            'checked',
            $(this).prop('checked')
        );

    });

</script>


<script>

/* =========================
   SELECT ALL
========================= */

$('#checkAll').on('change', function(){

    $('.checkItem').prop(
        'checked',
        $(this).prop('checked')
    );

});

/* =========================
   PRINT SELECTED
========================= */

$('#btnPrintSelected').on('click', async function(){

    // console.log('PRINT CLICK');

    let checked =
        document.querySelectorAll(
            '.checkItem:checked'
        );

    if(checked.length == 0)
    {
        alert('Pilih minimal 1 data');
        return;
    }

    let labels = '';

    for(let cb of checked)
    {
        let row =
            cb.closest('tr');

        labels +=
            await generateTableQR(row);
    }

    openPrintWindow(labels);

});

/* =========================
   GENERATE QR LABEL
========================= */

async function generateTableQR(row)
{
    let qrText =
        row.cells[2].innerText.trim();

    let bucket =
        row.cells[5].innerText.trim();

    let style =
        row.cells[6].innerText.trim();

    let po =
        row.cells[7].innerText.trim();

    let poItem =
        row.cells[8].innerText.trim();

    let gender =
        row.cells[10].innerText.trim();

    let colour =
        row.cells[11].innerText.trim();

    let size =
        row.cells[12].innerText.trim();

    let qty =
        row.cells[13].innerText.trim();

    let qrLast =
        qrText.split('-').pop();

    let qrImage =
        await QRCode.toDataURL(qrText, {

            width: 80,
            margin: 0

        });

    return `

        <div class="label">

            <div class="left-section">

                <div class="top-text">
                    ${bucket}
                </div>

                <div class="top-text">
                    ${po}-${poItem}
                </div>

                <div class="top-text">
                    ${style}
                </div>

                <div class="top-text">
                    ${gender} - ${colour}
                </div>

                <div class="bottom-row">

                    <div class="size">
                        ${size}
                    </div>

                    <div class="qty">
                        ${qty}
                    </div>

                </div>

            </div>

            <div class="right-section">

                <img src="${qrImage}"
                    class="qr-img">

                <div class="qr-text">
                    ${qrLast}
                </div>

            </div>

        </div>

    `;
}

/* =========================
   OPEN PRINT WINDOW
========================= */

function openPrintWindow(content)
{
    let printWindow =
        window.open('', '', 'width=800,height=600');

    printWindow.document.write(`

        <html>

        <head>

            <title>Print QR</title>

            <style>

                @page{
                    size: 50mm 30mm;
                    margin:0;
                }

                html, body{

                    margin:0;
                    padding:0;

                    font-family:Arial,sans-serif;
                }

                .print-area{

                    width:50mm;

                }

                .label{

                    width:50mm;
                    height:30mm;

                    box-sizing:border-box;

                    display:flex;

                    padding:2mm;

                    overflow:hidden;

                    page-break-after:always;

                    page-break-inside:avoid;
                }

                .left-section{

                    width:58%;

                    display:flex;
                    flex-direction:column;

                    justify-content:space-between;
                }

                .right-section{

                    width:42%;

                    display:flex;
                    flex-direction:column;

                    align-items:center;
                    justify-content:center;
                }

                .top-text{

                    font-size:3.2mm;
                    line-height:1.1;
                }

                .bottom-row{

                    display:flex;
                    align-items:flex-end;

                    gap:2mm;
                }

                .size{

                    font-size:10mm;
                    font-weight:bold;

                    line-height:1;
                }

                .qty{

                    font-size:5mm;

                    margin-bottom:1mm;
                }

                .qr-img{

                    width:14mm;
                    height:14mm;

                    object-fit:contain;
                }

                .qr-text{

                    font-size:3mm;

                    margin-top:1mm;
                }

            </style>

        </head>

        <body>

            <div class="print-area">

                ${content}

            </div>

        </body>

        </html>

    `);

    printWindow.document.close();

    setTimeout(() => {

        printWindow.focus();

        printWindow.print();

    }, 1000);
}

</script>


</body>
</html>