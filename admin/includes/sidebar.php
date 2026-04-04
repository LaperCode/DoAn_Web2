<?php
$page = substr($_SERVER['SCRIPT_NAME'], strripos($_SERVER['SCRIPT_NAME'], "/") + 1);
?>

<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 bg-gradient-dark" id="sidenav-main" style="margin: 0 !important; width: 280px; left: 0; top: 0; bottom: 0; height: 100vh; border-radius: 0 !important;">
  <div class="sidenav-header" style="padding-bottom: 0.3rem; padding-top: 0.3rem;">
    <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
    <a class="navbar-brand m-0" href="#" target="_blank" style="display: flex; flex-direction: column; align-items: center; text-align: center;">
      <img src="../images/logo2.jpg" class="navbar-brand-img" style="max-height: 50px; width: auto; border-radius: 8px; margin-bottom: 5px; margin-top: -5px; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">
      <span class="font-weight-bold text-white" style="font-size: 0.75rem; letter-spacing: 0.5px;">TRUNG TÂM ĐIỀU KHIỂN</span>
    </a>
  </div>
  <hr class="horizontal light mb-2" style="margin-top: 2.5rem;">
  <div class="collapse navbar-collapse  w-auto" id="sidenav-collapse-main" style="height: 75vh">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link text-white <?= $page == "index.php" ? 'active bg-gradient-primary' : '' ?>" href="../admin/index.php">
          <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="material-icons opacity-10">dashboard</i>
          </div>
          <span class="nav-link-text ms-1">Bảng điều khiển</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white <?= $page == "user.php" || $page == "edit_user.php" || $page == "add_user.php" ? 'active bg-gradient-primary' : '' ?>" href="user.php">
          <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="material-icons opacity-10">person</i>
          </div>
          <span class="nav-link-text ms-1">Quản lý người dùng</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white <?= $page == "order.php" || $page == "customer-orders.php" || $page == "customer-order-details.php" || $page == "ChiTietDonHang.php" ? 'active bg-gradient-primary' : '' ?>" href="order.php">
          <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="material-icons opacity-10">receipt_long</i>
          </div>
          <span class="nav-link-text ms-1">Quản lý đơn hàng</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white <?= $page == "category.php" || $page == "edit-category.php" || $page == "add-category.php" ? 'active bg-gradient-primary' : '' ?>" href="category.php">
          <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="material-icons opacity-10">table_view</i>
          </div>
          <span class="nav-link-text ms-1">Quản lý danh mục</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white <?= $page == "products.php" || $page == "edit-product.php" || $page == "product-manage.php" || $page == "add-product.php" ? 'active bg-gradient-primary' : '' ?>" href="products.php">
          <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="material-icons opacity-10">menu_book</i>
          </div>
          <span class="nav-link-text ms-1">Quản lý sản phẩm</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white <?= $page == "blog.php" || $page == "edit-blog.php" || $page == "add-blog.php" ? 'active bg-gradient-primary' : '' ?>" href="blog.php">
          <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="material-icons opacity-10">article</i>
          </div>
          <span class="nav-link-text ms-1">Quản lý bài viết</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white <?= $page == "import-manage.php" || $page == "import-stock.php" || $page == "import-receipt-detail.php" ? 'active bg-gradient-primary' : '' ?>" href="import-manage.php">
          <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="material-icons opacity-10">inventory</i>
          </div>
          <span class="nav-link-text ms-1">Quản lý nhập hàng</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white <?= $page == "manage-price.php" ? 'active bg-gradient-primary' : '' ?>" href="manage-price.php">
          <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="material-icons opacity-10">sell</i>
          </div>
          <span class="nav-link-text ms-1">Quản lý giá bán</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white <?= $page == "manage-stock.php" ? 'active bg-gradient-primary' : '' ?>" href="manage-stock.php">
          <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="material-icons opacity-10">inventory_2</i>
          </div>
          <span class="nav-link-text ms-1">Quản lý tồn kho</span>
        </a>
      </li>

    </ul>
  </div>

  <script>
    (function() {
      const sidebar = document.getElementById('sidenav-collapse-main');
      if (!sidebar) return;
      const storageKey = 'adminSidebarScrollTop';
      const saved = sessionStorage.getItem(storageKey);
      if (saved !== null) {
        sidebar.scrollTop = parseInt(saved, 10);
      }
      sidebar.addEventListener('scroll', function() {
        sessionStorage.setItem(storageKey, sidebar.scrollTop.toString());
      });
    })();
  </script>

  <!-- Footer: Đăng xuất -->
  <div class="sidenav-footer position-absolute w-100 bottom-0 ">
    <div class="mx-3">
      <a class="btn bg-gradient-primary mt-4 w-100" href="../logout.php" type="button">Đăng xuất</a>
    </div>
  </div>

</aside>