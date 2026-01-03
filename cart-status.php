<?php
include("./includes/header.php");
if (!isset($_SESSION['auth_user']['id'])) {
    die("Từ Chối truy cập <a href='./login'>Đăng nhập ngay</a>");
}
?>;

<style>
    th,
    td {
        padding: 5px;
        text-align: center;
    }

    .input-number {
        width: 100%;
        font-size: 20px;
        outline: none;
        border: none;
    }

    .btn-buy {
        border: none;
        outline: none;
        font-size: 17px;
        padding: 2px 6px;
        border-radius: 2px;
        background-color: #59e1ff;
        display: inline-block;
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
                        <p style="font-size: 20px; text-align: center;">
                            Giỏ hàng của bạn trống. mua ngay <a style="color: blue; text-decoration: underline" href="./products.php">Tại đây</a>
                        </p>
                    <?php } else { ?>
                        <div class="container my-4">
                            <div class="customer_order">
                                <h1 class="title-customers">Đơn hàng của bạn</h1>
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
                                                            echo "<div class='btn-buy' style='background-color: #f3fc8b'>Đang chuẩn bị hàng</div>";
                                                        } else if ($order['status'] == '3') {
                                                            echo "<div class='btn-buy' style='background-color: #b4fc8b'>Đang giao hàng</div>";
                                                        } else {
                                                            echo "<div class='btn-buy'>Đã giao</div>";
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