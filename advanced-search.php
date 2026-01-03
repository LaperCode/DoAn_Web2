<?php
include("./includes/header.php");

$name_bk = isset($_GET["name_bk"]) ? trim($_GET["name_bk"]) : '';
$price1 = isset($_GET["price1"]) ? trim($_GET["price1"]) : 0;
$price2 = isset($_GET["price2"]) ? trim($_GET["price2"]) : 99999;
$categories_selected = isset($_GET["categories"]) ? $_GET["categories"] : 0;
$page = isset($_GET["page"]) ? (int)$_GET["page"] : 1;
function advance_find($sql, $page = 1) {
    global $conn;
    $limit = 9;
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
                <div class="box filter-toggle-box">
                    <button class="btn-flat btn-hover" id="filter-close">close</button>
                </div>
                <div class="row">
                    <div class="col-3 filter-col" id="filter-col">
                        <h3>Tìm kiếm năng cao</h3><br>
                        <form action="" method="GET">
                            Tên sách: <input type="text" name="name_bk" value="<?= htmlspecialchars($name_bk) ?>"> <br><br>
                            Giá thấp nhất: <input type="number" name="price1" style="width: 80px;" value="<?= $price1 ?>"> <br><br>
                            Giá cao nhất: <input type="number" name="price2" style="width: 80px;" value="<?= $price2 ?>"> <br><br>
                            <?php 
                            $categories = getAllActive("categories");
                            if (mysqli_num_rows($categories) > 0) {
                                echo 'Thể loại: <select name="categories">';
                                echo '<option value="0"' . ($categories_selected == 0 ? ' selected' : '') . '>Tất cả</option>';
                                foreach ($categories as $item) {
                                    $selected = ($categories_selected == $item["id"]) ? "selected" : "";
                                    echo '<option value="'.$item["id"].'" '.$selected.'>'.$item["name"].'</option>';
                                }
                                echo '</select>';
                            }
                            ?>
                            <br><br><input type="submit" name="sm" value="Tìm kiếm">
                        </form>
                    </div>
                    <div class="col-9 col-md-12">
                        <div class="box filter-toggle-box">
                            <button id="filter-toggle">Lọc</button>
                        </div>
                        <div class="box">
                            <div class="row" id="products">
                                <?php foreach ($products_ad as $product) { ?>
                                    <div class="col-4 col-md-6 col-sm-12">
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
                        <div class="box">
                            <?php
                            $totalProducts = getTotalProducts($type ?? "", $search ?? "");
                            $totalPages = ceil($totalProducts / 9); 
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
