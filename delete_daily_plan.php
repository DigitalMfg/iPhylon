<?php

require 'function.php';

if(!isset($_GET['id']))
{
    header("Location: daily_plan.php");
    exit;
}

$id = (int)$_GET['id'];

/*
|--------------------------------------------------------------------------
| Hapus Detail Dulu
|--------------------------------------------------------------------------
*/

mysqli_query($conn,"
    DELETE FROM tbl_daily_plan_detail
    WHERE id_daily_header = '$id'
");

/*
|--------------------------------------------------------------------------
| Hapus Header
|--------------------------------------------------------------------------
*/

mysqli_query($conn,"
    DELETE FROM tbl_daily_plan_header
    WHERE id_daily_header = '$id'
");

/*
|--------------------------------------------------------------------------
| Redirect
|--------------------------------------------------------------------------
*/

header("Location: daily_plan.php?delete=1");
exit;