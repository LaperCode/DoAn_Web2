<?php

include("./includes/header.php");

$products   =   getLatestProducts(9, $page, $type, $search);
$page++;
?>

<body>
    <!-- products content -->
    <div class="bg-main">
        <div class="container">
            <div class="box">
                <div class="breadcumb">
                    <a href="index.php">Trang chủ</a>
                    <span><i class='bx bxs-chevrons-right'></i></span>
                    <a href="./products.php">Tất cả ấn phẩm</a>
                </div>
            </div>
            <div class="box">
                <div class="row">
                    <div class="col-3 filter-col" id="filter-col">
                        <div class="box filter-toggle-box">
                            <button class="btn-flat btn-hover" id="filter-close">close</button>
                        </div>
                        <div class="box">
                            <span class="filter-header">
                                Danh mục
                            </span>
                            <ul class="filter-list">
                                <?php
                                $categories = getAllActive("categories");

                                if (mysqli_num_rows($categories) > 0) {
                                    foreach ($categories as $item) {
                                        $active = (isset($_GET['type']) && $_GET['type'] == $item['slug']) ? 'style="font-weight:bold;color:red;"' : '';
                                        echo '<li><a href="./products.php?' . http_build_query(['type' => $item['slug']]) . '" ' . $active . '>' . htmlspecialchars($item['name']) . '</a></li>';
                                    }
                                } else {
                                    echo "<li>Không có danh mục nào</li>";
                                }
                                ?>
                            </ul>

                        </div>
                        <!-- <div class="box">
                            <ul class="filter-list">
                                <li>
                                    <button type="submit" class="btn btn-primary">OK</button>
                                </li>
                            </ul>
                        </div> -->
                    </div>
                    <div class="col-9 col-md-12">
                        <div class="box filter-toggle-box">
                            <button id="filter-toggle">Lọc</button>
                        </div>
                        <div class="box">
                            <div class="row" id="products">
                                <?php foreach ($products as $product) { ?>
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
                                                    <?php if (!isset($_SESSION['auth_user']['id'])) { ?>
                                                        <a href="./login.php">
                                                            <button class="btn-flat btn-hover btn-cart-add">
                                                                <i class='bx bxs-cart-add'></i>
                                                            </button>
                                                        </a>
                                                    <?php } else { ?>
                                                        <form method="post" action="./functions/muabangIconCart.php">
                                                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                                            <input type="hidden" name="quantity" id="quantity" value="1">
                                                            <input type="hidden" name="order" value="true">
                                                            <button type=submit class="btn-flat btn-hover btn-cart-add">
                                                                <i class='bx bxs-cart-add'></i>
                                                            </button>
                                                        <?php } ?>
                                                        </form>
                                                        <?php
                                                        if (isset($_SESSION['giohang'])) {
                                                            $message = $_SESSION['giohang'];
                                                            unset($_SESSION['giohang']); // Xóa message sau khi hiển thị để tránh lặp lại
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
                            $totalProducts = getTotalProducts($type, $search);
                            $totalPages = ceil($totalProducts / 9); // Mỗi trang 9 sản phẩm

                            if ($totalPages > 1) {
                                echo "<ul class='pagination'>";

                                for ($i = 1; $i <= $totalPages; $i++) {
                                    $active = ($i == $page) ? "class='active'" : "";
                                    echo "<li><a href='?page=$i&type=$type' $active>$i</a></li>";
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
    <!-- end products content -->

    <!-- footer -->
    <?php include("./includes/footer.php") ?>
    <!-- app js -->
    <script src="./assets/js/app.js"></script>
    <script src="./assets/js/products.js"></script>
    <script>
        window.onload = function() {
            <?php if (!empty($message)) { ?>
                alert("<?php echo addslashes($message); ?>");
            <?php } ?>
        };
    </script>
</body>

</html>