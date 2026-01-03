<?php
include("../admin/includes/header.php");

$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : -1;
$startDate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : "";
$endDate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : "";//var_dump($startDate); var_dump($endDate); exit;
$district = isset($_REQUEST['district']) ? $_REQUEST['district'] : "";
$city = isset($_REQUEST['city']) ? $_REQUEST['city'] : "";

$userid = isset($_REQUEST['id']) ? $_REQUEST['id'] : null; //var_dump($userid); exit;

$orders = donhangKH($startDate, $endDate, $district, $city, $userid);

?>
<style>
    input[name="district"],
    input[name="city"] {
        border: 2px solid rgb(233, 56, 103);
        /* Viền màu tím */
        border-radius: 6px;
        padding: 10px;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    input[name="district"]:focus,
    input[name="city"]:focus {
        border-color: #ff512f;
        /* Đổi viền thành màu cam khi focus */
        box-shadow: 0px 0px 8px rgba(255, 81, 47, 0.6);
        outline: none;
    }
</style>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">Đơn Hàng</h6>
                        </div>
                    </div>
                    <div class="card-body px-0 pb-2">
                        <div class="table-responsive p-0">

                            <!-- Form lọc đơn hàng -->
                            <form method="GET" action="./DonHang.php" style="margin: 20px;">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($userid) ?>">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="start_date">Từ ngày:</label>
                                        <input type="date" id="start_date" name="start_date" class="form-control" value="<?= $startDate ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="end_date">Đến ngày:</label>
                                        <input type="date" id="end_date" name="end_date" class="form-control" value="<?= $endDate ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="district">Quận/Huyện:</label>
                                        <input type="text" id="district" name="district" class="form-control" value="<?= $district ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="city">Thành phố:</label>
                                        <input type="text" id="city" name="city" class="form-control" value="<?= $city ?>">
                                    </div>
                                    <div class="col-md-2 text-right mt-4">
                                        <button type="submit" class="btn btn-primary">Lọc</button>
                                    </div>
                                </div>
                            </form>

                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Khách hàng</th>
                                        <th>Sản phẩm</th>
                                        <th>Địa chỉ</th>
                                        <th class="text-center">Trạng thái</th>
                                        <th class="text-center">Thời gian đặt</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($orders) == 0) { ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-danger">
                                                <strong>Không có đơn hàng nào phù hợp.</strong>
                                            </td>
                                        </tr>
                                    <?php } else { ?>
                                        <?php foreach ($orders as $order) { ?>
                                            <tr>
                                                <td>#<?= $order['id'] ?></td>
                                                <td>
                                                    <h6 class="mb-0"><?= $order['name'] ?></h6>
                                                    <p class="text-secondary mb-0"><?= $order['email'] ?></p>
                                                </td>
                                                <td>
                                                    <a href="./ChiTietDonHang.php?id_order=<?= $order['id'] ?>">View now</a>
                                                    <p class="text-secondary mb-0">Quantity: <?= $order['quantity'] ?></p>
                                                </td>
                                                <td>
                                                    <p><?= $order['address'] ?></p>
                                                </td>
                                                <td class="text-center">
                                                    <?php
                                                    if ($order['status'] == 1) echo '<span class="badge bg-warning">Chờ xác nhận</span>';
                                                    elseif ($order['status'] == 2) echo '<span class="badge bg-primary">Đã đặt</span>';
                                                    elseif ($order['status'] == 3) echo '<span class="badge bg-info">Đang giao</span>';
                                                    elseif ($order['status'] == 4) echo '<span class="badge bg-success">Hoàn tất</span>';
                                                    ?>
                                                </td>
                                                <td class="text-center"><?= date('d-m-Y', strtotime($order['created_at'])); ?></td>
                                            </tr>
                                        <?php } ?>
                                    <?php } ?>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<?php include("../admin/includes/footer.php"); ?>