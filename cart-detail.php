<?php 
include("./includes/header.php");
if (!isset($_SESSION['auth_user']['id'])){
    die("Từ Chối truy cập <a href='./login'>Đăng nhập ngay</a>");
}
?>;

<style>
    th,td{
        padding: 5px;
        text-align: center;
    }
    .input-number{
        width: 100%;
        font-size: 20px;
        outline: none;
        border: none;
    }
    .btn-buy{
        border: none;
        outline: none;
        font-size: 17px;
        padding: 2px 6px;
        border-radius: 2px;
        background-color: #59e1ff;
        display: inline-block;
    }
    .table td{
        text-align: center;
        vertical-align: middle;
    }
    .table .thead-dark th{
        text-align: center;
        vertical-align: middle;
    }
    .table-bordered {
        border: 1px solid #dee2e6;
    }
.table {
    width: 100%;
    margin-bottom: 1rem;
    color: #212529;
}
.table {
    --bs-table-bg: transparent;
    --bs-table-accent-bg: transparent;
    --bs-table-striped-color: #212529;
    --bs-table-striped-bg: rgba(0, 0, 0, 0.05);
    --bs-table-active-color: #212529;
    --bs-table-active-bg: rgba(0, 0, 0, 0.1);
    --bs-table-hover-color: #212529;
    --bs-table-hover-bg: rgba(0, 0, 0, 0.075);
    width: 100%;
    margin-bottom: 1rem;
    color: #212529;
    vertical-align: top;
    border-color: #dee2e6;
}
table {
    border-collapse: collapse;
}
table {
    caption-side: bottom;
    border-collapse: collapse;
}
.table>thead {
    vertical-align: bottom;
}
tbody, td, tfoot, th, thead, tr {
    border-color: inherit;
    border-style: solid;
    border-width: 0;
}
.table-bordered>:not(caption)>* {
    border-width: 1px 0;
}
tbody, td, tfoot, th, thead, tr {
    border-color: inherit;
    border-style: solid;
    border-width: 0;
}
.table>:not(:last-child)>:last-child>* {
    border-bottom-color: currentColor;
}
.table .thead-dark th {
    color: #fff;
    background-color: #343a40;
    border-color: #454d55;
}
.table .thead-dark th {
    text-align: center;
    vertical-align: middle;
}
.table-bordered thead td, .table-bordered thead th {
    border-bottom-width: 2px;
}
.table thead th {
    vertical-align: bottom;
    border-bottom: 2px solid #dee2e6;
}
.table-bordered td, .table-bordered th {
    border: 1px solid #dee2e6;
}
.table td, .table th {
    padding: 0.75rem;
    vertical-align: top;
    border-top: 1px solid #dee2e6;
}
.table-bordered>:not(caption)>*>* {
    border-width: 0 1px;
}
.table>:not(caption)>*>* {
    padding: 0.5rem 0.5rem;
    background-color: var(--bs-table-bg);
    border-bottom-width: 1px;
    box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg);
}
.text-center {
    text-align: center!important;
}
.table>tbody {
    vertical-align: inherit;
}
tbody, td, tfoot, th, thead, tr {
    border-color: inherit;
    border-style: solid;
    border-width: 0;
}
.table-bordered>:not(caption)>* {
    border-width: 1px 0;
}
tbody, td, tfoot, th, thead, tr {
    border-color: inherit;
    border-style: solid;
    border-width: 0;
}
.table-bordered td, .table-bordered th {
    border: 1px solid #dee2e6;
}
.table td, .table th {
    padding: 0.75rem;
    vertical-align: top;
    border-top: 1px solid #dee2e6;
}
.table td {
    text-align: center;
    vertical-align: middle;
}
.table-bordered>:not(caption)>*>* {
    border-width: 0 1px;
}
.text-right {
    text-align: right!important;
}
.text-right {
    text-align: right!important;
}
</style>
<!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css"
        integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous"> -->
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
                            $order_total+=$order["quantity"]*$order["selling_price"];
                            // foreach ($order as $key => $value) {
                            //     echo $key . ": " . $value . "<br>";
                            // }
                            // echo "<br>";
                        }
                    } else $orders = [];
                ?>

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
                                <?php foreach ($orders as $order){ ?>
                                <tr>
                                    <td>
                                        <center><a style="color:#0d6efd" href="./product-detail.php?slug=<?= $order['slug']?>" title="Sản phẩm"><?= $order['name']?></a></center> <br>
                                    </td>
                                    <td><center ><?= $order['slug']?></center></td>
                                    <td class="text-right "><center >$<?= $order['selling_price']?></center></td>
                                    <td class="text-center "><?= $order['quantity']?></td>
                                    <td class="text-right "><center>$<?= $order['selling_price']*$order['quantity']?></center></td>
                                </tr>
                                <?php } ?>
                                <tr class="order_summary">
                                    <td colspan="4" class="text-center "><b>Giá sản phẩm</b></td>
                                    <td class="text-right"><b><center>$<?= $order_total?></center></b></td>
                                </tr>
                                <tr class="order_summary">
                                    <td colspan="4" class="text-center "><b>Chuyển phát nhanh GHN</b></td>
                                    <td class="text-right"><b><center>$0</center></b></td>
                                </tr>
                                <tr class="order_summary order_total">
                                    <td colspan="4" class="text-center "><b>Tổng tiền</b></td>
                                    <td class="text-right"><b><center>$<?= $order_total?></center></b></td>
                                </tr>
                                </tbody>
                            </table>
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
        $(document).ready(function () {
            $('.input-number').on('change', function (e) {
                if (e.target.value == 0){
                    e.target.value = 1;
                }
                const node      = $(this).parent().parent();
                const price     = parseInt(node.find('.product-price').val());
                let total_order = parseInt(e.target.value);
                let total_price = price * total_order;
                node.find('.total-price').html(total_price);
            })
        });
    </script>
</html>