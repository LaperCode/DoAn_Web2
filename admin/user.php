<?php
include("../admin/includes/header.php");

// $users= getAll("users");
$users = getAllUsers();

?>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">Người dùng</h6>
                        </div>
                    </div>
                    <div class="card-header">
                        <form action="./add_user.php" method="POST">
                            <button type="submit" name="delete_product_btn" class="btn btn-danger" style="background-color: green;">Thêm </button>
                        </form>
                    </div>
                    <div class="card-body px-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Tên</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-10 ps-2">Số điện thoại</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-10 ps-2">Địa chỉ</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Tổng đơn</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Ngày bắt đầu</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Tùy chỉnh</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Khóa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user) { ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm"><?= $user['name'] ?></h6>
                                                        <p class="text-xs text-secondary mb-0"><?= $user['email'] ?></p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">
                                                    <?= $user['phone'] ?>
                                                </p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">
                                                    <?= $user['address'] ?>
                                                </p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <?= $user['total_buy'] ?>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="text-secondary text-xs font-weight-bold">
                                                    <?= date('d-m-Y', strtotime($user['creat_at'])); ?>
                                                </span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <form action="../functions/authcode.php" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này không?');">
                                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>" />
                                                    <button type="submit" name="delete_user_btn" class="btn btn-danger">Xóa </button>
                                                </form>
                                                <form action="./edit_user.php" method="POST">
                                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>" />
                                                    <button name="update_user_btn" class="btn btn-danger" style="background-color: blue; ">Sửa </button>
                                                </form>
                                            </td>
                                            <td class="align-middle text-center">
                                                <form action="../functions/authcode.php" method="POST">
                                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>" />
                                                    <input type="checkbox" name="lock_user" style="transform: scale(2.5);" onchange="this.form.submit()"
                                                        <?= $user['role_as'] == 2 ? 'checked' : '' ?>> </input>
                                                </form>
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