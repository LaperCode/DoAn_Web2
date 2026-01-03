<?php
session_start();
include("../config/dbcon.php");
include("../functions/myfunctions.php");

if (isset($_POST['order'])){
    $user_id    = $_SESSION['auth_user']['id'];
    $product_id = $_POST['product_id'];
    $quantity   = $_POST['quantity'];

    $product = getByID("products",$product_id);
    if(mysqli_num_rows($product) >0){
        $product = mysqli_fetch_array($product);
        $slug    = $product['slug'];
        if ($quantity != "" && $quantity <= $product['qty']){
            $selling_price  = $product['selling_price'];
            $insert_query   = "INSERT INTO order_detail (`user_id`, `product_id`, `selling_price`, `quantity`) VALUES ('$user_id','$product_id','$selling_price','$quantity')";
            $insert_query_run=mysqli_query($conn,$insert_query);
            if($insert_query_run){
                $_SESSION['message']="Thêm vào giỏ hàng thành công";
                header("Location: ../cart.php");
            }
        }else{
            $_SESSION['message']="Số lượng sản phẩm không phù hợp";
            header("Location: ../product-detail.php?slug=$slug");
        }
    }else{
        $_SESSION['message']="Đã xảy ra lỗi không đáng có";
        header("Location: ../products.php");
    }    
}else if (isset($_GET['deleteID'])){
    $user_id    = $_SESSION['auth_user']['id'];
    $order_id   = $_GET['deleteID'];
    $query =    "DELETE FROM `order_detail` 
                WHERE `id` = '$order_id' AND `user_id` = '$user_id'";
    mysqli_query($conn, $query);
    $_SESSION['message']="Xóa sản phẩm thành công";
    header("Location: ../cart.php");
}else if (isset($_POST['update_product'])){
    $user_id    = $_SESSION['auth_user']['id'];
    $product_id = $_POST['product_id'];
    $quantity   = $_POST['quantity'];

    // Lấy Số lương
    $query          = "SELECT `qty` FROM `products` WHERE `id` = '$product_id'";
    $total_quantity = mysqli_fetch_array(mysqli_query($conn, $query))['qty'];

    // Kiểm tra số lượng còn lại trong kho
    if ($total_quantity > $quantity){
        $query =    "UPDATE `order_detail` SET `quantity` = $quantity 
                WHERE `product_id` = '$product_id' AND `user_id` = '$user_id' AND `status` = '1'";
        mysqli_query($conn, $query);
        $_SESSION['message']="Cập nhập sản phẩm thành công";
    }else{
        $_SESSION['message']="Cập nhập số lượng sản phẩm quá lớn";
    }
    
    header("Location: ../cart.php");

}else if (isset($_POST['buy_product'])){
    $user_id    = $_SESSION['auth_user']['id'];
    $payment_method = $_POST['payment_method'];
    $addtional = $_POST['addtional'];
    $check = true;
    
    // Lấy số lượng trong kho và số lượng đặt hàng
    $query  =   "SELECT `order_detail`.`quantity`, `products`.`qty`,`products`.`id`, `products`.`name` FROM `order_detail`
                JOIN `products` ON `order_detail`.`product_id` = `products`.`id`
                WHERE `order_detail`.`status` = 1 AND `order_detail`.`user_id` = '$user_id'";    
    $check_products = mysqli_query($conn, $query);

    // Kiểm tra số lượng trong kho và số luongj đặt của từng sản phẩm
    foreach ($check_products as $product){
        if ($product['quantity'] > $product['qty']){
            $_SESSION['message'] = "Số lượng sản phẩm: " . $product['name'] . " không đủ trong kho. Chỉ còn " . $product['qty'] . " Sản phẩm";
            $check = false;
            header("Location: ../cart.php");
            break;
        }
    }

    // Nếu hợp lệ sẽ tiền hành đặt hàng
    if ($check) {
        $insert_query       = "INSERT INTO `orders`(`user_id`,`addtional`,`payment`) VALUES ('$user_id','$addtional','$payment_method')";
        $insert_query_run   = mysqli_query($conn, $insert_query);
        $order_id           = $conn->insert_id;

        $query =    "UPDATE `order_detail` SET `status` = '2', `order_id` = '$order_id'
                    WHERE `user_id` = '$user_id' AND `status` = '1'";
        mysqli_query($conn, $query);
        // Cập nhập lại số lượng trong kho
        foreach ($check_products as $product){
            $qty        = $product['qty'] - $product['quantity'];
            $product_id = $product['id'];
            $query = "UPDATE `products` SET `qty` = '$qty' WHERE `id` = '$product_id'";
            mysqli_query($conn, $query);
        }

        /*$id=$_SESSION['auth_user']['id'];
        $name= $_POST['name'];
        $phone= $_POST['phone'];
        $address= $_POST['address'];
        $update_query= "UPDATE `users` SET `name`='$name', `phone`='$phone', `address`='$address' WHERE `id`='$id' ";
        $update_query_run=mysqli_query($conn,$update_query);
        if($update_query_run)
        {
        }else $_SESSION['message']="Không thành công";*/
        $_SESSION['message']="Mua sản phẩm thành công";
        echo 1;
    }



}else if(isset($_POST['rate'])){
    $user_id    = $_SESSION['auth_user']['id'];
    $id         = $_POST['id'];
    $rate       = $_POST['rating'];
    $comment    = $_POST['comment'];

    $query =    "UPDATE `order_detail` SET `rate` = '$rate', `comment` = '$comment'
                WHERE `order_id` = '$id' AND `user_id` = '$user_id' AND `status` = '4'";
    mysqli_query($conn, $query);

    $_SESSION['message']="Đánh giá sản phẩm thành công";
    header("Location: ../cart-status.php");
}

?>