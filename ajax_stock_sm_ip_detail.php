<?php
require 'function.php';

$type    = $_POST['type'];
$item    = $_POST['item'];
$colour  = $_POST['colour'];
$bucket  = $_POST['bucket'];

$bucket_from  = $_POST['bucket_from'] ?? '';
$bucket_to    = $_POST['bucket_to'] ?? '';
$item_filter  = $_POST['item_filter'] ?? '';
$colour_filter= $_POST['colour_filter'] ?? '';

$isTotal = ($item == '');

$sizes = [
    '1','1T','2','2T','3','3T',
    '4','4T','5','5T','6','6T',
    '7','7T','8','8T','9','9T',
    '10','10T','11','11T','12','12T',
    '13','13T','14','14T','15'
];

if($isTotal){
    $filterWhere = "WHERE 1=1";

    if($bucket_from != '' && $bucket_to != ''){
        $filterWhere .= " AND bucket BETWEEN '$bucket_from' AND '$bucket_to'";
    }

    if($item_filter != ''){
        $filterWhere .= " AND item='$item_filter'";
    }

    if($colour_filter != ''){
        $filterWhere .= " AND colour='$colour_filter'";
    }

    // =========================================
    // TOTAL STOCK
    // =========================================
    if($type == 'STOCK'){

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

                WHERE
                    bucket BETWEEN '$bucket_from' AND '$bucket_to'
                    
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

            ORDER BY
                p.item,
                p.colour,
                p.bucket,
                p.size

        ");

    }else{

        // =========================================
        // TOTAL IN_SM / OUT_SM
        // =========================================

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
                $filterWhere

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

            ORDER BY
                p.item,
                p.colour,
                p.bucket,
                s.size

        ");

    }

}

// =======================
// DETAIL (hanya jika bukan TOTAL)
// =======================

if(!$isTotal && $type == "STOCK"){

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

            WHERE
                item='$item'
                AND colour='$colour'
                AND bucket='$bucket'

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

        ORDER BY p.size

    ");

}elseif(!$isTotal){

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

            WHERE
                item='$item'
                AND colour='$colour'
                AND bucket='$bucket'

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

        ORDER BY s.size

    ");

}

$data = [];
$grandTotal = 0;

while($row = mysqli_fetch_assoc($sql)){

    // key unik per row report
    $key =
        $row['style'].'||'.
        $row['item'].'||'.
        $row['gender'].'||'.
        $row['colour'].'||'.
        $row['bucket'];

    // buat row jika belum ada
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

    // simpan qty per size
    $data[$key]['size'][$row['size']] = $row['qty'];

    $grandTotal += $row['qty'];

}

?>

<h5 class="mb-3">
    <?= str_replace("_"," ",$type) ?>
</h5>

<div class="table-responsive">
    <table class="table table-bordered table-striped table-sm">
        <thead class="thead-light">
            <tr>

            <th>Style</th>
            <th>Item</th>
            <th>Gender</th>
            <th>Colour</th>
            <th>Bucket</th>
            <th class="text-right">Order</th>
            <?php foreach($sizes as $size){ ?>
            <th class="text-center">
                <?= $size ?>
            </th>

            <?php } ?>

            <th class="text-center">
                Total
            </th>

            </tr>

        </thead>

            <tbody>

                <?php
                $grandOrder = 0;
                $grandSize = [];
                $grandTotalColumn = 0;

                foreach($sizes as $s){
                    $grandSize[$s] = 0;
                }

                foreach($data as $row):

                    $rowTotal = 0;
                    $grandOrder += $row['qty_order'];
                ?>

                <tr>

                    <td style="white-space:nowrap;"><?= $row['style'] ?></td>
                    <td style="white-space:nowrap;"><?= $row['item'] ?></td>
                    <td style="white-space:nowrap;"><?= $row['gender'] ?></td>
                    <td style="white-space:nowrap;"><?= $row['colour'] ?></td>
                    <td style="white-space:nowrap;"><?= $row['bucket'] ?></td>

                    <td class="text-right font-weight-bold">
                        <?= number_format($row['qty_order']) ?>
                    </td>

                    <?php foreach($sizes as $size):

                        $qty = $row['size'][$size] ?? 0;
                        $rowTotal += $qty;
                        $grandSize[$size] += $qty;
                    ?>

                        <td class="text-right">
                            <?= number_format($qty) ?>
                        </td>

                    <?php endforeach; ?>

                    <td class="text-right font-weight-bold">
                        <?= number_format($rowTotal) ?>
                    </td>
                        <?php $grandTotalColumn += $rowTotal; ?>
                </tr>

                <?php endforeach; ?>

                </tbody>

        <tfoot>
            <tr class="font-weight-bold bg-light">

                <td colspan="5" class="text-center">
                    GRAND TOTAL
                </td>

                <td class="text-right">
                    <?= number_format($grandOrder) ?>
                </td>

                <?php foreach($sizes as $size): ?>
                    <td class="text-right">
                        <?= number_format($grandSize[$size]) ?>
                    </td>
                <?php endforeach; ?>

                <td class="text-right">
                    <?= number_format($grandTotalColumn) ?>
                </td>

            </tr>
        </tfoot>

    </table>
</div>