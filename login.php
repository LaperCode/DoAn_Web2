<?php
include("./includes/header.php");

?>
<script src="../assets/js/boostrap.bundle.js"></script>
<link rel="stylesheet" href="./assets/css/author.css">

<style>
    .login-wrapper {
        min-height: calc(100vh - 300px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60px 20px;
        background: linear-gradient(135deg, #f5f5f5 0%, #e3e3e3 100%);
    }

    .login-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        max-width: 500px;
        width: 100%;
    }

    .login-header {
        background: linear-gradient(135deg, #000 0%, #333 100%);
        padding: 40px 30px;
        text-align: center;
    }

    .login-header h1 {
        color: white;
        font-size: 28px;
        margin: 0;
        font-weight: 600;
    }

    .login-body {
        padding: 40px 30px;
    }

    .form-group {
        margin-bottom: 25px;
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

    .btn-login {
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
    }

    .btn-login:hover {
        background: #333;
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
    }

    .login-footer {
        text-align: center;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #e3e3e3;
    }

    .login-footer a {
        color: #000;
        text-decoration: none;
        font-weight: 500;
    }

    .login-footer a:hover {
        text-decoration: underline;
    }
</style>

<div class="login-wrapper">
    <div class="login-card">
        <div class="login-header">
            <h1>Đăng nhập</h1>
        </div>
        <div class="login-body">
            <form action="./functions/authcode.php" method="POST">
                <div class="form-group">
                    <label for="email">Địa chỉ Email</label>
                    <input type="email" name="email" id="email" placeholder="Nhập email của bạn" required>
                </div>
                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" name="password" id="password" placeholder="Nhập mật khẩu" required>
                </div>
                <button type="submit" name="login_btn" class="btn-login">Đăng nhập</button>
                <div class="login-footer">
                    <p>Chưa có tài khoản? <a href="./register.php">Đăng ký ngay</a></p>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include("./includes/footer.php") ?>