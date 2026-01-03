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
?>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Sản phẩm</h4>
                    </div>
                    <div class="col-md-2 text-right mt-1" style="padding-left: 1000px;">
                        <button class="btn btn-primary" onclick="openModal()">Tìm</button>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên</th>
                                    <th>Hình ảnh</th>
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
                                            <td><?= $item['name']; ?></td>
                                            <td>
                                                <img src="../images/<?= $item['image']; ?>" width="50px" height="50px" alt="<?= $item['name']; ?>">
                                            <td>
                                                <?= $item['status'] == '0' ? "Hiển thị" : "Ẩn" ?>
                                            </td>
                                            <td>
                                                <a href="edit-product.php?id=<?= $item['id']; ?>" class="btn btn-primary">Sửa</a>
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
                        <!-- Phân trang -->
                        <div class="pagination" style="text-align: center; margin-top: 20px;">
                            <?php
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
    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        overflow: auto;
    }

    .modal-content {
        background-color: #fff;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 800px;
        border-radius: 8px;
        font-family: 'Roboto', sans-serif;
        position: relative;
    }

    .modal-content h2 {
        text-align: center;
        font-size: 24px;
        margin-bottom: 20px;
    }

    .modal-content .info div {
        margin: 10px 0;
        font-size: 16px;
    }

    .modal-content .info label {
        font-weight: bold;
        display: inline-block;
        width: 120px;
    }

    .modal-content table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
    }

    .modal-content th,
    .modal-content td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
    }

    .modal-content th {
        background-color: #f2f2f2;
        font-size: 16px;
    }

    .modal-content td {
        font-size: 14px;
    }

    .modal-content .total {
        text-align: right;
        font-size: 18px;
        font-weight: bold;
        margin-top: 10px;
    }

    .modal-buttons {
        text-align: center;
        margin-top: 20px;
    }

    .modal-buttons button {
        padding: 10px 20px;
        margin: 0 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }

    .modal-buttons .download-btn {
        background-color: #28a745;
        color: white;
    }

    .modal-buttons .close-btn {
        background-color: #dc3545;
        color: white;
    }

    .modal-buttons button:hover {
        opacity: 0.8;
    }

    .close {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 24px;
        font-weight: bold;
        color: #333;
        cursor: pointer;
    }

    .close:hover {
        color: #dc3545;
    }
</style>
<!-- Invoice Modal -->
<div id="invoiceModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">×</span>
        <h2>Tìm kiếm sản phẩm</h2>
        <form method="GET" action="./products.php" style="margin: 20px;" id="form_tim">
            <div class="row">
                <div class="col-md-2">
                    <label for="tenSP"><b>Tên sản phẩm:</b></label>
                    <input type="text" id="tenSP" name="tensanpham" value="<?= htmlspecialchars($tensanpham) ?>" class="form-control border border-primary shadow-sm bg-light" />
                </div>
                <div class="col-md-2">
                    <label for="LSP"><b>Loại sản phẩm:</b></label>
                    <select id="LSP" name="loaisanpham" class="form-control border border-primary shadow-sm bg-light">
                        <option value="-1" <?= $loaisanpham == "-1" ? "selected" : "" ?>>Tất cả</option>
                        <?php
                        $categories = getAll("categories");
                        if (mysqli_num_rows($categories) > 0) {
                            foreach ($categories as $item) {
                        ?>
                                <option value="<?= $item['id']; ?>" <?= $loaisanpham == $item['id'] ? "selected" : "" ?>><?= $item['name'] ?></option>
                        <?php
                            }
                        } else {
                            echo "No Category available";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="SLmin"><b>SL tồn từ:</b></label>
                    <input type="number" id="SLmin" name="qtymin" min="0" value="<?= htmlspecialchars($qtymin) ?>" class="form-control border border-primary shadow-sm bg-light" />
                </div>
                <div class="col-md-2">
                    <label for="SLmax"><b>SL tồn đến:</b></label>
                    <input type="number" id="SLmax" name="qtymax" min="0" value="<?= htmlspecialchars($qtymax) ?>" class="form-control border border-primary shadow-sm bg-light" />
                </div>
                <div class="col-md-2">
                    <label for="Pricemin"><b>Giá bán từ:</b></label>
                    <input type="number" id="Pricemin" name="giamin" min="0" value="<?= htmlspecialchars($giamin) ?>" class="form-control border border-primary shadow-sm bg-light" />
                </div>
                <div class="col-md-2">
                    <label for="Pricemax"><b>Giá bán đến:</b></label>
                    <input type="number" id="Pricemax" name="giamax" min="0" value="<?= htmlspecialchars($giamax) ?>" class="form-control border border-primary shadow-sm bg-light" />
                </div>
                <div class="col-md-2">
                    <label for="status"><b>Trạng thái:</b></label>
                    <select id="status" name="trangthai" class="form-control border border-primary shadow-sm bg-light">
                        <option value="-1" <?= $trangthai == "-1" ? "selected" : "" ?>>Tất cả</option>
                        <option value="0" <?= $trangthai == "0" ? "selected" : "" ?>>Hiển thị</option>
                        <option value="1" <?= $trangthai == "1" ? "selected" : "" ?>>Đã ẩn</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="sort"><b>Sắp xếp:</b></label>
                    <select id="sort" name="sapxep" class="form-control border border-primary shadow-sm bg-light">
                        <option value="1" <?= $sapxep == "1" ? "selected" : "" ?>>Tăng dần</option>
                        <option value="2" <?= $sapxep == "2" ? "selected" : "" ?>>Giảm dần</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="by"><b>Theo cột:</b></label>
                    <select id="by" name="theocot" class="form-control border border-primary shadow-sm bg-light">
                        <option value="id" <?= $theocot == "id" ? "selected" : "" ?>>Sản phẩm</option>
                        <option value="category_id" <?= $theocot == "category_id" ? "selected" : "" ?>>Loại sản phẩm</option>
                        <option value="qty" <?= $theocot == "qty" ? "selected" : "" ?>>Số lượng tồn</option>
                        <option value="selling_price" <?= $theocot == "selling_price" ? "selected" : "" ?>>Giá bán</option>
                    </select>
                </div>
            </div>
            <div class="modal-buttons">
                <button type="submit" class="download-btn">OK</button>
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

    document.querySelector('#form_tim').addEventListener('submit', function(e) {
        const qtyMin = parseInt(document.getElementById('SLmin').value) || 0;
        const qtyMax = parseInt(document.getElementById('SLmax').value) || 0;
        const priceMin = parseInt(document.getElementById('Pricemin').value) || 0;
        const priceMax = parseInt(document.getElementById('Pricemax').value) || 0;

        // Kiểm tra min <= max
        if (qtyMax < qtyMin) {
            alert('Số lượng đến không được nhỏ hơn số lượng từ.');
            e.preventDefault();
            return;
        }

        if (priceMax < priceMin) {
            alert('Giá bán đến không được nhỏ hơn giá bán từ.');
            e.preventDefault();
            return;
        }
    });

    // Close modal when clicking outside the modal content
    window.onclick = function(event) {
        const modal = document.getElementById('invoiceModal');
        if (event.target === modal) {
            closeModal();
        }
    };
</script>
<?php include("../admin/includes/footer.php"); ?>