<?php session_start();
include("../config/dbcon.php");
include("../functions/myfunctions.php");

if (isset($_POST['order'])) {
    $user_id    = $_SESSION['auth_user']['id'];
    $product_id = $_POST['product_id'];
    $quantity   = $_POST['quantity'];

    // Kiểm tra kết nối database
    if (!$conn) {
        die("Lỗi kết nối database: " . mysqli_connect_error());
    }

    // Kiểm tra sản phẩm tồn tại chưa
    $product = getByID("products", $product_id);
    if (!$product || mysqli_num_rows($product) == 0) {
        $_SESSION['message'] = "Sản phẩm không tồn tại";
        header("Location: ../products.php");
        exit();
    }

    $product = mysqli_fetch_array($product);
    $slug    = $product['slug'];

    // Kiểm tra số lượng nhập vào
    if ($quantity == "" || $quantity > $product['qty']) {
        $_SESSION['message'] = "Số lượng sản phẩm không phù hợp";
        header("Location: ../product-detail.php?slug=$slug");
        exit();
    }

    // Kiểm tra sản phẩm đã có trong giỏ hàng chưa
    $order_details = getOrder_Detail($user_id);
    $product_exists = false;

    while ($row = mysqli_fetch_assoc($order_details)) {
        if ($row['product_id'] == $product_id) {
            // Nếu sản phẩm đã có, tăng số lượng lên 1
            $new_quantity = $row['quantity'] + 1;
            $update_query = "UPDATE order_detail 
                             SET quantity = '$new_quantity' 
                             WHERE id = '{$row['id']}'";
            if (!mysqli_query($conn, $update_query)) {
                die("Lỗi cập nhật giỏ hàng: " . mysqli_error($conn));
            }
            $product_exists = true;
            break;
        }
    }

    // Nếu sản phẩm chưa có trong giỏ hàng, thêm mới
    if (!$product_exists) {
        $selling_price  = $product['selling_price'];
        $insert_query   = "INSERT INTO order_detail (`user_id`, `product_id`, `selling_price`, `quantity`) 
                           VALUES ('$user_id','$product_id','$selling_price','$quantity')";
        if (!mysqli_query($conn, $insert_query)) {
            die("Lỗi thêm sản phẩm vào giỏ hàng: " . mysqli_error($conn));
        }
    }

    $_SESSION['giohang'] = "Đã Thêm Sản Phẩm Vào Giỏ Hàng";
    
    // Chuyển hướng về trang trước đó
    $previousPage = $_SERVER['HTTP_REFERER']; 
    header("Location: $previousPage");
    exit();
}

unset($_SESSION['message']);

?>