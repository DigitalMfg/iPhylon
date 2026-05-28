<?php

require 'function.php';

if(isset($_POST['id']))
{
    $id = $_POST['id'];

    mysqli_query($conn, "
        UPDATE tbl_master_barcode
        SET status_print = status_print + 1
        WHERE id_barcode = '$id'
    ");

    echo 'success';
}