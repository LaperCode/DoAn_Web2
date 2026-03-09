<?php
include("../admin/includes/header.php");

// $users= getAll("users");
$users = getAllUsers($_SESSION['auth_user']['id']);

?>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card my-4" style="border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                    <!-- Card Header -->
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="shadow-primary border-radius-lg pt-4 pb-3 px-3 d-flex align-items-center justify-content-between"
                            style="background: linear-gradient(90deg, #FF9800 0%, #81C784 100%);">
                            <h6 class="text-white mb-0 fw-bold" style="font-size: 16px; letter-spacing: 0.5px;">
                                <i class="material-icons align-middle me-2" style="font-size:20px;">group</i>
                                Quản lý người dùng
                            </h6>
                            <a href="add_user.php" class="btn btn-sm mb-0"
                                style="background:#fff; color:#FF9800; font-weight:700; border-radius:8px; padding: 6px 16px;">
                                <i class="fa fa-plus me-1"></i> Thêm người dùng
                            </a>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="card-body px-0 pb-2 pt-3">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0" style="font-size: 14px;">
                                <thead>
                                    <tr style="background: #FFF8F0;">
                                        <th class="text-uppercase text-xxs font-weight-bolder ps-3" style="color:#F57C00; padding: 12px 8px;">Người dùng</th>
                                        <th class="text-uppercase text-xxs font-weight-bolder" style="color:#F57C00; padding: 12px 8px;">Số điện thoại</th>
                                        <th class="text-uppercase text-xxs font-weight-bolder" style="color:#F57C00; padding: 12px 8px;">Địa chỉ</th>
                                        <th class="text-uppercase text-xxs font-weight-bolder text-center" style="color:#F57C00; padding: 12px 8px;">Tổng đơn</th>
                                        <th class="text-uppercase text-xxs font-weight-bolder text-center" style="color:#F57C00; padding: 12px 8px;">Ngày tham gia</th>
                                        <th class="text-uppercase text-xxs font-weight-bolder text-center" style="color:#F57C00; padding: 12px 8px;">Thao tác</th>
                                        <th class="text-uppercase text-xxs font-weight-bolder text-center" style="color:#F57C00; padding: 12px 8px;">Khóa TK</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user) { ?>
                                        <tr style="border-bottom: 1px solid #f0f0f0; transition: background 0.2s;"
                                            onmouseover="this.style.background='#FFF8F0'" onmouseout="this.style.background='#fff'">
                                            <!-- Tên + Email -->
                                            <td class="ps-3" style="padding: 12px 8px;">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="d-flex align-items-center justify-content-center rounded-circle text-white fw-bold"
                                                        style="width:38px; height:38px; min-width:38px; background: linear-gradient(135deg,#FF9800,#81C784); font-size:15px;">
                                                        <?= mb_strtoupper(mb_substr($user['name'], 0, 1, 'UTF-8')) ?>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark" style="font-size:13.5px;"><?= htmlspecialchars($user['name']) ?></div>
                                                        <div class="text-muted" style="font-size:12px;"><?= htmlspecialchars($user['email']) ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <!-- SĐT -->
                                            <td style="padding: 12px 8px;">
                                                <span style="font-size:13px; color:#344054;">
                                                    <i class="fa fa-phone me-1" style="color:#81C784;"></i>
                                                    <?= htmlspecialchars($user['phone']) ?>
                                                </span>
                                            </td>
                                            <!-- Địa chỉ -->
                                            <td style="padding: 12px 8px; max-width: 160px; word-break: break-word; white-space: normal;">
                                                <span style="font-size:13px; color:#344054;">
                                                    <i class="fa fa-map-marker-alt me-1" style="color:#FF9800;"></i>
                                                    <?= htmlspecialchars($user['address']) ?>
                                                </span>
                                            </td>
                                            <!-- Tổng đơn -->
                                            <td class="text-center" style="padding: 12px 8px;">
                                                <span class="badge rounded-pill" style="background: linear-gradient(90deg,#FF9800,#FFB74D); color:#fff; font-size:13px; padding: 5px 14px;">
                                                    <?= $user['total_buy'] ?>
                                                </span>
                                            </td>
                                            <!-- Ngày tham gia -->
                                            <td class="text-center" style="padding: 12px 8px;">
                                                <span style="font-size:12.5px; color:#6c757d;">
                                                    <i class="fa fa-calendar-alt me-1"></i>
                                                    <?= date('d/m/Y', strtotime($user['creat_at'])) ?>
                                                </span>
                                            </td>
                                            <!-- Thao tác -->
                                            <td class="text-center" style="padding: 12px 8px;">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <form action="./edit_user.php" method="POST">
                                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>" />
                                                        <button name="update_user_btn" class="btn btn-sm mb-0"
                                                            style="background: linear-gradient(90deg,#FF9800,#81C784); color:#fff; font-weight:600; border-radius:7px; padding:5px 14px;">
                                                            <i class="fa fa-edit me-1"></i>Sửa
                                                        </button>
                                                    </form>
                                                    <form action="../functions/authcode.php" method="POST"
                                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này không?');">
                                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>" />
                                                        <button type="submit" name="delete_user_btn" class="btn btn-sm btn-danger mb-0"
                                                            style="border-radius:7px; padding:5px 14px;">
                                                            <i class="fa fa-trash me-1"></i>Xóa
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                            <!-- Khóa tài khoản -->
                                            <td class="text-center" style="padding: 12px 8px;">
                                                <form action="../functions/authcode.php" method="POST">
                                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>" />
                                                    <div class="form-check form-switch d-flex justify-content-center m-0">
                                                        <input class="form-check-input" type="checkbox" name="lock_user"
                                                            style="transform: scale(1.4); cursor:pointer; accent-color: #FF9800;"
                                                            onchange="this.form.submit()"
                                                            <?= $user['role_as'] == 2 ? 'checked' : '' ?>>
                                                    </div>
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