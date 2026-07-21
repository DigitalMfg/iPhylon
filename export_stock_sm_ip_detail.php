<?php
require 'function.php';

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$type    = $_GET['type'] ?? '';
$item    = $_GET['item'] ?? '';
$colour  = $_GET['colour'] ?? '';
$bucket  = $_GET['bucket'] ?? '';

$isTotal = ($item == '');

$sizes = [
    '1','1T','2','2T','3','3T',
    '4','4T','5','5T','6','6T',
    '7','7T','8','8T','9','9T',
    '10','10T','11','11T','12','12T',
    '13','13T','14','14T','15'
];

// =====================================================
// QUERY
// =====================================================

if($type == 'STOCK'){

    $where = '';

    if(!$isTotal){
        $where = "WHERE
            p.item='$item'
            AND p.colour='$colour'
            AND p.bucket='$bucket'";
    }

    $sql = mysqli_query($conn,"

        SELECT
            p.style,
            p.item,
            p.gender,
            p.colour,
            p.bucket,
            p.size,

            (
                SELECT SUM(qty)
                FROM tbl_master_barcode m2
                WHERE
                    m2.item = p.item
                    AND m2.colour = p.colour
                    AND m2.bucket = p.bucket
            ) AS qty_order,

            COALESCE(i.qty_in,0) AS qty_in,
            COALESCE(o.qty_out,0) AS qty_out,

            (
                COALESCE(i.qty_in,0) -
                COALESCE(o.qty_out,0)
            ) AS qty

        FROM
        (
            SELECT
                style,
                item,
                gender,
                colour,
                bucket,
                size

            FROM tbl_master_barcode

            GROUP BY
                style,
                item,
                gender,
                colour,
                bucket,
                size

        ) p

        LEFT JOIN
        (
            SELECT
                mb.item,
                mb.colour,
                mb.bucket,
                mb.size,

                SUM(mb.qty) AS qty_in

            FROM tbl_transaction_scan ts

            INNER JOIN tbl_master_barcode mb
                ON ts.qr_code = mb.qr_code

            WHERE ts.type_scan='IN_SM'

            GROUP BY
                mb.item,
                mb.colour,
                mb.bucket,
                mb.size

        ) i
            ON p.item = i.item
            AND p.colour = i.colour
            AND p.bucket = i.bucket
            AND p.size = i.size

        LEFT JOIN
        (
            SELECT
                mb.item,
                mb.colour,
                mb.bucket,
                mb.size,

                SUM(mb.qty) AS qty_out

            FROM tbl_transaction_scan ts

            INNER JOIN tbl_master_barcode mb
                ON ts.qr_code = mb.qr_code

            WHERE ts.type_scan='OUT_SM'

            GROUP BY
                mb.item,
                mb.colour,
                mb.bucket,
                mb.size

        ) o
            ON p.item = o.item
            AND p.colour = o.colour
            AND p.bucket = o.bucket
            AND p.size = o.size

        $where

        ORDER BY
            p.item,
            p.colour,
            p.bucket,
            p.size

    ");

}else{

    $where = '';

    if(!$isTotal){
        $where = "WHERE
            p.item='$item'
            AND p.colour='$colour'
            AND p.bucket='$bucket'";
    }

    $sql = mysqli_query($conn,"

        SELECT
            p.style,
            p.item,
            p.gender,
            p.colour,
            p.bucket,
            p.qty_order,

            s.size,

            COALESCE(s.qty,0) AS qty

        FROM
        (
            SELECT
                style,
                item,
                gender,
                colour,
                bucket,

                SUM(qty) AS qty_order

            FROM tbl_master_barcode

            GROUP BY
                style,
                item,
                gender,
                colour,
                bucket

        ) p

        LEFT JOIN
        (
            SELECT
                mb.item,
                mb.colour,
                mb.bucket,
                mb.size,

                SUM(mb.qty) AS qty

            FROM tbl_transaction_scan ts

            INNER JOIN tbl_master_barcode mb
                ON ts.qr_code = mb.qr_code

            WHERE ts.type_scan='$type'

            GROUP BY
                mb.item,
                mb.colour,
                mb.bucket,
                mb.size

        ) s
            ON p.item = s.item
            AND p.colour = s.colour
            AND p.bucket = s.bucket

        $where

        ORDER BY
            p.item,
            p.colour,
            p.bucket,
            s.size

    ");

}

// =====================================================
// PIVOT DATA
// =====================================================

$data = [];
$grandTotal = 0;
$grandOrder = 0;
$grandSize = [];

foreach($sizes as $s){
    $grandSize[$s] = 0;
}

while($row = mysqli_fetch_assoc($sql)){

    $key =
        $row['style'].'||'.
        $row['item'].'||'.
        $row['gender'].'||'.
        $row['colour'].'||'.
        $row['bucket'];

    if(!isset($data[$key])){

        $data[$key] = [
            'style'     => $row['style'],
            'item'      => $row['item'],
            'gender'    => $row['gender'],
            'colour'    => $row['colour'],
            'bucket'    => $row['bucket'],
            'qty_order' => $row['qty_order'],
            'size'      => []
        ];

    }

    $data[$key]['size'][$row['size']] = $row['qty'];

}

// =====================================================
// CREATE XLSX
// =====================================================

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Judul Excel
if($isTotal){

    if($type == 'IN_SM'){

        $title = 'GRAND TOTAL SCAN IN';

    }elseif($type == 'OUT_SM'){

        $title = 'GRAND TOTAL SCAN OUT';

    }elseif($type == 'STOCK'){

        $title = 'GRAND TOTAL STOCK';

    }else{

        $title = 'GRAND TOTAL';

    }

}else{

    if($type == 'IN_SM'){

        $title = 'DETAIL SCAN IN';

    }elseif($type == 'OUT_SM'){

        $title = 'DETAIL SCAN OUT';

    }elseif($type == 'STOCK'){

        $title = 'DETAIL STOCK';

    }else{

        $title = 'DETAIL '.str_replace('_',' ',$type);

    }

}

$sheet->setCellValue('A1', $title);

$headers = ['Style','Item','Gender','Colour','Bucket','Order'];
$headers = array_merge($headers, $sizes);
$headers[] = 'Total';

$col = 'A';
foreach($headers as $header){
    $sheet->setCellValue($col.'3', $header);
    $col++;
}

$rowNum = 4;

foreach($data as $row){

    $sheet->setCellValue('A'.$rowNum, $row['style']);
    $sheet->setCellValue('B'.$rowNum, $row['item']);
    $sheet->setCellValue('C'.$rowNum, $row['gender']);
    $sheet->setCellValue('D'.$rowNum, $row['colour']);
    $sheet->setCellValue('E'.$rowNum, $row['bucket']);
    $sheet->setCellValue('F'.$rowNum, (int)$row['qty_order']);

    $grandOrder += $row['qty_order'];

    $colIndex = 7;
    $rowTotal = 0;

    foreach($sizes as $size){

        $qty = $row['size'][$size] ?? 0;

        $sheet->setCellValueByColumnAndRow($colIndex, $rowNum, (int)$qty);

        $rowTotal += $qty;
        $grandSize[$size] += $qty;

        $colIndex++;

    }

    $sheet->setCellValueByColumnAndRow($colIndex, $rowNum, (int)$rowTotal);

    $grandTotal += $rowTotal;

    $rowNum++;

}

// =====================================================
// GRAND TOTAL
// =====================================================

$sheet->setCellValue('A'.$rowNum, 'GRAND TOTAL');
$sheet->mergeCells('A'.$rowNum.':E'.$rowNum);

$sheet->setCellValue('F'.$rowNum, (int)$grandOrder);

$colIndex = 7;

foreach($sizes as $size){
    $sheet->setCellValueByColumnAndRow($colIndex, $rowNum, (int)$grandSize[$size]);
    $colIndex++;
}

$sheet->setCellValueByColumnAndRow($colIndex, $rowNum, (int)$grandTotal);

// =====================================================
// AUTO WIDTH
// =====================================================

foreach(range('A', $sheet->getHighestColumn()) as $column){
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// =====================================================
// SIMPLE BORDER
// =====================================================

$sheet->getStyle('A3:'.$sheet->getHighestColumn().$rowNum)
      ->getBorders()
      ->getAllBorders()
      ->setBorderStyle(
          \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
      );

// =====================================================
// DOWNLOAD
// =====================================================

$filename = 'Detail_Stock_SM_IP_'.$type.'_'.date('Ymd_His').'.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$filename.'"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
