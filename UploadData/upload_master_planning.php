<?php
// session_start();

require '../function.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$uploaded_by = $_SESSION['username'];

if(isset($_POST['upload'])){

    $fileName = $_FILES['spkPlanning']['name'];
    $fileTmp  = $_FILES['spkPlanning']['tmp_name'];

    $spreadsheet = IOFactory::load($fileTmp);
    $sheet = $spreadsheet->getActiveSheet();

    // =========================
    // HEADER SPK
    // =========================

    $no_dokumen = $sheet->getCell('F2')->getValue();
    $revisi     = $sheet->getCell('F3')->getValue();
    $item       = $sheet->getCell('B7')->getValue();
    $mesin      = $sheet->getCell('B8')->getValue();
    $injector   = $sheet->getCell('B9')->getValue();
    $line       = $sheet->getCell('B10')->getValue();
    $tanggal_spk = $sheet->getCell('B11')->getFormattedValue();

    // =========================
    // GENERATE JO
    // =========================

    $no_jo = 'JO-' . date('YmdHis');

    $tanggal_upload = date('Y-m-d H:i:s');

    // =========================
    // UPLOAD FILE
    // =========================

    $folder = "../FilePlanning/";

    if(!is_dir($folder)){
        mkdir($folder, 0777, true);
    }

    $newFileName = time() . '_' . $fileName;

    move_uploaded_file(
        $fileTmp,
        $folder . $newFileName
    );

    // =========================
    // INSERT HEADER
    // =========================

    mysqli_query($conn,"
        INSERT INTO tbl_jo_spk
        (
            no_jo,
            no_dokumen,
            revisi,
            item,
            mesin,
            injector,
            line_produksi,
            tanggal_spk,
            nama_spk,
            tanggal_upload,
            uploaded_by,
            file_excel
        )
        VALUES
        (
            '$no_jo',
            '$no_dokumen',
            '$revisi',
            '$item',
            '$mesin',
            '$injector',
            '$line',
            '$tanggal_spk',
            'SPK LOW CARBON MATERIAL (LC)',
            '$tanggal_upload',
            '$uploaded_by',
            '$newFileName'
        )
    ");

    $id_jo_spk = mysqli_insert_id($conn);

    // =========================
    // SIZE MAPPING
    // =========================

    $sizes = [

        'I'  => '1',
        'J'  => '1T',
        'K'  => '2',
        'L'  => '2T',
        'M'  => '3',
        'N'  => '3T',
        'O'  => '4',
        'P'  => '4T',
        'Q'  => '5',
        'R'  => '5T',
        'S'  => '6',
        'T'  => '6T',
        'U'  => '7',
        'V'  => '7T',
        'W'  => '8',
        'X'  => '8T',
        'Y'  => '9',
        'Z'  => '9T',
        'AA' => '10',
        'AB' => '10T',
        'AC' => '11',
        'AD' => '11T',
        'AE' => '12',
        'AF' => '12T',
        'AG' => '13',
        'AH' => '13T',
        'AI' => '14',
        'AJ' => '15'

    ];

    // =========================
    // LOOP DETAIL
    // =========================

    $highestRow = $sheet->getHighestRow();

    for($row = 13; $row <= $highestRow; $row++){

        $style = trim(
            (string)$sheet->getCell('A'.$row)->getValue()
        );

        // SKIP ROW KOSONG
        if(empty($style)){
            continue;
        }

        $colour = $sheet->getCell('B'.$row)->getValue();

        $gender = $sheet->getCell('C'.$row)->getValue();

        $bucket = $sheet->getCell('D'.$row)->getValue();

        $po = $sheet->getCell('E'.$row)->getValue();

        $po_item = $sheet->getCell('F'.$row)->getValue();

        $country = $sheet->getCell('G'.$row)->getValue();

        $total_order = $sheet->getCell('H'.$row)->getValue();

        // TOTAL FORMULA
        $total_qty = $sheet
                        ->getCell('AK'.$row)
                        ->getCalculatedValue();

        // =========================
        // INSERT DETAIL
        // =========================

        mysqli_query($conn,"
            INSERT INTO tbl_spk_detail
            (
                id_jo_spk,
                style,
                colour,
                gender,
                bucket,
                po,
                po_item,
                country,
                total_order,
                total_qty
            )
            VALUES
            (
                '$id_jo_spk',
                '$style',
                '$colour',
                '$gender',
                '$bucket',
                '$po',
                '$po_item',
                '$country',
                '$total_order',
                '$total_qty'
            )
        ");

        $id_detail = mysqli_insert_id($conn);

        // =========================
        // INSERT SIZE
        // =========================

        foreach($sizes as $column => $size){

    $qty = $sheet
            ->getCell($column.$row)
            ->getValue();

    if(!empty($qty) && $qty > 0){

        mysqli_query($conn,"
            INSERT INTO tbl_spk_size_qty
            (
                id_detail,
                size,
                qty
            )
            VALUES
            (
                '$id_detail',
                '$size',
                '$qty'
            )
        ");

    }

}

    }

    header('Location: ../master_planning.php?success=1');
    exit;

}
?>