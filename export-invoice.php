<?php
session_start();

// Load database connection FIRST
require_once('./config/dbcon.php');
require_once('./functions/userfunctions.php');

// Kiểm tra session trước
if (!isset($_SESSION['auth_user']['id'])) {
    echo '<!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Lỗi truy cập</title>
        <style>
            body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background: #f5f5f5; }
            .error-box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); max-width: 500px; margin: 0 auto; }
            h1 { color: #e74c3c; }
            a { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; }
        </style>
    </head>
    <body>
        <div class="error-box">
            <h1>Lỗi truy cập</h1>
            <p>Bạn chưa đăng nhập. Vui lòng đăng nhập để xem hóa đơn.</p>
            <a href="./login.php">Đăng nhập ngay</a>
        </div>
    </body>
    </html>';
    exit();
}

// Kiểm tra order_id
if (!isset($_GET['order_id'])) {
    echo '<!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Lỗi</title>
        <style>
            body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background: #f5f5f5; }
            .error-box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); max-width: 500px; margin: 0 auto; }
            h1 { color: #e74c3c; }
            a { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; }
        </style>
    </head>
    <body>
        <div class="error-box">
            <h1>Lỗi</h1>
            <p>Không tìm thấy mã đơn hàng.</p>
            <a href="./cart-status.php">Quay lại đơn hàng</a>
        </div>
    </body>
    </html>';
    exit();
}

$user_id = $_SESSION['auth_user']['id'];
$order_id = mysqli_real_escape_string($conn, $_GET['order_id']);

// Lấy thông tin đơn hàng
$order_query = "SELECT o.*, u.name, u.email, u.phone, u.address 
                FROM `orders` o
                JOIN `users` u ON o.user_id = u.id
                WHERE o.id = '$order_id' AND o.user_id = '$user_id'";
$order_result = mysqli_query($conn, $order_query);

if (!$order_result) {
    die("Lỗi truy vấn database: " . mysqli_error($conn));
}

if (mysqli_num_rows($order_result) == 0) {
    echo '<!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Lỗi</title>
        <style>
            body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background: #f5f5f5; }
            .error-box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); max-width: 500px; margin: 0 auto; }
            h1 { color: #e74c3c; }
            a { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; }
        </style>
    </head>
    <body>
        <div class="error-box">
            <h1>⚠️ Không tìm thấy đơn hàng</h1>
            <p>Đơn hàng không tồn tại hoặc không thuộc về bạn.</p>
            <a href="./cart-status.php">Quay lại danh sách đơn hàng</a>
        </div>
    </body>
    </html>';
    exit();
}

$order = mysqli_fetch_assoc($order_result);

// Lấy chi tiết sản phẩm trong đơn hàng
$details_query = "SELECT od.*, p.name, p.image 
                  FROM `order_detail` od
                  JOIN `products` p ON od.product_id = p.id
                  WHERE od.order_id = '$order_id' AND od.user_id = '$user_id'";
$details_result = mysqli_query($conn, $details_query);

if (!$details_result) {
    die("Lỗi truy vấn chi tiết đơn hàng: " . mysqli_error($conn));
}

// Set header để tải file
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa đơn #COSS<?= $order_id ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 30px;
            border-bottom: 3px solid #F39C12;
            margin-bottom: 30px;
        }

        .company-info h1 {
            color: #2C3E50;
            font-size: 32px;
            margin-bottom: 5px;
        }

        .company-info p {
            color: #7F8C8D;
            font-size: 14px;
            margin: 3px 0;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h2 {
            color: #F39C12;
            font-size: 36px;
            margin-bottom: 10px;
        }

        .invoice-title p {
            color: #2C3E50;
            font-size: 14px;
            margin: 3px 0;
        }

        .billing-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }

        .info-section h3 {
            color: #2C3E50;
            font-size: 16px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ECF0F1;
        }

        .info-section p {
            color: #34495E;
            font-size: 14px;
            margin: 8px 0;
            line-height: 1.6;
        }

        .info-section strong {
            color: #2C3E50;
            display: inline-block;
            width: 120px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        table thead {
            background: #2C3E50;
            color: white;
        }

        table th {
            padding: 15px;
            text-align: left;
            font-size: 14px;
            font-weight: 600;
        }

        table tbody td {
            padding: 15px;
            border-bottom: 1px solid #ECF0F1;
            color: #34495E;
            font-size: 14px;
        }

        table tbody tr:hover {
            background: #F8F9FA;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }

        .total-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .total-box {
            width: 350px;
            background: #F8F9FA;
            padding: 20px;
            border-radius: 8px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            font-size: 15px;
            color: #34495E;
        }

        .total-row.grand-total {
            border-top: 2px solid #2C3E50;
            padding-top: 15px;
            margin-top: 15px;
            font-size: 18px;
            font-weight: bold;
            color: #2C3E50;
        }

        .total-row.grand-total .amount {
            color: #F39C12;
            font-size: 22px;
        }

        .footer-note {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #ECF0F1;
            text-align: center;
            color: #7F8C8D;
            font-size: 13px;
        }

        /* Print Button - Right side */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #F39C12;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .print-button:hover {
            background: #E67E22;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .print-button {
                display: none;
            }

            .invoice-container {
                box-shadow: none;
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <!-- Print Button - Right side -->
    <button class="print-button" onclick="window.print()">🖨️ In hóa đơn</button>

    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <div class="company-info">
                <h1>📚 ZBooks Store</h1>
                <p>Nhà sách trực tuyến uy tín</p>
                <p>Email: support@zbooks.vn</p>
                <p>Hotline: 1900 xxxx</p>
            </div>
            <div class="invoice-title">
                <h2>HÓA ĐƠN</h2>
                <p><strong>Mã đơn:</strong> COSS<?= $order_id ?></p>
                <p><strong>Ngày:</strong> <?= date('d/m/Y', strtotime($order['created_at'])) ?></p>
            </div>
        </div>

        <!-- Billing Information -->
        <div class="billing-info">
            <div class="info-section">
                <h3>Thông tin khách hàng</h3>
                <p><strong>Họ tên:</strong> <?= htmlspecialchars($order['name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['address']) ?></p>
            </div>

            <div class="info-section">
                <h3>Thông tin đơn hàng</h3>
                <p><strong>Thanh toán:</strong>
                    <?= $order['payment'] == '1' ? 'COD (Tiền mặt)' : 'Chuyển khoản ngân hàng' ?>
                </p>
                <p><strong>Trạng thái:</strong>
                    <?php
                    switch ($order['status']) {
                        case '2':
                            echo 'Đang chuẩn bị hàng';
                            break;
                        case '3':
                            echo 'Đang giao hàng';
                            break;
                        case '4':
                            echo 'Đã giao';
                            break;
                        case '5':
                            echo 'Đã hủy';
                            break;
                        default:
                            echo 'Đang xử lý';
                    }
                    ?>
                </p>
                <p><strong>Ghi chú:</strong> <?= htmlspecialchars($order['additional'] ?? '') ?: 'Không có' ?></p>
            </div>
        </div>

        <!-- Order Details Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 60px;">Hình</th>
                    <th>Sản phẩm</th>
                    <th class="text-center" style="width: 100px;">Số lượng</th>
                    <th class="text-right" style="width: 120px;">Đơn giá</th>
                    <th class="text-right" style="width: 120px;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $subtotal = 0;
                while ($item = mysqli_fetch_assoc($details_result)) {
                    $item_total = $item['selling_price'] * $item['quantity'];
                    $subtotal += $item_total;
                ?>
                    <tr>
                        <td>
                            <img src="./images/<?= htmlspecialchars($item['image']) ?>"
                                alt="<?= htmlspecialchars($item['name']) ?>"
                                class="product-image">
                        </td>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td class="text-center"><?= $item['quantity'] ?></td>
                        <td class="text-right">$<?= number_format($item['selling_price'], 2) ?></td>
                        <td class="text-right">$<?= number_format($item_total, 2) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Total Section -->
        <div class="total-section">
            <div class="total-box">
                <div class="total-row">
                    <span>Tạm tính:</span>
                    <span>$<?= number_format($subtotal, 2) ?></span>
                </div>
                <div class="total-row">
                    <span>Phí vận chuyển:</span>
                    <span>$0.00</span>
                </div>
                <div class="total-row grand-total">
                    <span>TỔNG CỘNG:</span>
                    <span class="amount">$<?= number_format($subtotal, 2) ?></span>
                </div>
            </div>
        </div>

        <!-- Footer Note -->
        <div class="footer-note">
            <p>Cảm ơn bạn đã mua hàng tại ZBooks Store!</p>
            <p>Mọi thắc mắc vui lòng liên hệ hotline: 1900 xxxx hoặc email: support@zbooks.vn</p>
        </div>
    </div>
</body>

</html>