<?php
require 'function.php';

$Planning = mysqli_query($conn,"
    SELECT *
    FROM tbl_jo_spk
    ORDER BY tanggal_upload DESC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>iPhylon | Master Planning</title>

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

    <link rel="stylesheet"
          href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

    <!-- Theme style -->
    <link rel="stylesheet"
          href="dist/css/adminlte.min.css">

        <style>

        .modal-body-scroll{
            max-height: 500px;
            overflow-y: auto;
        }
      </style>

</head>

<body class="hold-transition sidebar-mini">

<div class="wrapper">

    <!-- HEADER -->
    <?php include 'header.php'; ?>

    <!-- CONTENT -->
    <div class="content-wrapper">

        <!-- HEADER CONTENT -->
        <section class="content-header">
                <!-- SUCCESS GENERATE -->
            <div class="container-fluid">
                <?php if(isset($_GET['generate'])) : ?>

                <div id="generateAlert"
                    class="alert alert-success alert-dismissible fade show">

                    QR Code berhasil digenerate

                </div>

                <?php endif; ?>
                <!-- DELETE ALERT -->
                <?php if(isset($_GET['delete'])) : ?>

                    <div id="deleteAlert"
                         class="alert alert-danger alert-dismissible fade show">

                        Planning berhasil dihapus

                    </div>

                <?php endif; ?>

                <!-- SUCCESS ALERT -->
                <?php if(isset($_GET['success'])) : ?>

                    <div id="successAlert"
                         class="alert alert-success alert-dismissible fade show">

                        SPK Planning berhasil diupload

                    </div>

                <?php endif; ?>

                <div class="row mb-2">

                    <div class="col-sm-6">
                        <h1>Master Planning</h1>
                    </div>

                    <div class="col-sm-6">

                        <ol class="breadcrumb float-sm-right">

                            <li class="breadcrumb-item">
                                <a href="index.php">Home</a>
                            </li>

                            <li class="breadcrumb-item active">
                                SPK Planning
                            </li>

                        </ol>

                    </div>

                </div>

            </div>

        </section>

        <!-- MAIN CONTENT -->
        <section class="content">
            <div class="container-fluid">
                <!-- UPLOAD CARD -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    Upload SPK Planning
                                </h3>
                            </div>

                            <div class="card-body">

                                <form action="./UploadData/upload_master_planning.php"
                                      method="POST"
                                      enctype="multipart/form-data">

                                    <div class="row">

                                        <!-- FILE -->
                                        <div class="col-md-4">

                                            <label>Upload Excel</label>

                                            <div class="custom-file">

                                                <input type="file"
                                                       class="custom-file-input"
                                                       id="spkPlanning"
                                                       name="spkPlanning"
                                                       accept=".xls,.xlsx"
                                                       required>

                                                <label class="custom-file-label">
                                                    Choose Excel File
                                                </label>
                                            </div>
                                        </div>

                                        <!-- BUTTON -->
                                        <div class="col-md-2">
                                            <label>&nbsp;</label>
                                            <button type="submit"
                                                    name="upload"
                                                    class="btn btn-primary btn-block">

                                                <i class="fas fa-upload"></i>
                                                Upload
                                            </button>
                                        </div>

                                        <!-- TEMPLATE -->
                                        <div class="col-md-2">
                                            <label>&nbsp;</label>
                                            <a href="./Template/template_spk_planning.xlsx"
                                               download
                                               class="btn btn-success btn-block">
                                                <i class="fas fa-download"></i>
                                                Download Template
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TABLE -->
                <div class="row">

                    <div class="col-12">

                        <div class="card card-default">

                            <div class="card-header">

                                <h3 class="card-title">
                                    Data SPK Planning
                                </h3>

                            </div>

                            <div class="card-body">

                                <table id="example1"
                                       class="table table-bordered table-striped">

                                    <thead>

                                        <tr>

                                            <th>No</th>
                                            <th>No JO</th>
                                            <th>Item</th>
                                            <th>Mesin</th>
                                            <th>Injector</th>
                                            <th>Line</th>
                                            <th>Tanggal SPK</th>
                                            <th>Upload Date</th>
                                            <th width="120" style="text-align: center;">Action</th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php $i = 1; ?>

                                        <?php foreach($Planning as $Plan) : ?>

                                        <tr>
                                            <td><?= $i++; ?></td>
                                            <td><?= $Plan['no_jo']; ?></td>
                                            <td><?= $Plan['item']; ?></td>
                                            <td><?= $Plan['mesin']; ?></td>
                                            <td><?= $Plan['injector']; ?></td>
                                            <td><?= $Plan['line_produksi']; ?></td>
                                            <td><?= $Plan['tanggal_spk']; ?></td>
                                            <td><?= $Plan['tanggal_upload']; ?></td>
                                            <td style="text-align: center;">

                                                <!-- DETAIL -->
                                                <button type="button"
                                                        class="btn btn-info btn-sm"
                                                        data-toggle="modal"
                                                        data-target="#detailSPK<?= $Plan['id_jo_spk']; ?>">
                                                    <i class="fas fa-eye"></i>

                                                </button>

                                                <!-- GENERATE QR -->
                                                <?php
                                                // cek apakah sudah generate qr
                                                $checkQR = mysqli_query($conn,"
                                                    SELECT *
                                                    FROM tbl_master_barcode
                                                    WHERE no_jo = '".$Plan['no_jo']."'
                                                ");

                                                $alreadyGenerate = mysqli_num_rows($checkQR);

                                                ?>

                                                <?php if($alreadyGenerate > 0) : ?>

                                                    <!-- BUTTON NON ACTIVE -->
                                                    <button class="btn btn-secondary btn-sm" disabled>

                                                        <i class="fas fa-qrcode"></i>

                                                    </button>

                                                <?php else : ?>

                                                    <!-- BUTTON ACTIVE -->
                                                    <a href="generate_qr.php?id=<?= $Plan['id_jo_spk']; ?>"
                                                      class="btn btn-success btn-sm">

                                                        <i class="fas fa-qrcode"></i>

                                                    </a>

                                                <?php endif; ?> 

                                                <!-- DELETE -->
                                                <a href="delete_planning.php?id=<?= $Plan['id_jo_spk']; ?>"
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Delete this planning?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>

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

        <div class="float-right d-none d-sm-block">
            <b>Version</b> 1.0.0
        </div>

        2024

        <strong>
            <a href="#">Mfg Project Officer</a>
        </strong>

        All rights reserved.

    </footer>

    <!-- SIDEBAR -->
    <aside class="control-sidebar control-sidebar-dark"></aside>

</div>

<!-- ================= MODAL SECTION ================= -->

<?php foreach($Planning as $Plan) : ?>

<div class="modal fade"
     id="detailSPK<?= $Plan['id_jo_spk']; ?>"
     tabindex="-1">

    <div class="modal-dialog modal-xl">

        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header bg-info">

                <h4 class="modal-title">
                    Detail SPK Planning
                </h4>

                <button type="button"
                        class="close"
                        data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <!-- BODY -->
            <div class="modal-body modal-body-scroll">

                <?php
                $detail = mysqli_query($conn,"
                    SELECT *
                    FROM tbl_spk_detail
                    WHERE id_jo_spk = '".$Plan['id_jo_spk']."'
                ");
                ?>

                <!-- INFORMASI MASTER -->
                <div class="row mb-3">

                    <div class="col-md-3">
                        <b>No JO :</b><br>
                        <?= $Plan['no_jo']; ?>
                    </div>

                    <div class="col-md-3">
                        <b>Item :</b><br>
                        <?= $Plan['item']; ?>
                    </div>

                    <div class="col-md-2">
                        <b>Mesin :</b><br>
                        <?= $Plan['mesin']; ?>
                    </div>

                    <div class="col-md-2">
                        <b>Injector :</b><br>
                        <?= $Plan['injector']; ?>
                    </div>

                    <div class="col-md-2">
                        <b>Line :</b><br>
                        <?= $Plan['line_produksi']; ?>
                    </div>

                </div>

                <!-- TABLE DETAIL -->
                <div class="table-responsive">

                    <table class="table table-bordered table-striped">

                        <thead class="bg-info">

                            <tr>
                                <th width="50">
                                    <input type="checkbox"
                                        id="checkAll<?= $Plan['id_jo_spk']; ?>"
                                        onclick="toggleAll('<?= $Plan['id_jo_spk']; ?>')">
                                </th>


                                <th>Bucket</th>
                                <th>Style</th>
                                <th>Gender</th>
                                <th>Colour</th>
                                <th>PO</th>
                                <th>PO Item</th>
                                <th>Size</th>
                                <th>Qty</th>
                                <th>Qr Code</th>
                                <th width="120">Action</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach($detail as $d) : ?>

                            <?php
                            $sizeQty = mysqli_query($conn,"
                                SELECT *
                                FROM tbl_spk_size_qty
                                WHERE id_detail = '".$d['id_detail']."'
                            ");
                            ?>

                            <?php

// CEK APAKAH SUDAH GENERATE
$checkGenerate = mysqli_query($conn,"
    SELECT *
    FROM tbl_master_barcode
    WHERE no_jo = '".$Plan['no_jo']."'
");

$alreadyGenerate = mysqli_num_rows($checkGenerate);

?>

<?php if($alreadyGenerate > 0) : ?>

    <?php

    // AMBIL DATA HASIL GENERATE QR
    $bundleData = mysqli_query($conn,"
        SELECT *
        FROM tbl_master_barcode
        WHERE no_jo = '".$Plan['no_jo']."'
    ");

    ?>

    <?php foreach($bundleData as $bundle) : ?>

    <tr>

        <td>
            <input type="checkbox"
                class="row-check-<?= $Plan['id_jo_spk']; ?>">
        </td>

        <td><?= $bundle['bucket']; ?></td>
        <td><?= $bundle['style']; ?></td>
        <td><?= $bundle['gender']; ?></td>
        <td><?= $bundle['colour']; ?></td>
        <td><?= $bundle['po']; ?></td>
        <td><?= $bundle['po_item']; ?></td>

        <!-- SIZE -->
        <td><?= $bundle['size']; ?></td>

        <!-- QTY -->
        <td><?= $bundle['qty']; ?></td>

        <!-- QR -->
        <td class="qr-value">
            <?= $bundle['qr_code']; ?>
        </td>

        <!-- ACTION -->
        <td>

            <button type="button"
                    class="btn btn-sm btn-success"
                    onclick="printSingleQR(this)">

                Print

            </button>

        </td>

    </tr>

    <?php endforeach; ?>

          <?php else : ?>

              <?php foreach($detail as $d) : ?>

                  <?php
                  $sizeQty = mysqli_query($conn,"
                      SELECT *
                      FROM tbl_spk_size_qty
                      WHERE id_detail = '".$d['id_detail']."'
                  ");
                  ?>

                  <?php foreach($sizeQty as $sz) : ?>

                  <tr>

                      <td>
                          <input type="checkbox"
                              class="row-check-<?= $Plan['id_jo_spk']; ?>">
                      </td>
                      <td><?= $d['bucket']; ?></td>
                      <td><?= $d['style']; ?></td>
                      <td><?= $d['gender']; ?></td>
                      <td><?= $d['colour']; ?></td>
                      <td><?= $d['po']; ?></td>
                      <td><?= $d['po_item']; ?></td>

                      <!-- SIZE -->
                      <td><?= $sz['size']; ?></td>

                      <!-- QTY -->
                      <td><?= $sz['qty']; ?></td>

                      <!-- QR -->
                      <td>

                      <!-- ACTION -->
                    <td>

                        <button type="button"
                                class="btn btn-sm btn-success"
                                onclick="printSingleQR(this)">

                            Print

                        </button>

                    </td>

                          <span class="badge badge-secondary">
                              Belum Generate
                          </span>
                      </td>
                  </tr>
                  <?php endforeach; ?>
              <?php endforeach; ?>
          <?php endif; ?>
                            <?php endforeach; ?>

                            </tbody>

                    </table>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="modal-footer">

                <button type="button"
                        class="btn btn-primary"
                        onclick="printSelectedRows('<?= $Plan['id_jo_spk']; ?>')">

                    Print Selected

                </button>

                <button type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">

                    Close

                </button>

            </div>
        </div>
    </div>
</div>

<?php endforeach; ?>

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>

<!-- Bootstrap -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>

<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>

<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>

<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>

<script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>

<!-- AdminLTE -->
<script src="dist/js/adminlte.min.js"></script>

<!-- QR Code -->
<script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>

<!-- FILE INPUT -->
<script>
$('.custom-file-input').on('change', function () {

    let fileName = $(this).val().split('\\').pop();

    $(this).next('.custom-file-label').html(fileName);

});
</script>

<!-- TOGGLE ALL CHECKBOXES -->
<script>

function toggleAll(planId)
{
    let checkAll = document.getElementById('checkAll' + planId);

    let checkboxes =
        document.querySelectorAll('.row-check-' + planId);

    checkboxes.forEach(function(cb) {
        cb.checked = checkAll.checked;
    });
}

</script>

<!-- PRINT SINGLE QR -->
 <script>

/* =========================================
   SELECT ALL
========================================= */

function toggleAll(planId)
{
    let checkAll =
        document.getElementById('checkAll' + planId);

    let checkboxes =
        document.querySelectorAll(
            '.row-check-' + planId
        );

    checkboxes.forEach(function(cb){

        cb.checked = checkAll.checked;

    });
}

/* =========================================
   PRINT SINGLE QR
========================================= */

async function printSingleQR(button)
{
    let row = button.closest('tr');

    let label =
        await generateQRLabel(row);

    openPrintWindow(label);
}

/* =========================================
   PRINT MULTIPLE QR
========================================= */

async function printSelectedRows(planId)
{
    let checkedRows =
        document.querySelectorAll(
            '.row-check-' + planId + ':checked'
        );

    if(checkedRows.length == 0)
    {
        alert('Pilih minimal 1 row');
        return;
    }

    let labels = '';

    for(let cb of checkedRows)
    {
        let row = cb.closest('tr');

        labels +=
            await generateQRLabel(row);
    }

    openPrintWindow(labels);
}openPrintWindow(labels);


/* =========================================
   GENERATE LABEL
========================================= */

async function generateQRLabel(row)
{
    let bucket  = row.cells[1].innerText;
    let style   = row.cells[2].innerText;
    let gender  = row.cells[3].innerText;
    let colour  = row.cells[4].innerText;
    let po      = row.cells[5].innerText;

    let size    = row.cells[7].innerText;
    let qty     = row.cells[8].innerText;

    let qrText =
        row.querySelector('.qr-value').innerText;

    let qrLast =
        qrText.split('-').pop();

    // GENERATE QR IMAGE BASE64
    let qrImage =
        await QRCode.toDataURL(qrText, {

            width: 80,
            margin: 0

        });

    return `

        <div class="label">

            <!-- LEFT -->
            <div class="left-section">

                <div class="top-text">
                    ${bucket}
                </div>

                <div class="top-text">
                    ${po}
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

            <!-- RIGHT -->
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


/* =========================================
   OPEN PRINT WINDOW
========================================= */

async function openPrintWindow(content)
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

                /* CONTAINER */
                .print-area{

                    width:50mm;

                }

                /* 1 LABEL */
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

                /* LEFT */
                .left-section{

                    width:58%;

                    display:flex;
                    flex-direction:column;

                    justify-content:space-between;
                }

                /* RIGHT */
                .right-section{

                    width:42%;

                    display:flex;
                    flex-direction:column;

                    align-items:center;
                    justify-content:center;
                }

                /* TEXT */
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

                /* QR */
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

        setTimeout(() => {

            printWindow.close();

        }, 1000);

    }, 3000);
}


</script>

<!-- DATATABLE -->
<script>
$(document).ready(function () {

    $('#example1').DataTable({

        "paging": true,
        "pageLength": 10,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true

    });

});
</script>

<!-- SUCCESS ALERT -->
<script>
setTimeout(function(){

    $('#successAlert').fadeOut('slow');

}, 1000);
</script>

<!-- DELETE ALERT -->
<script>
setTimeout(function(){

    $('#deleteAlert').fadeOut('slow');

}, 1000);
</script>

<script>
setTimeout(function(){

    $('#generateAlert').fadeOut('slow');

}, 1000);
</script>

</body>
</html>