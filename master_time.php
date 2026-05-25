<?php
require 'function.php';

$TransMaterial = mysqli_query($conn,"
    SELECT *
    FROM tbl_master_time
    ORDER BY date ASC, shift ASC, hour ASC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>iPhylon | Master Time</title>

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
            Master Time berhasil diupload
          </div>
          <?php endif; ?>

        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Master Time</h1>
          </div>

          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item">
                <a href="index.php">Home</a>
              </li>
              <li class="breadcrumb-item active">
                Master Time
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
                  Upload Data Master Time
                </h3>
              </div>

              <div class="card-body">
                <form action="./UploadData/upload_master_time.php"
                      method="POST"
                      enctype="multipart/form-data">

                  <div class="row">

                    <!-- START DATE -->
                    <div class="col-md-2">
                      <div class="form-group">
                        <label>Start Date</label>
                        <input type="date"
                               name="start_date"
                               class="form-control"
                               required>
                      </div>
                    </div>

                    <!-- END DATE -->
                    <div class="col-md-2">
                      <div class="form-group">
                        <label>End Date</label>
                        <input type="date"
                               name="end_date"
                               class="form-control"
                               required>
                      </div>
                    </div>

                    <!-- FILE -->
                    <div class="col-md-4">
                      <label>Upload Excel</label>
                      <div class="custom-file">
                        <input type="file"
                               class="custom-file-input"
                               id="masterTime"
                               name="masterTime"
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

                    <!-- DOWNLOAD TEMPLATE -->
                    <div class="col-md-2">
                      <label>&nbsp;</label>
                      <a href="./Template/master_time_template.xlsx"
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
                  Data Master Time
                </h3>
              </div>
              <div class="card-body">

                <table id="example1"
                       class="table table-bordered table-striped">

                  <thead>

                    <tr>

                      <th>No</th>
                      <th>Date</th>
                      <th>Start Time</th>
                      <th>End Time</th>
                      <th>Hour</th>
                      <th>Shift</th>

                    </tr>

                  </thead>

                  <tbody>

                    <?php $i = 1; ?>

                    <?php foreach($TransMaterial as $TMtr) : ?>
                    <tr>
                      <td><?= $i++; ?></td>
                      <td><?= $TMtr['date']; ?></td>
                      <td><?= $TMtr['time_start']; ?></td>
                      <td><?= $TMtr['time_end']; ?></td>
                      <td><?= $TMtr['hour']; ?></td>
                      <td><?= $TMtr['shift']; ?></td>
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