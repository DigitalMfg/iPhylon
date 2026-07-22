<?php
require 'function.php';

$line     = $_POST['line'];
$shift    = $_POST['shift'];
$tanggal  = $_POST['tanggal'];

$qHeader = mysqli_query($conn,"
    SELECT *
    FROM tbl_daily_plan_header
    WHERE line_produksi='$line'
    AND tanggal_plan = '$tanggal'
");

if(mysqli_num_rows($qHeader)==0){
    die("
        <div class='alert alert-danger'>
            Daily Plan tidak ditemukan
        </div>
    ");
}

$sizes = [
    '1','1T','2','2T','3','3T',
    '4','4T','5','5T','6','6T',
    '7','7T','8','8T','9','9T',
    '10','10T','11','11T','12','12T',
    '13','13T','14','15'
];

$typeList = [
    'MOLD',
    'PLAN',
    'ACTUAL',
    'PACKING'
];
?>

<?php while($dp = mysqli_fetch_assoc($qHeader)):

// Ambil bucket yang benar berdasarkan colour + item alternatif
$item1 = $dp['item'];
$item2 = str_replace('NIKE ','',$dp['item']);

$getBucket = mysqli_query($conn, "
    SELECT DISTINCT mb.bucket
    FROM tbl_master_barcode mb

    WHERE (
        mb.item = '$item1'
        OR mb.item = '$item2'
    )

    AND TRIM(UPPER(mb.colour))
        = TRIM(UPPER('{$dp['colour']}'))

    ORDER BY mb.bucket DESC
    LIMIT 1
");

$bucketRow = mysqli_fetch_assoc($getBucket);

$bucketHeader = $bucketRow['bucket'] ?? '-';

?>

<!-- HEADER CARD -->

<div class="card card-outline card-info mb-2">
    <div class="card-body py-2">
        <div class="row">
            <div class="col-md-3">
                <strong>Item :</strong>
                <?= $dp['item']; ?>
            </div>


        <div class="col-md-2">
            <strong>Colour :</strong>
            <?= $dp['colour']; ?>
        </div>

        <div class="col-md-2">
            <strong>Bucket :</strong>
            <?= $bucketHeader; ?>
        </div>

        <div class="col-md-1">
            <strong>Mesin :</strong>
            <?= $dp['mesin']; ?>
        </div>

        <div class="col-md-2">
            <strong>Injector :</strong>
            <?= $dp['injector']; ?>
        </div>

        <div class="col-md-2">
            <strong>Line :</strong>
            <?= $dp['line_produksi']; ?>
        </div>
    </div>
</div>


</div>

<!-- DETAIL TABLE -->

<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">
            SHIFT <?= $shift; ?> | <?= date('d M Y', strtotime($tanggal)); ?>
        </h3>
    </div>


<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>SIZE</th>
                    <?php foreach($sizes as $size): ?>
                        <th><?= $size; ?></th>
                    <?php endforeach; ?>
                    <th>TOTAL</th>
                </tr>
            </thead>

            <tbody>

            <?php foreach($typeList as $type): ?>

                <tr>
                    <th><?= $type; ?></th>

                    <?php
                    $total = 0;

                    foreach($sizes as $size):

                        // =========================
                        // PACKING
                        // =========================
                        if($type == 'PACKING')
                        {
                            $lineCost = 'Line '.$dp['line_produksi'];

                            // Ambil semua kemungkinan nama item
                            $item1 = $dp['item'];
                            $item2 = str_replace('NIKE ','',$dp['item']);

                            $qPack = mysqli_query($conn, "
                                SELECT COALESCE(SUM(mb.qty),0) AS total
                                FROM tbl_transaction_scan ts

                                INNER JOIN tbl_master_barcode mb
                                    ON ts.qr_code = mb.qr_code

                                WHERE ts.type_scan = 'OUT_PACKING'
                                AND ts.shift = '$shift'
                                AND DATE(ts.date_scan) = '$tanggal'
                                AND ts.cost_center = '$lineCost'

                                AND (
                                    mb.item = '$item1'
                                    OR mb.item = '$item2'
                                )

                                AND mb.colour = '{$dp['colour']}'
                                AND mb.size = '$size'
                            ");

                            $rPack = mysqli_fetch_assoc($qPack);

                            $qty = (int)$rPack['total'];
                        }   

                        // =========================
                        // MOLD / PLAN / ACTUAL
                        // =========================
                        else
                        {
                            $q = mysqli_query($conn, "
                                SELECT COALESCE(SUM(qty),0) AS total
                                FROM tbl_daily_plan_detail
                                WHERE id_daily_header = '{$dp['id_daily_header']}'
                                AND shift = '$shift'
                                AND type = '$type'
                                AND size = '$size'
                            ");

                            $r = mysqli_fetch_assoc($q);

                            $qty = (int)$r['total'];
                        }

                        $total += $qty;
                    ?>

                        <td><?= number_format($qty); ?></td>

                    <?php endforeach; ?>

                    <td class="bg-light font-weight-bold">
                        <?= number_format($total); ?>
                    </td>
                </tr>

            <?php endforeach; ?>

            </tbody>
        </table>
    </div>
</div>


</div>

<!-- OUTPUT PER HOUR -->

<div class="text-left mb-3">
    <button
        class="btn btn-success"
        onclick="loadOutputPerHour(
            '<?= $dp['line_produksi']; ?>',
            '<?= $shift; ?>',
            '<?= addslashes($dp['item']); ?>',
            '<?= addslashes($dp['colour']); ?>'
        )">


    <i class="fas fa-clock"></i>
    Output Per Hour
</button>


</div>

<?php endwhile; ?>
