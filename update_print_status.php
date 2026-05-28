<?php

require 'function.php';

$id = $_POST['id'];

mysqli_query($conn,"
    UPDATE tbl_master_barcode
    SET status_print = '1'
    WHERE id_barcode = '$id'
");

echo json_encode([
    'success' => true
]);