<?php
require 'function.php';

$DailyPlan = mysqli_query($conn,"
    SELECT *
    FROM tbl_daily_plan_header
    ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>iPhylon | Daily Production Plan</title>

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">

  <!-- DataTables -->
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- HEADER -->
  <?php include 'header.php'; ?>

  <!-- Content Wrapper -->
  <div class="content-wrapper">

    <!-- Content Header -->
    <section class="content-header">
      <!-- Notif Success -->
      <div class="container-fluid">
        <?php if(isset($_GET['success'])) : ?>
        <div id="successAlert"
            class="alert alert-success alert-dismissible fade show">
            Daily Plan berhasil diupload
        </div>
        <?php endif; ?>

        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Daily Production Plan</h1>
          </div>

          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item">
                <a href="index.php">Home</a>
              </li>
              <li class="breadcrumb-item active">
                Daily Plan
              </li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        
        <!-- UPLOAD -->
        <div class="row">
          <div class="col-md-12">
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">
                  Upload Daily Production Plan
                </h3>
              </div>

              <div class="card-body">
                <form action="./UploadData/upload_daily_plan.php"
                      method="POST"
                      enctype="multipart/form-data">

                  <div class="row">

                <!-- MASTER PLANNING -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Master Planning</label>
                        <select name="id_jo_spk"
                                class="form-control"
                                required>
                            <option value="">
                                -- Select Planning --
                            </option>
                            <?php
                            $spk = mysqli_query($conn,"
                                SELECT *
                                FROM tbl_jo_spk
                                ORDER BY tanggal_upload DESC
                            ");

                            while($s = mysqli_fetch_assoc($spk))
                            {
                            ?>
                            <option value="<?= $s['id_jo_spk']; ?>">

                                <?= $s['no_jo']; ?>
                                -
                                <?= $s['item']; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <!-- FILE -->
                <div class="col-md-5">
                    <label>Upload Daily Plan Excel</label>
                    <div class="custom-file">
                        <input type="file"
                              class="custom-file-input"
                              id="dailyPlan"
                              name="dailyPlan"
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
                    <a href="./Template/daily_plan_template.xlsx"
                      class="btn btn-success btn-block">
                        <i class="fas fa-download"></i>
                        Template
                      </a>
                  </div>
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
                  Data Daily Plan
                </h3>
              </div>
              <div class="card-body">

                <table id="example1"
                       class="table table-bordered table-striped">

                    <thead>
                        <tr>
                          <th>No</th>
                          <th>Tanggal Plan</th>
                          <th>Item</th>
                          <th>Colour</th>
                          <th>Mesin</th>
                          <th>Injector</th>
                          <th>Line</th>
                          <th>Uploaded By</th>
                          <th>Upload Date</th>
                          <th width="120">Action</th>
                        </tr>
                      </thead>

                  <tbody>

                      <?php $i=1; ?>

                      <?php foreach($DailyPlan as $dp) : ?>

                      <tr>

                          <td><?= $i++; ?></td>

                          <td><?= $dp['tanggal_plan']; ?></td>

                          <td><?= $dp['item']; ?></td>

                          <td><?= $dp['colour']; ?></td>

                          <td><?= $dp['mesin']; ?></td>

                          <td><?= $dp['injector']; ?></td>

                          <td><?= $dp['line_produksi']; ?></td>

                          <td><?= $dp['uploaded_by']; ?></td>

                          <td><?= $dp['created_at']; ?></td>

                          <td class="text-center">

                              <button
                                  class="btn btn-info btn-sm"
                                  data-toggle="modal"
                                  data-target="#detail<?= $dp['id_daily_header']; ?>">

                                  <i class="fas fa-eye"></i>

                              </button>

                              <a href="delete_daily_plan.php?id=<?= $dp['id_daily_header']; ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Delete Daily Plan ?')">

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
    </section>
  </div>

  <!-- FOOTER -->
  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 1.0.0
    </div>2024 
    <strong><a href="#">Mfg Project Officer</a>.</strong> All rights reserved.
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>

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
<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="plugins/jszip/jszip.min.js"></script>
<script src="plugins/pdfmake/pdfmake.min.js"></script>
<script src="plugins/pdfmake/vfs_fonts.js"></script>
<script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

<!-- AdminLTE -->
<script src="dist/js/adminlte.min.js"></script>

<!-- FILE INPUT -->
<script>
$('.custom-file-input').on('change', function () {

  let fileName = $(this).val().split('\\').pop();

  $(this).next('.custom-file-label').html(fileName);

});
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

<script>
setTimeout(function(){
    $('#successAlert').fadeOut('slow');
}, 2000);
</script>

<script>
if(window.location.href.indexOf("?success=1") > -1){
    window.history.replaceState({}, document.title, window.location.pathname);
}
</script>

</body>
</html>