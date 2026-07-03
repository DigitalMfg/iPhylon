<?php
require 'function.php';

$line  = $_POST['line'];
$shift = $_POST['shift'];

$qHeader = mysqli_query($conn,"
    SELECT *
    FROM tbl_daily_plan_header
    WHERE line_produksi='$line'
    AND tanggal_plan = CURDATE()
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
?>

<?php
$typeList = [
    'MOLD',
    'PLAN',
    'ACTUAL',
    'PACKING'
];
?>
    <?php while($dp = mysqli_fetch_assoc($qHeader)): ?>
    <div class="card card-outline card-info mb-2">
        <div class="card-body py-2">
            <div class="row">
                <div class="col-md-4">
                    <strong>Item :</strong>
                    <?= $dp['item']; ?>
                </div>

                <div class="col-md-2">
                    <strong>Colour :</strong>
                    <?= $dp['colour']; ?>
                </div>

                <div class="col-md-2">
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

<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">
            SHIFT <?= $shift; ?>
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
                            if($type == 'PACKING')
                            {
                                $qPack = mysqli_query($conn,"
                                SELECT COALESCE(SUM(m.qty),0) total
                                FROM tbl_transaction_scan t

                                INNER JOIN tbl_master_barcode m
                                    ON m.qr_code = t.qr_code

                                WHERE t.type_scan='OUT_PACKING'
                                AND t.shift='$shift'
                                AND DATE(t.date_scan)=CURDATE()

                                AND m.line='".$dp['line_produksi']."'
                                AND m.item='".$dp['item']."'
                                AND m.colour='".$dp['colour']."'
                                AND m.size='$size'
                                ");

                                $rPack = mysqli_fetch_assoc($qPack);

                                $qty = $rPack['total'];
                            }
                            else
                            {
                                $q = mysqli_query($conn,"
                                    SELECT
                                        COALESCE(SUM(qty),0) total
                                    FROM tbl_daily_plan_detail
                                    WHERE id_daily_header='".$dp['id_daily_header']."'
                                    AND shift='$shift'
                                    AND type='$type'
                                    AND size='$size'
                                ");
                                $r = mysqli_fetch_assoc($q);
                                $qty = $r['total'];
                            }

                            $total += $qty;
                        ?>

                            <td><?= $qty; ?></td>

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