<?php
include("../admin/includes/header.php");

$from_date = isset($_GET['from_date']) ? mysqli_real_escape_string($conn, $_GET['from_date']) : '';
$to_date = isset($_GET['to_date']) ? mysqli_real_escape_string($conn, $_GET['to_date']) : '';

$where_conditions = [];
if (!empty($from_date) && !empty($to_date)) {
    $where_conditions[] = "DATE(ir.created_at) BETWEEN '$from_date' AND '$to_date'";
}

$where_sql = '';
if (!empty($where_conditions)) {
    $where_sql = "WHERE " . implode(' AND ', $where_conditions);
}

$receipt_query = "SELECT 
    ir.id,
    ir.code,
    ir.total_quantity,
    ir.total_items,
    ir.total_value,
    ir.created_at,
    u.name AS admin_name
FROM import_receipts ir
LEFT JOIN users u ON ir.admin_id = u.id
$where_sql
ORDER BY ir.created_at DESC";

$receipts = mysqli_query($conn, $receipt_query);
?>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="background: #f5f2f0;">
                        <h4 style="color: #c05a56; margin: 0; font-weight: 600;">
                            Quản lý nhập hàng
                            <a href="import-stock.php" class="btn btn-warning btn-sm float-end" style="background: #f09a3e; border: none; color: white;">
                                <i class="fa fa-plus"></i> Thêm phiếu nhập
                            </a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="card" style="border: 1px solid #eee;">
                            <div class="card-body" style="background: #f6f3f2;">
                                <h6 style="color: #6d5c5b; font-weight: 600;">Tra cứu phiếu nhập</h6>
                                <form class="row g-3" method="GET">
                                    <div class="col-md-6">
                                        <label class="form-label" style="font-weight: 600; color: #6d5c5b;">Từ ngày</label>
                                        <input type="date" class="form-control" name="from_date" value="<?= htmlspecialchars($from_date) ?>" placeholder="dd/mm/yyyy">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" style="font-weight: 600; color: #6d5c5b;">Đến ngày</label>
                                        <input type="date" class="form-control" name="to_date" value="<?= htmlspecialchars($to_date) ?>" placeholder="dd/mm/yyyy">
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-success" style="background: #5ea860; border: none;">
                                            <i class="fa fa-search"></i> Tìm kiếm
                                        </button>
                                        <a href="import-manage.php" class="btn btn-secondary" style="background: #6c6c6c; border: none;">
                                            <i class="fa fa-undo"></i> Đặt lại
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive mt-4">
                            <style>
                                .receipt-table {
                                    border-collapse: collapse;
                                    border-spacing: 0;
                                }

                                .receipt-table {
                                    border: 1px solid #f0c6c5;
                                }

                                .receipt-table th,
                                .receipt-table td {
                                    border: 1px solid #f0c6c5 !important;
                                }

                                .receipt-table tbody tr td {
                                    border-top: 1px solid #f0c6c5 !important;
                                    border-bottom: 1px solid #f0c6c5 !important;
                                }

                                .receipt-table tbody tr:last-child td {
                                    border-bottom: 1px solid #f0c6c5 !important;
                                }

                                .receipt-row td {
                                    background: #fff;
                                }
                            </style>
                            <table class="table table-hover receipt-table">
                                <thead style="background: #d9534f; color: white;">
                                    <tr>
                                        <th style="width: 60px;">STT</th>
                                        <th style="width: 120px;">Mã phiếu</th>
                                        <th style="width: 140px;">Ngày nhập</th>
                                        <th>Số lượng sản phẩm</th>
                                        <th style="width: 160px;">Tổng giá trị</th>
                                        <th style="width: 110px;">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($receipts && mysqli_num_rows($receipts) > 0) {
                                        $index = 1;
                                        foreach ($receipts as $receipt) {
                                            $receipt_code = $receipt['code'] ? $receipt['code'] : ('PN' . str_pad($receipt['id'], 3, '0', STR_PAD_LEFT));
                                    ?>
                                            <tr class="receipt-row">
                                                <td class="text-center"><?= $index++ ?></td>
                                                <td class="text-center" style="color: #c05a56; font-weight: 700;">#<?= htmlspecialchars($receipt_code) ?></td>
                                                <td class="text-center"><?= date('d-m-Y', strtotime($receipt['created_at'])) ?></td>
                                                <td>
                                                    <?= (int)$receipt['total_quantity'] ?> sản phẩm<br>
                                                    <small style="color: #6d5c5b;"><?= (int)$receipt['total_items'] ?> mặt hàng</small>
                                                </td>
                                                <td class="text-end" style="font-weight: 600; color: #6d8b3f;">
                                                    <?= fmt_price($receipt['total_value']) ?> $
                                                </td>
                                                <td class="text-center">
                                                    <a class="btn btn-sm btn-primary" href="import-receipt-detail.php?id=<?= $receipt['id'] ?>">
                                                        <i class="fa fa-eye"></i> Xem
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                    } else {
                                        ?>
                                        <tr>
                                            <td colspan="6" class="text-center" style="color: #6d5c5b;">Chưa có phiếu nhập nào.</td>
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