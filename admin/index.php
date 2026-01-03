<?php
include("../admin/includes/header.php");

$from_date = $_GET['start_date'] ?? null;
$to_date = $_GET['end_date'] ?? null;

$users = thongkeKH($from_date, $to_date);

?>
<style>
    input[name="xem"] {
        background: linear-gradient(45deg, #ff512f, #dd2476);
        border: none;
        color: white;
        padding: 12px 24px;
        font-size: 10px;
        font-weight: bold;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    input[name="xem"]:hover {
        background: linear-gradient(45deg, #dd2476, #ff512f);
        transform: translateY(-2px);
        box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.3);
    }

    input[name="xem"]:active {
        transform: scale(0.95);
    }
</style>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <br>
                <br>
                <div class="row">
                    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                        <div class="card">
                            <div class="card-header p-3 pt-2">
                                <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                                    <i class="material-icons opacity-10">person</i>
                                </div>
                                <div class="text-end pt-1">
                                    <p class="text-sm mb-0 text-capitalize">Khách hàng</p>
                                    <h4 class="mb-0"><?= totalValue('users') ?></h4>
                                </div>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-3">
                                <p class="mb-0"><span class="text-success text-sm font-weight-bolder">+3% </span>so với tháng trước</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                        <div class="card">
                            <div class="card-header p-3 pt-2">
                                <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                                    <i class="material-icons opacity-10">table_view</i>
                                </div>
                                <div class="text-end pt-1">
                                    <p class="text-sm mb-0 text-capitalize">Tổng sản phẩm</p>
                                    <h4 class="mb-0"><?= totalValue('products') ?></h4>
                                </div>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-3">
                                <p class="mb-0"><span class="text-success text-sm font-weight-bolder">+55% </span>so với tuần trước</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                        <div class="card">
                            <div class="card-header p-3 pt-2">
                                <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                                    <i class="material-icons opacity-10">receipt_long</i>
                                </div>
                                <div class="text-end pt-1">
                                    <p class="text-sm mb-0 text-capitalize">Tổng đơn hàng</p>
                                    <h4 class="mb-0"><?= totalValue('orders') ?></h4>
                                </div>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-3">
                                <p class="mb-0"><span class="text-danger text-sm font-weight-bolder">-2%</span> so với hôm qua </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="card">
                            <div class="card-header p-3 pt-2">
                                <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                                    <i class="material-icons opacity-10">weekend</i>
                                </div>
                                <div class="text-end pt-1">
                                    <p class="text-sm mb-0 text-capitalize">Giảm giá</p>
                                    <h4 class="mb-0">$<?= totalPriceGet() ?></h4>
                                </div>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-3">
                                <p class="mb-0"><span class="text-success text-sm font-weight-bolder">+5% </span>so với hôm qua</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">5 Khách Hàng Mua Nhiều Nhất</h6>
                        </div>
                    </div>
                    <div class="card-body px-0 pb-2">
                        <div class="table-responsive p-0">
                            <!-- Form lọc đơn hàng -->
                            <form method="GET" action="./index.php" style="margin: 20px;">
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
                                    <div class="col-md-2 text-right mt-4">
                                        <button type="submit" class="btn btn-primary">OK</button>
                                    </div>
                                </div>
                            </form>
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-10 ps-2">ID</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Tên</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Tổng đơn</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Tổng tiền</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Xem chi tiết</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user) { ?>
                                        <tr>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">
                                                    <?= $user['id'] ?>
                                                </p>
                                            </td>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm"><?= $user['name'] ?></h6>
                                                        <p class="text-xs text-secondary mb-0"><?= $user['email'] ?></p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <?= $user['total_buy'] ?>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="text-secondary text-xs font-weight-bold">
                                                    <?= $user['total_spent'] ?>$
                                                </span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="text-secondary text-xs font-weight-bold">
                                                    <form method="GET" action="DonHang.php">
                                                        <input type="hidden" name="id" value=<?= $user['id'] ?> />
                                                        <input type="hidden" name="start_date" value="<?= htmlspecialchars($from_date) ?>" />
                                                        <input type="hidden" name="end_date" value="<?= htmlspecialchars($to_date) ?>" />
                                                        <input type="submit" name="xem" value="Xem Chi Tiết" />
                                                    </form>
                                                </span>
                                            </td>
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