<?php
require 'function.php';

// =========================
// Notifikasi
// =========================
$status  = '';
$message = '';

if(isset($_SESSION['status'])){

    $status  = $_SESSION['status'];
    $message = $_SESSION['message'];

    unset($_SESSION['status']);
    unset($_SESSION['message']);
}

// =========================
// UPDATE MASTER TIME
// =========================
if(isset($_POST['update_master_time'])){

    $id_time    = mysqli_real_escape_string($conn, $_POST['id_time']);
    $date       = mysqli_real_escape_string($conn, $_POST['date']);
    $time_start = mysqli_real_escape_string($conn, $_POST['time_start']);
    $time_end   = mysqli_real_escape_string($conn, $_POST['time_end']);
    $hour       = mysqli_real_escape_string($conn, $_POST['hour']);
    $shift      = mysqli_real_escape_string($conn, $_POST['shift']);

    $update = mysqli_query($conn, "
        UPDATE tbl_master_time
        SET
            date = '$date',
            time_start = '$time_start',
            time_end = '$time_end',
            hour = '$hour',
            shift = '$shift'
        WHERE id_time = '$id_time'
    ");

    if($update){

    $_SESSION['status']  = 'success';
    $_SESSION['message'] = 'Master Time has been successfully updated.';

    }else{

        $_SESSION['status']  = 'error';
        $_SESSION['message'] = 'Master Time has failed to update.';

    }

    header("Location: master_time.php");
    exit;
}


// =========================
// DELETE MASTER TIME
// =========================
if(isset($_GET['delete'])){

    $id_time = mysqli_real_escape_string($conn, $_GET['delete']);

    $delete = mysqli_query($conn, "
        DELETE FROM tbl_master_time
        WHERE id_time = '$id_time'
    ");

    if($delete){

    $_SESSION['status']  = 'success';
    $_SESSION['message'] = 'Master Time has been successfully deleted.';

      }else{

          $_SESSION['status']  = 'error';
          $_SESSION['message'] = 'Master Time has failed to delete.';

      }

      header("Location: master_time.php");
      exit;
}


// =========================
// GET MASTER TIME
// =========================
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

  <link rel="icon" href="assets/images/i.Phylon.png" type="image/x-icon">
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
      <div class="container-fluid">
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
                      <th width="200" style="text-align: center;">Action</th>

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
                        <td style="text-align: center;">
                            <!-- EDIT -->
                            <button
                                type="button"
                                class="btn btn-warning btn-sm"
                                data-toggle="modal"
                                data-target="#editMasterTime<?= $TMtr['id_time']; ?>">
                                <i class="fas fa-edit"></i>
                                Edit
                            </button>
                              |
                            <!-- HAPUS -->
                            <button
                                type="button"
                                class="btn btn-danger btn-sm"
                                onclick="deleteMasterTime(<?= $TMtr['id_time']; ?>)">
                                <i class="fas fa-trash"></i>
                                Delete
                            </button>
                        </td>
                      </tr>

                    <?php endforeach; ?>
                    </tbody>
                </table>

                <?php foreach($TransMaterial as $TMtr) : ?>
                  <div class="modal fade"
                      id="editMasterTime<?= $TMtr['id_time']; ?>">  
                      <div class="modal-dialog">
                          <div class="modal-content">
                              <div class="modal-header bg-warning">
                                  <h4 class="modal-title">
                                      <i class="fas fa-edit"></i>
                                      Edit Master Time
                                  </h4>
                                  <button type="button"
                                          class="close"
                                          data-dismiss="modal">
                                      <span>&times;</span>
                                  </button>
                              </div>

                              <form method="POST">
                                  <div class="modal-body">
                                      <!-- ID -->
                                      <input type="hidden"
                                            name="id_time"
                                            value="<?= $TMtr['id_time']; ?>">
                                      <!-- DATE -->
                                      <div class="form-group">
                                          <label>Date</label>
                                          <input type="date"
                                                name="date"
                                                class="form-control"
                                                value="<?= $TMtr['date']; ?>"
                                                required>
                                      </div>

                                      <!-- START TIME -->
                                      <div class="form-group">
                                          <label>Start Time</label>
                                          <input type="time"
                                                name="time_start"
                                                class="form-control"
                                                value="<?= substr($TMtr['time_start'], 0, 5); ?>"
                                                required>
                                      </div>

                                      <!-- END TIME -->
                                      <div class="form-group">
                                          <label>End Time</label>
                                          <input type="time"
                                                name="time_end"
                                                class="form-control"
                                                value="<?= substr($TMtr['time_end'], 0, 5); ?>"
                                                required>
                                      </div>

                                      <!-- HOUR -->
                                      <div class="form-group">
                                          <label>Hour</label>
                                          <input type="text"
                                                name="hour"
                                                class="form-control"
                                                value="<?= htmlspecialchars($TMtr['hour']); ?>"
                                                required>
                                      </div>

                                      <!-- SHIFT -->
                                      <div class="form-group">
                                          <label>Shift</label>
                                          <select name="shift"
                                                  class="form-control"
                                                  required>
                                              <option value="1"
                                                  <?= $TMtr['shift'] == '1' ? 'selected' : ''; ?>>
                                                  Shift 1
                                              </option>

                                              <option value="2"
                                                  <?= $TMtr['shift'] == '2' ? 'selected' : ''; ?>>
                                                  Shift 2
                                              </option>

                                              <option value="3"
                                                  <?= $TMtr['shift'] == '3' ? 'selected' : ''; ?>>
                                                  Shift 3
                                              </option>
                                          </select>
                                      </div>
                                  </div>

                                  <div class="modal-footer">
                                      <button type="button"
                                              class="btn btn-secondary"
                                              data-dismiss="modal">

                                          <i class="fas fa-times"></i>
                                          Cancel
                                      </button>

                                      <button type="submit"
                                              name="update_master_time"
                                              class="btn btn-warning">

                                          <i class="fas fa-save"></i>
                                          Save Changes
                                      </button>
                                  </div>
                              </form>
                           </div>
                        </div>
                    </div>
                  <?php endforeach; ?>

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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


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
  function deleteMasterTime(id_time){
      Swal.fire({
          title: 'Delete master time?',
          text: 'Deleted data cannot be recovered',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#6c757d',
          confirmButtonText: '<i class="fas fa-trash"></i> Delete',
          cancelButtonText: '<i class="fas fa-times"></i> Cancel'

      }).then((result) => {

          if(result.isConfirmed){
              window.location.href =
                  'master_time.php?delete=' + id_time;
          }
      });
  }
</script>

<script>
document.addEventListener('DOMContentLoaded', function(){

    <?php if($status != ''): ?>

    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: '<?= $status ?>',
        title: '<?= $message ?>',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });

    // SUARA SUCCESS
    <?php if($status == 'success'): ?>
        new Audio('assets/sound/success.mp3').play();
    <?php endif; ?>

    // SUARA ERROR / WARNING
    <?php if($status == 'error' || $status == 'warning'): ?>
        new Audio('assets/sound/error.mp3').play();
    <?php endif; ?>

    <?php endif; ?>

});
</script>

</body>
</html>