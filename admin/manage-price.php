<?php
include("../admin/includes/header.php");

// Lấy danh sách sản phẩm
$all_products_res = getAll("products");
$all_products = [];
while ($row = mysqli_fetch_assoc($all_products_res)) {
    $all_products[] = $row;
}

// Sản phẩm đang chọn
$selected_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$product = null;
$import_logs = null;

$msg_success = '';
$msg_error   = '';

// Xử lý cập nhật tỉ lệ lợi nhuận
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_margin'])) {
    $selected_id = (int)$_POST['product_id'];
    $new_margin  = (float)$_POST['profit_margin'];
    $res = getByID("products", $selected_id);
    $p   = mysqli_fetch_assoc($res);
    if ($p) {
        if ($new_margin < 0 || $new_margin > 1000) {
            $msg_error = 'Tỉ lệ lợi nhuận phải từ 0% đến 1000%.';
        } else {
            $new_selling = round($p['original_price'] * (1 + $new_margin / 100));
            $sql = "UPDATE products SET profit_margin='$new_margin', selling_price='$new_selling' WHERE id='$selected_id'";
            if (mysqli_query($conn, $sql)) {
                $p['profit_margin'] = $new_margin;
                $p['selling_price'] = $new_selling;
                $msg_success = 'Đã cập nhật tỉ lệ lợi nhuận! Giá bán mới: <strong>' . fmt_price($new_selling) . ' $</strong>';
            } else {
                $msg_error = 'Lỗi: ' . mysqli_error($conn);
            }
        }
        $product = $p;
    }
}

// Load thông tin sản phẩm nếu đã chọn
if ($selected_id && !$product) {
    $res = getByID("products", $selected_id);
    $product = mysqli_fetch_assoc($res);
}

// Load lịch sử nhập
if ($product) {
    $pid = $product['id'];
    $import_logs = mysqli_query(
        $conn,
        "SELECT ih.*, u.name as admin_name
         FROM import_history ih
         INNER JOIN users u ON ih.admin_id = u.id
         WHERE ih.product_id = '$pid'
         ORDER BY ih.created_at DESC"
    );
}
?>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card">
                    <div class="card-header" style="background: linear-gradient(135deg, #43A047 0%, #2E7D32 100%);">
                        <h4 style="color:white;margin:0;">
                            <i class="material-icons" style="vertical-align:middle;">sell</i>
                            Quản lý giá bán
                        </h4>
                    </div>
                    <div class="card-body">

                        <!-- Chọn sản phẩm -->
                        <form method="GET" action="manage-price.php" class="mb-4">
                            <label class="form-label fw-bold">Chọn sản phẩm:</label>
                            <div class="input-group" style="max-width:600px;">
                                <select name="product_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">-- Chọn sản phẩm --</option>
                                    <?php foreach ($all_products as $p): ?>
                                        <option value="<?= $p['id'] ?>" <?= $selected_id == $p['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($p['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </form>

                        <?php if ($product): ?>

                            <?php if ($msg_success): ?>
                                <div class="alert alert-success"><?= $msg_success ?></div>
                            <?php endif; ?>
                            <?php if ($msg_error): ?>
                                <div class="alert alert-danger"><?= $msg_error ?></div>
                            <?php endif; ?>

                            <!-- 3 ô tóm tắt -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div style="background:linear-gradient(135deg,#E3F2FD,#BBDEFB);padding:18px;border-radius:10px;border-left:4px solid #1976D2;">
                                        <div style="font-size:12px;color:#1565C0;font-weight:700;text-transform:uppercase;margin-bottom:4px;">Giá vốn (BQ hiện tại)</div>
                                        <div style="font-size:24px;font-weight:700;color:#0D47A1;"><?= fmt_price($product['original_price']) ?> $</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div style="background:linear-gradient(135deg,#FFF3E0,#FFE0B2);padding:18px;border-radius:10px;border-left:4px solid #F57C00;">
                                        <div style="font-size:12px;color:#E65100;font-weight:700;text-transform:uppercase;margin-bottom:4px;">Tỉ lệ lợi nhuận</div>
                                        <div style="font-size:24px;font-weight:700;color:#BF360C;"><?= number_format($product['profit_margin'] ?? 0, 2) ?>%</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div style="background:linear-gradient(135deg,#E8F5E9,#C8E6C9);padding:18px;border-radius:10px;border-left:4px solid #388E3C;">
                                        <div style="font-size:12px;color:#1B5E20;font-weight:700;text-transform:uppercase;margin-bottom:4px;">Giá bán hiện tại</div>
                                        <div style="font-size:24px;font-weight:700;color:#1B5E20;"><?= fmt_price($product['selling_price']) ?> $</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form cập nhật % lợi nhuận -->
                            <div style="background:#FFFDE7;padding:20px;border-radius:10px;border:1px solid #F9A825;margin-bottom:30px;">
                                <h5 style="color:#F57F17;margin-bottom:16px;">
                                    <i class="fa fa-edit"></i> Cập nhật tỉ lệ lợi nhuận
                                </h5>
                                <form method="POST" action="manage-price.php">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <div class="row align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label"><b>Tỉ lệ lợi nhuận (%)</b></label>
                                            <input type="number" step="0.01" min="0" max="1000"
                                                name="profit_margin"
                                                value="<?= number_format($product['profit_margin'] ?? 0, 2) ?>"
                                                class="form-control" id="marginInput" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label"><b>Giá bán dự kiến</b></label>
                                            <div id="previewPrice" style="font-size:18px;font-weight:700;color:#388E3C;padding:8px 0;">
                                                <?= fmt_price($product['selling_price']) ?> $
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="submit" name="update_margin" class="btn btn-warning w-100" style="font-weight:600;">
                                                <i class="fa fa-save"></i> Lưu thay đổi
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Bảng lịch sử lô nhập -->
                            <h5 style="color:#2E7D32;margin-bottom:12px;">
                                <i class="fa fa-history"></i> Lịch sử các lô nhập — giá vốn &amp; giá bán
                            </h5>
                            <?php if ($import_logs && mysqli_num_rows($import_logs) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover" style="font-size:14px;">
                                        <thead style="background:linear-gradient(135deg,#81C784,#4CAF50);color:white;">
                                            <tr>
                                                <th>#</th>
                                                <th>Thời gian nhập</th>
                                                <th>SL nhập</th>
                                                <th>Giá nhập lô</th>
                                                <th>Giá vốn BQ mới</th>
                                                <th>% Lợi nhuận</th>
                                                <th>Giá bán mới</th>
                                                <th>Admin</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $stt = 1;
                                            foreach ($import_logs as $log): ?>
                                                <tr>
                                                    <td class="text-center"><?= $stt++ ?></td>
                                                    <td><?= $log['created_at'] ?></td>
                                                    <td class="text-center"><span class="badge bg-primary"><?= $log['quantity_imported'] ?></span></td>
                                                    <td class="text-end"><?= fmt_price($log['import_price']) ?> $</td>
                                                    <td class="text-end" style="color:#1565C0;font-weight:600;"><?= fmt_price($log['new_average_price']) ?> $</td>
                                                    <td class="text-center" style="color:#E65100;font-weight:600;"><?= number_format($log['profit_margin'], 2) ?>%</td>
                                                    <td class="text-end" style="color:#1B5E20;font-weight:600;"><?= fmt_price($log['new_selling_price']) ?> $</td>
                                                    <td><?= htmlspecialchars($log['admin_name']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div style="background:#FFF8E1;border:1px solid #FFD54F;border-radius:8px;padding:14px 18px;color:#5D4037;font-size:14px;">
                                    <i class="material-icons" style="vertical-align:middle;margin-right:6px;font-size:18px;">info_outline</i>
                                    Chưa có lịch sử nhập hàng cho sản phẩm này.
                                </div>
                            <?php endif; ?>

                        <?php else: ?>
                            <div style="background:#E8F5E9;border:1px solid #A5D6A7;border-radius:10px;padding:18px 22px;color:#1B5E20;font-size:15px;font-weight:600;">
                                <i class="material-icons" style="vertical-align:middle;margin-right:6px;">info</i>
                                Vui lòng chọn sản phẩm để xem thông tin giá bán.
                            </div>
                        <?php endif; ?>

                    </div><!-- card-body -->
                </div><!-- card -->

            </div>
        </div>
    </div>
</body>

<script>
    const marginInput = document.getElementById('marginInput');
    const previewPrice = document.getElementById('previewPrice');
    const basePrice = <?= $product ? (float)$product['original_price'] : 0 ?>;

    if (marginInput && previewPrice) {
        marginInput.addEventListener('input', function() {
            const margin = parseFloat(this.value) || 0;
            const selling = Math.round(basePrice * (1 + margin / 100));
            previewPrice.textContent = selling.toLocaleString('vi-VN') + ' $';
        });
    }
</script>

<?php include("../admin/includes/footer.php"); ?>