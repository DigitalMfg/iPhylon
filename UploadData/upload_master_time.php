<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../function.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if(isset($_POST['upload'])){

    $start_date = $_POST['start_date'];
    $end_date   = $_POST['end_date'];

    $file = $_FILES['masterTime']['tmp_name'];

    // =========================================
    // LOAD EXCEL
    // =========================================
    $spreadsheet = IOFactory::load($file);

    $sheet = $spreadsheet->getActiveSheet()->toArray();

    // =========================================
    // VALIDASI HEADER TEMPLATE
    // =========================================
    $header = array_map('strtolower', $sheet[0]);

    $template = [
        'time_start',
        'time_end',
        'hour',
        'shift'
    ];

    if($header != $template){

        $_SESSION['status']  = 'error';
        $_SESSION['message'] = 'Master Time template format is invalid.';

        header('Location: ../master_time.php');
        exit;
    }

    // =========================================
    // HAPUS HEADER EXCEL
    // =========================================
    unset($sheet[0]);

    // =========================================
    // LOOPING DATE
    // =========================================
    $start = strtotime($start_date);
    $end   = strtotime($end_date);

    for($date = $start; $date <= $end; $date += 86400){

        $tanggal = date('Y-m-d', $date);

        foreach($sheet as $row){

            // =========================================
            // SKIP ROW KOSONG
            // =========================================
            if(
                empty($row[0]) &&
                empty($row[1]) &&
                empty($row[2]) &&
                empty($row[3])
            ){
                continue;
            }

            // =========================================
            // AMBIL DATA EXCEL
            // =========================================
            $time_start = date('H:i:s', strtotime($row[0]));
            $time_end   = date('H:i:s', strtotime($row[1]));
            $hour       = trim($row[2]);
            $shift      = trim($row[3]);

            // =========================================
            // VALIDASI DATA KOSONG
            // =========================================
            if(
                empty($time_start) ||
                empty($time_end) ||
                empty($hour) ||
                empty($shift)
            ){
                continue;
            }

            // =========================================
            // CEK DUPLICATE
            // =========================================
            $cek = mysqli_query($conn,"
                SELECT *
                FROM tbl_master_time
                WHERE
                    date='$tanggal'
                    AND hour='$hour'
                    AND shift='$shift'
            ");

            if(mysqli_num_rows($cek) > 0){
                continue;
            }

            // =========================================
            // INSERT DATABASE
            // =========================================
            mysqli_query($conn,"
                INSERT INTO tbl_master_time
                (
                    date,
                    time_start,
                    time_end,
                    hour,
                    shift
                )
                VALUES
                (
                    '$tanggal',
                    '$time_start',
                    '$time_end',
                    '$hour',
                    '$shift'
                )
            ");

        }

    }

    // =========================================
    // REDIRECT SUCCESS
    // =========================================
    $_SESSION['status']  = 'success';
    $_SESSION['message'] = 'Master Time has been successfully uploaded.';

    header('Location: ../master_time.php');
    exit;

}
?>