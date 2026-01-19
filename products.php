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
                            <button id="filter-toggle">🔍 Lọc sản phẩm</button>
                        </div>
                        <div class="box">
                            <div class="row" id="products" style="gap: 20px 0;">
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
                            $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

                            if ($totalProducts > 0) {
                                echo "<div style='text-align: center; margin-bottom: 15px; color: #666; font-size: 14px;'>";
                                echo "Hiển thị " . count($products) . " trong tổng số " . $totalProducts . " sản phẩm";
                                echo "</div>";
                            }

                            if ($totalPages > 1) {
                                echo "<ul class='pagination'>";

                                // Previous button
                                if ($currentPage > 1) {
                                    echo "<li><a href='?page=" . ($currentPage - 1) . "&type=$type'>‹</a></li>";
                                }

                                for ($i = 1; $i <= $totalPages; $i++) {
                                    $active = ($i == $currentPage) ? "class='active'" : "";
                                    echo "<li><a href='?page=$i&type=$type' $active>$i</a></li>";
                                }

                                // Next button
                                if ($currentPage < $totalPages) {
                                    echo "<li><a href='?page=" . ($currentPage + 1) . "&type=$type'>›</a></li>";
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

    <!-- Back to top button -->
    <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})"
        style="position: fixed; bottom: 30px; right: 30px; width: 50px; height: 50px; 
                   background: linear-gradient(135deg, #2C3E50 0%, #34495E 100%); 
                   color: white; border: none; border-radius: 50%; cursor: pointer; 
                   display: none; align-items: center; justify-content: center; 
                   font-size: 24px; box-shadow: 0 6px 20px rgba(44, 62, 80, 0.3); 
                   transition: all 0.3s ease; z-index: 999;"
        id="backToTop">
        ↑
    </button>

    <!-- app js -->
    <script src="./assets/js/app.js"></script>
    <script src="./assets/js/products.js"></script>
    <script>
        // Back to top button functionality
        window.addEventListener('scroll', function() {
            const backToTop = document.getElementById('backToTop');
            if (window.pageYOffset > 300) {
                backToTop.style.display = 'flex';
            } else {
                backToTop.style.display = 'none';
            }
        });

        // Back to top hover effect
        document.getElementById('backToTop').addEventListener('mouseenter', function() {
            this.style.background = 'linear-gradient(135deg, #F39C12 0%, #E67E22 100%)';
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 8px 24px rgba(243, 156, 18, 0.4)';
        });

        document.getElementById('backToTop').addEventListener('mouseleave', function() {
            this.style.background = 'linear-gradient(135deg, #2C3E50 0%, #34495E 100%)';
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 6px 20px rgba(44, 62, 80, 0.3)';
        });

        // Alert message
        window.onload = function() {
            <?php if (!empty($message)) { ?>
                alert("<?php echo addslashes($message); ?>");
            <?php } ?>
        };

        // Add cart animation
        document.querySelectorAll('.btn-cart-add').forEach(btn => {
            btn.addEventListener('click', function(e) {
                this.style.animation = 'cartSuccess 0.5s ease';
                setTimeout(() => {
                    this.style.animation = '';
                }, 500);
            });
        });

        // Add CSS for cart animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes cartSuccess {
                0%, 100% { transform: rotate(0deg) scale(1); }
                50% { transform: rotate(15deg) scale(1.2); }
            }
            
            .product-card {
                animation: fadeInUp 0.6s ease-out backwards;
            }
            
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .product-card:nth-child(3n+1) { animation-delay: 0.1s; }
            .product-card:nth-child(3n+2) { animation-delay: 0.2s; }
            .product-card:nth-child(3n+3) { animation-delay: 0.3s; }
        `;
        document.head.appendChild(style);
    </script>
</body>

</html>