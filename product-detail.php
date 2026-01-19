<?php include("./includes/header.php") ?>;

<body>
    <?php
    if (isset($_GET['slug'])) {
        $slug       = $_GET['slug'];
        $product    = getBySlug("products", $slug);

        if (mysqli_num_rows($product) > 0) {
            $product        = mysqli_fetch_array($product);
            $categoryName   = getByID("categories", $product['category_id']);
            $categoryName   = mysqli_fetch_array($categoryName);
    ?>
            <!-- product-detail content -->
            <div class="bg-main" style="padding: 30px 0;">
                <div class="container">
                    <div class="box">
                        <div class="breadcumb">
                            <a href="index.php">Trang chủ</a>
                            <span><i class='bx bxs-chevrons-right'></i></span>
                            <a href="./products.php">Tất cả ấn phẩm</a>
                            <span><i class='bx bxs-chevrons-right'></i></span>
                            <a href="#"><?= $product['name'] ?></a>
                        </div>
                    </div>

                    <div class="row product-row" style="display: flex !important; flex-wrap: nowrap !important; align-items: flex-start;">
                        <div class="col-5" style="flex: 0 0 41.66%; max-width: 41.66%; min-width: 350px;">
                            <div class="product-img" id="product-img">
                                <img src="./images/<?= $product['image'] ?>" alt="">
                            </div>
                            <div class="box">
                                <div class="product-img-list">
                                    <div class="product-img-item">
                                        <img src="./images/<?= $product['image'] ?>" alt="">
                                    </div>
                                    <div class="product-img-item">
                                        <img src="./images/<?= $product['image'] ?>" alt="">
                                    </div>
                                    <div class="product-img-item">
                                        <img src="./images/<?= $product['image'] ?>" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-7" style="flex: 1; min-width: 0;">
                            <div class="product-info">
                                <h1>
                                    <?= $product['name'] ?>
                                </h1>
                                <div class="product-info-detail">
                                    <span class="product-info-detail-title">Danh mục:</span>
                                    <a><?= $categoryName['name'] ?></a>
                                </div>
                                <div class="product-info-detail">
                                    <span class="product-info-detail-title">Còn:</span>
                                    <a><?= $product['qty'] ?></a><span class="product-info-detail-title">&nbsp;Ấn phẩm</span>
                                </div>
                                <div class="product-info-detail">
                                    <span class="product-info-detail-title">Đánh giá:</span>
                                    <span class="rating">
                                        <?= avgRate($product['id']) ?>
                                        <i class='bx bxs-star'></i>
                                    </span>
                                </div>
                                <h3>Tóm tắt nhanh</h3>
                                <p class="product-description">
                                    <?= nl2br($product['small_description']) ?>
                                </p>
                                <div class="product-info-price">$<?= $product['selling_price'] ?></div>
                                <div class="product-quantity-wrapper">
                                    <span class="product-quantity-btn" onclick="QualityChange('down')">
                                        <i class='bx bx-minus'></i>
                                    </span>
                                    <span class="product-quantity" id="quantity-show">1</span>
                                    <span class="product-quantity-btn" onclick="QualityChange('up')">
                                        <i class='bx bx-plus'></i>
                                    </span>
                                </div>
                                <form method="post" action="./functions/ordercode.php">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <input type="hidden" name="quantity" id="quantity" value="1">
                                    <input type="hidden" name="order" value="true">
                                    <?php if (!isset($_SESSION['auth_user']['id'])) { ?>
                                        <a href="./login.php">
                                            <button type="button" class="btn-flat btn-hover">Đăng nhập để tiếp tục</button>
                                        </a>
                                        <?php } else {
                                        $check = checkOrder($product['id']);
                                        if ($check == 1) { ?>
                                            <a href="./cart.php">
                                                <button type="button" class="btn-flat btn-hover">Mua ngay</button>
                                            </a>
                                    <?php
                                        } else {
                                            echo '<button class="btn-flat btn-hover">Thêm vào giỏ hàng</button>';
                                        }
                                    }
                                    ?>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="box">
                        <div class="box-header">
                            📝 Mô Tả Chi Tiết
                        </div>
                        <div class="product-detail-description">
                            <p>
                                <?= nl2br($product['description']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="box">
                        <div class="box-header">
                            ⭐ Đánh Giá Từ Khách Hàng
                        </div>
                        <div>
                            <?php
                            $rates = getRate($product['id']);
                            if (mysqli_num_rows($rates) > 0) {
                                foreach ($rates as $rate) {
                            ?>
                                    <div class="user-rate">
                                        <div class="user-info">
                                            <div class="user-avt">
                                                <img src="./images/avatar.jpg" alt="">
                                            </div>
                                            <div class="user-name">
                                                <span class="name"><?= $rate['name'] ?></span>
                                                <span class="rating">
                                                    <?php
                                                    for ($i = 0; $i < $rate['rate']; $i++) {
                                                        echo "<i class='bx bxs-star'></i>";
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="user-rate-content">
                                            <?= $rate['comment'] ?>
                                        </div>
                                    </div>
                                <?php
                                }
                            } else {
                                ?>
                                <div style="text-align: center; padding: 40px; background: #f8f9fa; border-radius: 12px;">
                                    <i class='bx bx-comment-detail' style="font-size: 60px; color: #ddd; margin-bottom: 15px;"></i>
                                    <div style="color: #999; font-size: 16px;">Chưa có lượt bình luận hoặc đánh giá nào</div>
                                </div>
                            <?php } ?>

                        </div>
                    </div>
                </div>
        <?php
        } else {
            echo '<div class="box-header" style="text-align: center;"> Product not found </div>';
        }
    } else {
        echo '<div class="box-header" style="text-align: center;"> Id missing from url </div>';
    }
        ?>
            </div>
            <!-- end product-detail content -->
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

            <script src="./assets/js/app.js"></script>
            <script src="./assets/js/index.js"></script>
            <script>
                let quantity = 1;
                const QualityShower = document.getElementById('quantity-show');
                const QualityInput = document.getElementById('quantity');

                function QualityChange(type) {
                    if (type == 'up') {
                        quantity++;
                    } else {
                        quantity--;
                        if (quantity == 0) {
                            quantity = 1;
                        }
                    }
                    QualityShower.textContent = quantity + "";
                    QualityInput.value = quantity;
                }

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

                // Add animations
                const style = document.createElement('style');
                style.textContent = `
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
            
            @keyframes fadeInLeft {
                from {
                    opacity: 0;
                    transform: translateX(-30px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
            
            @keyframes fadeInRight {
                from {
                    opacity: 0;
                    transform: translateX(30px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
            
            .product-row .col-5 {
                animation: fadeInLeft 0.6s ease-out;
            }
            
            .product-row .col-7 {
                animation: fadeInRight 0.6s ease-out;
            }
            
            .user-rate {
                animation: fadeInUp 0.6s ease-out backwards;
            }
            
            .user-rate:nth-child(1) { animation-delay: 0.1s; }
            .user-rate:nth-child(2) { animation-delay: 0.2s; }
            .user-rate:nth-child(3) { animation-delay: 0.3s; }
            .user-rate:nth-child(4) { animation-delay: 0.4s; }
            .user-rate:nth-child(5) { animation-delay: 0.5s; }
        `;
                document.head.appendChild(style);
            </script>
</body>

</html>