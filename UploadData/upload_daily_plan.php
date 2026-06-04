<?php
require '../function.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

if(isset($_POST['upload']))
{
    session_start();

    $id_jo_spk   = $_POST['id_jo_spk'];
    $uploaded_by = $_SESSION['username'];

    $file = $_FILES['dailyPlan']['tmp_name'];

    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();



    // =========================
    // HEADER
    // =========================
    $item     = trim($sheet->getCell('B1')->getValue());
    $mesin    = trim($sheet->getCell('B2')->getValue());
    $injector = trim($sheet->getCell('B3')->getValue());
    $line     = trim($sheet->getCell('B4')->getValue());
    $colour   = trim($sheet->getCell('B6')->getValue());

    $tanggalCell = $sheet->getCell('B5')->getValue();

    if(is_numeric($tanggalCell))
    {
        $tanggal = Date::excelToDateTimeObject($tanggalCell)->format('Y-m-d');
    }
    else
    {
        $tanggal = date('Y-m-d', strtotime($tanggalCell));
    }

    mysqli_query($conn,"
        INSERT INTO tbl_daily_plan_header
        (
            id_jo_spk,
            item,
            colour,
            mesin,
            injector,
            line_produksi,
            tanggal_plan,
            uploaded_by,
            created_at
        )
        VALUES
        (
            '$id_jo_spk',
            '$item',
            '$colour',
            '$mesin',
            '$injector',
            '$line',
            '$tanggal',
            '$uploaded_by',
            NOW()
        )
    ");

    $id_header = mysqli_insert_id($conn);

   // =========================
// AMBIL HEADER SIZE
// =========================
$sizeHeader = [];

for($col = 2; $col <= 29; $col++)
{
    $columnLetter = Coordinate::stringFromColumnIndex($col);

    $size = trim($sheet->getCell($columnLetter.'8')->getValue());

    if($size != '' && strtoupper($size) != 'TOTAL')
    {
        $sizeHeader[$columnLetter] = $size;
    }
}

    // =========================
// DETAIL
// =========================
$highestRow = $sheet->getHighestRow();

$currentShift = null;

for($row = 1; $row <= $highestRow; $row++)
{
    $label = strtoupper(trim($sheet->getCell('A'.$row)->getValue()));

    // SHIFT
    if($label == 'SHIF')
    {
        $currentShift = (int)$sheet->getCell('B'.$row)->getValue();
        continue;
    }

    // TYPE
    $validType = ['MOLD','PLAN','ACTUAL','PACKING'];

    if(in_array($label, $validType))
    {
        foreach($sizeHeader as $col => $size)
        {
            $qty = $sheet->getCell($col.$row)->getValue();

            if($qty === null || $qty === '')
            {
                continue;
            }

            mysqli_query($conn,"
                INSERT INTO tbl_daily_plan_detail
                (
                    id_daily_header,
                    shift,
                    type,
                    size,
                    qty
                )
                VALUES
                (
                    '$id_header',
                    '$currentShift',
                    '$label',
                    '$size',
                    '$qty'
                )
            ");
        }
    }
}

    header("Location: ../daily_plan.php?success=1");
    exit;
}
?>