<?php
include("../admin/includes/header.php");

// Lấy danh sách sản phẩm
$all_products_res = getAll("products");
$all_products = [];
while ($row = mysqli_fetch_assoc($all_products_res)) {
    $all_products[] = $row;
}
// Lấy danh sách danh mục
$all_categories_res = getAll("categories");
$all_categories = [];
while ($row = mysqli_fetch_assoc($all_categories_res)) {
    $all_categories[] = $row;
}
// Sản phẩm đang chọn
$selected_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$product     = null;

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'all';
if (!in_array($active_tab, ['all', 'check', 'report'], true)) {
    $active_tab = 'all';
}

// Lưu & lấy quy định tồn kho (lưu vào DB để giữ sau đăng xuất)
mysqli_query(
    $conn,
    "CREATE TABLE IF NOT EXISTS settings (
        setting_key VARCHAR(100) PRIMARY KEY,
        setting_value VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
);

$stored_threshold = null;
$setting_res = mysqli_query(
    $conn,
    "SELECT setting_value FROM settings WHERE setting_key = 'stock_threshold' LIMIT 1"
);
if ($setting_res && mysqli_num_rows($setting_res) > 0) {
    $stored_threshold = (int)mysqli_fetch_assoc($setting_res)['setting_value'];
}

if ($selected_id) {
    $res     = getByID("products", $selected_id);
    $product = mysqli_fetch_assoc($res);
}

$id = $selected_id;

// Tra cứu tồn kho tại thời điểm (lọc theo danh mục)
$check_date = isset($_GET['check_date']) ? $_GET['check_date'] : '';
$check_category = isset($_GET['check_category']) ? (int)$_GET['check_category'] : 0;
$check_results = [];

// Báo cáo nhập - xuất theo khoảng thời gian
$range_from   = isset($_GET['range_from']) ? $_GET['range_from'] : '';
$range_to     = isset($_GET['range_to'])   ? $_GET['range_to']   : '';
$total_import = 0;
$total_export = 0;

if ($range_from && $range_to) {
    $product_filter = "";
    $q_import = mysqli_query(
        $conn,
        "SELECT COALESCE(SUM(ih.quantity_imported),0) as total
                 FROM import_history ih
                 INNER JOIN import_receipts ir ON ih.receipt_id = ir.id
                 WHERE ir.import_date >= '$range_from'
                     AND ir.import_date <= '$range_to'
                     AND ir.status = 1
                     $product_filter"
    );
    if ($r = mysqli_fetch_assoc($q_import)) $total_import = (int)$r['total'];

    $q_export = mysqli_query(
        $conn,
        "SELECT COALESCE(SUM(od.quantity),0) as total
                 FROM order_detail od
                 INNER JOIN orders o ON od.order_id = o.id
                 WHERE DATE(o.created_at) >= '$range_from'
                     AND o.status != 5
                     AND DATE(o.created_at) <= '$range_to'" . ($product ? " AND od.product_id='$id'" : "")
    );
    if ($r = mysqli_fetch_assoc($q_export)) $total_export = (int)$r['total'];
}

// Quy định tồn kho
$stock_threshold = $stored_threshold !== null ? max(1, (int)$stored_threshold) : 10;
if (isset($_GET['stock_threshold'])) {
    $stock_threshold = max(1, (int)$_GET['stock_threshold']);
    $value = mysqli_real_escape_string($conn, (string)$stock_threshold);
    mysqli_query(
        $conn,
        "REPLACE INTO settings (setting_key, setting_value) VALUES ('stock_threshold', '$value')"
    );
}
$stock_out_threshold = 0;

// Danh sách sắp hết hàng (qty < threshold)
$low_stock_res = mysqli_query(
    $conn,
    "SELECT id, name, qty FROM products WHERE qty < $stock_threshold ORDER BY qty ASC"
);
$low_stock_list = [];
while ($ls = mysqli_fetch_assoc($low_stock_res)) $low_stock_list[] = $ls;

// Kết quả tra cứu tồn kho theo danh mục + thời điểm
if ($check_date && $check_category > 0) {
    $products_q = mysqli_query($conn, "SELECT id, name, qty FROM products WHERE category_id = '$check_category' ORDER BY name ASC");
    $products_list = [];
    while ($row = mysqli_fetch_assoc($products_q)) {
        $products_list[] = $row;
    }

    foreach ($products_list as $p) {
        $pid = (int)$p['id'];
        $current_qty = (int)$p['qty'];

        $q_in_after = mysqli_query($conn, "
            SELECT COALESCE(SUM(ih.quantity_imported),0) as total
            FROM import_history ih
            INNER JOIN import_receipts ir ON ih.receipt_id = ir.id
            WHERE ih.product_id='$pid'
              AND ir.status = 1
              AND ir.import_date > '$check_date'
        ");
        $imported_after = (int)mysqli_fetch_assoc($q_in_after)['total'];

        $q_out_after = mysqli_query($conn, "
            SELECT COALESCE(SUM(od.quantity),0) as total
            FROM order_detail od
            INNER JOIN orders o ON od.order_id = o.id
            WHERE od.product_id='$pid'
              AND o.status != 5
              AND DATE(o.created_at) > '$check_date'
        ");
        $exported_after = (int)mysqli_fetch_assoc($q_out_after)['total'];

        $qty_at_date_item = $current_qty - $imported_after + $exported_after;

        $check_results[] = [
            'id' => $pid,
            'name' => $p['name'],
            'qty' => max(0, (int)$qty_at_date_item)
        ];
    }
}

// Dữ liệu popup báo cáo
$report_product_id = isset($_GET['report_product_id']) ? (int)$_GET['report_product_id'] : 0;
$report_view = isset($_GET['report_view']) ? $_GET['report_view'] : '';
$report_receipt_id = isset($_GET['receipt_id']) ? (int)$_GET['receipt_id'] : 0;
$report_order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

$report_rows = [];
$report_import_list = [];
$report_export_list = [];
$report_detail_rows = [];

if ($range_from && $range_to) {
    $report_sql = "
        SELECT p.id, p.name,
               COALESCE(imp.total_import, 0) AS total_import,
               COALESCE(exp.total_export, 0) AS total_export
        FROM products p
        LEFT JOIN (
            SELECT ih.product_id, SUM(ih.quantity_imported) AS total_import
            FROM import_history ih
            INNER JOIN import_receipts ir ON ih.receipt_id = ir.id
            WHERE ir.status = 1
              AND ir.import_date BETWEEN '$range_from' AND '$range_to'
            GROUP BY ih.product_id
        ) imp ON p.id = imp.product_id
        LEFT JOIN (
            SELECT od.product_id, SUM(od.quantity) AS total_export
            FROM order_detail od
            INNER JOIN orders o ON od.order_id = o.id
            WHERE o.status != 5
              AND DATE(o.created_at) BETWEEN '$range_from' AND '$range_to'
            GROUP BY od.product_id
        ) exp ON p.id = exp.product_id
        WHERE (imp.total_import IS NOT NULL OR exp.total_export IS NOT NULL)
        ORDER BY p.name ASC";
    $report_res = mysqli_query($conn, $report_sql);
    while ($row = mysqli_fetch_assoc($report_res)) {
        $report_rows[] = $row;
    }
}

if ($report_product_id && $range_from && $range_to) {
    $report_import_list_res = mysqli_query($conn, "
        SELECT ir.id, ir.code, ir.import_date,
               SUM(ih.quantity_imported) AS total_qty,
               SUM(ih.quantity_imported * ih.import_price) AS total_value
        FROM import_history ih
        INNER JOIN import_receipts ir ON ih.receipt_id = ir.id
        WHERE ih.product_id = '$report_product_id'
          AND ir.status = 1
          AND ir.import_date BETWEEN '$range_from' AND '$range_to'
        GROUP BY ir.id, ir.code, ir.import_date
        ORDER BY ir.import_date DESC
    ");
    while ($row = mysqli_fetch_assoc($report_import_list_res)) {
        $report_import_list[] = $row;
    }

    $report_export_list_res = mysqli_query($conn, "
        SELECT o.id, o.created_at,
               SUM(od.quantity) AS total_qty,
               SUM(od.quantity * od.selling_price) AS total_value
        FROM order_detail od
        INNER JOIN orders o ON od.order_id = o.id
        WHERE od.product_id = '$report_product_id'
          AND o.status != 5
          AND DATE(o.created_at) BETWEEN '$range_from' AND '$range_to'
        GROUP BY o.id, o.created_at
        ORDER BY o.created_at DESC
    ");
    while ($row = mysqli_fetch_assoc($report_export_list_res)) {
        $report_export_list[] = $row;
    }

    if ($report_view === 'receipt' && $report_receipt_id) {
        $detail_res = mysqli_query($conn, "
            SELECT ih.quantity_imported, ih.import_price, p.name
            FROM import_history ih
            INNER JOIN products p ON ih.product_id = p.id
            WHERE ih.receipt_id = '$report_receipt_id'
            ORDER BY ih.id ASC
        ");
        while ($row = mysqli_fetch_assoc($detail_res)) {
            $report_detail_rows[] = $row;
        }
    }

    if ($report_view === 'order' && $report_order_id) {
        $detail_res = mysqli_query($conn, "
            SELECT od.quantity, od.selling_price, p.name
            FROM order_detail od
            INNER JOIN products p ON od.product_id = p.id
            WHERE od.order_id = '$report_order_id'
            ORDER BY od.id ASC
        ");
        while ($row = mysqli_fetch_assoc($detail_res)) {
            $report_detail_rows[] = $row;
        }
    }
}
?>

<style>
    .stock-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        padding: 24px;
        margin-bottom: 24px;
    }

    .stock-card-title {
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .stock-select-wrap select {
        border: 2px solid #1976D2;
        border-radius: 10px;
        padding: 10px 16px;
        font-size: 15px;
        font-weight: 500;
        cursor: pointer;
        background: #fff;
        transition: border-color .2s;
        width: 100%;
    }

    .stock-select-wrap select:focus {
        outline: none;
        border-color: #0D47A1;
        box-shadow: 0 0 0 3px rgba(25, 118, 210, 0.15);
    }

    .stat-box {
        border-radius: 12px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .stat-box .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        flex-shrink: 0;
    }

    .stat-box .stat-label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        opacity: .75;
        margin-bottom: 2px;
    }

    .stat-box .stat-value {
        font-size: 28px;
        font-weight: 800;
        line-height: 1;
    }

    .stat-box .stat-sub {
        font-size: 12px;
        margin-top: 4px;
        opacity: .7;
    }

    .tool-box {
        background: #F8FAFF;
        border: 1.5px solid #DBEAFE;
        border-radius: 12px;
        padding: 20px;
    }

    .tool-box .tool-title {
        font-size: 13px;
        font-weight: 700;
        color: #1565C0;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .result-box {
        border-radius: 10px;
        padding: 16px;
        margin-top: 14px;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .result-box .result-num {
        font-size: 30px;
        font-weight: 800;
        line-height: 1;
    }

    .result-box .result-label {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 2px;
    }

    .result-box .result-detail {
        font-size: 12px;
        opacity: .75;
        margin-top: 4px;
    }

    .low-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: transform .15s, box-shadow .15s;
    }

    .low-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }
</style>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card">
                    <div class="card-header" style="background: linear-gradient(135deg, #1976D2 0%, #0D47A1 100%); border-radius: 12px 12px 0 0;">
                        <h4 style="color:white;margin:0;display:flex;align-items:center;gap:10px;">
                            <i class="material-icons">inventory_2</i>
                            Quản lý tồn kho
                        </h4>
                    </div>
                    <div class="card-body" style="padding: 24px;">

                        <div style="background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 100%); padding: 16px; border-radius: 10px; border-left: 4px solid #2196F3; margin-bottom: 20px;">
                            <h6 style="color: #1565C0; margin: 0 0 8px; font-weight: 700;">
                                <i class="fa fa-info-circle"></i> Quy định tồn kho
                            </h6>
                            <form method="GET" style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;margin:0;">
                                <input type="hidden" name="tab" value="<?= htmlspecialchars($active_tab) ?>">
                                <?php if ($range_from): ?><input type="hidden" name="range_from" value="<?= $range_from ?>"><?php endif; ?>
                                <?php if ($range_to): ?><input type="hidden" name="range_to" value="<?= $range_to ?>"><?php endif; ?>
                                <?php if ($check_date): ?><input type="hidden" name="check_date" value="<?= $check_date ?>"><?php endif; ?>
                                <?php if ($check_category): ?><input type="hidden" name="check_category" value="<?= $check_category ?>"><?php endif; ?>
                                <span style="color:#1976D2;font-weight:700;">Còn hàng khi số lượng ≥</span>
                                <input type="number" name="stock_threshold" min="1" value="<?= $stock_threshold ?>"
                                    style="width:80px;border:1.5px solid #90CAF9;border-radius:8px;padding:6px 10px;">
                                <button type="submit" class="btn" style="background:#1976D2;color:#fff;border-radius:8px;padding:6px 14px;font-weight:700;border:none;">Áp dụng</button>
                            </form>
                        </div>

                        <!-- Cảnh báo sắp hết hàng -->
                        <?php if (count($low_stock_list) > 0): ?>
                            <div style="background:linear-gradient(135deg,#FFF8E1,#FFF3CD);border:1.5px solid #FFC107;border-radius:12px;padding:18px 20px;margin-bottom:24px;">
                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
                                    <span style="font-size:20px;">⚠️</span>
                                    <span style="font-weight:700;color:#E65100;font-size:15px;">
                                        Cảnh báo tồn kho thấp — <?= count($low_stock_list) ?> sản phẩm cần chú ý
                                    </span>
                                </div>
                                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                                    <?php foreach ($low_stock_list as $ls): ?>
                                        <?php
                                        $is_out   = $ls['qty'] == 0;
                                        $bg_badge = $is_out ? '#FFEBEE' : '#FFF8E1';
                                        $bd_badge = $is_out ? '#EF9A9A' : '#FFD54F';
                                        $cl_badge = $is_out ? '#B71C1C' : '#E65100';
                                        $bg_pill  = $is_out ? '#EF5350' : '#FFA726';
                                        ?>
                                        <a href="manage-stock.php?product_id=<?= $ls['id'] ?>"
                                            class="low-badge"
                                            style="background:<?= $bg_badge ?>;border:1.5px solid <?= $bd_badge ?>;color:<?= $cl_badge ?>;">
                                            <?= htmlspecialchars($ls['name']) ?>
                                            <span style="background:<?= $bg_pill ?>;color:#fff;border-radius:10px;padding:1px 8px;font-size:12px;">
                                                <?= $is_out ? 'Hết hàng' : $ls['qty'] . ' còn' ?>
                                            </span>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Tabs điều hướng -->
                        <ul class="nav nav-tabs" style="margin-bottom:16px;">
                            <li class="nav-item">
                                <a class="nav-link <?= $active_tab === 'all' ? 'active' : '' ?>" href="manage-stock.php?tab=all">Tồn kho hiện tại</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $active_tab === 'check' ? 'active' : '' ?>" href="manage-stock.php?tab=check">Tra cứu tồn kho tại thời điểm</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $active_tab === 'report' ? 'active' : '' ?>" href="manage-stock.php?tab=report">Báo cáo nhập – xuất</a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <!-- Tab: Tồn kho hiện tại -->
                            <div class="tab-pane fade <?= $active_tab === 'all' ? 'show active' : '' ?>" id="tab-all">
                                <div class="stock-card" style="border: 1.5px solid #DBEAFE;">
                                    <div class="stock-card-title" style="color:#1565C0;">
                                        <i class="material-icons" style="font-size:20px;">inventory_2</i>
                                        Danh sách tồn kho tất cả sản phẩm
                                    </div>
                                    <div class="table-responsive" style="border:1.5px solid #DBEAFE;border-radius:10px;overflow:hidden;padding-bottom:0;">
                                        <style>
                                            .stock-table,
                                            .stock-table th,
                                            .stock-table td {
                                                border: 1px solid #DBEAFE !important;
                                            }

                                            .stock-table {
                                                table-layout: fixed;
                                                width: 100%;
                                                margin-bottom: 0;
                                            }

                                            .stock-table td:nth-child(2) {
                                                word-break: break-word;
                                                white-space: normal;
                                            }

                                            .stock-table tbody tr:last-child td {
                                                border-bottom: 1px solid #DBEAFE !important;
                                            }
                                        </style>
                                        <table class="table table-bordered table-hover stock-table">
                                            <thead style="background:#E3F2FD;color:#0D47A1;">
                                                <tr>
                                                    <th style="width:60px;">STT</th>
                                                    <th>Sản phẩm</th>
                                                    <th style="width:120px;">Tồn kho</th>
                                                    <th style="width:160px;">Trạng thái</th>
                                                    <th style="width:140px;">Thao tác</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $idx = 1;
                                                foreach ($all_products as $p):
                                                    $qty = (int)$p['qty'];
                                                    if ($qty >= $stock_threshold) {
                                                        $badge_bg = '#4CAF50';
                                                        $label = 'Còn hàng';
                                                    } elseif ($qty > 0) {
                                                        $badge_bg = '#FF9800';
                                                        $label = 'Sắp hết';
                                                    } else {
                                                        $badge_bg = '#EF5350';
                                                        $label = 'Hết hàng';
                                                    }
                                                ?>
                                                    <tr>
                                                        <td class="text-center"><?= $idx++ ?></td>
                                                        <td><?= htmlspecialchars($p['name']) ?></td>
                                                        <td class="text-center" style="font-weight:700;">
                                                            <?= $qty ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <span style="background:<?= $badge_bg ?>;color:#fff;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;">
                                                                <?= $label ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="import-stock.php?product_id=<?= $p['id'] ?>" class="btn btn-sm btn-primary">
                                                                <i class="fa fa-plus"></i> Nhập hàng
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab: Tra cứu tồn kho tại thời điểm -->
                            <div class="tab-pane fade <?= $active_tab === 'check' ? 'show active' : '' ?>" id="tab-check">
                                <div class="tool-box">
                                    <div class="tool-title">
                                        <i class="material-icons" style="font-size:18px;">event</i>
                                        Tra cứu tồn kho tại thời điểm
                                    </div>
                                    <form method="GET" action="manage-stock.php">
                                        <input type="hidden" name="tab" value="check">
                                        <?php if ($range_from): ?><input type="hidden" name="range_from" value="<?= $range_from ?>"><?php endif; ?>
                                        <?php if ($range_to): ?><input type="hidden" name="range_to" value="<?= $range_to ?>"><?php endif; ?>
                                        <?php if ($stock_threshold): ?><input type="hidden" name="stock_threshold" value="<?= $stock_threshold ?>"><?php endif; ?>
                                        <div class="row g-2 mb-3">
                                            <div class="col-md-6">
                                                <label style="font-size:15px;font-weight:600;color:#555;margin:6px;display:block;">Chọn loại sản phẩm</label>
                                                <select name="check_category" class="form-select" style="border-color:#BFDBFE;font-size:14px;border-radius:8px;">
                                                    <option value="0">-- Chọn loại sản phẩm --</option>
                                                    <?php foreach ($all_categories as $cat): ?>
                                                        <option value="<?= $cat['id'] ?>" <?= $check_category == (int)$cat['id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($cat['name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php if ($range_from): ?><input type="hidden" name="range_from" value="<?= $range_from ?>"><?php endif; ?>
                                        <?php if ($range_to): ?><input type="hidden" name="range_to" value="<?= $range_to ?>"><?php endif; ?>
                                        <label style="font-size:15px;font-weight:600;color:#555;margin:6px;display:block;">Chọn ngày muốn tra cứu</label>
                                        <div style="display:flex;gap:8px;">
                                            <input type="date" name="check_date" class="form-control"
                                                value="<?= $check_date ?>"
                                                style="border-color:#BFDBFE;font-size:14px;border-radius:8px;">
                                            <button type="submit" class="btn" style="font-weight:700;white-space:nowrap;padding:0 18px;border-radius:8px;background:linear-gradient(135deg,#FFA726,#F57C00);border:none;color:#fff;">
                                                TRA CỨU
                                            </button>
                                        </div>
                                    </form>
                                    <?php if ($check_date && $check_category > 0): ?>
                                        <?php if (!empty($check_results)): ?>
                                            <div class="table-responsive" style="margin-top:14px;">
                                                <table class="table table-bordered table-hover" style="border-color:#DBEAFE;">
                                                    <thead style="background:#E3F2FD;color:#0D47A1;">
                                                        <tr>
                                                            <th style="width:60px;">#</th>
                                                            <th>Sản phẩm</th>
                                                            <th style="width:140px;">Tồn kho</th>
                                                            <th style="width:140px;">Trạng thái</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($check_results as $idx => $row): ?>
                                                            <?php
                                                            $qd = (int)$row['qty'];
                                                            if ($qd >= $stock_threshold) {
                                                                $qd_bd = '#4CAF50';
                                                                $qd_lbl = 'Còn hàng';
                                                            } elseif ($qd > 0) {
                                                                $qd_bd = '#FF9800';
                                                                $qd_lbl = 'Sắp hết';
                                                            } else {
                                                                $qd_bd = '#EF5350';
                                                                $qd_lbl = 'Hết hàng';
                                                            }
                                                            ?>
                                                            <tr>
                                                                <td class="text-center"><?= $idx + 1 ?></td>
                                                                <td><?= htmlspecialchars($row['name']) ?></td>
                                                                <td class="text-center"><?= $qd ?></td>
                                                                <td class="text-center">
                                                                    <span style="background:<?= $qd_bd ?>;color:#fff;padding:4px 14px;border-radius:20px;font-size:12px;font-weight:700;white-space:nowrap;"><?= $qd_lbl ?></span>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php else: ?>
                                            <div style="margin-top:12px;padding:10px 14px;background:#F5F5F5;border-radius:8px;font-size:13px;color:#888;">
                                                Không tìm thấy sản phẩm thuộc loại này trong ngày đã chọn.
                                            </div>
                                        <?php endif; ?>
                                    <?php elseif ($check_date): ?>
                                        <div style="margin-top:12px;padding:10px 14px;background:#F5F5F5;border-radius:8px;font-size:13px;color:#888;">
                                            Vui lòng chọn loại sản phẩm để tra cứu.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Tab: Báo cáo nhập - xuất theo khoảng thời gian -->
                            <div class="tab-pane fade <?= $active_tab === 'report' ? 'show active' : '' ?>" id="tab-report">
                                <div class="tool-box">
                                    <div class="tool-title">
                                        <i class="material-icons" style="font-size:18px;">bar_chart</i>
                                        Báo cáo nhập – xuất theo khoảng thời gian
                                    </div>
                                    <form method="GET" action="manage-stock.php">
                                        <input type="hidden" name="tab" value="report">
                                        <?php if ($check_date): ?><input type="hidden" name="check_date" value="<?= $check_date ?>"><?php endif; ?>
                                        <div class="row g-2 mb-3">
                                            <div class="col-6">
                                                <label style="font-size:12px;font-weight:600;color:#555;margin-bottom:6px;display:block;">
                                                    <i class="material-icons" style="font-size:14px;vertical-align:middle;">arrow_forward</i> Từ ngày
                                                </label>
                                                <input type="date" name="range_from" class="form-control"
                                                    value="<?= $range_from ?>"
                                                    style="border-color:#BFDBFE;font-size:14px;">
                                            </div>
                                            <div class="col-6">
                                                <label style="font-size:12px;font-weight:600;color:#555;margin-bottom:6px;display:block;">
                                                    <i class="material-icons" style="font-size:14px;vertical-align:middle;">arrow_back</i> Đến ngày
                                                </label>
                                                <input type="date" name="range_to" class="form-control"
                                                    value="<?= $range_to ?>"
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
                                                        <div style="font-size:30px;font-weight:800;color:#1565C0;"><?= $total_import ?></div>
                                                        <div style="font-size:11px;color:#64748B;">sản phẩm</div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div style="background:#FFF0F0;border:1.5px solid #FECACA;border-radius:10px;padding:14px;text-align:center;">
                                                        <div style="font-size:11px;color:#B91C1C;font-weight:700;text-transform:uppercase;margin-bottom:4px;">📤 Tổng xuất</div>
                                                        <div style="font-size:30px;font-weight:800;color:#B91C1C;"><?= $total_export ?></div>
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

                                        <?php if (!empty($report_rows)): ?>
                                            <div style="margin-top:16px;">
                                                <table class="table table-bordered table-hover" style="border-color:#DBEAFE;width:100%;table-layout:fixed;">
                                                    <thead style="background:#E3F2FD;color:#0D47A1;">
                                                        <tr>
                                                            <th style="width:60px;">STT</th>
                                                            <th style="width:auto;">Sản phẩm</th>
                                                            <th style="width:110px;">Tổng nhập</th>
                                                            <th style="width:110px;">Tổng xuất</th>
                                                            <th style="width:110px;">Chênh lệch</th>
                                                            <th style="width:90px;">Chi tiết</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($report_rows as $idx => $row): ?>
                                                            <?php $diff_row = (int)$row['total_import'] - (int)$row['total_export']; ?>
                                                            <tr>
                                                                <td class="text-center" style="vertical-align:middle;">
                                                                    <?= $idx + 1 ?>
                                                                </td>
                                                                <td style="word-break:break-word;white-space:normal;">
                                                                    <?= htmlspecialchars($row['name']) ?>
                                                                </td>
                                                                <td class="text-center" style="color:#1565C0;font-weight:700;">+<?= (int)$row['total_import'] ?></td>
                                                                <td class="text-center" style="color:#B91C1C;font-weight:700;">-<?= (int)$row['total_export'] ?></td>
                                                                <td class="text-center" style="font-weight:800;color:<?= $diff_row >= 0 ? '#15803D' : '#B91C1C' ?>;">
                                                                    <?= ($diff_row >= 0 ? '+' : '') . $diff_row ?>
                                                                </td>
                                                                <td class="text-center">
                                                                    <a href="manage-stock.php?tab=report&range_from=<?= $range_from ?>&range_to=<?= $range_to ?>&stock_threshold=<?= $stock_threshold ?>&report_product_id=<?= $row['id'] ?>"
                                                                        class="btn btn-sm" style="background:#1976D2;color:#fff;border-radius:20px;padding:4px 12px;font-weight:700;">
                                                                        Xem
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php endif; ?>
                                    <?php elseif ($range_from || $range_to): ?>
                                        <div style="margin-top:12px;padding:10px 14px;background:#F5F5F5;border-radius:8px;font-size:13px;color:#888;">
                                            Vui lòng chọn đủ khoảng thời gian để xem báo cáo.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                    </div><!-- card-body -->
                </div><!-- card -->
            </div>
        </div>
    </div>

    <?php if ($report_product_id && $range_from && $range_to): ?>
        <?php
        $report_base = http_build_query([
            'tab' => 'report',
            'range_from' => $range_from,
            'range_to' => $range_to,
            'stock_threshold' => $stock_threshold,
            'report_product_id' => $report_product_id
        ]);
        ?>
        <div style="position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:2147483647;display:flex;align-items:center;justify-content:center;padding:20px;">
            <div style="background:#fff;max-width:980px;width:92vw;border-radius:14px;box-shadow:0 20px 40px rgba(0,0,0,0.2);padding:20px;max-height:90vh;overflow:auto;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                    <h5 style="margin:0;color:#1E293B;">Chi tiết nhập / xuất</h5>
                    <a href="manage-stock.php?tab=report&range_from=<?= $range_from ?>&range_to=<?= $range_to ?>&stock_threshold=<?= $stock_threshold ?>" style="text-decoration:none;font-weight:700;color:#EF4444;">Đóng</a>
                </div>

                <?php if ($report_view === 'receipt' && $report_receipt_id): ?>
                    <div style="margin-bottom:12px;">
                        <a href="manage-stock.php?<?= $report_base ?>" class="btn btn-sm btn-secondary">← Quay lại danh sách</a>
                    </div>
                    <h6 style="margin-bottom:10px;color:#0F172A;">Chi tiết phiếu nhập #<?= $report_receipt_id ?></h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead style="background:#E3F2FD;">
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th style="width:120px;">Số lượng</th>
                                    <th style="width:140px;">Giá nhập</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($report_detail_rows)): ?>
                                    <?php foreach ($report_detail_rows as $row): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['name']) ?></td>
                                            <td class="text-center"><?= (int)$row['quantity_imported'] ?></td>
                                            <td class="text-end">$<?= number_format($row['import_price'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">Không có dữ liệu.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php elseif ($report_view === 'order' && $report_order_id): ?>
                    <div style="margin-bottom:12px;">
                        <a href="manage-stock.php?<?= $report_base ?>" class="btn btn-sm btn-secondary">← Quay lại danh sách</a>
                    </div>
                    <h6 style="margin-bottom:10px;color:#0F172A;">Chi tiết đơn hàng #<?= $report_order_id ?></h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead style="background:#E3F2FD;">
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th style="width:120px;">Số lượng</th>
                                    <th style="width:140px;">Giá bán</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($report_detail_rows)): ?>
                                    <?php foreach ($report_detail_rows as $row): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['name']) ?></td>
                                            <td class="text-center"><?= (int)$row['quantity'] ?></td>
                                            <td class="text-end">$<?= number_format($row['selling_price'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">Không có dữ liệu.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 style="color:#1565C0;">Chi tiết nhập hàng</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead style="background:#E3F2FD;">
                                        <tr>
                                            <th>Mã phiếu</th>
                                            <th style="width:100px;">Ngày</th>
                                            <th style="width:90px;">SL</th>
                                            <th style="width:120px;">Giá nhập</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($report_import_list)): ?>
                                            <?php foreach ($report_import_list as $row): ?>
                                                <?php $code = $row['code'] ?: ('PN' . str_pad($row['id'], 3, '0', STR_PAD_LEFT)); ?>
                                                <tr>
                                                    <td>
                                                        <a href="manage-stock.php?<?= $report_base ?>&report_view=receipt&receipt_id=<?= $row['id'] ?>" style="color:#1976D2;font-weight:700;"><?= htmlspecialchars($code) ?></a>
                                                    </td>
                                                    <td><?= date('d/m/Y', strtotime($row['import_date'])) ?></td>
                                                    <td class="text-center"><?= (int)$row['total_qty'] ?></td>
                                                    <td class="text-end">$<?= number_format($row['total_value'], 2) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center">Không có dữ liệu.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 style="color:#1565C0;">Chi tiết xuất (đơn hàng)</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead style="background:#E3F2FD;">
                                        <tr>
                                            <th>Mã đơn</th>
                                            <th style="width:100px;">Ngày</th>
                                            <th style="width:90px;">SL</th>
                                            <th style="width:120px;">Giá bán</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($report_export_list)): ?>
                                            <?php foreach ($report_export_list as $row): ?>
                                                <tr>
                                                    <td>
                                                        <a href="manage-stock.php?<?= $report_base ?>&report_view=order&order_id=<?= $row['id'] ?>" style="color:#1976D2;font-weight:700;">#<?= $row['id'] ?></a>
                                                    </td>
                                                    <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                                                    <td class="text-center"><?= (int)$row['total_qty'] ?></td>
                                                    <td class="text-end">$<?= number_format($row['total_value'], 2) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center">Không có dữ liệu.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</body>

<?php include("../admin/includes/footer.php"); ?>