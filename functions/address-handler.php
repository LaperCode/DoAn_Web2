<?php
session_start();
include('../config/dbcon.php');

// Check if user is logged in
if (!isset($_SESSION['auth_user']['id'])) {
    redirect("../login.php", "Vui lòng đăng nhập");
    exit();
}

$user_id = $_SESSION['auth_user']['id'];

// Redirect function
function redirect($url, $message)
{
    $_SESSION['message'] = $message;
    header('Location: ' . $url);
    exit();
}

// === ADD ADDRESS ===
if (isset($_POST['action']) && $_POST['action'] == 'add') {
    $address_name = mysqli_real_escape_string($conn, trim($_POST['address_name']));
    $recipient_name = mysqli_real_escape_string($conn, trim($_POST['recipient_name']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $address = mysqli_real_escape_string($conn, trim($_POST['address']));
    $district = mysqli_real_escape_string($conn, trim($_POST['district']));
    $city = mysqli_real_escape_string($conn, trim($_POST['city']));
    $is_default = isset($_POST['is_default']) ? 1 : 0;

    // Validation
    if (empty($address_name) || empty($recipient_name) || empty($phone) || empty($address)) {
        redirect("../manage-addresses.php", "Vui lòng điền đầy đủ thông tin bắt buộc");
    }

    // Nếu đặt làm mặc định, bỏ mặc định của các địa chỉ khác
    if ($is_default == 1) {
        $update_query = "UPDATE user_addresses SET is_default=0 WHERE user_id='$user_id'";
        mysqli_query($conn, $update_query);
    } else {
        // Nếu đây là địa chỉ đầu tiên, tự động đặt làm mặc định
        $check_query = "SELECT COUNT(*) as count FROM user_addresses WHERE user_id='$user_id'";
        $check_result = mysqli_query($conn, $check_query);
        $check_data = mysqli_fetch_assoc($check_result);
        if ($check_data['count'] == 0) {
            $is_default = 1;
        }
    }

    // Insert address
    $insert_query = "INSERT INTO user_addresses 
                     (user_id, address_name, recipient_name, phone, address, district, city, is_default) 
                     VALUES 
                     ('$user_id', '$address_name', '$recipient_name', '$phone', '$address', '$district', '$city', '$is_default')";

    if (mysqli_query($conn, $insert_query)) {
        redirect("../manage-addresses.php", "Thêm địa chỉ thành công");
    } else {
        redirect("../manage-addresses.php", "Lỗi khi thêm địa chỉ: " . mysqli_error($conn));
    }
}

// === EDIT ADDRESS ===
if (isset($_POST['action']) && $_POST['action'] == 'edit') {
    $address_id = mysqli_real_escape_string($conn, $_POST['address_id']);
    $address_name = mysqli_real_escape_string($conn, trim($_POST['address_name']));
    $recipient_name = mysqli_real_escape_string($conn, trim($_POST['recipient_name']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $address = mysqli_real_escape_string($conn, trim($_POST['address']));
    $district = mysqli_real_escape_string($conn, trim($_POST['district']));
    $city = mysqli_real_escape_string($conn, trim($_POST['city']));
    $is_default = isset($_POST['is_default']) ? 1 : 0;

    // Validation
    if (empty($address_name) || empty($recipient_name) || empty($phone) || empty($address)) {
        redirect("../manage-addresses.php", "Vui lòng điền đầy đủ thông tin bắt buộc");
    }

    // Check if address belongs to user
    $check_query = "SELECT id FROM user_addresses WHERE id='$address_id' AND user_id='$user_id'";
    $check_result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($check_result) == 0) {
        redirect("../manage-addresses.php", "Địa chỉ không hợp lệ");
    }

    // Nếu đặt làm mặc định, bỏ mặc định của các địa chỉ khác
    if ($is_default == 1) {
        $update_query = "UPDATE user_addresses SET is_default=0 WHERE user_id='$user_id' AND id!='$address_id'";
        mysqli_query($conn, $update_query);
    } else {
        // Không cho phép bỏ mặc định nếu đây là địa chỉ duy nhất hoặc đang là mặc định
        $count_query = "SELECT COUNT(*) as count FROM user_addresses WHERE user_id='$user_id'";
        $count_result = mysqli_query($conn, $count_query);
        $count_data = mysqli_fetch_assoc($count_result);

        $current_query = "SELECT is_default FROM user_addresses WHERE id='$address_id'";
        $current_result = mysqli_query($conn, $current_query);
        $current_data = mysqli_fetch_assoc($current_result);

        if ($count_data['count'] == 1 || $current_data['is_default'] == 1) {
            $is_default = 1; // Force default
        }
    }

    // Update address
    $update_query = "UPDATE user_addresses SET 
                     address_name='$address_name',
                     recipient_name='$recipient_name',
                     phone='$phone',
                     address='$address',
                     district='$district',
                     city='$city',
                     is_default='$is_default'
                     WHERE id='$address_id' AND user_id='$user_id'";

    if (mysqli_query($conn, $update_query)) {
        redirect("../manage-addresses.php", "Cập nhật địa chỉ thành công");
    } else {
        redirect("../manage-addresses.php", "Lỗi khi cập nhật địa chỉ: " . mysqli_error($conn));
    }
}

// === SET DEFAULT ADDRESS ===
if (isset($_POST['action']) && $_POST['action'] == 'set_default') {
    $address_id = mysqli_real_escape_string($conn, $_POST['address_id']);

    // Check if address belongs to user
    $check_query = "SELECT id FROM user_addresses WHERE id='$address_id' AND user_id='$user_id'";
    $check_result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($check_result) == 0) {
        redirect("../manage-addresses.php", "Địa chỉ không hợp lệ");
    }

    // Remove default from all addresses
    $remove_default_query = "UPDATE user_addresses SET is_default=0 WHERE user_id='$user_id'";
    mysqli_query($conn, $remove_default_query);

    // Set new default
    $set_default_query = "UPDATE user_addresses SET is_default=1 WHERE id='$address_id' AND user_id='$user_id'";

    if (mysqli_query($conn, $set_default_query)) {
        redirect("../manage-addresses.php", "Đã đặt địa chỉ mặc định");
    } else {
        redirect("../manage-addresses.php", "Lỗi khi đặt địa chỉ mặc định: " . mysqli_error($conn));
    }
}

// === DELETE ADDRESS ===
if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    $address_id = mysqli_real_escape_string($conn, $_POST['address_id']);

    // Check if address belongs to user
    $check_query = "SELECT is_default FROM user_addresses WHERE id='$address_id' AND user_id='$user_id'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) == 0) {
        redirect("../manage-addresses.php", "Địa chỉ không hợp lệ");
    }

    $check_data = mysqli_fetch_assoc($check_result);

    // Không cho xóa địa chỉ mặc định
    if ($check_data['is_default'] == 1) {
        redirect("../manage-addresses.php", "Không thể xóa địa chỉ mặc định. Vui lòng đặt địa chỉ khác làm mặc định trước.");
    }

    // Delete address
    $delete_query = "DELETE FROM user_addresses WHERE id='$address_id' AND user_id='$user_id'";

    if (mysqli_query($conn, $delete_query)) {
        redirect("../manage-addresses.php", "Xóa địa chỉ thành công");
    } else {
        redirect("../manage-addresses.php", "Lỗi khi xóa địa chỉ: " . mysqli_error($conn));
    }
}

// If no action matched
redirect("../manage-addresses.php", "Hành động không hợp lệ");
