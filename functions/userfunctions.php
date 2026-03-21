<?php
include("./config/dbcon.php");

// Format số thành dạng 10.000, 1.500.000
if (!function_exists('fmt_price')) {
    function fmt_price($number)
    {
        return number_format((float)$number, 0, ',', '.');
    }
}

function getAllActive($table)
{
    global $conn;
    $query = "SELECT * FROM $table WHERE status='0'";
    return $query_run = mysqli_query($conn, $query);
}
function getIDActive($table, $id)
{
    global $conn;
    $query = "SELECT * FROM $table WHERE id='$id' AND status='0'";
    return $query_run = mysqli_query($conn, $query);
}
function getByID($table, $id)
{
    global $conn;
    $query = "SELECT * FROM $table WHERE id='$id'";
    return $query_run = mysqli_query($conn, $query);
}
function getAll($table)
{
    global $conn;
    $query = "SELECT * FROM $table";
    return $query_run = mysqli_query($conn, $query);
}
function getBySlug($table, $slug)
{
    global $conn;
    $query = "SELECT * FROM $table WHERE slug='$slug'";
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
function getBestSelling($numberGet)
{
    global $conn;
    $query =    "SELECT `products`.*, COUNT(`order_detail`.id) as total_buy FROM `products` 
                LEFT JOIN `order_detail` ON `products`.`id` = `order_detail`.`product_id`
                WHERE `products`.`status` = 0
                GROUP BY `products`.`id`
                ORDER BY `total_buy` DESC
                LIMIT $numberGet";
    return mysqli_query($conn, $query);
}
function getLatestProducts($numberGet, $page = 0, $type = "", $search = "")
{
    global $conn;
    $page_extra = $numberGet * $page;

    // Escape input để tránh SQL Injection
    $search = mysqli_real_escape_string($conn, $search);
    $type = mysqli_real_escape_string($conn, $type);

    // Mặc định WHERE là 1 để dễ dàng thêm điều kiện
    $whereClause = "1";

    // Lọc theo tên sản phẩm nếu có
    if (!empty($search)) {
        $whereClause .= " AND `name` LIKE '%$search%'";
    }

    // Kiểm tra danh mục có tồn tại không
    if (!empty($type)) {
        $categoryResult = getBySlug("categories", $type);
        $categoryRow = mysqli_fetch_array($categoryResult);

        if ($categoryRow) {
            $categoryId = $categoryRow['id'];
            $whereClause .= " AND `category_id` = '$categoryId'";
        } else {
            return []; // Nếu không có danh mục, trả về mảng rỗng
        }
    }

    // Truy vấn lấy sản phẩm theo điều kiện
    $query = "SELECT * FROM `products` 
              WHERE $whereClause
              AND `products`.`status` = 0
              ORDER BY `id` DESC 
              LIMIT $numberGet OFFSET $page_extra";

    $result = mysqli_query($conn, $query);

    return mysqli_fetch_all($result, MYSQLI_ASSOC); // Chuyển kết quả thành mảng
}

//đếm số sản phẩm theo danh mục
function getTotalProducts($type = "", $search = "")
{
    global $conn;

    // Escape input để tránh SQL Injection
    $search = mysqli_real_escape_string($conn, $search);
    $type = mysqli_real_escape_string($conn, $type);

    $whereClause = "1"; // Mặc định WHERE luôn đúng

    if (!empty($search)) {
        $whereClause .= " AND `name` LIKE '%$search%'";
    }

    if (!empty($type)) {
        $categoryResult = getBySlug("categories", $type);
        $categoryRow = mysqli_fetch_array($categoryResult);

        if ($categoryRow) {
            $categoryId = $categoryRow['id'];
            $whereClause .= " AND `category_id` = '$categoryId'";
        } else {
            return 0; // Nếu danh mục không tồn tại, trả về 0 sản phẩm
        }
    }

    // Truy vấn số lượng sản phẩm
    $query = "SELECT COUNT(*) as total FROM `products` WHERE $whereClause";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    return (int)$row['total']; // Trả về tổng số sản phẩm
}

function getBlogs($page, $keyWold)
{
    global $conn;
    $page_extra = 10 * $page;
    $query =    "SELECT * FROM `blog` 
                WHERE `title` LIKE '%$keyWold%'
                ORDER BY `id` DESC
                LIMIT 10 OFFSET $page_extra";
    return mysqli_query($conn, $query);
}

// order
function checkOrder($id_product)
{
    global $conn;
    $user_id = $_SESSION['auth_user']['id'];
    $query  =   "SELECT `status` FROM `order_detail` 
                WHERE `product_id` = '$id_product' AND `user_id` = '$user_id' AND `status` != 0 
                ORDER BY `status`";
    $checkOrsder = mysqli_query($conn, $query);
    if (mysqli_num_rows($checkOrsder)) {
        $checkOrsder = mysqli_fetch_array($checkOrsder)['status'];
        return $checkOrsder;
    } else {
        return 0;
    }
}

function getMyOrders()
{
    global $conn;
    $user_id = $_SESSION['auth_user']['id'];
    $query =    "SELECT `order_detail`.*, `products`.`name`, `products`.`slug` FROM `order_detail` 
                JOIN `products` on `order_detail`.`product_id` = `products`.`id`
                WHERE `order_detail`.`user_id` = '$user_id' AND `order_detail`.`status` = 1";
    return mysqli_query($conn, $query);
}

function getMyOrderVote($id)
{
    global $conn;
    $user_id = $_SESSION['auth_user']['id'];
    $id = intval($id); // Ép kiểu số nguyên để tránh SQL Injection

    $query = "SELECT `order_detail`.*, `products`.`name`, `products`.`description`, 
                     `products`.`small_description`, `products`.`image`, `products`.`slug`, 
                     `order_detail`.`rate`, `order_detail`.`comment`
              FROM `order_detail` 
              JOIN `products` ON `order_detail`.`product_id` = `products`.`id`
              WHERE `order_detail`.`order_id` = '$id' 
              AND `order_detail`.`status` = 4 
              AND `order_detail`.`user_id` = '$user_id'";

    $result = mysqli_query($conn, $query);

    // Kiểm tra lỗi SQL
    if (!$result) {
        die("Lỗi truy vấn SQL: " . mysqli_error($conn));
    }

    $data = mysqli_fetch_assoc($result);

    return $data ?: null; // Trả về null nếu không có dữ liệu
}

function getOrderWasBuy($cart_id)
{
    global $conn;
    $user_id = $_SESSION['auth_user']['id'];
    $query =    "SELECT `order_detail`.`created_at`,`order_detail`.`selling_price`, `order_detail`.`quantity`,
                           `products`.`name`, `products`.`slug`, `products`.`image`
                    FROM `order_detail` 
                    JOIN `products` on `order_detail`.`product_id` = `products`.`id`
                    WHERE `order_detail`.`user_id` = '$user_id' AND `order_detail`.`status` NOT IN (0,1) and `order_detail`.`order_id` = '$cart_id'
                    ORDER BY `order_detail`.`id` DESC";
    $result = mysqli_query($conn, $query);

    $orders = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
    return $orders;
}

function getOrderByUserId($sort = 'newest', $payment_filter = '', $status_filter = '')
{
    global $conn;
    $user_id = $_SESSION['auth_user']['id'];

    $where = "o.user_id = '$user_id'";
    if ($payment_filter === 'cod') {
        $where .= " AND o.payment = '1'";
    } elseif ($payment_filter === 'bank') {
        $where .= " AND o.payment = '0'";
    }
    if ($status_filter !== '') {
        $status_val = (int)$status_filter;
        $where .= " AND o.status = '$status_val'";
    }

    switch ($sort) {
        case 'oldest':
            $order_by = "o.created_at ASC";
            break;
        case 'price_asc':
            $order_by = "total ASC";
            break;
        case 'price_desc':
            $order_by = "total DESC";
            break;
        case 'newest':
        default:
            $order_by = "o.created_at DESC";
            break;
    }

    $query = "SELECT
                    o.payment,
                    SUM(od.quantity * od.selling_price) AS total,
                    o.status,
                    o.id,
                    o.created_at,
                    o.additional
                FROM
                    orders o
                JOIN
                    order_detail od ON od.order_id = o.id
                WHERE
                    $where
                GROUP BY
                    o.id
                ORDER BY $order_by
            ";
    return mysqli_query($conn, $query);
}

function getRate($product_id)
{
    global $conn;
    $query = "SELECT `order_detail`.*, `users`.`name` FROM `order_detail` 
            JOIN `users` ON `order_detail`.`user_id` = `users`.`id`
            WHERE `order_detail`.`product_id` = '$product_id' AND `order_detail`.`status` = 4 AND `order_detail`.`rate` > 0";

    return mysqli_query($conn, $query);
}

function avgRate($product_id)
{
    global $conn;
    $query = "SELECT AVG(`rate`) as `avg_rate` FROM `order_detail` WHERE `product_id` = '$product_id' AND `status` = 4 AND `rate` > 0";
    $rate = mysqli_query($conn, $query);
    $rate = mysqli_fetch_array($rate);
    return round($rate['avg_rate'], 1);
}

function redirect($url, $message)
{
    $_SESSION['message'] = $message;
    header('Location:' . $url);
    exit();
}
