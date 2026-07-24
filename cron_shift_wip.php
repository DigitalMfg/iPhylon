<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

require_once 'function.php';
require_once 'shift_helper.php';

/*
|--------------------------------------------------------------------------
| SHIFT AKTIF
|--------------------------------------------------------------------------
*/

$current = getCurrentShift($conn);

$currentDate  = $current['date'];
$currentShift = $current['shift'];

$previous = getPreviousShift($currentDate,$currentShift);

$previousDate  = $previous['date'];
$previousShift = $previous['shift'];

/*
|--------------------------------------------------------------------------
| LIST LINE
|--------------------------------------------------------------------------
*/

$listLine = mysqli_query($conn,"
SELECT DISTINCT line
FROM tbl_master_barcode
WHERE line IS NOT NULL
AND line <> ''
ORDER BY CAST(line AS UNSIGNED)
");

while($line = mysqli_fetch_assoc($listLine))
{

    $lineNo = $line['line'];

    /*
    |--------------------------------------------------------------------------
    | LAST WIP SHIFT SEBELUMNYA
    |--------------------------------------------------------------------------
    */

    $sqlLast = mysqli_query($conn,"
        SELECT
            production_wip,
            sm_wip
        FROM tbl_shift_wip
        WHERE tanggal='$previousDate'
        AND shift='$previousShift'
        AND line='$lineNo'
        ORDER BY id DESC
        LIMIT 1
    ");

    if(mysqli_num_rows($sqlLast))
    {
        $last = mysqli_fetch_assoc($sqlLast);

        $productionLast = $last['production_wip'];
        $smLast         = $last['sm_wip'];
    }
    else
    {
        $productionLast = 0;
        $smLast         = 0;
    }

    /*
    |--------------------------------------------------------------------------
    | SCAN OUT PACKING
    |--------------------------------------------------------------------------
    */

    $sqlPacking = mysqli_query($conn,"
        SELECT COALESCE(SUM(mb.qty),0) total
        FROM tbl_transaction_scan ts
        INNER JOIN tbl_master_barcode mb
            ON ts.qr_code = mb.qr_code
        WHERE ts.type_scan='OUT_PACKING'
        AND ts.shift='$currentShift'
        AND DATE(ts.date_scan)='$currentDate'
        AND mb.line='$lineNo'
    ");

    $productionScanOut = mysqli_fetch_assoc($sqlPacking)['total'];

    /*
    |--------------------------------------------------------------------------
    | SCAN IN SUPERMARKET
    |--------------------------------------------------------------------------
    */

    $sqlInSM = mysqli_query($conn,"
        SELECT COALESCE(SUM(mb.qty),0) total
        FROM tbl_transaction_scan ts
        INNER JOIN tbl_master_barcode mb
            ON ts.qr_code = mb.qr_code
        WHERE ts.type_scan='IN_SM'
        AND ts.shift='$currentShift'
        AND DATE(ts.date_scan)='$currentDate'
        AND mb.line='$lineNo'
    ");

    $smScanIn = mysqli_fetch_assoc($sqlInSM)['total'];

    /*
    |--------------------------------------------------------------------------
    | SCAN OUT SUPERMARKET
    |--------------------------------------------------------------------------
    */

    $sqlOutSM = mysqli_query($conn,"
        SELECT COALESCE(SUM(mb.qty),0) total
        FROM tbl_transaction_scan ts
        INNER JOIN tbl_master_barcode mb
            ON ts.qr_code = mb.qr_code
        WHERE ts.type_scan='OUT_SM'
        AND ts.shift='$currentShift'
        AND DATE(ts.date_scan)='$currentDate'
        AND mb.line='$lineNo'
    ");

    $smScanOut = mysqli_fetch_assoc($sqlOutSM)['total'];

    /*
    |--------------------------------------------------------------------------
    | HITUNG WIP
    |--------------------------------------------------------------------------
    */

    $productionWIP = $productionLast + $productionScanOut - $smScanIn;

    $smWIP = $smLast + $smScanIn - $smScanOut;

    mysqli_query($conn,"
        INSERT INTO tbl_shift_wip
        (
            tanggal,
            shift,
            line,

            production_last_wip,
            production_scan_out,
            production_wip,

            sm_last_wip,
            sm_scan_in,
            sm_scan_out,
            sm_wip,

            status
        )
        VALUES
        (
            '$currentDate',
            '$currentShift',
            '$lineNo',

            '$productionLast',
            '$productionScanOut',
            '$productionWIP',

            '$smLast',
            '$smScanIn',
            '$smScanOut',
            '$smWIP',

            'OPEN'
        )

        ON DUPLICATE KEY UPDATE

        production_last_wip = VALUES(production_last_wip),
        production_scan_out = VALUES(production_scan_out),
        production_wip      = VALUES(production_wip),

        sm_last_wip         = VALUES(sm_last_wip),
        sm_scan_in          = VALUES(sm_scan_in),
        sm_scan_out         = VALUES(sm_scan_out),
        sm_wip              = VALUES(sm_wip);
        ");

}