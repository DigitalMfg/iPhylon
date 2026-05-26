<?php

require 'function.php';

$id = $_POST['id'];

mysqli_query($conn,"
    UPDATE tbl_master_barcode
    SET status_print = 'YES'
    WHERE id_barcode = '$id'
");

echo json_encode([
    'success' => true
]);