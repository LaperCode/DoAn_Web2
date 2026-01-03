<?php
session_start();
include("../config/dbcon.php");
include("../functions/myfunctions.php");

if (isset($_POST['register-btn'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $cpassword = mysqli_real_escape_string($conn, $_POST['cpassword']);


    //Check email already 
    $check_email_query = "SELECT email FROM users WHERE email='$email' ";
    $check_email_query_run = mysqli_query($conn, $check_email_query);
    if (mysqli_num_rows($check_email_query_run) > 0) {
        redirect("../register.php", "Email của bạn đã được sử dụng. Xin hãy sử dụng Email khác");
    }
    //Check password no match
    else {
        if ($password == $cpassword) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                //Inser user data
                $pass_hash = password_hash($password, PASSWORD_DEFAULT);
                $insert_query = "INSERT INTO `users` (`name`,`email`,`phone`,`password`) VALUES ('$name','$email','$phone','$pass_hash')";
                $insert_query_run = mysqli_query($conn, $insert_query);
                if ($insert_query_run) {
                    redirect("../login.php", "Đăng ký tài khoản thành công");
                } else {
                    redirect("../register.php", "Đã xảy ra lỗi");
                }
            } else {
                redirect("../register.php", "Địa chỉ email không hợp lệ");
            }
        } else {
            redirect("../register.php", "Mật khẩu không khớp");
        }
    }
} else if (isset($_POST['login_btn'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $login_query = "SELECT * FROM `users` WHERE `email`='$email'";
    $login_query_run = mysqli_query($conn, $login_query);

    if (mysqli_num_rows($login_query_run) > 0) {
        $userdata   =   mysqli_fetch_array($login_query_run);
        $verify = password_verify($password, $userdata['password']);
        if ($verify && $userdata['role_as'] != 2) {
            $_SESSION['auth'] = true;

            $userid     =   $userdata['id'];
            $username   =   $userdata['name'];
            $useremail  =   $userdata['email'];
            $role_as    =   $userdata['role_as'];

            $_SESSION['auth_user'] = [
                'id'    =>  $userid,
                'name'  =>  $username,
                'email' =>  $useremail
            ];

            $_SESSION['role_as'] = $role_as;
            if ($role_as == 1) {
                redirect("../admin/index.php", "Welcome to ADMIN ");
            } else {
                redirect("../index.php", "Đăng nhập thành công");
            }
        } else if ($verify && $userdata['role_as'] == 2) {
            redirect("../login.php", "Tài khoản của bạn đã bị khóa");
        } else {
            redirect("../login.php", "Mật khẩu không đúng");
        }
    } else {
        redirect("../login.php", "Tài khoản email không tồn tại");
    }
} else if (isset($_POST['update_user_btn'])) {
    $id = $_SESSION['auth_user']['id'];
    $name = $_POST['name'];

    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    if (empty($password)) {
        $update_query = "UPDATE `users` SET `name`='$name', `email`='$email', `phone`='$phone', `address`='$address' WHERE `id`='$id' ";
        $update_query_run = mysqli_query($conn, $update_query);
        if ($update_query_run) {
            redirect("../user-profile.php", "Cập nhật thông tin thành công");
        } else {
            redirect("../user-profile.php", "Xảy ra lỗi, vui lòng cập nhật lại");
        }
    } else {
        if ($password == $cpassword) {
            $p_hash = password_hash($password, PASSWORD_DEFAULT);
            $update_query = "UPDATE `users` SET `name`='$name', `email`='$email', `phone`='$phone', `address`='$address', `password`='$p_hash' WHERE `id`='$id' ";
            $update_query_run = mysqli_query($conn, $update_query);
            if ($update_query_run) {
                redirect("../user-profile.php", "Cập nhật thông tin thành công");
            } else {
                redirect("../user-profile.php", "Xảy ra lỗi, vui lòng cập nhật lại");
            }
        } else {
            redirect("../user-profile.php", "Mật khẩu không khớp, vui lòng nhập lại");
        }
    }
} //Xóa người dùng 
else if (isset($_POST['delete_user_btn'])) {
    $id = $_POST['user_id'];
    echo "Mã người dùng là: " . $id;
    $delete_query = "DELETE FROM users WHERE id = '$id'";
    $delete_query_run = mysqli_query($conn, $delete_query);
    if ($delete_query_run) {
        redirect("../admin/user.php", "Đã xóa người dùng");
    } else {
        redirect("../admin/user.php", "Lỗi, không thể xóa người dùng");
    }
} //thêm người dùng 
else if (isset($_POST['add-user-btn'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $cpassword = mysqli_real_escape_string($conn, $_POST['cpassword']);


    //Check email already 
    $check_email_query = "SELECT email FROM users WHERE email='$email' ";
    $check_email_query_run = mysqli_query($conn, $check_email_query);
    if (mysqli_num_rows($check_email_query_run) > 0) {
        redirect("../admin/add_user.php", "Email của bạn đã được sử dụng. Xin hãy sử dụng Email khác");
    }
    //Check password no match
    else {
        if ($password == $cpassword) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                //Inser user data
                $pass_hash = password_hash($password, PASSWORD_DEFAULT);
                $insert_query = "INSERT INTO `users` (`name`,`email`,`phone`,`password`,`address`) VALUES ('$name','$email','$phone','$pass_hash','$address')";
                $insert_query_run = mysqli_query($conn, $insert_query);
                if ($insert_query_run) {
                    redirect("../admin/user.php", "Thêm người dùng thành công");
                } else {
                    redirect("../admin/user.php", "Đã xảy ra lỗi");
                }
            } else {
                redirect("../admin/user.php", "Địa chỉ email không hợp lệ");
            }
        } else {
            redirect("../admin/user.php", "Mật khẩu không khớp");
        }
    }
}
//khóa người dùng
else if(isset($_POST['user_id'])){
    $id = $_POST['user_id'];
    $newRole = isset($_POST['lock_user']) ? 2 : 0; //nếu checkbox được tick thì trả ra 2, nếu đã bỏ tick thì trả ra 0

    $query = "UPDATE users SET role_as = '$newRole' WHERE id = '$id'";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        //Nếu $newRole == 2 (tức là đang khóa người dùng)  Gán "Khóa người dùng thành công" vào biến $message.
        //Ngược lại (tức là $newRole == 0, nghĩa là mở khóa) Gán "Mở khóa người dùng thành công" vào $message.
        $message = $newRole == 2 ? "Khóa người dùng thành công" : "Mở khóa người dùng thành công";
        redirect("../admin/user.php", $message);
    } else {
        redirect("../admin/user.php", "Lỗi, không thể khóa người dùng");
    }
}
//sửa người dùng 
else if (isset($_POST['user_idd'])) {
    echo "Mã người dùng là: " . $_POST['user_idd'];
    $id = $_POST['user_idd'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    $query = "SELECT password FROM users WHERE id = '$id'";
    $query_run = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($query_run);
    $current_password = $row['password'];

    if (empty($password) || $password == $current_password) {
        $update_query = "UPDATE `users` SET `name`='$name', `email`='$email', `phone`='$phone', `address`='$address' WHERE `id`='$id' ";
        $update_query_run = mysqli_query($conn, $update_query);
        if ($update_query_run) {
            redirect("../admin/user.php", "Cập nhật thông tin thành công");
        } else {
            redirect("../admin/user.php", "Xảy ra lỗi, vui lòng cập nhật lại");
        }
    }
     else {
        if ($password == $cpassword) {
            $p_hash = password_hash($password, PASSWORD_DEFAULT);
            $update_query = "UPDATE `users` SET `name`='$name', `email`='$email', `phone`='$phone', `address`='$address', `password`='$p_hash' WHERE `id`='$id' ";
            $update_query_run = mysqli_query($conn, $update_query);
            if ($update_query_run) {
                redirect("../admin/user.php", "Cập nhật thông tin thành công");
            } else {
                redirect("../admin/user.php", "Xảy ra lỗi, vui lòng cập nhật lại");
            }
        } else {
            redirect("../admin/edit_user.php", "Mật khẩu không khớp, vui lòng nhập lại");
        }
    }
}