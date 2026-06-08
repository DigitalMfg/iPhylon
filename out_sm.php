<?php
session_start();

$nama_user = $_SESSION['username'];
$nik_user  = $_SESSION['nik'];

$conn = mysqli_connect("localhost:3306", "root", "", "db_iphylon");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

date_default_timezone_set("Asia/Jakarta");

$success = false;
$error   = false;
$message = "";
$status  = "";

// =========================================
// AMBIL MESSAGE DARI SESSION
// =========================================
if(isset($_SESSION['message'])){

    $message = $_SESSION['message'];
    $status  = $_SESSION['status'];

    unset($_SESSION['message']);
    unset($_SESSION['status']);
}


if (isset($_POST['outSupermarket'])) {

    $qr_code     = trim($_POST['qr_code']);
    $type_scan   = $_POST['type_scan'];
    $nik         = $_POST['nik'];
    $username    = $_POST['username'];
    $cost_center = $_POST['cost_center'];

    $date_transaction = date('Y-m-d H:i:s');
    $date_scan        = date('Y-m-d');
    $current_time     = date('H:i:s');

    // =========================================
    // CEK HOUR & SHIFT
    // =========================================
    $getTime = mysqli_query($conn,"
        SELECT *
        FROM tbl_master_time
        WHERE '$current_time'
        BETWEEN time_start AND time_end
    ");

    $dataTime = mysqli_fetch_assoc($getTime);

    if ($dataTime) {

        $hour_scan = $dataTime['hour'];
        $shift     = $dataTime['shift'];

    } else {

        $hour_scan = '';
        $shift     = '';
    }

    // =========================================
    // CEK QR ADA DI MASTER BARCODE
    // =========================================
    $cekBarcode = mysqli_query($conn,"
        SELECT *
        FROM tbl_master_barcode
        WHERE qr_code='$qr_code'
    ");

    if(mysqli_num_rows($cekBarcode) > 0){

        $dataBarcode = mysqli_fetch_assoc($cekBarcode);

        // =========================================
        // VALIDASI STATUS PRINT
        // =========================================
        if(strtoupper($dataBarcode['status_print']) == 'NO'){

            $_SESSION['status']  = 'error';
            $_SESSION['message'] = 'QR CODE BELUM DIPRINT';

            header("Location: out_sm.php");
            exit;
        }

        // =========================================
        // CEK SUDAH OUT PACKING ATAU BELUM
        // =========================================
        $cekPacking = mysqli_query($conn,"
            SELECT *
            FROM tbl_transaction_scan
            WHERE qr_code='$qr_code'
            AND type_scan='OUT_PACKING'
        ");

        if(mysqli_num_rows($cekPacking) == 0){

            $_SESSION['status']  = 'error';
            $_SESSION['message'] = 'QR BELUM SCAN OUT PACKING';

            header("Location: out_sm.php");
            exit;
        }

        // =========================================
        // CEK DOUBLE SCAN
        // =========================================
        $cekDouble = mysqli_query($conn,"
            SELECT *
            FROM tbl_transaction_scan
            WHERE qr_code='$qr_code'
            AND type_scan='$type_scan'
        ");

        if(mysqli_num_rows($cekDouble) > 0){

            $_SESSION['status']  = 'warning';
            $_SESSION['message'] = 'QR CODE SUDAH DISCAN';

            header("Location: out_sm.php");
            exit;
        }

        // =========================================
        // INSERT TRANSACTION
        // =========================================
        $insert = mysqli_query($conn,"
            INSERT INTO tbl_transaction_scan
            (
                qr_code,
                date_transaction,
                type_scan,
                hour_scan,
                shift,
                nik,
                username,
                cost_center,
                date_scan
            )
            VALUES
            (
                '$qr_code',
                '$date_transaction',
                '$type_scan',
                '$hour_scan',
                '$shift',
                '$nik',
                '$username',
                '$cost_center',
                '$date_scan'
            )
        ");

        if($insert){

            $_SESSION['status']  = 'success';
            $_SESSION['message'] = 'SCAN BERHASIL';

        } else {

            $_SESSION['status']  = 'error';
            $_SESSION['message'] = 'GAGAL INSERT DATA';
        }

        header("Location: out_sm.php");
        exit;

    } else {

        $_SESSION['status']  = 'error';
        $_SESSION['message'] = 'QR CODE TIDAK DITEMUKAN';

        header("Location: out_sm.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>iPhylon | Scan Out</title>
  <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Bootstrap Color Picker -->
  <link rel="stylesheet" href="plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <!-- Bootstrap4 Duallistbox -->
  <link rel="stylesheet" href="plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
  <!-- BS Stepper -->
  <link rel="stylesheet" href="plugins/bs-stepper/css/bs-stepper.min.css">
  <!-- dropzonejs -->
  <link rel="stylesheet" href="plugins/dropzone/min/dropzone.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
   <!-- DataTables -->
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  
  <!-- Header -->
    <?php include 'header.php';?>
  <!-- End Header -->
  

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>SUPERMARKET IP<Output></Output></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Home</a></li>
              <li class="breadcrumb-item active">Scan Out SM</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
      <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title"></h3>

            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
              <button type="button" class="btn btn-tool" data-card-widget="remove">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
          <!-- /.card-header -->
          <form class="row g-3 needs-validation" action="" method="post" enctype="multipart/form-data">
          <div class="card-body">
            <div class="form">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <?php $UserAuto = mysqli_query($conn,"SELECT * FROM tbl_user WHERE nik='". $_SESSION["nik"] ."'"); ?>
                    <?php foreach ($UserAuto as $row) :?>
                    <label for="inputText" class="col-sm-6 col-form-label">SCAN OUT SUPERMARKET</label>
                      <div class="col-sm-12">
                        <input type="text" class="form-control" id="qr_code" name="qr_code" placeholder="Scan here" required="" autocomplete="off" autofocus="" maxlength="36" minlength="12">
                        <input type="text" class="form-control" id="type_scan" name="type_scan" value="OUT_SM" hidden>
                        <input type="text" class="form-control" id="nik" name="nik" value="<?php echo $row['nik']?>" hidden>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo $row['username']?>" hidden>
                        <input type="text" class="form-control" id="cost_center" name="cost_center" value="<?php echo $row['cost_center']?>" hidden>
                      </div>
                      <?php endforeach;?>

                      <?php $tanggal = date('Y-m-d'); ?>
                    <?php $hr_scan = date('H:i:s'); ?>
                    <?php 
                    // $manipul = mktime(23,30);
                    // $hr_scan = date('H:i', $manipul); 
                    ?>
                    
                    
                    
                  </div>
                </div>
              </div>  
              <button type="submit" name="outSupermarket" hidden></button>
              
            </form>
            </div>
          </div>
          

        <!-- /.row -->
        
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
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
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Select2 -->
<script src="plugins/select2/js/select2.full.min.js"></script>
<!-- Bootstrap4 Duallistbox -->
<script src="plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
<!-- InputMask -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/inputmask/jquery.inputmask.min.js"></script>
<!-- date-range-picker -->
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<!-- bootstrap color picker -->
<script src="plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- BS-Stepper -->
<script src="plugins/bs-stepper/js/bs-stepper.min.js"></script>
<!-- dropzonejs -->
<script src="plugins/dropzone/min/dropzone.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<!-- <script src="dist/js/demo.js"></script> -->
<!-- Page specific script -->
<script>
  $(function () {
    //Initialize Select2 Elements
    $('.select2').select2()

    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })

    //Datemask dd/mm/yyyy
    $('#datemask').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' })
    //Datemask2 mm/dd/yyyy
    $('#datemask2').inputmask('mm/dd/yyyy', { 'placeholder': 'mm/dd/yyyy' })
    //Money Euro
    $('[data-mask]').inputmask()

    //Date picker
    $('#reservationdate').datetimepicker({
        format: 'L'
    });

    //Date and time picker
    $('#reservationdatetime').datetimepicker({ icons: { time: 'far fa-clock' } });

    //Date range picker
    $('#reservation').daterangepicker()
    //Date range picker with time picker
    $('#reservationtime').daterangepicker({
      timePicker: true,
      timePickerIncrement: 30,
      locale: {
        format: 'MM/DD/YYYY hh:mm A'
      }
    })
    //Date range as a button
    $('#daterange-btn').daterangepicker(
      {
        ranges   : {
          'Today'       : [moment(), moment()],
          'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month'  : [moment().startOf('month'), moment().endOf('month')],
          'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'days'),
        endDate  : moment()
      },
      function (start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
      }
    )

    //Timepicker
    $('#timepicker').datetimepicker({
      format: 'LT'
    })

    //Bootstrap Duallistbox
    $('.duallistbox').bootstrapDualListbox()

    //Colorpicker
    $('.my-colorpicker1').colorpicker()
    //color picker with addon
    $('.my-colorpicker2').colorpicker()

    $('.my-colorpicker2').on('colorpickerChange', function(event) {
      $('.my-colorpicker2 .fa-square').css('color', event.color.toString());
    })
  })
  // BS-Stepper Init
  document.addEventListener('DOMContentLoaded', function () {
    window.stepper = new Stepper(document.querySelector('.bs-stepper'))
  })

  // DropzoneJS Demo Code Start
  Dropzone.autoDiscover = false

  // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
  var previewNode = document.querySelector("#template")
  previewNode.id = ""
  var previewTemplate = previewNode.parentNode.innerHTML
  previewNode.parentNode.removeChild(previewNode)

  var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
    url: "/target-url", // Set the url
    thumbnailWidth: 80,
    thumbnailHeight: 80,
    parallelUploads: 20,
    previewTemplate: previewTemplate,
    autoQueue: false, // Make sure the files aren't queued until manually added
    previewsContainer: "#previews", // Define the container to display the previews
    clickable: ".fileinput-button" // Define the element that should be used as click trigger to select files.
  })

  myDropzone.on("addedfile", function(file) {
    // Hookup the start button
    file.previewElement.querySelector(".start").onclick = function() { myDropzone.enqueueFile(file) }
  })

  // Update the total progress bar
  myDropzone.on("totaluploadprogress", function(progress) {
    document.querySelector("#total-progress .progress-bar").style.width = progress + "%"
  })

  myDropzone.on("sending", function(file) {
    // Show the total progress bar when upload starts
    document.querySelector("#total-progress").style.opacity = "1"
    // And disable the start button
    file.previewElement.querySelector(".start").setAttribute("disabled", "disabled")
  })

  // Hide the total progress bar when nothing's uploading anymore
  myDropzone.on("queuecomplete", function(progress) {
    document.querySelector("#total-progress").style.opacity = "0"
  })

  // Setup the buttons for all transfers
  // The "add files" button doesn't need to be setup because the config
  // `clickable` has already been specified.
  document.querySelector("#actions .start").onclick = function() {
    myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED))
  }
  document.querySelector("#actions .cancel").onclick = function() {
    myDropzone.removeAllFiles(true)
  }
  // DropzoneJS Demo Code End
</script>

<!-- DataTables  & Plugins -->
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

<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
</script>



<?php if(!empty($status)): ?>

<script>

document.addEventListener('DOMContentLoaded', function(){

    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: '<?= $status ?>',
        title: '<?= $message ?>',
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true
    });

    let qr = document.getElementById('qr_code');

    if(qr){
        qr.value = '';
        qr.focus();
    }

    // SUARA SUCCESS
    <?php if($status == 'success'): ?>
        new Audio('assets/sound/success.mp3').play();
    <?php endif; ?>

    // SUARA ERROR / WARNING
    <?php if($status == 'error' || $status == 'warning'): ?>
        new Audio('assets/sound/error.mp3').play();
    <?php endif; ?>

});

</script>

<?php endif; ?>

</body>
</html>
