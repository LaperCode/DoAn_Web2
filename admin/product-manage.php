<?php
include("../admin/includes/header.php");

if (!isset($_GET['id'])) {
    echo "<script>alert('Thiếu ID sản phẩm!'); window.location='products.php';</script>";
    exit;
}

$id = (int)$_GET['id'];
$product_res = getByID("products", $id);
if (mysqli_num_rows($product_res) == 0) {
    echo "<script>alert('Không tìm thấy sản phẩm!'); window.location='products.php';</script>";
    exit;
}
$product = mysqli_fetch_assoc($product_res);

// Active tab
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'price';

// ---- Xử lý cập nhật tỉ lệ lợi nhuận ----
$msg_success = '';
$msg_error   = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_margin'])) {
    $new_margin = (float)$_POST['profit_margin'];
    if ($new_margin < 0 || $new_margin > 1000) {
        $msg_error = 'Tỉ lệ lợi nhuận phải từ 0% đến 1000%.';
    } else {
        $new_selling = round($product['original_price'] * (1 + $new_margin / 100));
        $sql = "UPDATE products SET profit_margin='$new_margin', selling_price='$new_selling' WHERE id='$id'";
        if (mysqli_query($conn, $sql)) {
            $product['profit_margin'] = $new_margin;
            $product['selling_price'] = $new_selling;
            $msg_success = 'Đã cập nhật tỉ lệ lợi nhuận thành công! Giá bán mới: ' . fmt_price($new_selling) . ' $';
        } else {
            $msg_error = 'Lỗi khi cập nhật: ' . mysqli_error($conn);
        }
    }
    $active_tab = 'price';
}

// ---- Dữ liệu Tab Quản lý tồn kho ----
$check_date   = isset($_GET['check_date']) ? $_GET['check_date'] : '';
$range_from   = isset($_GET['range_from']) ? $_GET['range_from'] : '';
$range_to     = isset($_GET['range_to'])   ? $_GET['range_to']   : '';

// Tổng nhập / xuất theo khoảng thời gian
$total_import = 0;
$total_export = 0;
if ($range_from && $range_to) {
    $q_import = mysqli_query(
        $conn,
        "SELECT COALESCE(SUM(quantity_imported),0) as total
         FROM import_history
         WHERE product_id='$id'
           AND DATE(created_at) >= '$range_from'
           AND DATE(created_at) <= '$range_to'"
    );
    if ($row = mysqli_fetch_assoc($q_import)) $total_import = (int)$row['total'];

    $q_export = mysqli_query(
        $conn,
        "SELECT COALESCE(SUM(oi.quantity),0) as total
         FROM order_items oi
         INNER JOIN orders o ON oi.order_id = o.id
         WHERE oi.product_id='$id'
           AND DATE(o.created_at) >= '$range_from'
           AND DATE(o.created_at) <= '$range_to'"
    );
    if ($row = mysqli_fetch_assoc($q_export)) $total_export = (int)$row['total'];
}

// Tồn kho tại thời điểm check_date
$qty_at_date = null;
if ($check_date) {
    // SL nhập tính đến check_date
    $q_in = mysqli_query(
        $conn,
        "SELECT COALESCE(SUM(quantity_imported),0) as total
         FROM import_history
         WHERE product_id='$id' AND DATE(created_at) <= '$check_date'"
    );
    $imported_total = 0;
    if ($row = mysqli_fetch_assoc($q_in)) $imported_total = (int)$row['total'];

    // SL xuất (bán) tính đến check_date
    $q_out = mysqli_query(
        $conn,
        "SELECT COALESCE(SUM(oi.quantity),0) as total
         FROM order_items oi
         INNER JOIN orders o ON oi.order_id = o.id
         WHERE oi.product_id='$id' AND DATE(o.created_at) <= '$check_date'"
    );
    $exported_total = 0;
    if ($row = mysqli_fetch_assoc($q_out)) $exported_total = (int)$row['total'];

    $qty_at_date = $imported_total - $exported_total;
}

// Lịch sử nhập theo sản phẩm (cho tab giá)
$import_logs = mysqli_query(
    $conn,
    "SELECT ih.*, u.name as admin_name
     FROM import_history ih
     INNER JOIN users u ON ih.admin_id = u.id
     WHERE ih.product_id = '$id'
     ORDER BY ih.created_at DESC"
);
?>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card">
                    <div class="card-header" style="background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);">
                        <h4 style="color: white; margin: 0;">
                            <i class="material-icons" style="vertical-align: middle;">manage_search</i>
                            Quản lý: <strong><?= htmlspecialchars($product['name']) ?></strong>
                            <a href="edit-product.php?id=<?= $id ?>&<?= http_build_query(array_filter([
                                                                        'tensanpham' => $_GET['tensanpham'] ?? '',
                                                                        'loaisanpham' => $_GET['loaisanpham'] ?? '',
                                                                        'qtymin' => $_GET['qtymin'] ?? '',
                                                                        'qtymax' => $_GET['qtymax'] ?? '',
                                                                        'giamin' => $_GET['giamin'] ?? '',
                                                                        'giamax' => $_GET['giamax'] ?? '',
                                                                        'trangthai' => $_GET['trangthai'] ?? '',
                                                                        'sapxep' => $_GET['sapxep'] ?? 1,
                                                                        'theocot' => $_GET['theocot'] ?? 'id',
                                                                        'page' => $_GET['page'] ?? 1,
                                                                    ])) ?>" class="btn btn-danger btn-sm float-end">
                                <i class="fa fa-arrow-left"></i> Quay lại
                            </a>
                        </h4>
                    </div>

                    <div class="card-body">

                        <!-- Tab nav -->
                        <ul class="nav nav-tabs mb-4" id="manageTabs">
                            <li class="nav-item">
                                <a class="nav-link <?= $active_tab === 'price' ? 'active' : '' ?>"
                                    href="product-manage.php?id=<?= $id ?>&tab=price"
                                    style="<?= $active_tab === 'price' ? 'color:#F57C00; border-bottom:3px solid #F57C00; font-weight:600;' : '' ?>">
                                    <i class="fa fa-tag"></i> Quản lý giá bán
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $active_tab === 'stock' ? 'active' : '' ?>"
                                    href="product-manage.php?id=<?= $id ?>&tab=stock"
                                    style="<?= $active_tab === 'stock' ? 'color:#F57C00; border-bottom:3px solid #F57C00; font-weight:600;' : '' ?>">
                                    <i class="fa fa-warehouse"></i> Quản lý tồn kho
                                </a>
                            </li>
                        </ul>

                        <!-- ===================== TAB GIÁ BÁN ===================== -->
                        <?php if ($active_tab === 'price'): ?>

                            <?php if ($msg_success): ?>
                                <div class="alert alert-success"><?= $msg_success ?></div>
                            <?php endif; ?>
                            <?php if ($msg_error): ?>
                                <div class="alert alert-danger"><?= $msg_error ?></div>
                            <?php endif; ?>

                            <!-- Tóm tắt giá hiện tại -->
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

                            <!-- Form cập nhật tỉ lệ lợi nhuận -->
                            <div style="background:#FFFDE7;padding:20px;border-radius:10px;border:1px solid #F9A825;margin-bottom:30px;">
                                <h5 style="color:#F57F17;margin-bottom:16px;">
                                    <i class="fa fa-edit"></i> Cập nhật tỉ lệ lợi nhuận
                                </h5>
                                <form method="POST" action="product-manage.php?id=<?= $id ?>&tab=price">
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
                                            <div id="previewPrice"
                                                style="font-size:18px;font-weight:700;color:#388E3C;padding:8px 0;">
                                                <?= fmt_price($product['selling_price']) ?> $
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="submit" name="update_margin" class="btn btn-warning w-100"
                                                style="font-weight:600;">
                                                <i class="fa fa-save"></i> Lưu thay đổi
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Bảng lịch sử lô nhập hàng -->
                            <h5 style="color:#F57C00;margin-bottom:12px;">
                                <i class="fa fa-history"></i> Lịch sử các lô nhập — giá vốn & giá bán
                            </h5>
                            <?php if ($import_logs && mysqli_num_rows($import_logs) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover" style="font-size:14px;">
                                        <thead style="background:linear-gradient(135deg,#FFB74D,#FFA726);color:white;">
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
                                            <?php
                                            $stt = 1;
                                            mysqli_data_seek($import_logs, 0);
                                            foreach ($import_logs as $log): ?>
                                                <tr>
                                                    <td class="text-center"><?= $stt++ ?></td>
                                                    <td><?= $log['created_at'] ?></td>
                                                    <td class="text-center">
                                                        <span class="badge bg-primary"><?= $log['quantity_imported'] ?></span>
                                                    </td>
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

                            <!-- ===================== TAB TỒN KHO ===================== -->
                        <?php elseif ($active_tab === 'stock'): ?>

                            <!-- Cảnh báo tồn kho hiện tại -->
                            <?php
                            $current_qty = (int)$product['qty'];
                            $stock_color   = $current_qty >= 10 ? '#388E3C' : ($current_qty > 0 ? '#F57C00' : '#C62828');
                            $stock_bg      = $current_qty >= 10 ? '#E8F5E9'  : ($current_qty > 0 ? '#FFF3E0'  : '#FFEBEE');
                            $stock_border  = $current_qty >= 10 ? '#388E3C'  : ($current_qty > 0 ? '#F57C00'  : '#C62828');
                            if ($current_qty >= 10)      $stock_label = '✅ Còn hàng';
                            elseif ($current_qty > 0)    $stock_label = '⚠️ Sắp hết hàng';
                            else                         $stock_label = '❌ Hết hàng';
                            ?>
                            <div style="background:<?= $stock_bg ?>;padding:20px;border-radius:10px;border-left:5px solid <?= $stock_border ?>;margin-bottom:28px;display:flex;align-items:center;gap:20px;">
                                <div style="font-size:40px;"><?= $current_qty >= 10 ? '📦' : ($current_qty > 0 ? '⚠️' : '🚫') ?></div>
                                <div>
                                    <div style="font-size:13px;color:<?= $stock_color ?>;font-weight:700;text-transform:uppercase;margin-bottom:2px;">Tồn kho hiện tại</div>
                                    <div style="font-size:32px;font-weight:700;color:<?= $stock_color ?>;"><?= $current_qty ?> sản phẩm</div>
                                    <div style="font-size:15px;font-weight:600;color:<?= $stock_color ?>;"><?= $stock_label ?></div>
                                    <?php if ($current_qty < 10 && $current_qty > 0): ?>
                                        <div style="font-size:12px;color:#8D6E63;margin-top:4px;">Cần nhập thêm hàng để đảm bảo tồn kho!</div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="row g-3">
                                <!-- Tra cứu tồn kho tại thời điểm -->
                                <div class="col-md-6">
                                    <div style="background:#F8FAFF;border:1.5px solid #DBEAFE;border-radius:12px;padding:20px;height:100%;">
                                        <div style="font-size:13px;font-weight:700;color:#1565C0;margin-bottom:14px;display:flex;align-items:center;gap:6px;">
                                            <i class="material-icons" style="font-size:18px;">event</i>
                                            Tra cứu tồn kho tại thời điểm
                                        </div>
                                        <form method="GET" action="product-manage.php">
                                            <input type="hidden" name="id" value="<?= $id ?>">
                                            <input type="hidden" name="tab" value="stock">
                                            <?php if ($range_from): ?><input type="hidden" name="range_from" value="<?= $range_from ?>"><?php endif; ?>
                                            <?php if ($range_to): ?><input type="hidden" name="range_to" value="<?= $range_to ?>"><?php endif; ?>
                                            <label style="font-size:12px;font-weight:600;color:#555;margin-bottom:6px;display:block;">Chọn ngày muốn tra cứu</label>
                                            <div style="display:flex;gap:8px;">
                                                <input type="date" name="check_date" class="form-control"
                                                    value="<?= $check_date ?>" max="<?= date('Y-m-d') ?>"
                                                    style="border-color:#BFDBFE;font-size:14px;border-radius:8px;">
                                                <button type="submit" class="btn btn-warning" style="font-weight:700;white-space:nowrap;padding:0 18px;border-radius:8px;background:linear-gradient(135deg,#FFA726,#F57C00);border:none;color:#fff;">
                                                    TRA CỨU
                                                </button>
                                            </div>
                                        </form>
                                        <?php if ($check_date && $qty_at_date !== null): ?>
                                            <?php
                                            $qd = max(0, $qty_at_date);
                                            $qd_c  = $qd >= 10 ? '#1B5E20' : ($qd > 0 ? '#E65100' : '#B71C1C');
                                            $qd_bg = $qd >= 10 ? '#E8F5E9' : ($qd > 0 ? '#FFF3E0' : '#FFEBEE');
                                            $qd_bd = $qd >= 10 ? '#4CAF50' : ($qd > 0 ? '#FF9800' : '#EF5350');
                                            $qd_lbl = $qd >= 10 ? 'Còn hàng' : ($qd > 0 ? 'Sắp hết' : 'Hết hàng');
                                            ?>
                                            <div style="margin-top:14px;background:<?= $qd_bg ?>;border:1.5px solid <?= $qd_bd ?>;border-radius:10px;padding:14px 16px;display:flex;align-items:center;gap:14px;">
                                                <div style="flex:1;">
                                                    <div style="font-size:12px;font-weight:600;text-transform:uppercase;color:<?= $qd_c ?>;margin-bottom:2px;">Tồn kho ngày <?= $check_date ?></div>
                                                    <div style="font-size:28px;font-weight:800;color:<?= $qd_c ?>;line-height:1;"><?= $qd ?> <span style="font-size:15px;font-weight:600;">sản phẩm</span></div>
                                                    <div style="font-size:12px;opacity:.75;margin-top:4px;">Tổng nhập: <strong><?= $imported_total ?></strong> &nbsp;|&nbsp; Tổng bán: <strong><?= $exported_total ?></strong></div>
                                                </div>
                                                <span style="background:<?= $qd_bd ?>;color:#fff;padding:4px 14px;border-radius:20px;font-size:13px;font-weight:700;white-space:nowrap;"><?= $qd_lbl ?></span>
                                            </div>
                                        <?php elseif ($check_date): ?>
                                            <div style="margin-top:12px;padding:10px 14px;background:#F5F5F5;border-radius:8px;font-size:13px;color:#888;">
                                                Không có dữ liệu cho ngày này.
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Báo cáo nhập - xuất theo khoảng thời gian -->
                                <div class="col-md-6">
                                    <div style="background:#F8FAFF;border:1.5px solid #DBEAFE;border-radius:12px;padding:20px;height:100%;">
                                        <div style="font-size:13px;font-weight:700;color:#1565C0;margin-bottom:14px;display:flex;align-items:center;gap:6px;">
                                            <i class="material-icons" style="font-size:18px;">bar_chart</i>
                                            Báo cáo nhập – xuất theo khoảng thời gian
                                        </div>
                                        <form method="GET" action="product-manage.php">
                                            <input type="hidden" name="id" value="<?= $id ?>">
                                            <input type="hidden" name="tab" value="stock">
                                            <?php if ($check_date): ?><input type="hidden" name="check_date" value="<?= $check_date ?>"><?php endif; ?>
                                            <div class="row g-2 mb-3">
                                                <div class="col-6">
                                                    <label style="font-size:12px;font-weight:600;color:#555;margin-bottom:6px;display:block;">
                                                        <i class="material-icons" style="font-size:14px;vertical-align:middle;">arrow_forward</i> Từ ngày
                                                    </label>
                                                    <input type="date" name="range_from" class="form-control"
                                                        value="<?= $range_from ?>" style="border-color:#BFDBFE;font-size:14px;">
                                                </div>
                                                <div class="col-6">
                                                    <label style="font-size:12px;font-weight:600;color:#555;margin-bottom:6px;display:block;">
                                                        <i class="material-icons" style="font-size:14px;vertical-align:middle;">arrow_back</i> Đến ngày
                                                    </label>
                                                    <input type="date" name="range_to" class="form-control"
                                                        value="<?= $range_to ?>" max="<?= date('Y-m-d') ?>"
                                                        style="border-color:#BFDBFE;font-size:14px;">
                                                </div>
                                            </div>
                                            <button type="submit" class="btn w-100" style="font-weight:700;background:linear-gradient(135deg,#FFA726,#F57C00);border:none;color:#fff;border-radius:8px;padding:10px;">
                                                <i class="material-icons" style="font-size:16px;vertical-align:middle;">search</i> XEM BÁO CÁO
                                            </button>
                                        </form>
                                        <?php if ($range_from && $range_to): ?>
                                            <div style="margin-top:16px;">
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <div style="background:#EFF6FF;border:1.5px solid #BFDBFE;border-radius:10px;padding:14px;text-align:center;">
                                                            <div style="font-size:11px;color:#1565C0;font-weight:700;text-transform:uppercase;margin-bottom:4px;">📥 Tổng nhập</div>
                                                            <div style="font-size:28px;font-weight:800;color:#1565C0;"><?= $total_import ?></div>
                                                            <div style="font-size:11px;color:#64748B;">sản phẩm</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div style="background:#FFF0F0;border:1.5px solid #FECACA;border-radius:10px;padding:14px;text-align:center;">
                                                            <div style="font-size:11px;color:#B91C1C;font-weight:700;text-transform:uppercase;margin-bottom:4px;">📤 Tổng xuất</div>
                                                            <div style="font-size:28px;font-weight:800;color:#B91C1C;"><?= $total_export ?></div>
                                                            <div style="font-size:11px;color:#64748B;">sản phẩm</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php $diff = $total_import - $total_export; ?>
                                                <div style="margin-top:10px;background:<?= $diff >= 0 ? '#F0FDF4' : '#FFF0F0' ?>;border:1.5px solid <?= $diff >= 0 ? '#BBF7D0' : '#FECACA' ?>;border-radius:10px;padding:12px 16px;display:flex;justify-content:space-between;align-items:center;">
                                                    <span style="font-size:13px;color:#475569;font-weight:600;">
                                                        Chênh lệch &nbsp;<span style="color:#94A3B8;font-weight:400;">(<?= $range_from ?> → <?= $range_to ?>)</span>
                                                    </span>
                                                    <span style="font-size:20px;font-weight:800;color:<?= $diff >= 0 ? '#15803D' : '#B91C1C' ?>;">
                                                        <?= ($diff >= 0 ? '+' : '') . $diff ?>
                                                    </span>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div><!-- row -->

                        <?php endif; ?>

                    </div><!-- card-body -->
                </div><!-- card -->

            </div>
        </div>
    </div>
</body>

<script>
    // Preview giá bán khi nhập % lợi nhuận
    const marginInput = document.getElementById('marginInput');
    const previewPrice = document.getElementById('previewPrice');
    const basePrice = <?= (float)$product['original_price'] ?>;

    if (marginInput && previewPrice) {
        marginInput.addEventListener('input', function() {
            const margin = parseFloat(this.value) || 0;
            const selling = Math.round(basePrice * (1 + margin / 100));
            previewPrice.textContent = selling.toLocaleString('vi-VN') + ' $';
        });
    }
</script>

<?php include("../admin/includes/footer.php"); ?>