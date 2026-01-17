<?php
include("./includes/header.php");
?>
<script src="../assets/js/boostrap.bundle.js"></script>

<style>
    .register-wrapper {
        min-height: calc(100vh - 300px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60px 20px;
        background: linear-gradient(135deg, #f5f5f5 0%, #e3e3e3 100%);
    }

    .register-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        max-width: 500px;
        width: 100%;
    }

    .register-header {
        background: linear-gradient(135deg, #000 0%, #333 100%);
        padding: 40px 30px;
        text-align: center;
    }

    .register-header h1 {
        color: white;
        font-size: 28px;
        margin: 0;
        font-weight: 600;
    }

    .register-body {
        padding: 40px 30px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
        font-size: 14px;
    }

    .form-group input {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e3e3e3;
        border-radius: 5px;
        font-size: 15px;
        transition: all 0.3s ease;
    }

    .form-group input:focus {
        outline: none;
        border-color: #000;
    }

    .btn-register {
        width: 100%;
        padding: 14px;
        background: #000;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-top: 10px;
    }

    .btn-register:hover {
        background: #333;
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
    }

    .register-footer {
        text-align: center;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #e3e3e3;
    }

    .register-footer a {
        color: #000;
        text-decoration: none;
        font-weight: 500;
    }

    .register-footer a:hover {
        text-decoration: underline;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    @media (max-width: 600px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="register-wrapper">
    <div class="register-card">
        <div class="register-header">
            <h1>Đăng ký</h1>
        </div>
        <div class="register-body">
            <form action="./functions/authcode.php" method="POST" id="register-account">
                <div class="form-group">
                    <label for="name">Họ tên</label>
                    <input type="text" required name="name" id="name" placeholder="Nhập họ tên của bạn">
                </div>

                <div class="form-group">
                    <label for="InputEmail">Email</label>
                    <input type="email" required name="email" id="InputEmail" placeholder="Nhập email của bạn">
                </div>

                <div class="form-group">
                    <label for="phone">Số điện thoại</label>
                    <input type="tel" required name="phone" id="phone" placeholder="Nhập số điện thoại">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="InputPassword1">Mật khẩu</label>
                        <input type="password" required name="password" id="InputPassword1" placeholder="Nhập mật khẩu">
                    </div>

                    <div class="form-group">
                        <label for="InputPassword2">Xác nhận</label>
                        <input type="password" required name="cpassword" id="InputPassword2" placeholder="Xác nhận mật khẩu">
                    </div>
                </div>

                <input type="hidden" name="register-btn" value="check">
                <button type="submit" class="btn-register">Đăng ký</button>

                <div class="register-footer">
                    <p>Đã có tài khoản? <a href="./login.php">Đăng nhập ngay</a></p>
                </div>
            </form>
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
    document.getElementById("register-account").addEventListener('submit', function(e) {
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
            alertify.error('Vui lòng nhập mật khẩu nhiều hơn 6 ký tự');
            e.preventDefault();
        }
    })
</script>
<?php include("./includes/footer.php") ?>