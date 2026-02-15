<?php
include("./includes/header.php");
if (!isset($_SESSION['auth_user']['id'])) {
    die("Từ Chối truy cập <a href='./login'>Đăng nhập ngay</a>");
}
?>

<style>
    body {
        background: #f5f7fa;
    }

    .container.text-center.mx-auto.mt-5 {
        background: white;
        border-radius: 12px;
        padding: 25px 40px 30px 40px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        margin-top: 20px !important;
        margin-bottom: 30px;
    }

    h1 {
        color: #2C3E50;
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 3px solid #F39C12;
    }

    h1 span {
        color: #F39C12;
        font-weight: 700;
    }

    .title-customers {
        color: #2C3E50;
        font-size: 20px;
        font-weight: 700;
        margin: 30px 0 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .table .thead-dark th {
        background-color: #2C3E50 !important;
        color: white !important;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 14px;
        letter-spacing: 0.5px;
        padding: 15px;
        border: none !important;
    }

    .table tbody tr {
        transition: background-color 0.3s ease;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .table td {
        padding: 15px !important;
        vertical-align: middle;
        color: #2C3E50;
        border-color: #ecf0f1 !important;
    }

    .table td a {
        color: #F39C12;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .table td a:hover {
        color: #E67E22;
        text-decoration: underline;
    }

    .order_summary td {
        background-color: #f8f9fa !important;
        font-weight: 600;
        color: #2C3E50;
    }

    .order_total td {
        background-color: #2C3E50 !important;
        color: white !important;
        font-size: 18px;
        font-weight: 700;
    }

    .order_total td b {
        color: #F39C12;
        font-size: 20px;
    }

    .breadcumb {
        padding: 20px 0;
    }

    .breadcumb a {
        color: #2C3E50;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .breadcumb a:hover {
        color: #F39C12;
    }

    .breadcumb span {
        color: #7f8c8d;
        margin: 0 8px;
    }

    /* Export Invoice Button */
    .export-invoice-btn {
        display: inline-block;
        padding: 14px 32px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white !important;
        text-decoration: none !important;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        cursor: pointer;
        border: none;
        outline: none;
    }

    .export-invoice-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    }

    .export-invoice-btn:active {
        transform: translateY(0);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .export-invoice-btn i {
        margin-right: 8px;
        font-size: 18px;
    }

    /* Back Button */
    .back-to-orders-btn {
        display: inline-block;
        padding: 10px 20px;
        background: #2C3E50;
        color: white !important;
        text-decoration: none !important;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
        margin-bottom: 20px;
        box-shadow: 0 2px 6px rgba(44, 62, 80, 0.3);
    }

    .back-to-orders-btn:hover {
        background: #34495E;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(44, 62, 80, 0.5);
    }

    .back-to-orders-btn i {
        margin-right: 8px;
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
                    if (isset($_GET["cart_id"])) {
                        $cart_id = $_GET["cart_id"];
                    } else {
                        $cart_id = "null";
                    }
                    $order_total = 0;
                    if ($cart_id != "null") {
                        $orders = getOrderWasBuy($cart_id);

                        foreach ($orders as $order) {
                            $order_total += $order["quantity"] * $order["selling_price"];
                            // foreach ($order as $key => $value) {
                            //     echo $key . ": " . $value . "<br>";
                            // }
                            // echo "<br>";
                        }
                    } else $orders = [];
                    ?>

                    <!-- Back Button - Outside container -->
                    <?php if (!empty($orders)) { ?>
                        <div style="padding-left: 15px; margin-top: 30px; margin-bottom: 10px;">
                            <a href="./cart-status.php" class="back-to-orders-btn">
                                ← Quay lại danh sách đơn hàng
                            </a>
                        </div>
                    <?php } ?>

                    <div class="container text-center mx-auto mt-5">
                        <?php if (!empty($orders)) { ?>
                            <h1>ĐƠN HÀNG: <span>COSS<?= $cart_id ?></span>, ĐẶT LÚC --- <span><?= $orders[0]["created_at"] ?></span></h1>

                            <div class="container">
                                <h2 class="title-customers">Đơn hàng sản phẩm</h2>
                                <table class="table table-bordered">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th scope="col" class="text-center ">Sản phẩm</th>
                                            <th scope="col" class="text-center ">Mã sản phẩm</th>
                                            <th scope="col" class="text-center ">Giá</th>
                                            <th scope="col" class="text-center ">Số lượng</th>
                                            <th scope="col" class="text-center ">Tổng cộng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order) { ?>
                                            <tr>
                                                <td>
                                                    <center><a style="color:#0d6efd" href="./product-detail.php?slug=<?= $order['slug'] ?>" title="Sản phẩm"><?= $order['name'] ?></a></center> <br>
                                                </td>
                                                <td>
                                                    <center><?= $order['slug'] ?></center>
                                                </td>
                                                <td class="text-right ">
                                                    <center>$<?= $order['selling_price'] ?></center>
                                                </td>
                                                <td class="text-center "><?= $order['quantity'] ?></td>
                                                <td class="text-right ">
                                                    <center>$<?= $order['selling_price'] * $order['quantity'] ?></center>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        <tr class="order_summary">
                                            <td colspan="4" class="text-center "><b>Giá sản phẩm</b></td>
                                            <td class="text-right"><b>
                                                    <center>$<?= $order_total ?></center>
                                                </b></td>
                                        </tr>
                                        <tr class="order_summary">
                                            <td colspan="4" class="text-center "><b>Chuyển phát nhanh GHN</b></td>
                                            <td class="text-right"><b>
                                                    <center>$0</center>
                                                </b></td>
                                        </tr>
                                        <tr class="order_summary order_total">
                                            <td colspan="4" class="text-center "><b>Tổng tiền</b></td>
                                            <td class="text-right"><b>
                                                    <center>$<?= $order_total ?></center>
                                                </b></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <!-- Nút xuất hóa đơn -->
                                <div style="text-align: center; margin-top: 30px;">
                                    <a href="./export-invoice.php?order_id=<?= $cart_id ?>"
                                        class="export-invoice-btn"
                                        target="_blank"
                                        title="Xuất hóa đơn PDF">
                                        <i class='bx bx-download'></i> 📄 Xuất hóa đơn
                                    </a>
                                </div>
                            </div>
                        <?php } else { ?>
                            <h1>Đơn hàng không tồn tại</h1>
                        <?php } ?>
                    </div>

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