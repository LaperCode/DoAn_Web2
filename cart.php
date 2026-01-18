<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['auth_user']['id'])) {
?>
    <!DOCTYPE html>
    <html lang="vi">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Yêu cầu đăng nhập - ZBooks</title>
        <link rel="icon" href="./images/logo_no_text_2.jpg" type="image/x-icon">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #2C3E50 0%, #34495E 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }

            .auth-required-container {
                background: white;
                border-radius: 16px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
                padding: 60px 50px;
                text-align: center;
                max-width: 500px;
                width: 100%;
                animation: slideUp 0.5s ease;
            }

            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .lock-icon {
                width: 80px;
                height: 80px;
                background: linear-gradient(135deg, #F39C12 0%, #E67E22 100%);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 30px;
                box-shadow: 0 6px 20px rgba(243, 156, 18, 0.3);
            }

            .lock-icon svg {
                width: 40px;
                height: 40px;
                fill: white;
            }

            .auth-required-container h1 {
                color: #2C3E50;
                font-size: 28px;
                font-weight: 700;
                margin-bottom: 15px;
            }

            .auth-required-container p {
                color: #7F8C8D;
                font-size: 16px;
                line-height: 1.6;
                margin-bottom: 35px;
            }

            .auth-buttons {
                display: flex;
                gap: 15px;
                justify-content: center;
                flex-wrap: wrap;
            }

            .btn-login {
                display: inline-block;
                padding: 14px 35px;
                background: #F39C12;
                color: white;
                text-decoration: none;
                border-radius: 8px;
                font-weight: 600;
                font-size: 16px;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3);
            }

            .btn-login:hover {
                background: #E67E22;
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(243, 156, 18, 0.4);
            }

            .btn-home {
                display: inline-block;
                padding: 14px 35px;
                background: transparent;
                color: #2C3E50;
                text-decoration: none;
                border-radius: 8px;
                font-weight: 600;
                font-size: 16px;
                border: 2px solid #2C3E50;
                transition: all 0.3s ease;
            }

            .btn-home:hover {
                background: #2C3E50;
                color: white;
                transform: translateY(-2px);
            }

            .divider {
                margin: 25px 0;
                color: #BDC3C7;
                font-size: 14px;
            }

            @media (max-width: 576px) {
                .auth-required-container {
                    padding: 40px 30px;
                }

                .auth-required-container h1 {
                    font-size: 24px;
                }

                .auth-buttons {
                    flex-direction: column;
                }

                .btn-login,
                .btn-home {
                    width: 100%;
                }
            }
        </style>
    </head>

    <body>
        <div class="auth-required-container">
            <div class="lock-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12 2C9.243 2 7 4.243 7 7v3H6c-1.103 0-2 .897-2 2v8c0 1.103.897 2 2 2h12c1.103 0 2-.897 2-2v-8c0-1.103-.897-2-2-2h-1V7c0-2.757-2.243-5-5-5zm6 10l.002 8H6v-8h12zm-9-2V7c0-1.654 1.346-3 3-3s3 1.346 3 3v3H9z" />
                </svg>
            </div>

            <h1>Yêu cầu đăng nhập</h1>
            <p>
                Bạn cần đăng nhập để xem giỏ hàng của mình.<br>
                Vui lòng đăng nhập hoặc tạo tài khoản mới để tiếp tục.
            </p>

            <div class="auth-buttons">
                <a href="./login.php" class="btn-login">
                    Đăng nhập ngay
                </a>
                <a href="./index.php" class="btn-home">
                    Về trang chủ
                </a>
            </div>

            <div class="divider">hoặc</div>

            <p style="margin-bottom: 0; font-size: 14px;">
                Chưa có tài khoản?
                <a href="./register.php" style="color: #F39C12; font-weight: 600; text-decoration: none;">
                    Đăng ký ngay
                </a>
            </p>
        </div>
    </body>

    </html>
<?php
    exit();
}

// Nếu đã đăng nhập, include header và hiển thị giỏ hàng bình thường
include("./includes/header.php");
?>

<style>
    th,
    td {
        padding: 5px;
        text-align: center;
    }

    .quantity-control {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        background: white;
        border-radius: 8px;
        padding: 5px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .qty-btn {
        width: 35px;
        height: 35px;
        border: 2px solid #F39C12;
        background: white;
        color: #F39C12;
        font-size: 20px;
        font-weight: bold;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }

    .qty-btn:hover {
        background: #F39C12;
        color: white;
        transform: scale(1.1);
    }

    .qty-btn:active {
        transform: scale(0.95);
    }

    .qty-input {
        width: 60px;
        height: 35px;
        text-align: center;
        font-size: 18px;
        font-weight: 600;
        border: 2px solid #e3e3e3;
        border-radius: 6px;
        outline: none;
        color: #2C3E50;
    }

    .qty-input:focus {
        border-color: #F39C12;
        box-shadow: 0 0 0 3px rgba(243, 156, 18, 0.1);
    }

    /* Remove default number input arrows */
    .qty-input::-webkit-outer-spin-button,
    .qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .qty-input[type=number] {
        -moz-appearance: textfield;
        appearance: textfield;
    }

    .btn-buy {
        border: none;
        outline: none;
        font-size: 17px;
        cursor: pointer;
        padding: 8px 16px;
        border-radius: 6px;
        background-color: #F39C12;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-buy:hover {
        background-color: #E67E22;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(243, 156, 18, 0.3);
    }
</style>

<body>
    <!-- product-detail content -->
    <div class="bg-main">
        <div class="container">
            <div class="box">
                <div class="breadcumb">
                    <a href="index.php">Trang chủ</a>
                    <span><i class='bx bxs-chevrons-right'></i></span>
                    <a href="#">Giỏ hàng của tôi</a>
                </div>
            </div>

            <div class="box" style="padding: 0 40px">
                <div class="product-info">
                    <?php
                    $products = getMyOrders();
                    $total_price = 0;
                    if (mysqli_num_rows($products) == 0) {
                    ?>
                        <p style="font-size: 20px; text-align: center;">
                            Giỏ hàng của bạn trống. mua ngay <a style="color: blue; text-decoration: underline" href="./products.php">Tại đây</a>
                        </p>
                    <?php } else { ?>
                        <table width="100%" border="1" cellspacing="0">
                            <tr>
                                <th>Tên sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Giá</th>
                                <th>Tổng</th>
                                <th>Xóa</th>
                                <th>Cập nhật</th>
                            </tr>
                            <?php foreach ($products as $product) { ?>
                                <tr>
                                    <td style="text-align: left;">
                                        <a href="./product-detail.php?slug=<?= $product['slug'] ?>">
                                            <?= $product['name'] ?>
                                        </a>
                                    </td>
                                    <form action="./functions/ordercode.php" method="post">
                                        <td width=150>
                                            <input type="hidden" name="update_product" value="true">
                                            <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                            <input type="hidden" class="product-price" value="<?= $product['selling_price'] ?>">
                                            <div class="quantity-control">
                                                <button type="button" class="qty-btn qty-minus">−</button>
                                                <input type="number" name="quantity" value="<?= $product['quantity'] ?>" class="input-number qty-input" min="1">
                                                <button type="button" class="qty-btn qty-plus">+</button>
                                            </div>
                                        </td>
                                        <td>
                                            $
                                            <span>
                                                <?= $product['selling_price'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            $
                                            <span class="total-price">
                                                <?= $product['selling_price'] * $product['quantity'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a class="btn-buy" style="font-size: 15px; background-color: #fc8d8b" href="./functions/ordercode.php?deleteID=<?= $product['id'] ?>">Xóa</a>
                                        </td>
                                        <td>
                                            <button class="btn-buy">Cập nhật</button>
                                        </td>
                                    </form>
                                </tr>
                            <?php
                                $total_price +=  $product['selling_price'] * $product['quantity'];
                            }
                            ?>
                        </table>
                        <!-- <form action="./functions/ordercode.php" method="post">
                        <input type="hidden" name="buy_product" value="true">
                        <p style="display: block;">Tổng tiền: $<?= $total_price ?></p>
                        <button class="btn-buy" style="float: right;">Đặt hàng</button>
                    </form> -->
                        <p style="display: block;">Tổng tiền: $<?= $total_price ?></p>
                        <a href="pay.php" class="btn-buy" style="float: right;">Thanh toán</a>
                    <?php }     ?>
                    <a href="./cart-status.php">
                        <h4>Xem tất cả đơn hàng</h4>
                    </a>
                    <br>
                    <br>
                </div>
            </div>
        </div>
    </div>
    <!-- end product-detail content -->
    <?php include("./includes/footer.php") ?>
    <script src="./assets/js/app.js"></script>
    <script src="./assets/js/index.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</body>
<script>
    $(document).ready(function() {
        // Quantity plus/minus buttons
        $('.qty-plus').on('click', function() {
            const input = $(this).siblings('.qty-input');
            let value = parseInt(input.val());
            input.val(value + 1).trigger('change');
        });

        $('.qty-minus').on('click', function() {
            const input = $(this).siblings('.qty-input');
            let value = parseInt(input.val());
            if (value > 1) {
                input.val(value - 1).trigger('change');
            }
        });

        // Update total price on quantity change
        $('.qty-input').on('change', function(e) {
            if (e.target.value == 0 || e.target.value < 1) {
                e.target.value = 1;
            }
            const node = $(this).closest('form');
            const price = parseInt(node.find('.product-price').val());
            let total_order = parseInt(e.target.value);
            let total_price = price * total_order;
            node.find('.total-price').html(total_price);
        });
    });
</script>

</html>