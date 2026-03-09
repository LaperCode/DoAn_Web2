<?php
include("../admin/includes/header.php");
if (isset($_POST['user_id'])) {
    $id = $_POST['user_id'];
    $users = getByID("users", $id);
    $data = mysqli_fetch_array($users);
}
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card" style="border-radius:14px; overflow:hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.10);">
                <!-- Header -->
                <div class="card-header p-0">
                    <div class="d-flex align-items-center justify-content-between px-4 py-3"
                        style="background: linear-gradient(90deg, #FF9800 0%, #81C784 100%);">
                        <h5 class="text-white mb-0 fw-bold" style="font-size:18px; letter-spacing:0.3px;">
                            <i class="material-icons align-middle me-2" style="font-size:22px;">manage_accounts</i>
                            Chỉnh sửa người dùng
                        </h5>
                        <a href="user.php" class="btn btn-sm mb-0"
                            style="background:#fff; color:#FF9800; font-weight:700; border-radius:8px; padding:6px 16px;">
                            <i class="fa fa-arrow-left me-1"></i> Quay lại
                        </a>
                    </div>
                </div>

                <!-- Body -->
                <div class="card-body px-4 py-4" style="background:#FFFDF9;">
                    <form action="../functions/authcode.php" method="POST" id="update_user">

                        <!-- Họ tên -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="color:#344054; font-size:13.5px;">
                                <i class="fa fa-user me-1" style="color:#FF9800;"></i> Họ tên
                            </label>
                            <input type="text" required name="name" class="form-control" value="<?= htmlspecialchars($data['name']) ?>"
                                style="border-radius:8px; border:1.5px solid #d0d5dd; font-size:14px; padding:9px 12px;"
                                onfocus="this.style.borderColor='#FF9800'; this.style.boxShadow='0 0 0 3px rgba(255,152,0,0.15)'"
                                onblur="this.style.borderColor='#d0d5dd'; this.style.boxShadow='none'">
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="color:#344054; font-size:13.5px;">
                                <i class="fa fa-envelope me-1" style="color:#FF9800;"></i> Email
                            </label>
                            <input type="email" required name="email" id="InputEmail" class="form-control" value="<?= htmlspecialchars($data['email']) ?>"
                                style="border-radius:8px; border:1.5px solid #d0d5dd; font-size:14px; padding:9px 12px;"
                                onfocus="this.style.borderColor='#FF9800'; this.style.boxShadow='0 0 0 3px rgba(255,152,0,0.15)'"
                                onblur="this.style.borderColor='#d0d5dd'; this.style.boxShadow='none'">
                        </div>

                        <!-- SĐT -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="color:#344054; font-size:13.5px;">
                                <i class="fa fa-phone me-1" style="color:#FF9800;"></i> Số điện thoại
                            </label>
                            <input type="text" required name="phone" class="form-control" value="<?= htmlspecialchars($data['phone']) ?>"
                                style="border-radius:8px; border:1.5px solid #d0d5dd; font-size:14px; padding:9px 12px;"
                                onfocus="this.style.borderColor='#FF9800'; this.style.boxShadow='0 0 0 3px rgba(255,152,0,0.15)'"
                                onblur="this.style.borderColor='#d0d5dd'; this.style.boxShadow='none'">
                        </div>

                        <!-- Địa chỉ -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="color:#344054; font-size:13.5px;">
                                <i class="fa fa-map-marker-alt me-1" style="color:#FF9800;"></i> Địa chỉ
                            </label>
                            <input type="text" required name="address" class="form-control" value="<?= htmlspecialchars($data['address']) ?>"
                                style="border-radius:8px; border:1.5px solid #d0d5dd; font-size:14px; padding:9px 12px;"
                                onfocus="this.style.borderColor='#FF9800'; this.style.boxShadow='0 0 0 3px rgba(255,152,0,0.15)'"
                                onblur="this.style.borderColor='#d0d5dd'; this.style.boxShadow='none'">
                        </div>

                        <!-- Divider -->
                        <hr style="border-color:#FFE0B2; margin: 20px 0;">

                        <!-- Mật khẩu -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="color:#344054; font-size:13.5px;">
                                <i class="fa fa-lock me-1" style="color:#FF9800;"></i> Mật khẩu
                            </label>
                            <input type="password" required name="password" id="InputPassword1" class="form-control" value="<?= $data['password'] ?>"
                                style="border-radius:8px; border:1.5px solid #d0d5dd; font-size:14px; padding:9px 12px;"
                                onfocus="this.style.borderColor='#FF9800'; this.style.boxShadow='0 0 0 3px rgba(255,152,0,0.15)'"
                                onblur="this.style.borderColor='#d0d5dd'; this.style.boxShadow='none'">
                        </div>

                        <!-- Xác nhận mật khẩu -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold" style="color:#344054; font-size:13.5px;">
                                <i class="fa fa-lock me-1" style="color:#81C784;"></i> Xác nhận mật khẩu
                            </label>
                            <input type="password" required name="cpassword" id="InputPassword2" class="form-control" value="<?= $data['password'] ?>"
                                style="border-radius:8px; border:1.5px solid #d0d5dd; font-size:14px; padding:9px 12px;"
                                onfocus="this.style.borderColor='#FF9800'; this.style.boxShadow='0 0 0 3px rgba(255,152,0,0.15)'"
                                onblur="this.style.borderColor='#d0d5dd'; this.style.boxShadow='none'">
                        </div>

                        <input type="hidden" name="user_idd" value="<?= $data['id'] ?>" />
                        <input type="hidden" name="user_update">

                        <button type="submit" class="btn w-100 fw-bold"
                            style="background: linear-gradient(90deg,#FF9800 0%,#81C784 100%); color:#fff; border-radius:9px; padding:10px; font-size:15px; letter-spacing:0.5px; border:none; box-shadow: 0 4px 15px rgba(255,152,0,0.3); transition: all 0.3s;"
                            onmouseover="this.style.opacity='0.88'; this.style.transform='translateY(-1px)'"
                            onmouseout="this.style.opacity='1'; this.style.transform='translateY(0)'">
                            <i class="fa fa-save me-2"></i> Cập nhật
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const validateEmail = (email) => {
        return String(email).toLowerCase().match(
            /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
        );
    };
    document.getElementById("update_user").addEventListener('submit', function(e) {
        let email = document.getElementById("InputEmail").value;
        let password1 = document.getElementById("InputPassword1").value;
        let password2 = document.getElementById("InputPassword2").value;
        if (!validateEmail(email)) {
            alertify.set('notifier', 'position', 'top-right');
            alertify.error('Email không hợp lệ');
            e.preventDefault();
        } else if (password1 != password2) {
            alertify.set('notifier', 'position', 'top-right');
            alertify.error('Mật khẩu chưa khớp');
            e.preventDefault();
        } else if (password1.length <= 6) {
            alertify.set('notifier', 'position', 'top-right');
            alertify.error('Mật khẩu phải nhiều hơn 6 ký tự');
            e.preventDefault();
        }
    });
</script>
<?php include("./includes/footer.php") ?>