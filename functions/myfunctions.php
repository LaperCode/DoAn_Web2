<?php
include("../config/dbcon.php");
function getAll($table)
{
    global $conn;
    $query = "SELECT * FROM $table ORDER BY id DESC";
    return $query_run = mysqli_query($conn, $query);
}
function getByID($table, $id)
{
    global $conn;
    $query = "SELECT * FROM $table WHERE id='$id'";
    return $query_run = mysqli_query($conn, $query);
}

function totalValue($table)
{
    global $conn;
    $query = "SELECT COUNT(*) as `number` FROM $table";
    $totalValue = mysqli_query($conn, $query);
    $totalValue = mysqli_fetch_array($totalValue);
    return $totalValue['number'];
}
function getAllUsers()
{
    global $conn;
    $query = "SELECT `users`.*, COUNT(`order_detail`.`id`) AS `total_buy` FROM `users`
            LEFT JOIN `order_detail` ON `users`.`id` = `order_detail`.`user_id`
            GROUP BY `users`.`id`
            ORDER BY `users`.`creat_at` DESC";
    return $query_run = mysqli_query($conn, $query);
}

// order
function getAllOrder($status = -1, $from_date = null, $to_date = null, $district = null, $city = null)
{
    global $conn;
    $query = "SELECT `orders`.*, COUNT(`order_detail`.`id`) AS `quantity`,
                     `users`.`name`, `users`.`email`, `users`.`phone`, `users`.`address` 
              FROM `orders`
              JOIN `users` ON `orders`.`user_id` = `users`.`id`
              LEFT JOIN `order_detail` ON `order_detail`.`order_id` = `orders`.`id`
              WHERE 1"; // Mặc định luôn đúng, giúp dễ dàng thêm điều kiện lọc

    // Lọc theo trạng thái đơn hàng
    if ($status != -1) {
        $query .= " AND `orders`.`status` = '$status'";
    }

    // Lọc theo khoảng thời gian đặt hàng
    if (!empty($from_date) && !empty($to_date)) {
        $query .= " AND DATE(`orders`.`created_at`) BETWEEN '$from_date' AND '$to_date'";
    }

    // Lọc theo địa điểm giao hàng (quận/huyện)
    if (!empty($district)) {
        $query .= " AND `users`.`address` LIKE '%$district%'";
    }

    // Lọc theo địa điểm giao hàng (thành phố)
    if (!empty($city)) {
        $query .= " AND `users`.`address` LIKE '%$city%'";
    }

    $query .= " GROUP BY `orders`.`id` ORDER BY `orders`.`id` DESC";

    return mysqli_query($conn, $query);
}

function getOrderDetail($order_id)
{
    global $conn;
    $query =    "SELECT `users`.`name`,`users`.`email`,`users`.`phone`,`users`.`address`,
                `products`.`name` as `name_product`, `products`.`selling_price`,`products`.`image`,
                `order_detail`.*  FROM `order_detail` 
                JOIN `users` ON `order_detail`.`user_id` = `users`.`id`
                JOIN `products` ON `products`.`id` = `order_detail`.`product_id`
                WHERE `order_id` = '$order_id'";
    return mysqli_query($conn, $query);
}

function totalPriceGet()
{
    global $conn;
    $query = "SELECT selling_price * quantity as price FROM `order_detail` WHERE `status` = 4";
    $prices = mysqli_query($conn, $query);
    $total_price = 0;
    foreach ($prices as $price) {
        $total_price += $price['price'];
    }
    return $total_price;
}

function redirect($url, $message)
{
    $_SESSION['message'] = $message;
    header("Location:" . $url);
    exit();
}

function thongkeKH($from_date = null, $to_date = null)
{
    global $conn;

    // Câu truy vấn SQL
    $query = "SELECT users.id, users.name, users.email, users.phone, 
                     COUNT(DISTINCT orders.id) AS total_buy,
                     COALESCE(SUM(order_detail.selling_price * order_detail.quantity), 0) AS total_spent
              FROM users
              LEFT JOIN orders ON users.id = orders.user_id
              LEFT JOIN order_detail ON orders.id = order_detail.order_id
              WHERE orders.status = 4";  // Chỉ lấy đơn hàng đã hoàn tất

    // Lọc theo khoảng thời gian nếu có
    if (!empty($from_date) && !empty($to_date)) {
        $query .= " AND DATE(orders.created_at) BETWEEN '$from_date' AND '$to_date'";
    }

    $query .= " GROUP BY users.id
                ORDER BY total_spent DESC
                LIMIT 5";

    return mysqli_query($conn, $query);
}


function donhangKH($from_date = null, $to_date = null, $district = null, $city = null, $userid)
{
    global $conn;
    $query = "SELECT `orders`.*, COUNT(`order_detail`.`id`) AS `quantity`,
                     `users`.`name`, `users`.`email`, `users`.`phone`, `users`.`address` 
              FROM `orders`
              JOIN `users` ON `orders`.`user_id` = `users`.`id`
              LEFT JOIN `order_detail` ON `order_detail`.`order_id` = `orders`.`id`
              WHERE 1"; // Mặc định luôn đúng, giúp dễ dàng thêm điều kiện lọc

    // Lọc theo trạng thái đơn hàng
    $query .= " AND `orders`.`status` = 4";
    $query .= " AND `orders`.`user_id` = " . intval($userid);

    // Lọc theo khoảng thời gian đặt hàng
    if (!empty($from_date) && !empty($to_date)) {
        $query .= " AND DATE(`orders`.`created_at`) BETWEEN '$from_date' AND '$to_date'";
    }

    // Lọc theo địa điểm giao hàng (quận/huyện)
    if (!empty($district)) {
        $query .= " AND `users`.`address` LIKE '%$district%'";
    }

    // Lọc theo địa điểm giao hàng (thành phố)
    if (!empty($city)) {
        $query .= " AND `users`.`address` LIKE '%$city%'";
    }

    $query .= " GROUP BY `orders`.`id` ORDER BY `orders`.`id` DESC";

    return mysqli_query($conn, $query);
}

function getOrder_Detail($user_id)
{ //lấy đơn hàng đã mà chưa thanh toán (giỏ hàng) theo user_id
    global $conn;
    $query =    "SELECT * 
                FROM `order_detail` 
                WHERE `user_id` = '$user_id'
                AND `order_id` IS NULL";
    return mysqli_query($conn, $query);
}

function getTotalProducts($tenSP, $LSP, $SLmin, $SLmax, $Pmin, $Pmax, $status)
{
    global $conn;
    $query = "SELECT COUNT(*) as total FROM `products` pro WHERE 1";
    if ($tenSP != "") {
        $query .= " AND pro.name LIKE '%" . mysqli_real_escape_string($conn, $tenSP) . "%' ";
    }
    if ($LSP != "") {
        if ($LSP != -1) {
            $query .= " AND pro.category_id = '$LSP' ";
        }
    }
    if ($SLmin != "" && $SLmax != "") {
        if ($SLmin != 0 && $SLmax != 0) {
            $query .= " AND pro.qty BETWEEN '$SLmin' AND '$SLmax' ";
        } else if ($SLmin == 0 && $SLmax != 0) {
            $query .= " AND pro.qty BETWEEN '$SLmin' AND '$SLmax' ";
        }
    }
    if ($Pmin != "" && $Pmax != "") {
        if ($Pmin != 0 && $Pmax != 0) {
            $query .= " AND pro.selling_price BETWEEN '$Pmin' AND '$Pmax' ";
        } else if ($Pmin == 0 && $Pmax != 0) {
            $query .= " AND pro.selling_price BETWEEN '$Pmin' AND '$Pmax' ";
        }
    }
    if ($status != "") {
        if ($status != -1) {
            $query .= " AND pro.status = '$status' ";
        }
    }
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

function getSanPham($tenSP, $LSP, $SLmin, $SLmax, $Pmin, $Pmax, $status, $sort, $by, $limit = 10, $offset = 0)
{
    global $conn;
    $query = "SELECT *
              FROM `products` pro
              WHERE 1";
    if ($tenSP != "") {
        $query .= " AND pro.name LIKE '%" . mysqli_real_escape_string($conn, $tenSP) . "%' ";
    }
    if ($LSP != "") {
        if ($LSP != -1) {
            $query .= " AND pro.category_id = '$LSP' ";
        }
    }
    if ($SLmin != "" && $SLmax != "") {
        if ($SLmin != 0 && $SLmax != 0) {
            $query .= " AND pro.qty BETWEEN '$SLmin' AND '$SLmax' ";
        } else if ($SLmin == 0 && $SLmax != 0) {
            $query .= " AND pro.qty BETWEEN '$SLmin' AND '$SLmax' ";
        }
    }
    if ($Pmin != "" && $Pmax != "") {
        if ($Pmin != 0 && $Pmax != 0) {
            $query .= " AND pro.selling_price BETWEEN '$Pmin' AND '$Pmax' ";
        } else if ($Pmin == 0 && $Pmax != 0) {
            $query .= " AND pro.selling_price BETWEEN '$Pmin' AND '$Pmax' ";
        }
    }
    if ($status != "") {
        if ($status != -1) {
            $query .= " AND pro.status = '$status' ";
        }
    }
    if ($sort == 1) {
        $query .= " ORDER BY $by ASC ";
    } else {
        $query .= " ORDER BY $by DESC ";
    }
    $query .= " LIMIT $limit OFFSET $offset";
    return mysqli_query($conn, $query);
}
