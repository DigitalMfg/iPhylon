<?php
require_once 'function.php';

/*
|--------------------------------------------------------------------------
| SHIFT AKTIF
|--------------------------------------------------------------------------
*/

$shiftNow = 1;

$getShift = mysqli_query($conn,"
SELECT shift
FROM tbl_master_time
WHERE CURDATE()=date
AND CURTIME() BETWEEN time_start AND time_end
LIMIT 1
");

if(mysqli_num_rows($getShift)>0){
    $shiftNow = mysqli_fetch_assoc($getShift)['shift'];
}


/*
|--------------------------------------------------------------------------
| SHIFT SEBELUMNYA
|--------------------------------------------------------------------------
*/

$today = date('Y-m-d');

if($shiftNow==1){

    $lastShift=3;

    $lastDate=date('Y-m-d',strtotime('-1 day'));

}else{

    $lastShift=$shiftNow-1;

    $lastDate=$today;

}


/*
|--------------------------------------------------------------------------
| AUTO CLOSE SHIFT
|--------------------------------------------------------------------------
*/

mysqli_query($conn,"
UPDATE tbl_shift_wip

SET status='CLOSE'

WHERE tanggal='$lastDate'
AND shift='$lastShift'
AND status='OPEN'
");

?>