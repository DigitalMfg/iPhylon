<?php
require 'function.php';

$line  = $_POST['line'];
$shift = $_POST['shift'];
$qHeader = mysqli_query($conn,"
    SELECT *
    FROM tbl_daily_plan_header
    WHERE line_produksi='$line'
    AND tanggal_plan = CURDATE()
    LIMIT 1
");

$header = mysqli_fetch_assoc($qHeader);

$sizes = [
    '1','1T','2','2T','3','3T',
    '4','4T','5','5T','6','6T',
    '7','7T','8','8T','9','9T',
    '10','10T','11','11T','12','12T',
    '13','13T','14','15'
];

$data = [];

$q = mysqli_query($conn,"
SELECT
    t.hour_scan,
    m.size,
    SUM(m.qty) qty

FROM tbl_transaction_scan t

INNER JOIN tbl_master_barcode m
    ON m.qr_code = t.qr_code

WHERE t.type_scan = 'OUT_PACKING'
AND t.shift = '$shift'
AND t.cost_center = 'Line $line'
AND DATE(t.date_scan) = CURDATE()

GROUP BY
    t.hour_scan,
    m.size
");

while($r = mysqli_fetch_assoc($q))
{
    $data[$r['hour_scan']][$r['size']] = $r['qty'];
}
$grandTotalAll = 0;
?>

<div class="row mb-3">

    <div class="col-md-4">
        <strong>Item :</strong>
        <?= $header['item']; ?>
    </div>

    <div class="col-md-2">
        <strong>Colour :</strong>
        <?= $header['colour']; ?>
    </div>

    <div class="col-md-2">
        <strong>Mesin :</strong>
        <?= $header['mesin']; ?>
    </div>

    <div class="col-md-2">
        <strong>Injector :</strong>
        <?= $header['injector']; ?>
    </div>

    <div class="col-md-2">
        <strong>Line :</strong>
        <?= $header['line_produksi']; ?>
    </div>

</div>

<div class="table-responsive">

<table class="table table-bordered table-striped text-center">

    <thead class="bg-success">

        <tr>

            <th>Hour</th>

            <?php foreach($sizes as $size): ?>
                <th><?= $size ?></th>
            <?php endforeach; ?>

            <th>Total</th>

        </tr>

    </thead>

    <tbody>

        <?php for($hour=1; $hour<=8; $hour++): ?>

        <tr>

            <th>
                <?= $hour ?>
            </th>

            <?php

            $grandTotal = 0;
            $grandTotalAllHour = 0;

            foreach($sizes as $size):

                $qty = $data[$hour][$size] ?? 0;

                $grandTotal += $qty;
                $grandTotalAll += $qty;

            ?>

                <td>
                    <?= $qty ?>
                </td>

            <?php endforeach; ?>

            <td class="font-weight-bold bg-light">
                <?= number_format($grandTotal) ?>
            </td>

        </tr>

        <?php endfor; ?>

        <tr style="
    background:#; font-weight:bold; font-size:16px;">
            <th>
                TOTAL
            </th>
            <?php

            foreach($sizes as $size)
            {
                $sizeTotal = 0;
                for($hour=1; $hour<=8; $hour++)
                {
                    $sizeTotal += $data[$hour][$size] ?? 0;
                }
                echo "<td>".$sizeTotal."</td>";
            }
            ?>
            <td>
                <?= number_format($grandTotalAll); ?>
            </td>

        </tr>
    </tbody>

</table>

</div>