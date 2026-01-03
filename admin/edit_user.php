<?php
include("../admin/includes/header.php");
if (isset($_POST['user_id'])) {
    $id = $_POST['user_id'];
    $users = getByID("users", $id);
    $data = mysqli_fetch_array($users);
}
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="../assets/js/boostrap.bundle.js"></script>

<div class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h1 style="color:white ; ">Sửa Người Dùng</h1>
                    </div>
                    <div class="card-body">
                        <form action="../functions/authcode.php" method="POST" id="update_user">
                            <div class="mb-3">
                                <b><label class="form-label">Họ tên</label></b>
                                <input type="text" required name="name" class="form-control" value="<?= $data['name'] ?>">
                            </div>
                            <div class="mb-3">
                                <b><label for="exampleInputEmail1" class="form-label">Email</label></b>
                                <input type="email" required name="email" class="form-control" id="InputEmail" aria-describedby="emailHelp" value="<?= $data['email'] ?>">
                            </div>
                            <div class="mb-3">
                                <b><label for="exampleInputEmail1" class="form-label">SĐT</label></b>
                                <input type="text" required name="phone" class="form-control" value="<?= $data['phone'] ?>">
                            </div class="mb-3">
                            <div class="mb-3">
                                <b><label for="exampleInputEmail1" class="form-label">Địa chỉ</label></b>
                                <input type="text" required name="address" class="form-control" value="<?= $data['address'] ?>">
                            </div class="mb-3">
                            <div class="mb-3">
                                <b><label for="exampleInputPassword1" class="form-label">Mật khẩu</label></b>
                                <input type="password" required name="password" id="InputPassword1" class="form-control" value="<?= $data['password']?>">
                            </div>
                            <div class="mb-3">
                                <b><label for="exampleInputPassword1" class="form-label">Xác nhận mật khẩu</label></b>
                                <input type="password" required name="cpassword" id="InputPassword2" class="form-control" value="<?= $data['password']?>">
                            </div>
                            <!-- Đăng kí -->
                            <input type="hidden" name="user_idd" value="<?= $data['id'] ?>" />
                            <input type="hidden" name="user_update">
                            <button type="submit" class="btn btn-primary">Cập Nhật</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const validateEmail = (email) => {
        return String(email)
            .toLowerCase()
            .match(
                /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
            );
    };
    document.getElementById("update_user").addEventListener('submit', function(e) {
        let email = document.getElementById("InputEmail").value;
        let password1 = document.getElementById("InputPassword1").value;
        let password2 = document.getElementById("InputPassword2").value;
        if (!validateEmail(email)) {
            alertify.set('notifier', 'position', 'top-right');
            alertify.success('Lỗi email không hợp lệ');
            e.preventDefault();
        } else if (password1 != password2) {
            alertify.set('notifier', 'position', 'top-right');
            alertify.success('Mật khẩu chưa khớp');
            e.preventDefault();
        } else if (password1.length <= 6) {
            alertify.set('notifier', 'position', 'top-right');
            alertify.success('Vui lòng nhập mật khẩu nhiều hơn 6 kí tự');
            e.preventDefault();
        }
    })
</script>
<?php include("./includes/footer.php") ?>