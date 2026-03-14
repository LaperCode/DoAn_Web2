<?php
include("../admin/includes/header.php");

// Tabs
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'margin';
if (!in_array($active_tab, ['margin', 'lookup'], true)) {
    $active_tab = 'margin';
}

// Danh mục
$all_categories_res = getAll("categories");
$all_categories = [];
while ($row = mysqli_fetch_assoc($all_categories_res)) {
    $all_categories[] = $row;
}

// Tìm kiếm
$search_name = isset($_GET['search_name']) ? trim($_GET['search_name']) : '';
$search_category = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

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
                $msg_success = 'Đã cập nhật tỉ lệ lợi nhuận cho sản phẩm <strong>' . htmlspecialchars($p['name']) . '</strong>. Giá bán mới: <strong>' . fmt_price($new_selling) . ' $</strong>';
            } else {
                $msg_error = 'Lỗi: ' . mysqli_error($conn);
            }
        }
    }
    $active_tab = 'margin';
}

// Danh sách sản phẩm theo bộ lọc
$product_conditions = [];
if ($search_name !== '') {
    $safe_name = mysqli_real_escape_string($conn, $search_name);
    $product_conditions[] = "p.name LIKE '%$safe_name%'";
}
if ($search_category) {
    $product_conditions[] = "p.category_id = '$search_category'";
}
$product_where = $product_conditions ? 'WHERE ' . implode(' AND ', $product_conditions) : '';

$products_res = mysqli_query(
    $conn,
    "SELECT p.*, c.name as category_name
     FROM products p
     LEFT JOIN categories c ON p.category_id = c.id
     $product_where
     ORDER BY p.id DESC"
);
$products_list = [];
while ($row = mysqli_fetch_assoc($products_res)) {
    $products_list[] = $row;
}

// Tra cứu lịch sử lô nhập theo bộ lọc
$import_conditions = $product_conditions;
$import_where = $import_conditions ? 'WHERE ' . implode(' AND ', $import_conditions) : '';

$import_logs = mysqli_query(
    $conn,
    "SELECT ih.*, p.name as product_name, c.name as category_name, u.name as admin_name
     FROM import_history ih
     INNER JOIN products p ON ih.product_id = p.id
     LEFT JOIN categories c ON p.category_id = c.id
     LEFT JOIN users u ON ih.admin_id = u.id
     $import_where
     ORDER BY ih.created_at DESC"
);
?>

<style>
    .price-table {
        table-layout: auto;
        width: 100%;
    }

    .price-table th,
    .price-table td {
        white-space: normal;
        word-break: break-word;
        vertical-align: middle;
    }

    .price-table .col-stt {
        width: 44px;
        text-align: center;
    }

    .price-table .col-number {
        width: 110px;
    }

    .price-table .col-actions {
        width: 140px;
    }

    .price-table .col-time {
        min-width: 150px;
    }

    .price-table .col-product {
        min-width: 260px;
    }

    .price-table .col-category {
        min-width: 170px;
    }

    .price-table .col-profit {
        min-width: 160px;
    }

    .margin-edit-form {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin: 0;
    }

    .margin-edit-form .margin-input {
        width: 90px;
        border-radius: 10px;
        border: 1px solid #E0E0E0;
        text-align: center;
        padding: 0 10px;
        height: 36px;
        box-sizing: border-box;
        font-size: 14px;
    }

    .margin-edit-form .btn-save {
        border-radius: 10px;
        padding: 0 18px;
        font-weight: 600;
        box-shadow: 0 4px 10px rgba(255, 152, 0, 0.2);
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        height: 34px;
        box-sizing: border-box;
        font-size: 14px;
    }

    .price-table td,
    .price-table th {
        padding: 14px 12px;
    }
</style>

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

                        <?php if ($msg_success): ?>
                            <div class="alert alert-success"><?= $msg_success ?></div>
                        <?php endif; ?>
                        <?php if ($msg_error): ?>
                            <div class="alert alert-danger"><?= $msg_error ?></div>
                        <?php endif; ?>

                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link <?= $active_tab === 'margin' ? 'active' : '' ?>" href="manage-price.php?tab=margin&search_name=<?= urlencode($search_name) ?>&category_id=<?= $search_category ?>">
                                    Tỷ lệ lợi nhuận theo sản phẩm
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link <?= $active_tab === 'lookup' ? 'active' : '' ?>" href="manage-price.php?tab=lookup&search_name=<?= urlencode($search_name) ?>&category_id=<?= $search_category ?>">
                                    Tra cứu giá bán của sản phẩm
                                </a>
                            </li>
                        </ul>

                        <!-- Bộ lọc tìm kiếm -->
                        <form method="GET" action="manage-price.php" class="mb-4">
                            <input type="hidden" name="tab" value="<?= $active_tab ?>">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-5">
                                    <label class="form-label fw-bold">Tìm kiếm sản phẩm</label>
                                    <input type="text" name="search_name" class="form-control" placeholder="Nhập tên sản phẩm..." value="<?= htmlspecialchars($search_name) ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Danh mục</label>
                                    <select name="category_id" class="form-select">
                                        <option value="0">Tất cả danh mục</option>
                                        <?php foreach ($all_categories as $cat): ?>
                                            <option value="<?= $cat['id'] ?>" <?= $search_category == $cat['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-success me-2">
                                        <i class="fa fa-search"></i> Tìm kiếm
                                    </button>
                                    <a class="btn btn-secondary" href="manage-price.php?tab=<?= $active_tab ?>">Đặt lại</a>
                                </div>
                            </div>
                        </form>

                        <?php if ($active_tab === 'margin'): ?>
                            <h5 style="color:#C62828;margin-bottom:12px;">
                                <i class="fa fa-percent"></i> Nhập / sửa tỉ lệ lợi nhuận theo sản phẩm
                            </h5>
                            <table class="table table-bordered table-striped table-hover price-table" style="font-size:13px;">
                                <thead style="background:linear-gradient(135deg,#EF9A9A,#E53935);color:white;">
                                    <tr>
                                        <th class="col-stt">#</th>
                                        <th class="col-product">Sản phẩm</th>
                                        <th class="col-category">Danh mục</th>
                                        <th class="col-number">Giá vốn (BQ)</th>
                                        <th class="col-profit">Tỉ lệ lợi nhuận (%)</th>
                                        <th class="col-number">Giá bán hiện tại</th>
                                        <th class="col-actions">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($products_list) > 0): ?>
                                        <?php $stt = 1;
                                        foreach ($products_list as $p): ?>
                                            <tr>
                                                <td class="col-stt"><?= $stt++ ?></td>
                                                <td class="col-product"><?= htmlspecialchars($p['name']) ?></td>
                                                <td><?= htmlspecialchars($p['category_name'] ?? 'Chưa có') ?></td>
                                                <td class="text-end"><?= fmt_price($p['original_price']) ?> $</td>
                                                <td class="col-profit">
                                                    <form method="POST" action="manage-price.php?tab=margin&search_name=<?= urlencode($search_name) ?>&category_id=<?= $search_category ?>" class="margin-edit-form">
                                                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                                        <input type="number" step="0.01" min="0" max="1000" name="profit_margin" value="<?= number_format($p['profit_margin'] ?? 0, 2) ?>" class="margin-input" required>
                                                        <button type="submit" name="update_margin" class="btn btn-warning btn-sm btn-save">
                                                            <i class="fa fa-save"></i> Lưu
                                                        </button>
                                                    </form>
                                                </td>
                                                <td class="text-end" style="font-weight:600;color:#1B5E20;">
                                                    <?= fmt_price($p['selling_price']) ?> $
                                                </td>
                                                <td class="text-center">
                                                    <a class="btn btn-outline-success btn-sm" href="manage-price.php?tab=lookup&search_name=<?= urlencode($p['name']) ?>">
                                                        <i class="fa fa-search"></i> Tra cứu lô
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Không tìm thấy sản phẩm phù hợp.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <h5 style="color:#2E7D32;margin-bottom:12px;">
                                <i class="fa fa-history"></i> Tra cứu giá vốn, % lợi nhuận, giá bán theo lô nhập
                            </h5>
                            <?php if ($import_logs && mysqli_num_rows($import_logs) > 0): ?>
                                <table class="table table-bordered table-striped table-hover price-table" style="font-size:13px;">
                                    <thead style="background:linear-gradient(135deg,#81C784,#4CAF50);color:white;">
                                        <tr>
                                            <th class="col-stt">#</th>
                                            <th class="col-time">Thời gian nhập</th>
                                            <th class="col-product">Sản phẩm</th>
                                            <th class="col-category">Danh mục</th>
                                            <th class="col-number">SL nhập</th>
                                            <th class="col-number">Giá nhập lô</th>
                                            <th class="col-number">Giá vốn BQ mới</th>
                                            <th class="col-number">% Lợi nhuận</th>
                                            <th class="col-number">Giá bán mới</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $stt = 1;
                                        foreach ($import_logs as $log): ?>
                                            <tr>
                                                <td class="col-stt"><?= $stt++ ?></td>
                                                <td><?= $log['created_at'] ?></td>
                                                <td class="col-product"><?= htmlspecialchars($log['product_name']) ?></td>
                                                <td><?= htmlspecialchars($log['category_name'] ?? 'Chưa có') ?></td>
                                                <td class="text-center"><span class="badge bg-primary"><?= $log['quantity_imported'] ?></span></td>
                                                <td class="text-end"><?= fmt_price($log['import_price']) ?> $</td>
                                                <td class="text-end" style="color:#1565C0;font-weight:600;">
                                                    <?= fmt_price($log['new_average_price']) ?> $
                                                </td>
                                                <td class="text-center" style="color:#E65100;font-weight:600;">
                                                    <?= number_format($log['profit_margin'], 2) ?>%
                                                </td>
                                                <td class="text-end" style="color:#1B5E20;font-weight:600;">
                                                    <?= fmt_price($log['new_selling_price']) ?> $
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div style="background:#FFF8E1;border:1px solid #FFD54F;border-radius:8px;padding:14px 18px;color:#5D4037;font-size:14px;">
                                    <i class="material-icons" style="vertical-align:middle;margin-right:6px;font-size:18px;">info_outline</i>
                                    Chưa có dữ liệu lô nhập phù hợp với bộ lọc.
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                    </div><!-- card-body -->
                </div><!-- card -->

            </div>
        </div>
    </div>
</body>

<?php include("../admin/includes/footer.php"); ?>