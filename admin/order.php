<?php
include("../admin/includes/header.php");

$type = isset($_GET['type']) ? $_GET['type'] : -1;
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : "";
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : "";
$district = isset($_GET['district']) ? $_GET['district'] : "";
$city = isset($_GET['city']) ? $_GET['city'] : "";

$orders = getAllOrder($type, $startDate, $endDate, $district, $city);
?>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">Quản lý đơn hàng</h6>
                        </div>
                    </div>
                    <div class="card-body px-0 pb-2">
                        <div class="table-responsive p-0">

                            <!-- Form lọc đơn hàng -->
                            <form method="GET" action="./order.php" style="margin: 20px;">
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
                                    <div class="col-md-2">
                                        <label for="type">Trạng thái:</label>
                                        <select id="type" name="type" class="form-control">
                                            <option value="-1" <?= ($type == -1) ? 'selected' : '' ?>>Tất cả</option>
                                            <option value="1" <?= ($type == 1) ? 'selected' : '' ?>>Chờ xác nhận</option>
                                            <option value="2" <?= ($type == 2) ? 'selected' : '' ?>>Đã đặt</option>
                                            <option value="3" <?= ($type == 3) ? 'selected' : '' ?>>Đang giao</option>
                                            <option value="4" <?= ($type == 4) ? 'selected' : '' ?>>Hoàn tất</option>
                                        </select>
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
                                    <?php foreach ($orders as $order) { ?>
                                        <tr>
                                            <td>#<?= $order['id'] ?></td>
                                            <td>
                                                <h6 class="mb-0"><?= $order['name'] ?></h6>
                                                <p class="text-secondary mb-0"><?= $order['email'] ?></p>
                                            </td>
                                            <td>
                                                <a href="./order-detail.php?id_order=<?= $order['id'] ?>" class="btn btn-primary btn-sm mb-1">
                                                    <i class="fas fa-eye"></i> View now
                                                </a>
                                                <p class="mb-0 fw-bold text-dark">
                                                    <i class="fas fa-box-open"></i> Quantity: <?= $order['quantity'] ?>
                                                </p>
                                            </td>

                                            <td>
                                                <p><?= $order['address'] ?></p>
                                                <p class="text-secondary mb-0"><?= $order['phone'] ?></p>
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