<?php
include("../admin/includes/header.php");
$tensanpham = isset($_GET['tensanpham']) ? $_GET['tensanpham'] : "";
$loaisanpham = isset($_GET['loaisanpham']) ? $_GET['loaisanpham'] : "";
$qtymin = isset($_GET['qtymin']) ? $_GET['qtymin'] : "";
$qtymax = isset($_GET['qtymax']) ? $_GET['qtymax'] : "";
$giamin = isset($_GET['giamin']) ? $_GET['giamin'] : "";
$giamax = isset($_GET['giamax']) ? $_GET['giamax'] : "";
$trangthai = isset($_GET['trangthai']) ? $_GET['trangthai'] : "";
$sapxep = isset($_GET['sapxep']) ? $_GET['sapxep'] : 1;
$theocot = isset($_GET['theocot']) ? $_GET['theocot'] : "id";
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$query_string = http_build_query([
    'tensanpham' => $tensanpham,
    'loaisanpham' => $loaisanpham,
    'qtymin' => $qtymin,
    'qtymax' => $qtymax,
    'giamin' => $giamin,
    'giamax' => $giamax,
    'trangthai' => $trangthai,
    'sapxep' => $sapxep,
    'theocot' => $theocot
]);
?>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Sản phẩm</h4>
                    </div>
                    <div class="card-body">
                        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 10px;">
                            <a class="btn btn-success" href="add-product.php">
                                <i class="fa fa-plus"></i> Thêm sản phẩm
                            </a>
                            <button class="btn btn-primary" onclick="openModal()">Tìm</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" style="width: 100%; table-layout: fixed;">
                                <colgroup>
                                    <col style="width: 5%;">
                                    <col style="width: 42%;">
                                    <col style="width: 10%;">
                                    <col style="width: 10%;">
                                    <col style="width: 13%;">
                                    <col style="width: 10%;">
                                    <col style="width: 10%;">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên</th>
                                        <th>Hình ảnh</th>
                                        <th>Số lượng</th>
                                        <th>Trạng thái</th>
                                        <th>Sửa</th>
                                        <th>Xóa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $products = getSanPham($tensanpham, $loaisanpham, $qtymin, $qtymax, $giamin, $giamax, $trangthai, $sapxep, $theocot, $limit, $offset);
                                    $total_products = getTotalProducts($tensanpham, $loaisanpham, $qtymin, $qtymax, $giamin, $giamax, $trangthai);
                                    $total_pages = ceil($total_products / $limit);

                                    if (mysqli_num_rows($products) > 0) {
                                        echo "Kết quả: " . $total_products;
                                        foreach ($products as $item) {
                                    ?>
                                            <tr>
                                                <td><?= $item['id']; ?> </td>
                                                <td style="word-break: break-word; white-space: normal;"><?= $item['name']; ?></td>
                                                <td>
                                                    <img src="../images/<?= $item['image']; ?>" width="50px" height="50px" alt="<?= $item['name']; ?>">
                                                <td>
                                                    <?php
                                                    $qty = (int)$item['qty'];
                                                    if ($qty == 0) {
                                                        echo '<span class="badge bg-danger">Hết hàng</span>';
                                                    } elseif ($qty <= 5) {
                                                        echo '<span class="badge bg-warning text-dark">' . $qty . '</span>';
                                                    } else {
                                                        echo '<span class="badge bg-success">' . $qty . '</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?= $item['status'] == '0' ? "Hiển thị" : "Ẩn" ?>
                                                </td>
                                                <td>
                                                    <a href="edit-product.php?id=<?= $item['id']; ?>&<?= $query_string ?>&page=<?= $page ?>" class="btn btn-primary">Sửa</a>
                                                </td>
                                                <td>
                                                    <form action="code.php" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?');">
                                                        <input type="hidden" name="product_id" value="<?= $item['id']; ?>">
                                                        <button type="submit" name="delete_product_btn" class="btn btn-danger">Xóa </button>
                                                    </form>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    } else {
                                        echo "<tr><td colspan='6'>Không tìm thấy sản phẩm nào</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div><!-- end table-responsive -->
                        <!-- Phân trang -->
                        <div class="pagination" style="text-align: center; margin-top: 20px;">
                            <?php

                            // Nút "Trước"
                            if ($page > 1) {
                                echo '<a href="products.php?' . $query_string . '&page=' . ($page - 1) . '" class="btn btn-secondary" style="margin: 0 5px;">Trước</a>';
                            }

                            // Số lượng trang hiển thị xung quanh trang hiện tại
                            $max_pages_to_show = 5;
                            $half_pages = floor($max_pages_to_show / 2);
                            $start_page = max(1, $page - $half_pages);
                            $end_page = min($total_pages, $page + $half_pages);

                            // Điều chỉnh để luôn hiển thị đúng số lượng nút
                            if ($end_page - $start_page + 1 < $max_pages_to_show) {
                                if ($start_page == 1) {
                                    $end_page = min($total_pages, $start_page + $max_pages_to_show - 1);
                                } else {
                                    $start_page = max(1, $end_page - $max_pages_to_show + 1);
                                }
                            }

                            // Hiển thị trang đầu tiên
                            if ($start_page > 1) {
                                echo '<a href="products.php?' . $query_string . '&page=1" class="btn btn-secondary" style="margin: 0 5px;">1</a>';
                                if ($start_page > 2) {
                                    echo '<span style="margin: 0 5px;">...</span>';
                                }
                            }

                            // Hiển thị các trang trong khoảng
                            for ($i = $start_page; $i <= $end_page; $i++) {
                                $active = $i == $page ? 'background-color:rgb(255, 0, 64); color: white;' : '';
                                echo '<a href="products.php?' . $query_string . '&page=' . $i . '" class="btn btn-secondary" style="margin: 0 5px; ' . $active . '">' . $i . '</a>';
                            }

                            // Hiển thị trang cuối cùng
                            if ($end_page < $total_pages) {
                                if ($end_page < $total_pages - 1) {
                                    echo '<span style="margin: 0 5px;">...</span>';
                                }
                                echo '<a href="products.php?' . $query_string . '&page=' . $total_pages . '" class="btn btn-secondary" style="margin: 0 5px;">' . $total_pages . '</a>';
                            }

                            // Nút "Tiếp"
                            if ($page < $total_pages) {
                                echo '<a href="products.php?' . $query_string . '&page=' . ($page + 1) . '" class="btn btn-secondary" style="margin: 0 5px;">Tiếp</a>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<style>
    /* ===== SEARCH MODAL ===== */
    #invoiceModal {
        display: none;
        position: fixed;
        z-index: 1050;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.45);
        overflow: auto;
    }

    #invoiceModal .modal-content {
        background: #fff;
        margin: 4% auto;
        padding: 0;
        border: none;
        width: 90%;
        max-width: 860px;
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.18);
        font-family: 'Roboto', sans-serif;
        position: relative;
        overflow: hidden;
    }

    #invoiceModal .modal-header-custom {
        background: linear-gradient(90deg, #FF9800 0%, #81C784 100%);
        padding: 18px 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    #invoiceModal .modal-header-custom h2 {
        color: #fff;
        font-size: 20px;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    #invoiceModal .modal-header-custom .close {
        color: #fff;
        font-size: 26px;
        font-weight: bold;
        cursor: pointer;
        line-height: 1;
        opacity: 0.85;
        position: static;
    }

    #invoiceModal .modal-header-custom .close:hover {
        opacity: 1;
    }

    #invoiceModal .modal-body-custom {
        padding: 24px 28px 8px;
    }

    #invoiceModal .filter-section-title {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #F57C00;
        margin: 4px 0 12px;
        padding-bottom: 6px;
        border-bottom: 2px solid #FFE0B2;
    }

    #invoiceModal .form-group-custom {
        margin-bottom: 18px;
    }

    #invoiceModal .form-group-custom label {
        font-size: 13px;
        font-weight: 600;
        color: #344054;
        margin-bottom: 5px;
        display: block;
    }

    #invoiceModal .form-group-custom label i {
        color: #FF9800;
        margin-right: 4px;
    }

    #invoiceModal .form-control-custom {
        width: 100%;
        padding: 8px 12px;
        font-size: 13.5px;
        border: 1.5px solid #d0d5dd;
        border-radius: 7px;
        background: #f9fafb;
        color: #1d2939;
        transition: border-color 0.2s, box-shadow 0.2s;
        outline: none;
    }

    #invoiceModal .form-control-custom:focus {
        border-color: #FF9800;
        box-shadow: 0 0 0 3px rgba(255, 152, 0, 0.15);
        background: #fff;
    }

    #invoiceModal .range-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    #invoiceModal .range-group input {
        flex: 1;
    }

    #invoiceModal .range-separator {
        color: #6c757d;
        font-size: 13px;
        white-space: nowrap;
        font-weight: 500;
    }

    #invoiceModal .modal-footer-custom {
        padding: 16px 28px 20px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        border-top: 1px solid #f0f0f0;
    }

    #invoiceModal .btn-reset {
        padding: 9px 22px;
        font-size: 14px;
        border-radius: 7px;
        border: 1.5px solid #d0d5dd;
        background: #fff;
        color: #344054;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    #invoiceModal .btn-reset:hover {
        background: #f5f5f5;
    }

    #invoiceModal .btn-search {
        padding: 9px 28px;
        font-size: 14px;
        border-radius: 10px;
        border: none;
        background: linear-gradient(90deg, #FF9800 0%, #81C784 100%);
        color: #fff;
        font-weight: 700;
        cursor: pointer;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(255, 140, 0, 0.3);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    #invoiceModal .btn-search:hover {
        opacity: 1;
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(255, 140, 0, 0.5);
        background: linear-gradient(90deg, #81C784 0%, #FF9800 100%);
    }
</style>

<!-- Search Modal -->
<div id="invoiceModal">
    <div class="modal-content">
        <!-- Header -->
        <div class="modal-header-custom">
            <h2><i class="fa fa-search"></i> Tìm kiếm sản phẩm</h2>
            <span class="close" onclick="closeModal()">×</span>
        </div>

        <form method="GET" action="./products.php" id="form_tim">
            <div class="modal-body-custom">

                <!-- ROW 1: Tên + Loại -->
                <p class="filter-section-title"><i class="fa fa-info-circle"></i> Thông tin sản phẩm</p>
                <div class="row g-3 mb-2">
                    <div class="col-md-6">
                        <div class="form-group-custom">
                            <label for="tenSP"><i class="fa fa-book"></i> Tên sản phẩm</label>
                            <input type="text" id="tenSP" name="tensanpham"
                                value="<?= htmlspecialchars($tensanpham) ?>"
                                placeholder="Nhập tên sản phẩm..."
                                class="form-control-custom" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group-custom">
                            <label for="LSP"><i class="fa fa-tag"></i> Loại sản phẩm</label>
                            <select id="LSP" name="loaisanpham" class="form-control-custom">
                                <option value="-1" <?= $loaisanpham == "-1" ? "selected" : "" ?>>-- Tất cả loại --</option>
                                <?php
                                $categories = getAll("categories");
                                if (mysqli_num_rows($categories) > 0) {
                                    foreach ($categories as $item) { ?>
                                        <option value="<?= $item['id']; ?>" <?= $loaisanpham == $item['id'] ? "selected" : "" ?>><?= $item['name'] ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- ROW 2: SL tồn + Giá bán -->
                <p class="filter-section-title"><i class="fa fa-filter"></i> Lọc theo số lượng & giá</p>
                <div class="row g-3 mb-2">
                    <div class="col-md-6">
                        <div class="form-group-custom">
                            <label><i class="fa fa-cubes"></i> Số lượng tồn</label>
                            <div class="range-group">
                                <input type="number" id="SLmin" name="qtymin" min="0"
                                    value="<?= htmlspecialchars($qtymin) ?>"
                                    placeholder="Từ" class="form-control-custom" />
                                <span class="range-separator">—</span>
                                <input type="number" id="SLmax" name="qtymax" min="0"
                                    value="<?= htmlspecialchars($qtymax) ?>"
                                    placeholder="Đến" class="form-control-custom" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group-custom">
                            <label><i class="fa fa-dollar-sign"></i> Giá bán (VNĐ)</label>
                            <div class="range-group">
                                <input type="number" id="Pricemin" name="giamin" min="0"
                                    value="<?= htmlspecialchars($giamin) ?>"
                                    placeholder="Từ" class="form-control-custom" />
                                <span class="range-separator">—</span>
                                <input type="number" id="Pricemax" name="giamax" min="0"
                                    value="<?= htmlspecialchars($giamax) ?>"
                                    placeholder="Đến" class="form-control-custom" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ROW 3: Trạng thái + Sắp xếp ID + Theo cột -->
                <p class="filter-section-title"><i class="fa fa-sort-amount-down"></i> Trạng thái & Sắp xếp</p>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-group-custom">
                            <label for="status"><i class="fa fa-eye"></i> Trạng thái</label>
                            <select id="status" name="trangthai" class="form-control-custom">
                                <option value="-1" <?= $trangthai == "-1" ? "selected" : "" ?>>-- Tất cả --</option>
                                <option value="0" <?= $trangthai == "0" ? "selected" : "" ?>>Hiển thị</option>
                                <option value="1" <?= $trangthai == "1" ? "selected" : "" ?>>Đã ẩn</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group-custom">
                            <label for="by"><i class="fa fa-columns"></i> Sắp xếp theo cột</label>
                            <select id="by" name="theocot" class="form-control-custom">
                                <option value="id" <?= $theocot == "id" ? "selected" : "" ?>>ID sản phẩm</option>
                                <option value="category_id" <?= $theocot == "category_id" ? "selected" : "" ?>>Loại sản phẩm</option>
                                <option value="qty" <?= $theocot == "qty" ? "selected" : "" ?>>Số lượng tồn</option>
                                <option value="selling_price" <?= $theocot == "selling_price" ? "selected" : "" ?>>Giá bán</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group-custom">
                            <label for="sort"><i class="fa fa-sort"></i> Thứ tự sắp xếp ID</label>
                            <select id="sort" name="sapxep" class="form-control-custom">
                                <option value="1" <?= $sapxep == "1" ? "selected" : "" ?>>Tăng dần (A → Z)</option>
                                <option value="2" <?= $sapxep == "2" ? "selected" : "" ?>>Giảm dần (Z → A)</option>
                            </select>
                        </div>
                    </div>
                </div>

            </div><!-- end modal-body-custom -->

            <!-- Footer buttons -->
            <div class="modal-footer-custom">
                <button type="button" class="btn-reset" onclick="resetForm()">
                    <i class="fa fa-undo"></i> Đặt lại
                </button>
                <button type="submit" class="btn-search">
                    <i class="fa fa-search"></i> Tìm kiếm
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    function openModal() {
        document.getElementById('invoiceModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('invoiceModal').style.display = 'none';
    }

    function resetForm() {
        document.getElementById('tenSP').value = '';
        document.getElementById('LSP').value = '-1';
        document.getElementById('SLmin').value = '';
        document.getElementById('SLmax').value = '';
        document.getElementById('Pricemin').value = '';
        document.getElementById('Pricemax').value = '';
        document.getElementById('status').value = '-1';
        document.getElementById('by').value = 'id';
        document.getElementById('sort').value = '1';
    }

    document.querySelector('#form_tim').addEventListener('submit', function(e) {
        const qtyMin = parseInt(document.getElementById('SLmin').value) || 0;
        const qtyMax = parseInt(document.getElementById('SLmax').value) || 0;
        const priceMin = parseInt(document.getElementById('Pricemin').value) || 0;
        const priceMax = parseInt(document.getElementById('Pricemax').value) || 0;

        if (document.getElementById('SLmax').value !== '' && qtyMax < qtyMin) {
            alert('⚠️ Số lượng "Đến" không được nhỏ hơn số lượng "Từ".');
            e.preventDefault();
            return;
        }

        if (document.getElementById('Pricemax').value !== '' && priceMax < priceMin) {
            alert('⚠️ Giá bán "Đến" không được nhỏ hơn giá bán "Từ".');
            e.preventDefault();
            return;
        }
    });

    // Đóng modal khi click ra ngoài
    window.onclick = function(event) {
        const modal = document.getElementById('invoiceModal');
        if (event.target === modal) {
            closeModal();
        }
    };
</script>
<?php include("../admin/includes/footer.php"); ?>