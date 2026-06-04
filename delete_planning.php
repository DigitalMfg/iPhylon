<?php
require 'function.php';

$id_jo_spk = $_GET['id'];


// =========================
// AMBIL FILE EXCEL
// =========================

$getFile = mysqli_query($conn,"
    SELECT file_excel
    FROM tbl_jo_spk
    WHERE id_jo_spk = '$id_jo_spk'
");

$fileData = mysqli_fetch_assoc($getFile);

$file_excel = $fileData['file_excel'];


// =========================
// AMBIL SEMUA DETAIL
// =========================

$getDetail = mysqli_query($conn,"
    SELECT id_detail
    FROM tbl_spk_detail
    WHERE id_jo_spk = '$id_jo_spk'
");

while($detail = mysqli_fetch_assoc($getDetail)){

    $id_detail = $detail['id_detail'];

    // =========================
    // DELETE SIZE
    // =========================

    mysqli_query($conn,"
        DELETE FROM tbl_spk_size_qty
        WHERE id_detail = '$id_detail'
    ");

}


// =========================
// DELETE DETAIL
// =========================

mysqli_query($conn,"
    DELETE FROM tbl_spk_detail
    WHERE id_jo_spk = '$id_jo_spk'
");


// =========================
// DELETE HEADER
// =========================

mysqli_query($conn,"
    DELETE FROM tbl_jo_spk
    WHERE id_jo_spk = '$id_jo_spk'
");


// =========================
// DELETE FILE EXCEL
// =========================

$filePath = 'FilePlanning/' . $file_excel;

if(file_exists($filePath)){

    unlink($filePath);

}


// =========================
// REDIRECT
// =========================
$_SESSION['delete'] = 'SPK Planning berhasil dihapus';
    header('Location: master_planning.php');
    exit;

?>