<?php
// session_start();
include 'function.php';

if (isset($_POST["login"])) {

    $nik = trim($_POST['nik']);
    $password = trim($_POST['password']);

    $login = mysqli_query($conn,
        "SELECT * FROM tbl_user 
         WHERE nik='$nik' 
         AND password='$password'"
    );

    if(!$login){
        die(mysqli_error($conn));
    }

    $cek = mysqli_num_rows($login);

    if($cek > 0){

        $data = mysqli_fetch_assoc($login);

        $_SESSION["login"]        = true;

        $_SESSION['id_user']      = $data['id_user'];
        $_SESSION['nik']          = $data['nik'];
        $_SESSION['username']     = $data['username'];

        $_SESSION['authorize']    = $data['authorize'];
        $_SESSION['scan_type']    = $data['scan_type'];
        $_SESSION['cost_center']  = $data['cost_center'];

        header("location:index.php");
        exit;

    } else {

        $error = true;

    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>iRubber | Log in</title>
  
  <link rel="icon" href="assets/images/i.Phylon.png" type="image/x-icon">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<!-- Error -->
<?php if (isset($error)) :?>
  <dialog id="myDialog" class="alert alert-danger alert-dismissible"> 
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><span class="fas fa-envelope"></span> Oops...!</h5>
    <p>The username and password you entered is wrong, Please Try again...</p>
  </dialog>
  <?php unset($error);?>
<?php endif; ?>
<!-- End Error -->
<body class="hold-transition login-page">
<div class="login-box">
  <!-- /.login-logo -->
  <div class="card card-outline card-primary">
     
 
    <div class="card-header text-center">
      <a href="#" class="h1"><b>i</b>Phylon</a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Sign in to start your session</p>

      <form method="post" enctype="multipart/form-data" >
        <div class="form-group mb-1">
          <label class="form-label" >NIK</label>
          <input class="form-control " placeholder="NIK" id="nik" maxlength="7" type="text" name="nik">
          <span class="text-danger" style="max-width: 300px; display: inline-block; overflow-wrap: break-word;"></span>
        </div>

        <div class="form-group mb-2">
          <label class="form-label" for="password">Password</label>
          <div class="input-group ">
            <input class="form-control" placeholder="Password" id="password" autocomplete="off" maxlength="20" type="password" name="password">
          </div>
          <span class="text-danger" style="max-width: 300px; display: inline-block; overflow-wrap: break-word;"></span>
        </div>
        <div class="row">
          <div class="col-12">
            <button  class="btn btn-primary btn-block" type="submit" name="login" >Sign In</button>
          </div>
        </div>
      </form>

      

      
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<!-- Notification Alert -->
<script type="text/javascript">
    function showDialog(dialog) {
      dialog.show();
      closeDialog(dialog);
    }

    function closeDialog(dialog) {
      setTimeout(function () {
        dialog.close();
      }, 2500);
    }

    function init() {
      setTimeout(function () {
        const dialog = document.getElementById("myDialog");
        showDialog(dialog);
      }, 100);
    }

    init();
</script> 
</body>
</html>
