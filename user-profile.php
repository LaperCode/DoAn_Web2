<?php

include("./includes/header.php");

if (!isset($_SESSION['auth_user']['id'])) {
  die("Tu choi truy cap");
}


$id = $_SESSION['auth_user']['id'];

$users = getByID("users", $id);
$data = mysqli_fetch_array($users);

?>
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script> -->
<style>
  .profile-container {
    max-width: 900px;
    margin: 50px auto;
    padding: 0 20px;
  }

  .profile-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    margin-bottom: 30px;
    animation: fadeInUp 0.6s ease-out;
  }

  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .profile-header {
    background: linear-gradient(135deg, #2C3E50 0%, #34495E 100%);
    color: white;
    padding: 50px 30px;
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  .profile-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 400px;
    height: 400px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 50%;
  }

  .profile-header::after {
    content: '';
    position: absolute;
    bottom: -50%;
    left: -10%;
    width: 300px;
    height: 300px;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 50%;
  }

  .profile-header h1 {
    font-size: 36px;
    font-weight: 700;
    margin: 0 0 10px 0;
    position: relative;
    z-index: 1;
    letter-spacing: -0.5px;
  }

  .profile-header p {
    font-size: 16px;
    margin: 0;
    opacity: 0.85;
    position: relative;
    z-index: 1;
    font-weight: 300;
  }

  .profile-body {
    padding: 40px;
  }

  .form-group {
    margin-bottom: 25px;
  }

  .form-group label {
    font-weight: 600;
    color: #2C3E50;
    margin-bottom: 8px;
    display: block;
    font-size: 14px;
  }

  .form-group label i {
    margin-right: 8px;
    color: #F39C12;
  }

  .form-control {
    border: 2px solid #E0E0E0;
    border-radius: 10px;
    padding: 12px 16px;
    font-size: 15px;
    transition: all 0.3s ease;
  }

  .form-control:focus {
    border-color: #F39C12;
    box-shadow: 0 0 0 3px rgba(243, 156, 18, 0.1);
    outline: none;
  }

  .form-control[readonly] {
    background: #F8F9FA;
    cursor: not-allowed;
    color: #6c757d;
  }

  .btn-save {
    background: linear-gradient(135deg, #F39C12 0%, #E67E22 100%);
    border: none;
    color: white;
    padding: 14px 40px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3);
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .btn-save:hover {
    background: linear-gradient(135deg, #E67E22 0%, #F39C12 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(243, 156, 18, 0.4);
  }

  .btn-save:active {
    transform: scale(0.98);
  }

  .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
  }

  @media (max-width: 768px) {
    .form-row {
      grid-template-columns: 1fr;
    }

    .profile-body {
      padding: 30px 20px;
    }

    .profile-header h1 {
      font-size: 24px;
    }
  }

  .password-section {
    margin-top: 40px;
    padding-top: 40px;
    border-top: 2px dashed #E0E0E0;
  }

  .password-section h3 {
    color: #2C3E50;
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 20px;
  }

  .password-hint {
    background: #FFF3CD;
    border-left: 4px solid #FFA726;
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 14px;
    color: #856404;
  }

  .password-hint i {
    margin-right: 8px;
    color: #FFA726;
  }
</style>

<body>

  <!-- header -->

  <!-- end header -->
  <div class="profile-container">
    <div class="profile-card">
      <div class="profile-header">
        <h1>Thông Tin Cá Nhân</h1>
        <p>Quản lý và cập nhật thông tin tài khoản của bạn</p>
      </div>
      <div class="profile-body">
        <form action="./functions/authcode.php" method="POST">

          <!-- Thông tin cơ bản -->
          <div class="form-row">
            <div class="form-group">
              <label for="name"><i class="fas fa-user"></i>Họ và Tên</label>
              <input class="form-control" required type="text" id="name" name="name" value="<?= $data['name'] ?>" placeholder="Nhập họ tên đầy đủ">
            </div>

            <div class="form-group">
              <label for="email"><i class="fas fa-envelope"></i>Email</label>
              <input readonly class="form-control" required type="email" id="email" name="email" value="<?= $data['email'] ?>" placeholder="email@example.com">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="phone"><i class="fas fa-phone"></i>Số Điện Thoại</label>
              <input class="form-control" required type="text" id="phone" name="phone" value="<?= $data['phone'] ?>" placeholder="0123456789">
            </div>

            <div class="form-group">
              <label for="address"><i class="fas fa-map-marker-alt"></i>Địa Chỉ</label>
              <input class="form-control" required type="text" id="address" name="address" value="<?= $data['address'] ?>" placeholder="Nhập địa chỉ đầy đủ">
            </div>
          </div>

          <!-- Đổi mật khẩu -->
          <div class="password-section">
            <h3><i class="fas fa-lock"></i> Đổi Mật Khẩu</h3>
            <div class="password-hint">
              <i class="fas fa-info-circle"></i>
              <strong>Lưu ý:</strong> Chỉ điền vào phần này nếu bạn muốn thay đổi mật khẩu. Để trống nếu không muốn đổi.
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="password"><i class="fas fa-key"></i>Mật Khẩu Mới</label>
                <input class="form-control" type="password" id="password" name="password" placeholder="Nhập mật khẩu mới">
              </div>

              <div class="form-group">
                <label for="cpassword"><i class="fas fa-check-circle"></i>Xác Nhận Mật Khẩu</label>
                <input class="form-control" type="password" id="cpassword" name="cpassword" placeholder="Nhập lại mật khẩu mới">
              </div>
            </div>
          </div>

          <input type="hidden" name="update_user_btn" value="true">
          <div style="text-align: center; margin-top: 30px;">
            <button type="submit" class="btn-save">
              <i class="fas fa-save"></i> Lưu Thay Đổi
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>
<script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<script>
  <?php if (isset($_SESSION['message'])) {
  ?>
    alertify.set('notifier', 'position', 'top-right');
    alertify.success('<?= $_SESSION['message'] ?>');
  <?php
    unset($_SESSION['message']);
  }
  ?>
</script>

</html>