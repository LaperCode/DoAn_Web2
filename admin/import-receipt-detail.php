<?php
include("../admin/includes/header.php");

$receipt_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$receipt_query = "SELECT ir.*, u.name AS admin_name FROM import_receipts ir LEFT JOIN users u ON ir.admin_id = u.id WHERE ir.id = $receipt_id";
$receipt_result = mysqli_query($conn, $receipt_query);
$receipt = $receipt_result ? mysqli_fetch_assoc($receipt_result) : null;

if (!$receipt) {
    redirect("import-manage.php", "Không tìm thấy phiếu nhập");
}

$items_query = "SELECT ih.*, p.name AS product_name 
FROM import_history ih 
LEFT JOIN products p ON ih.product_id = p.id 
WHERE ih.receipt_id = $receipt_id
ORDER BY ih.id ASC";
$items = mysqli_query($conn, $items_query);
$receipt_code = $receipt['code'] ? $receipt['code'] : ('PN' . str_pad($receipt['id'], 3, '0', STR_PAD_LEFT));
?>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);">
                        <h4 style="color: white; margin: 0;">
                            <i class="material-icons" style="vertical-align: middle;">receipt_long</i>
                            Chi tiết phiếu nhập #<?= htmlspecialchars($receipt_code) ?>
                            <a href="import-manage.php" class="btn btn-light btn-sm float-end">
                                <i class="fa fa-arrow-left"></i> Quay lại
                            </a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div style="background: #FFF3E0; padding: 15px; border-radius: 8px; border-left: 4px solid #FF9800;">
                                    <p><strong>Mã phiếu:</strong> #<?= htmlspecialchars($receipt_code) ?></p>
                                    <p><strong>Ngày nhập:</strong> <?= $receipt['import_date'] ? date('d/m/Y', strtotime($receipt['import_date'])) : date('d/m/Y', strtotime($receipt['created_at'])) ?></p>
                                    <p><strong>Ngày tạo:</strong> <?= date('d/m/Y H:i', strtotime($receipt['created_at'])) ?></p>
                                    <p><strong>Admin nhập:</strong> <?= htmlspecialchars($receipt['admin_name'] ?? 'N/A') ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div style="background: #E8F5E9; padding: 15px; border-radius: 8px; border-left: 4px solid #4CAF50;">
                                    <p><strong>Tổng số lượng:</strong> <?= (int)$receipt['total_quantity'] ?> sản phẩm</p>
                                    <p><strong>Số mặt hàng:</strong> <?= (int)$receipt['total_items'] ?> mặt hàng</p>
                                    <p><strong>Tổng giá trị:</strong> <?= fmt_price($receipt['total_value']) ?> $</p>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($receipt['note'])) { ?>
                            <div class="alert" style="background: #1976D2; color: #fff; border-left: 6px solid #0D47A1; font-weight: 600;">
                                <strong>Ghi chú:</strong> <?= nl2br(htmlspecialchars($receipt['note'])) ?>
                            </div>
                        <?php } ?>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead style="background: linear-gradient(135deg, #FFB74D 0%, #FFA726 100%); color: white;">
                                    <tr>
                                        <th style="width: 60px;">STT</th>
                                        <th>Sản phẩm</th>
                                        <th style="width: 110px;">SL nhập</th>
                                        <th style="width: 120px;">Giá nhập/sp</th>
                                        <th style="width: 110px;">SL tồn cũ</th>
                                        <th style="width: 120px;">Giá nhập cũ</th>
                                        <th style="width: 140px;">Giá BQ mới</th>
                                        <th style="width: 130px;">Giá bán mới</th>
                                        <th style="width: 90px;">% LN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($items && mysqli_num_rows($items) > 0) {
                                        $idx = 1;
                                        foreach ($items as $item) {
                                    ?>
                                            <tr>
                                                <td class="text-center"><?= $idx++ ?></td>
                                                <td><?= htmlspecialchars($item['product_name']) ?></td>
                                                <td class="text-center"><span class="badge bg-primary" style="font-size: 14px;"><?= (int)$item['quantity_imported'] ?></span></td>
                                                <td class="text-end"><?= fmt_price($item['import_price']) ?> $</td>
                                                <td class="text-center"><?= (int)$item['old_quantity'] ?></td>
                                                <td class="text-end"><?= fmt_price($item['old_original_price']) ?> $</td>
                                                <td class="text-end"><strong style="color: #FF9800;"><?= fmt_price($item['new_average_price']) ?> $</strong></td>
                                                <td class="text-end"><strong style="color: #4CAF50;"><?= fmt_price($item['new_selling_price']) ?> $</strong></td>
                                                <td class="text-center"><span class="badge bg-success"><?= number_format($item['profit_margin'], 2) ?>%</span></td>
                                            </tr>
                                        <?php
                                        }
                                    } else {
                                        ?>
                                        <tr>
                                            <td colspan="9" class="text-center">Chưa có sản phẩm trong phiếu này.</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

<?php include("../admin/includes/footer.php"); ?>