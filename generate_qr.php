<?php
require 'function.php';

session_start();

$updated_by = $_SESSION['username'];

$id = $_GET['id'];
$check = mysqli_query($conn,"
    SELECT *
    FROM tbl_master_barcode
    WHERE no_jo = (
        SELECT no_jo
        FROM tbl_jo_spk
        WHERE id_jo_spk = '$id'
    )
");

if(mysqli_num_rows($check) > 0){

    header("Location: master_planning.php?already=1");
    exit;
}

$max_bundle = 12;

// QUERY DATA
$data = mysqli_query($conn,"
    SELECT 
        j.no_jo,
        j.line_produksi,
        j.item,

        d.*,

        s.id_size_qty,
        s.size,
        s.qty

    FROM tbl_spk_detail d

    JOIN tbl_jo_spk j
    ON d.id_jo_spk = j.id_jo_spk

    JOIN tbl_spk_size_qty s
    ON d.id_detail = s.id_detail

    WHERE d.id_jo_spk = '$id'
");

// CEK QUERY
if(!$data){
    die(mysqli_error($conn));
}

while($row = mysqli_fetch_assoc($data)){
    $qty = $row['qty'];
    while($qty > 0){

        // SPLIT QTY
        if($qty >= $max_bundle){
            $bundle_qty = $max_bundle;
        } else {
            $bundle_qty = $qty;
        }

        // GENERATE QR
        $qr_code = uniqid() . '-' . substr(md5(rand()),0,8);

        // INSERT
        $insert = mysqli_query($conn,"
            INSERT INTO tbl_master_barcode (

                id_size_qty,
                qr_code,
                bucket,
                no_jo,
                po,
                po_item,
                style,
                gender,
                colour,
                item,
                line,
                size,
                qty,
                status_scan,
                status_print,
                last_update,
                updated_by

            ) VALUES (

                '".$row['id_size_qty']."',
                '$qr_code',
                '".$row['bucket']."',
                '".$row['no_jo']."',
                '".$row['po']."',
                '".$row['po_item']."',
                '".$row['style']."',
                '".$row['gender']."',
                '".$row['colour']."',
                '".$row['item']."',
                '".$row['line_produksi']."',
                '".$row['size']."',
                '$bundle_qty',
                'NO',
                'NO',
                NOW(),
                '$updated_by'

            )
        ");

        // CEK ERROR INSERT
        if(!$insert){

            die(mysqli_error($conn));

        }

        // KURANGI QTY
        $qty -= $bundle_qty;
    }
}

header("Location: master_planning.php?generate=success");
exit;
?>