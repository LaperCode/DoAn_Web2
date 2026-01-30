<?php
include("./includes/header.php");

$name_bk = isset($_GET["name_bk"]) ? trim($_GET["name_bk"]) : '';
$price1 = isset($_GET["price1"]) ? trim($_GET["price1"]) : 0;
$price2 = isset($_GET["price2"]) ? trim($_GET["price2"]) : 99999;
$categories_selected = isset($_GET["categories"]) ? $_GET["categories"] : 0;
$page = isset($_GET["page"]) ? (int)$_GET["page"] : 1;
function advance_find($sql, $page = 1) {
    global $conn;
    $limit = 12;
    $offset = ($page - 1) * $limit;
    $sql .= " LIMIT $limit OFFSET $offset";
    return mysqli_query($conn, $sql);
}
$check = false;
$tmp = "SELECT * FROM products WHERE ";
if (!empty($name_bk)) {
    $check = true;
    $tmp .= "name LIKE '%" . $name_bk . "%'";
}
if (!empty($price1)) {
    if ($check) $tmp .= " AND ";
    $tmp .= "selling_price >= '" . $price1 . "'";
    $check = true;
}
if (!empty($price2)) {
    if ($check) $tmp .= " AND ";
    $tmp .= "selling_price <= '" . $price2 . "'";
    $check = true;
}
if ($categories_selected != 0) {
    if ($check) $tmp .= " AND ";
    $tmp .= "category_id = '" . $categories_selected . "'";
    $check = true;
}

if ($check) {
    $products_ad = advance_find($tmp, $page);
} else {
    $products_ad = getLatestProducts(9, $page, $type ?? "", $search ?? "");
}
?>


<style>
    .advanced-search {
    max-width: 100%;
    background: #ffffff;
    padding: 15px 20px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.search-row {
    display: flex;
    align-items: flex-end;
    gap: 40px;
    flex-wrap: wrap;
    justify-content: center;
}

.form-group {
    display: flex;
    flex-direction: column;
    font-size: 20px;
}

.form-group label {
    margin-bottom: 6px;
    font-weight: 600;
}

.form-group input,
.form-group select {
    height: 36px;
    padding: 6px 20px;
    border-radius: 6px;
    border: 1px solid #ccc;
    min-width: 150px;
}

.price-row {
    display: grid;
    grid-template-columns: auto 1fr;
    align-items: center;
    gap: 15px;
}

.search-btn {
    text-align: center;
    margin-top: 20px;
}

.search-btn button {
    padding: 10px 28px;
    border: none;
    border-radius: 10px;
    background: #53708d;
    color: #fff;
    font-size: 11px;
    cursor: pointer;
    transition: 0.2s;
}

.search-btn button:hover {
    background: #30475d;
}
</style>

<body>
    <div class="bg-main">
        <div class="container">
            <div class="box">
                <div class="breadcumb">
                    <a href="index.php">Trang chủ</a>
                    <span><i class='bx bxs-chevrons-right'></i></span>
                    <a href="./advanced-search.php">Tìm kiếm sản phẩm nâng cao</a>
                </div>
            </div>

            <div class="box">
                <h3>Tìm kiếm nâng cao</h3>
                <form action="" method="GET" class="advanced-search">
                    <div class="search-row">
                        <!-- Tên sách -->
                        <div class="form-group">
                            <label>Tên sách</label>
                            <input type="text" name="name_bk" value="<?= htmlspecialchars($name_bk) ?>">
                        </div>

                        <!-- Thể loại -->
                        <div class="form-group">
                            <label>Thể loại</label>
                            <select name="categories">
                                <option value="0" <?= ($categories_selected == 0 ? 'selected' : '') ?>>Tất cả</option>
                                <?php
                                $categories = getAllActive("categories");
                                if (mysqli_num_rows($categories) > 0) {
                                    foreach ($categories as $item) {
                                        $selected = ($categories_selected == $item["id"]) ? "selected" : "";
                                        echo '<option value="'.$item["id"].'" '.$selected.'>'.$item["name"].'</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Giá -->
                        <div class="form-group">
                            <label>Giá ($)</label>
                            <div class="price-row">
                                <div>
                                    <input type="number" name="price1" style="width: 80px;" value="<?= $price1 ?>">
                                    <span style="width: 20px;">~</span>
                                    <input type="number" name="price2" style="width: 80px;" value="<?= $price2 ?>">
                                </div>
                            </div>
                        </div>

                    <!-- Button -->
                        <div class="search-btn">
                            <button type="submit" name="sm">🔍 Tìm kiếm</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="box">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="row" id="products">
                                <?php foreach ($products_ad as $product) { ?>
                                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
                                        <div class="product-card">
                                            <div class="product-card-img">
                                                <a href="./product-detail.php?slug=<?= $product['slug'] ?>">
                                                    <img src="./images/<?= $product['image'] ?>" alt="">
                                                    <img src="./images/<?= $product['image'] ?>" alt="">
                                                </a>
                                            </div>
                                            <div class="product-card-info">
                                                <div class="product-btn">
                                                    <a href="./product-detail.php?slug=<?= $product['slug'] ?>" class="btn-flat btn-hover btn-shop-now">Mua ngay</a>
                                                    <form method="post" action="./functions/muabangIconCart.php">
                                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                                        <input type="hidden" name="quantity" id="quantity" value="1">
                                                        <input type="hidden" name="order" value="true">
                                                        <button type=submit class="btn-flat btn-hover btn-cart-add">
                                                            <i class='bx bxs-cart-add'></i>
                                                        </button>
                                                    </form>
                                                    <?php
                                                    if (isset($_SESSION['giohang'])) {
                                                        $message = $_SESSION['giohang'];
                                                        unset($_SESSION['giohang']); 
                                                    }
                                                    ?>
                                                </div>
                                                <div class="product-card-name">
                                                    <?= $product['name'] ?>
                                                </div>
                                                <div class="product-card-price">
                                                    <span><del>$<?= $product['original_price'] ?></del></span>
                                                    <span class="curr-price">$<?= $product['selling_price'] ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="box">
                            <?php
                            $totalProducts = getTotalProducts($type ?? "", $search ?? "");
                            $totalPages = ceil($totalProducts / 12); 
                            if ($totalPages > 1) {
                                echo "<ul class='pagination'>";
                                for ($i = 1; $i <= $totalPages; $i++) {
                                    $active = ($i == $page) ? "class='active'" : "";
                                    $url = "advanced-search.php?page=$i";
                                    if (!empty($name_bk)) $url .= "&name_bk=" . urlencode($name_bk);
                                    if (!empty($price1)) $url .= "&price1=" . urlencode($price1);
                                    if (!empty($price2)) $url .= "&price2=" . urlencode($price2);
                                    if (!empty($categories_selected)) $url .= "&categories=" . urlencode($categories_selected);
                                    echo "<li><a href='$url' $active>$i</a></li>";
                                }
                                echo "</ul>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include("./includes/footer.php") ?>
    <script src="./assets/js/app.js"></script>
    <script src="./assets/js/products.js"></script>
    <script>
        window.onload = function() {
            <?php if (!empty($message)) { ?>
                alert("<?= addslashes($message); ?>");
            <?php } ?>
        };
    </script>
</body>
</html>
