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
$product     = null;

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'all';
if (!in_array($active_tab, ['all', 'check', 'report'], true)) {
    $active_tab = 'all';
}

if ($selected_id) {
    $res     = getByID("products", $selected_id);
    $product = mysqli_fetch_assoc($res);
}

$id = $selected_id;

// Tra cứu tồn kho tại thời điểm
$check_date     = isset($_GET['check_date'])  ? $_GET['check_date']  : '';
$qty_at_date    = null;
$imported_total = 0;
$exported_total = 0;

if ($check_date) {
    $product_filter = $product ? "AND product_id='$id'" : "";
    $q_in = mysqli_query(
        $conn,
        "SELECT COALESCE(SUM(quantity_imported),0) as total
         FROM import_history
         WHERE DATE(created_at) <= '$check_date' $product_filter"
    );
    if ($r = mysqli_fetch_assoc($q_in)) $imported_total = (int)$r['total'];

    $q_out = mysqli_query(
        $conn,
        "SELECT COALESCE(SUM(od.quantity),0) as total
         FROM order_detail od
         INNER JOIN orders o ON od.order_id = o.id
         WHERE DATE(o.created_at) <= '$check_date'" . ($product ? " AND od.product_id='$id'" : "")
    );
    if ($r = mysqli_fetch_assoc($q_out)) $exported_total = (int)$r['total'];

    $qty_at_date = $imported_total - $exported_total;
}

// Báo cáo nhập - xuất theo khoảng thời gian
$range_from   = isset($_GET['range_from']) ? $_GET['range_from'] : '';
$range_to     = isset($_GET['range_to'])   ? $_GET['range_to']   : '';
$total_import = 0;
$total_export = 0;

if ($range_from && $range_to) {
    $product_filter = $product ? "AND product_id='$id'" : "";
    $q_import = mysqli_query(
        $conn,
        "SELECT COALESCE(SUM(quantity_imported),0) as total
                 FROM import_history
                 WHERE DATE(created_at) >= '$range_from'
                     AND DATE(created_at) <= '$range_to' $product_filter"
    );
    if ($r = mysqli_fetch_assoc($q_import)) $total_import = (int)$r['total'];

    $q_export = mysqli_query(
        $conn,
        "SELECT COALESCE(SUM(od.quantity),0) as total
                 FROM order_detail od
                 INNER JOIN orders o ON od.order_id = o.id
                 WHERE DATE(o.created_at) >= '$range_from'
                     AND DATE(o.created_at) <= '$range_to'" . ($product ? " AND od.product_id='$id'" : "")
    );
    if ($r = mysqli_fetch_assoc($q_export)) $total_export = (int)$r['total'];
}

// Danh sách sắp hết hàng (qty < 10)
$low_stock_res = mysqli_query(
    $conn,
    "SELECT id, name, qty FROM products WHERE qty < 10 ORDER BY qty ASC"
);
$low_stock_list = [];
while ($ls = mysqli_fetch_assoc($low_stock_res)) $low_stock_list[] = $ls;
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
                                                    if ($qty >= 10) {
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
                                        <label style="font-size:12px;font-weight:600;color:#555;margin-bottom:6px;display:block;">Chọn ngày muốn tra cứu</label>
                                        <div style="display:flex;gap:8px;">
                                            <input type="date" name="check_date" class="form-control"
                                                value="<?= $check_date ?>" max="<?= date('Y-m-d') ?>"
                                                style="border-color:#BFDBFE;font-size:14px;border-radius:8px;">
                                            <button type="submit" class="btn" style="font-weight:700;white-space:nowrap;padding:0 18px;border-radius:8px;background:linear-gradient(135deg,#FFA726,#F57C00);border:none;color:#fff;">
                                                TRA CỨU
                                            </button>
                                        </div>
                                    </form>

                                    <?php if ($check_date && $qty_at_date !== null): ?>
                                        <?php
                                        $qd = max(0, $qty_at_date);
                                        if ($qd >= 10) {
                                            $qd_c = '#1B5E20';
                                            $qd_bg = '#E8F5E9';
                                            $qd_bd = '#4CAF50';
                                            $qd_lbl = 'Còn hàng';
                                        } elseif ($qd > 0) {
                                            $qd_c = '#E65100';
                                            $qd_bg = '#FFF3E0';
                                            $qd_bd = '#FF9800';
                                            $qd_lbl = 'Sắp hết';
                                        } else {
                                            $qd_c = '#B71C1C';
                                            $qd_bg = '#FFEBEE';
                                            $qd_bd = '#EF5350';
                                            $qd_lbl = 'Hết hàng';
                                        }
                                        ?>
                                        <div class="result-box" style="background:<?= $qd_bg ?>;border:1.5px solid <?= $qd_bd ?>;">
                                            <div>
                                                <div class="result-label" style="color:<?= $qd_c ?>;">Tồn kho ngày <?= $check_date ?></div>
                                                <div class="result-num" style="color:<?= $qd_c ?>;"><?= $qd ?> <span style="font-size:16px;font-weight:600;">sản phẩm</span></div>
                                                <div class="result-detail">Tổng nhập: <strong><?= $imported_total ?></strong> &nbsp;|&nbsp; Tổng bán: <strong><?= $exported_total ?></strong></div>
                                            </div>
                                            <span style="background:<?= $qd_bd ?>;color:#fff;padding:4px 14px;border-radius:20px;font-size:13px;font-weight:700;margin-left:auto;white-space:nowrap;"><?= $qd_lbl ?></span>
                                        </div>
                                    <?php elseif ($check_date): ?>
                                        <div style="margin-top:12px;padding:10px 14px;background:#F5F5F5;border-radius:8px;font-size:13px;color:#888;">
                                            Không có dữ liệu cho ngày này.
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
                                                    value="<?= $range_from ?>" max="<?= date('Y-m-d') ?>"
                                                    style="border-color:#BFDBFE;font-size:14px;">
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
</body>

<?php include("../admin/includes/footer.php"); ?>