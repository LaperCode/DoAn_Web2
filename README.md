# ZBooks - Hệ Thống Quản Lý Cửa Hàng Sách Trực Tuyến

## Giới Thiệu

ZBooks là hệ thống quản lý cửa hàng sách trực tuyến được phát triển bằng PHP thuần (không sử dụng CMS). Dự án được xây dựng nhằm áp dụng các kiến thức về lập trình web và ứng dụng nâng cao, bao gồm quản lý sản phẩm, đơn hàng, người dùng và hệ thống nhập hàng với công thức tính giá trung bình.

---

## Tính Năng Chính

### Dành Cho Khách Hàng:

- Hiển thị sản phẩm theo danh mục với phân trang
- Xem chi tiết sản phẩm (tên, giá, mô tả, đánh giá)
- Tìm kiếm cơ bản theo tên sản phẩm
- Tìm kiếm nâng cao kết hợp tên, danh mục và khoảng giá
- Quản lý giỏ hàng (thêm, xóa, cập nhật số lượng)
- Quản lý nhiều địa chỉ giao hàng
- Thanh toán qua chuyển khoản, COD hoặc online
- Xem lịch sử đơn hàng
- Đánh giá sản phẩm đã mua

### Dành Cho Quản Trị Viên:

- Dashboard thống kê tổng quan
- Quản lý sản phẩm, danh mục
- Hệ thống nhập hàng với công thức giá trung bình:
  ```
  Giá nhập BQ = (SL tồn × Giá cũ + SL nhập × Giá mới) / (SL tồn + SL nhập)
  Giá bán = Giá nhập BQ × (1 + % Lợi nhuận)
  ```
- Xem lịch sử nhập hàng
- Quản lý người dùng và phân quyền
- Quản lý đơn hàng và cập nhật trạng thái
- Quản lý blog

---

## Công Nghệ Sử Dụng

**Backend:** PHP 8.x, MySQL/MariaDB, MySQLi

**Frontend:** HTML5, CSS3, JavaScript, jQuery, Bootstrap 5, Font Awesome

---

## Cài Đặt

### Yêu Cầu:

- XAMPP (PHP 8.x + MySQL)
- Trình duyệt web hiện đại

### Các Bước:

1. Clone hoặc giải nén project vào `C:\xampp\htdocs\web-2\ProjectWeb2-main\`

2. Tạo database:
   - Mở phpMyAdmin: `http://localhost/phpmyadmin`
   - Tạo database: `zbook_db`
   - Import file: `update_database.sql`

3. Cấu hình kết nối database trong `config/dbcon.php`:

   ```php
   $servername = "localhost";
   $username = "root";
   $password = "";
   $db_name = "zbook_db";
   ```

4. Khởi động XAMPP (Apache + MySQL)

5. Truy cập: `http://localhost/web-2/ProjectWeb2-main/`

---

## Tài Khoản Mặc Định

**Admin:**

- Email: test@example.com
- Password: 

**Customer:**

- Email: lap2@gmail.com
- Password: 

---

## Cấu Trúc Thư Mục

```
ProjectWeb2-main/
├── admin/                  # Quản trị viên
├── assets/                 # CSS, JS, Images
├── config/                 # Cấu hình database
├── functions/              # Logic nghiệp vụ
├── middleware/             # Phân quyền
├── includes/               # Header, Footer, Navbar
├── images/                 # Hình ảnh sản phẩm
├── index.php               # Trang chủ
├── products.php            # Danh sách sản phẩm
├── cart.php                # Giỏ hàng
└── update_database.sql     # Script cập nhật DB
```

---

## Đặc Điểm Nổi Bật

- Hệ thống nhập hàng thông minh với công thức giá trung bình gia quyền
- Quản lý nhiều địa chỉ giao hàng linh hoạt
- Tìm kiếm nâng cao kết hợp nhiều tiêu chí
- Bảo mật tốt với password hashing và prepared statements
- Giao diện responsive, thân thiện với mọi thiết bị

---

## Thành Viên Nhóm

| STT | Họ và Tên         | MSSV       |
| --- | ----------------- | ---------- |
| 1   | Nguyễn Hoàng Lập  | 3123410194 |
| 2   | Dương Thiên Quý   | 3123410298 |
| 3   | Nguyễn Quang Minh | 3122410241 |
| 4   | Đào Song Lộc      | 3123410200 |
