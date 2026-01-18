<?php
include("./includes/header.php");
if (!isset($_SESSION['auth_user']['id'])) {
    die("Từ Chối truy cập <a href='./login'>Đăng nhập ngay</a>");
}
?>

<style>
    /* Order Status Page Styling */
    .cart-status-container {
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 30px;
    }

    .cart-status-container h4 {
        color: #2C3E50;
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 3px solid #F39C12;
    }

    /* Table Styling */
    .cart-status-container table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .cart-status-container thead {
        background: #2C3E50;
        color: white;
    }

    .cart-status-container thead th {
        padding: 16px 12px;
        text-align: center;
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .cart-status-container tbody td {
        padding: 16px 12px;
        border-bottom: 1px solid #ecf0f1;
        font-size: 15px;
        color: #34495E;
        vertical-align: middle;
        text-align: center;
    }

    /* First column (Order ID) align left */
    .cart-status-container tbody td:first-child {
        text-align: left;
    }

    .cart-status-container tbody tr {
        transition: background-color 0.3s ease;
    }

    .cart-status-container tbody tr:hover {
        background-color: #f8f9fa;
    }

    /* Order ID Link */
    .cart-status-container tbody td a[href*='cart-detail.php'] {
        color: #F39C12;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .cart-status-container tbody td a[href*='cart-detail.php']:hover {
        color: #E67E22;
        text-decoration: underline;
    }

    /* Status Badges */
    .status-badge {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-preparing {
        background-color: #FFF3CD;
        color: #856404;
        border: 1px solid #F39C12;
    }

    .status-shipping {
        background-color: #D1ECF1;
        color: #0C5460;
        border: 1px solid #2C3E50;
    }

    .status-delivered {
        background-color: #D4EDDA;
        color: #155724;
        border: 1px solid #28A745;
    }

    /* Total Price */
    .total-price {
        color: #F39C12;
        font-weight: 700;
        font-size: 16px;
    }

    /* Rating Links */
    .cart-status-container tbody td a[href*='vote.php'] {
        display: inline-block;
        padding: 8px 16px;
        background: #F39C12;
        color: white;
        border-radius: 6px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .cart-status-container tbody td a[href*='vote.php']:hover {
        background: #E67E22;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(243, 156, 18, 0.3);
    }

    /* Waiting Rating Text */
    .cart-status-container tbody td a:not([href*='vote.php']):not([href*='cart-detail.php']) {
        color: #95A5A6;
        font-style: italic;
        cursor: default;
        pointer-events: none;
    }

    /* Responsive Table */
    @media (max-width: 768px) {
        .cart-status-container {
            padding: 20px;
            overflow-x: auto;
        }

        .cart-status-container table {
            min-width: 800px;
        }
    }

    /* Empty State Button Hover */
    .empty-cart-btn {
        display: inline-block;
        padding: 12px 30px;
        background: #F39C12;
        color: white !important;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .empty-cart-btn:hover {
        background: #E67E22;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(243, 156, 18, 0.4);
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
                    <a href="./cart.php">Giỏ hàng của tôi</a>
                    <span><i class='bx bxs-chevrons-right'></i></span>
                    <a href="#">Đơn hàng</a>
                </div>
            </div>

            <div class="box" style="padding: 0 40px">
                <div class="product-info">
                    <?php
                    $orders = getOrderByUserId();
                    if (mysqli_num_rows($orders) == 0) {
                    ?>
                        <div class="cart-status-container" style="text-align: center;">
                            <h4 style="border: none;">Giỏ hàng trống</h4>
                            <p style="font-size: 16px; color: #7F8C8D; margin-bottom: 20px;">
                                Bạn chưa có đơn hàng nào. Hãy khám phá các sản phẩm của chúng tôi!
                            </p>
                            <a href="./products.php" class="empty-cart-btn">
                                Mua sắm ngay
                            </a>
                        </div>
                    <?php } else { ?>
                        <div class="container my-4">
                            <div class="cart-status-container">
                                <h4>Đơn hàng của bạn</h4>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Mã đơn hàng</th>
                                                <th>Ngày đặt</th>
                                                <th>Trạng thái thanh toán</th>
                                                <th>Vận chuyển</th>
                                                <th>Tổng tiền</th>
                                                <th>Đánh giá</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($orders as $order) {
                                                //  foreach ($order as $key => $value) {
                                                //     echo $key . ": " . $value . "<br>";
                                                // }
                                                // echo "<br>";
                                            ?>
                                                <tr>
                                                    <td style="text-align: left;">
                                                        <a style="color: #0d6efd;" href="./cart-detail.php?cart_id=<?= $order['id'] ?>">
                                                            COSS<?= $order['id'] ?>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <?= $order['created_at'] ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if ($order['status'] == '2') {
                                                            echo "<span class='status-badge status-preparing'>Đang chuẩn bị hàng</span>";
                                                        } else if ($order['status'] == '3') {
                                                            echo "<span class='status-badge status-shipping'>Đang giao hàng</span>";
                                                        } else {
                                                            echo "<span class='status-badge status-delivered'>Đã giao</span>";
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if ($order['payment'] == '1') {
                                                            echo "COD";
                                                        } else if ($order['payment'] == '0') {
                                                            echo "Ngân hàng";
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        $
                                                        <span class="total-price">
                                                            <?= $order['total'] ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if ($order['status'] == 4) {
                                                            $id = $order['id'];
                                                            if (isset($order['rate']) && $order['rate'] > 0) {
                                                                echo "<a href='./vote.php?id=$id'> Đánh giá lại </a>";
                                                            } else {
                                                                echo "<a href='./vote.php?id=$id'> Đánh giá </a>";
                                                            }
                                                        } else {
                                                            echo '<a> Chờ đánh giá </a>';
                                                        }
                                                        ?>
                                                    </td>

                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
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
        $('.input-number').on('change', function(e) {
            if (e.target.value == 0) {
                e.target.value = 1;
            }
            const node = $(this).parent().parent();
            const price = parseInt(node.find('.product-price').val());
            let total_order = parseInt(e.target.value);
            let total_price = price * total_order;
            node.find('.total-price').html(total_price);
        })
    });
</script>

</html>