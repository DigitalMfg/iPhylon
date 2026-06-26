<?php
require 'function.php';

// =========================================
// REGISTER USER
// =========================================
if(isset($_POST['register'])){

    $username  = trim($_POST['username']);
    $nik       = trim($_POST['nik']);
    $password  = trim($_POST['password']);
    $authorize = trim($_POST['authorize']);
    $scan_type = trim($_POST['scan_type']);
    $cost_center = trim($_POST['cost_center']);

    // VALIDASI NIK
    $cekUser = mysqli_query($conn,"
        SELECT *
        FROM tbl_user
        WHERE nik='$nik'
    ");

    if(mysqli_num_rows($cekUser) > 0){

        $status  = "error";
        $message = "NIK SUDAH TERDAFTAR";

    } else {

    $insert = mysqli_query($conn,"
        INSERT INTO tbl_user
        (
            username,
            nik,
            password,
            authorize,
            scan_type,
            cost_center
        )
        VALUES
        (
            '$username',
            '$nik',
            '$password',
            '$authorize',
            '$scan_type',
            '$cost_center'
        )
    ");

    if($insert){

        header("Location: master_user.php?success=1");
        exit;

    } else {

        $status  = "error";
        $message = "REGISTER USER GAGAL";

    }
  }
}

// =========================================
// UPDATE USER
// =========================================
if(isset($_POST['update_user'])){

    $id_user     = $_POST['id_user'];
    $username    = $_POST['username'];
    $nik         = $_POST['nik'];
    $password    = $_POST['password'];
    $authorize   = $_POST['authorize'];
    $scan_type   = $_POST['scan_type'];
    $cost_center = $_POST['cost_center'];

    mysqli_query($conn,"
        UPDATE tbl_user
        SET
            username='$username',
            nik='$nik',
            password='$password',
            authorize='$authorize',
            scan_type='$scan_type',
            cost_center='$cost_center'
        WHERE id_user='$id_user'
    ");

    header("Location: master_user.php?success=update");
    exit;
}

// =========================================
// GET DATA USER
// =========================================
$MasterUser = mysqli_query($conn,"
    SELECT *
    FROM tbl_user
    ORDER BY id_user DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>iPhylon | Master User</title>

<link rel="icon" href="assets/images/i.Phylon.png" type="image/x-icon">
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

</head>

<body class="hold-transition sidebar-mini">

<div class="wrapper">

<!-- HEADER -->
<?php include 'header.php'; ?>

<!-- CONTENT -->
<div class="content-wrapper">

<!-- HEADER PAGE -->
<section class="content-header">

<div class="container-fluid">

<?php if(isset($_GET['success'])) : ?>

<div id="successAlert"
class="alert alert-success alert-dismissible fade show">
Register User Berhasil

</div>
<?php endif; ?>

<?php if(isset($status) && $status == "error") : ?>
<div id="errorAlert"
class="alert alert-danger alert-dismissible fade show">
<?= $message; ?>
</div>
<?php endif; ?>

<?php if(isset($_GET['delete'])) : ?>

<div id="deleteAlert"
class="alert alert-danger alert-dismissible fade show">
User berhasil dihapus
</div>

<?php endif; ?>

<div class="row mb-2">

<div class="col-sm-6">
<h1>Master User</h1>
</div>

<div class="col-sm-6">

<ol class="breadcrumb float-sm-right">

<li class="breadcrumb-item">
<a href="index.php">Home</a>
</li>

<li class="breadcrumb-item active">
Master User
</li>

</ol>

</div>

</div>

</div>

</section>

<!-- MAIN CONTENT -->
<section class="content">

<div class="container-fluid">

<!-- FORM -->
<div class="row">

<div class="col-md-12">

<div class="card card-primary">

<div class="card-header">
<h3 class="card-title">
Register New User
</h3>
</div>

<div class="card-body">
  <form method="POST">
    <div class="row">
      <div class="col-md-4">
        <div class="form-group">
          <label>Nama</label>
          <input type="text"
          name="username"
          class="form-control"
          required>
        </div>
      </div>

    <div class="col-md-4">
      <div class="form-group">
        <label>NIK</label>
        <input type="text"
        name="nik"
        class="form-control" required>
      </div>
    </div>

    <div class="col-md-4">
      <div class="form-group">
        <label>Password</label>
        <input type="password"
        name="password"
        class="form-control" required>
      </div>
    </div>

    <div class="col-md-4">
      <div class="form-group">
        <label>Authorize</label>
          <select name="authorize"
          class="form-control" required>

          <option value="">
          Select Authorize
          </option>

          <option value="Admin">
          Admin
          </option>

          <option value="User">
          User
          </option>

          </select>

      </div>

    </div>

    <div class="col-md-4">
      <div class="form-group">
        <label>Scan Type</label>
        <select name="scan_type"
          class="form-control"
          required>

          <option value="">
          Select Scan Type
          </option>

          <option value="OUT_PACKING">
          OUT PACKING
          </option>

          <option value="IN_SM">
          IN SUPERMARKET
          </option>

          <option value="OUT_SM">
          OUT SUPERMARKET
          </option>
        </select>
      </div>
    </div>

    <div class="col-md-4">
      <div class="form-group">
        <label>Line</label>
        <select name="cost_center"
          class="form-control"
          required>

          <option value="">
          Select Line
          </option>

          <option value="Line 1">
          Line 1
          </option>
          <option value="Line 2">
          Line 2
          </option>
          <option value="Line 3">
          Line 3
          </option>
          <option value="Line 4">
          Line 4
          </option>
          <option value="Line 5">
          Line 5
          </option>
          <option value="Line 6">
            Line 6
          </option>
          <option value="Line 7">
            Line 7
          </option>
          <option value="Line 8">
            Line 8
          </option>
          <option value="Supermarket">
            Supermarket
          </option>
           <option value="Planning">
            Planning
          </option>
          
        </select>
      </div>
    </div>

    <div class="col-md-12">
      <button type="submit"
      name="register"
      class="btn btn-primary">
      <i class="fas fa-save"></i>
      Save

      </button>
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
Data User
</h3>
</div>

<div class="card-body">
<table id="example1"
class="table table-bordered table-striped">
<thead>

<tr>

<th width="50">No</th>
<th width="250">Nama</th>
<th>NIK</th>
<th>Authorize</th>
<th>Scan Type</th>
<th>Line</th>
<th width="50">Action</th>
</tr>
</thead>

<tbody>
<?php $i = 1; ?>
<?php foreach($MasterUser as $MU) : ?>

<tr>
    <td>
        <?= $i++; ?>
    </td>
    <td>
        <?= $MU['username']; ?>
    </td>
    <td>
        <?= $MU['nik']; ?>
    </td>
    <td>
        <?= $MU['authorize']; ?>
    </td>
    <td>
        <?= $MU['scan_type']; ?>
    </td>
    <td>
        <?= $MU['cost_center']; ?>
    </td>
    <td width="80">

        <button
            type="button"
            class="btn btn-warning btn-sm"
            data-toggle="modal"
            data-target="#editUser<?= $MU['id_user']; ?>">
            <i class="fas fa-edit"></i>
        </button>

        <a
            href="delete_user.php?id=<?= $MU['id_user']; ?>"
            class="btn btn-danger btn-sm"
            onclick="return confirm('Yakin hapus user ini?')">
            <i class="fas fa-trash"></i>
        </a>
    </td>
</tr>

<!-- MODAL EDIT USER -->
<div
    class="modal fade"
    id="editUser<?= $MU['id_user']; ?>"
    tabindex="-1">

    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header bg-warning">

                    <h4 class="modal-title">
                        Edit User
                    </h4>

                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <input
                        type="hidden"
                        name="id_user"
                        value="<?= $MU['id_user']; ?>">

                    <div class="form-group">

                        <label>
                            Username
                        </label>

                        <input
                            type="text"
                            name="username"
                            class="form-control"
                            value="<?= $MU['username']; ?>"
                            required>

                    </div>

                    <div class="form-group">
                        <label>
                            NIK
                        </label>

                        <input
                            type="text"
                            name="nik"
                            class="form-control"
                            value="<?= $MU['nik']; ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label>
                            Password
                        </label>

                        <input
                            type="text"
                            name="password"
                            class="form-control"
                            value="<?= $MU['password']; ?>"
                            required>

                    </div>

                    <div class="form-group">
                        <label>
                            Authorize
                        </label>

                        <select
                            name="authorize"
                            class="form-control"
                            required>

                            <option
                                value="Admin"
                                <?= ($MU['authorize'] == 'Admin') ? 'selected' : ''; ?>>
                                Admin
                            </option>

                            <option
                                value="User"
                                <?= ($MU['authorize'] == 'User') ? 'selected' : ''; ?>>
                                User
                            </option>
                        </select>
                    </div>

                    <div class="form-group">

                        <label>
                            Scan Type
                        </label>

                        <select
                            name="scan_type"
                            class="form-control"
                            required>

                            <option
                                value="OUT_PACKING"
                                <?= ($MU['scan_type'] == 'OUT_PACKING') ? 'selected' : ''; ?>>
                                OUT PACKING
                            </option>

                            <option
                                value="IN_SM"
                                <?= ($MU['scan_type'] == 'IN_SM') ? 'selected' : ''; ?>>
                                IN SUPERMARKET
                            </option>

                            <option
                                value="OUT_SM"
                                <?= ($MU['scan_type'] == 'OUT_SM') ? 'selected' : ''; ?>>
                                OUT SUPERMARKET
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>
                            Line
                        </label>

                        <select
                            name="cost_center"
                            class="form-control"
                            required>

                            <option
                                value="Line 1"
                                <?= ($MU['cost_center'] == 'Line 1') ? 'selected' : ''; ?>>
                                Line 1
                            </option>

                            <option
                                value="Line 2"
                                <?= ($MU['cost_center'] == 'Line 2') ? 'selected' : ''; ?>>
                                Line 2
                            </option>

                            <option
                                value="Line 3"
                                <?= ($MU['cost_center'] == 'Line 3') ? 'selected' : ''; ?>>
                                Line 3
                            </option>

                            <option
                                value="Line 4"
                                <?= ($MU['cost_center'] == 'Line 4') ? 'selected' : ''; ?>>
                                Line 4
                            </option>

                            <option
                                value="Line 5"
                                <?= ($MU['cost_center'] == 'Line 5') ? 'selected' : ''; ?>>

                                Line 5
                            </option>

                            <option
                                value="Line 6"
                                <?= ($MU['cost_center'] == 'Line 6') ? 'selected' : ''; ?>>
                                Line 6
                            </option>

                            <option
                                value="Line 7"
                                <?= ($MU['cost_center'] == 'Line 7') ? 'selected' : ''; ?>>
                                Line 7
                            </option>

                            <option
                                value="Line 8"
                                <?= ($MU['cost_center'] == 'Line 8') ? 'selected' : ''; ?>>
                                Line 8
                            </option>
                            <option
                                value="Supermarket"
                                <?= ($MU['cost_center'] == 'Supermarket') ? 'selected' : ''; ?>>
                                Supermarket
                            </option>
                            <option
                                value="Planning"
                                <?= ($MU['cost_center'] == 'Planning') ? 'selected' : ''; ?>>
                                Planning    
                            </option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">

                        Close

                    </button>

                    <button
                        type="submit"
                        name="update_user"
                        class="btn btn-warning">

                        Update User

                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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

<!-- AdminLTE -->
<script src="dist/js/adminlte.min.js"></script>

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

<!-- AUTO HIDE ALERT -->
<script>
setTimeout(function(){

    $('#successAlert').fadeOut('slow');
    $('#errorAlert').fadeOut('slow');
    $('#deleteAlert').fadeOut('slow');

}, 2000);
</script>

<!-- CLEAN URL -->
<script>
if(
window.location.href.indexOf("?success=1") > -1 ||
window.location.href.indexOf("?delete=1") > -1){

    window.history.replaceState({}, document.title, window.location.pathname);

}
</script>

</body>
</html>