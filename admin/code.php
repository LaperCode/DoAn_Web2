<?php
session_start();
include("../middleware/adminMiddleware.php");
include("../config/dbcon.php");

if (isset($_POST['add_category_btn'])) {    //Thêm danh mục

    $name = $_POST['name'];
    $slug = $_POST['slug'] . "-" . rand(10, 99);
    $description = $_POST['description'];
    $status = isset($_POST['status']) ? '1' : '0';
    $image = $_FILES['image']['name'];

    $path = "../images";
    $image_ext = pathinfo($image, PATHINFO_EXTENSION);
    $filename = time() . '.' . $image_ext;

    $cate_query = "INSERT INTO categories (name,slug,description,status,image) 
    VALUES ('$name', '$slug','$description',' $status', '$filename')";

    $cate_query_run = mysqli_query($conn, $cate_query);

    if ($cate_query_run) {
        move_uploaded_file($_FILES['image']['tmp_name'], $path . '/' . $filename);
        redirect("add-category.php", "Thêm danh mục thành công");
    } else {
        redirect("add-category.php", "Đã xảy ra lỗi");
    }
} else if (isset($_POST['update_category_btn'])) {  //Cập nhật danh mục

    $category_id = $_POST['category_id'];
    $name = $_POST['name'];
    $slug = $_POST['slug'];
    $description = $_POST['description'];
    $status = isset($_POST['status']) ? '1' : '0';

    $new_image = $_FILES['image']['name'];
    $old_image = $_POST['old_image'];

    if ($new_image != "") {
        //$update_filename= $new_image;
        $image_ext = pathinfo($new_image, PATHINFO_EXTENSION);
        $update_filename = time() . '.' . $image_ext;
    } else {
        $update_filename = $old_image;
    }
    $path = "../images";
    $update_query = "UPDATE categories SET name='$name', slug='$slug', description='$description', status='$status', image='$update_filename' WHERE id='$category_id'";
    $update_query_run = mysqli_query($conn, $update_query);

    if ($update_query_run) {
        if ($_FILES['image']['name'] != "") {
            move_uploaded_file($_FILES['image']['tmp_name'], $path . '/' . $update_filename);
            if (file_exists("../images/" . $old_image)) {
                unlink("../images/" . $old_image);
            }
        }
        redirect("edit-category.php?id=$category_id", "Cập nhật danh mục thành công");
    } else {
        redirect("edit-category.php?id=$category_id", "Đã xảy ra lỗi");
    }
} else if (isset($_POST['delete_category_btn'])) { //Xóa danh mục
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);

    $category_query = "SELECT * FROM categories WHERE id='$category_id'";
    $category_query_run = mysqli_query($conn, $category_query);
    $category_data = mysqli_fetch_array($category_query_run);
    $image = $category_data['image'];

    $delete_query = "DELETE FROM categories WHERE id='$category_id'";
    $delete_query_run = mysqli_query($conn, $delete_query);

    if ($delete_query_run) {
        if (file_exists("../images/" . $image)) {
            unlink("../images/" . $image);
        }
        redirect("category.php", "Xóa danh mục thành công");
    } else {
        redirect("caterory.php", "Đã xảy ra lỗi");
    }
} else if (isset($_POST['add_product_btn'])) {  //Thêm sản phẩm
    $category_id = $_POST['category_id'];

    $name = $_POST['name'];
    $slug = $_POST['slug']  . "-" . rand(10, 99);
    $small_description = $_POST['small_description'];
    $description = $_POST['description'];
    $original_price = $_POST['original_price'];
    $selling_price = $_POST['selling_price'] == '' || $_POST['selling_price'] == 0 ? $_POST['original_price'] : $_POST['selling_price'];
    $status = isset($_POST['status']) ? '1' : '0';
    $qty = 100;
    $image = $_FILES['image']['name'];

    $path = "../images";
    $image_ext = pathinfo($image, PATHINFO_EXTENSION);
    $filename = time() . '.' . $image_ext;

    // Validate input fields
    if ($name != "" && $slug != "" && $description != "") {
        $product_query = "INSERT INTO products (category_id,name,slug,small_description,description,original_price,selling_price,image,qty,status) VALUES 
        ('$category_id','$name','$slug','$small_description','$description','$original_price','$selling_price','$filename','$qty','$status')";

        $product_query_run = mysqli_query($conn, $product_query);

        if ($product_query_run) {
            move_uploaded_file($_FILES['image']['tmp_name'], $path . '/' . $filename);
            redirect("add-product.php", "Thêm sản phẩm thành công");
        } else {
            redirect("add-product.php", "Đã xảy ra lỗi");
        }
    } else {
        redirect("add-product.php", "Bạn chưa điền đủ thông tin");
    }
} else if (isset($_POST['update_product_btn'])) {   //Cập nhật sản phẩm
    $product_id = $_POST['product_id'];
    $category_id = $_POST['category_id'];

    $name = $_POST['name'];
    $slug = $_POST['slug']  . "-" . rand(10, 99);
    $small_description = $_POST['small_description'];
    $description = $_POST['description'];
    $original_price = $_POST['original_price'];
    $selling_price = $_POST['selling_price'] == '' || $_POST['selling_price'] == 0 ? $_POST['original_price'] : $_POST['selling_price'];
    $status = isset($_POST['status']) ? '1' : '0';
    $qty = $_POST['qty'];

    $path = "../images";

    $new_image = $_FILES['image']['name'];
    $old_image = $_POST['old_image'];

    if ($new_image != "") {
        //$update_filename= $new_image;
        $image_ext = pathinfo($new_image, PATHINFO_EXTENSION);
        $update_filename = time() . '.' . $image_ext;
    } else {
        $update_filename = $old_image;
    }

    $update_product_query = "UPDATE products SET name='$name', category_id='$category_id', slug='$slug', small_description='$small_description', description='$description',
    original_price='$original_price', selling_price='$selling_price', status='$status', qty='$qty', image='$update_filename' WHERE id='$product_id' ";
    $update_product_query_run = mysqli_query($conn, $update_product_query);

    if ($update_product_query_run) {
        if ($_FILES['image']['name'] != "") {
            move_uploaded_file($_FILES['image']['tmp_name'], $path . '/' . $update_filename);
            if (file_exists("../images/" . $old_image)) {
                unlink("../images/" . $old_image);
            }
        }
        redirect("edit-product.php?id=$product_id", "Cập nhật sản phẩm thành công");
    } else {
        redirect("edit-product.php?id=$product_id", "Đã xảy ra lỗi");
    }
} else if (isset($_POST['delete_product_btn'])) {   //Xóa sản phẩm
    $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);

    $product_query = "SELECT * FROM products WHERE id='$product_id'";
    $product_query_run = mysqli_query($conn, $product_query);
    $product_data = mysqli_fetch_array($product_query_run);
    $image = $product_data['image'];

    $query_orderdetail = "SELECT * FROM order_detail WHERE product_id='$product_id'";
    $orderdetail_query_run = mysqli_query($conn, $query_orderdetail);
    if (mysqli_num_rows($orderdetail_query_run) > 0) {
        $update_product_query = "UPDATE products SET status='1' WHERE id='$product_id' ";
        $update_product_query_run = mysqli_query($conn, $update_product_query);
        if ($update_product_query_run) {
            redirect("products.php", "Sản phẩm đã có trong đơn hàng. Đã ẩn thành công.");
        } else {
            redirect("products.php", "Có lỗi xảy ra khi ẩn sản phẩm.");
        }
    } else {
        $delete_query = "DELETE FROM products WHERE id='$product_id'";
        $delete_query_run = mysqli_query($conn, $delete_query);
        if ($delete_query_run) {
            if (file_exists("../images/" . $image)) {
                unlink("../images/" . $image);
            }
            redirect("products.php", "Xóa sản phẩm thành công");
        } else {
            redirect("products.php", "Không thể xóa sản phẩm vì có đơn hàng chứa sản phẩm đó");
        }
    }
} else if (isset($_POST['add_blog_btn'])) {  //Thêm bài viết
    $title          = $_POST['title'];
    $slug           = $_POST['slug']  . "-" . rand(10, 99);
    $small_content  = $_POST['small_content'];
    $content        = addslashes($_POST['content']);

    $image = $_FILES['image']['name'];

    $path = "../images";
    $image_ext = pathinfo($image, PATHINFO_EXTENSION);
    $filename = time() . '.' . $image_ext;

    if ($title != "" && $slug != "" && $content != "") {
        $blog_query = "INSERT INTO blog (title,slug,img,small_content,content) VALUES 
        ('$title', '$slug', '$filename', '$small_content', '$content')";

        $blog_query_run = mysqli_query($conn, $blog_query);

        if ($blog_query_run) {
            move_uploaded_file($_FILES['image']['tmp_name'], $path . '/' . $filename);
            redirect("add-blog.php", "Thêm bài viết thành công");
        } else {
            redirect("add-blog.php", "Đã xảy ra lỗi");
        }
    } else {
        redirect("add-product.php", "Bạn chưa điền đủ thông tin");
    }
} else if (isset($_POST['update_blog_btn'])) {  //Cập nhật bài viết

    $id             = $_POST['id'];
    $title          = $_POST['title'];
    $slug           = $_POST['slug']  . "-" . rand(10, 99);
    $small_content  = $_POST['small_content'];
    $content        = addslashes($_POST['content']);

    $path   =   "../images";

    $new_image = $_FILES['image']['name'];
    $old_image = $_POST['old_image'];

    if ($new_image != "") {
        //$update_filename= $new_image;
        $image_ext = pathinfo($new_image, PATHINFO_EXTENSION);
        $update_filename = time() . '.' . $image_ext;
    } else {
        $update_filename = $old_image;
    }

    $update_blog_query = "UPDATE
                            `blog`
                        SET
                            `title`         = '$title',
                            `slug`          = '$slug',
                            `img`           = '$update_filename',
                            `small_content` = '$small_content',
                            `content`       = '$content'
                        WHERE
                            `id` = '$id'";

    $update_blog_query_run  = mysqli_query($conn, $update_blog_query);

    if ($update_blog_query_run) {
        if ($_FILES['image']['name'] != "") {
            move_uploaded_file($_FILES['image']['tmp_name'], $path . '/' . $update_filename);
            if (file_exists("../images/" . $old_image)) {
                unlink("../images/" . $old_image);
            }
        }
        redirect("edit-blog.php?id=$id", "Cập nhật bài viết thành công");
    } else {
        redirect("edit-blog.php?id=$id", "Đã xảy ra lỗi");
    }
} else if (isset($_POST['delete_blog_btn'])) {  //Xóa bài viết
    $blog_id    =   $_POST['blog_id'];

    $blog_query =   "SELECT * FROM blog WHERE id='$blog_id'";

    $blog_query_run = mysqli_query($conn, $blog_query);

    $blog_data  =  mysqli_fetch_array($blog_query_run);

    $image      =   $blog_data['img'];

    $delete_query   = "DELETE FROM blog WHERE id='$blog_id'";

    $delete_query_run = mysqli_query($conn, $delete_query);

    if ($delete_query_run) {
        if (file_exists("../images/" . $image)) {
            unlink("../images/" . $image);
        }
        redirect("blog.php", "Xóa bài viết thành công");
    } else {
        redirect("blog.php", "Đã xảy ra lỗi");
    }
} else if (isset($_GET['order'])) { //Cập nhật trạng thái đơn hàng
    $order_id   = $_GET['id'];
    $type       = $_GET['order'];
    $query =    "UPDATE `orders` SET `status` = '$type'
                WHERE `id` = '$order_id'";
    mysqli_query($conn, $query);

    $query =    "UPDATE `order_detail` SET `status` = '$type'
                WHERE `order_id` = '$order_id'";
    mysqli_query($conn, $query);

    redirect("customer-order-details.php?id_order=$order_id", "Cập nhập trạng thái thành công");
} else if (isset($_POST['import_stock'])) {  // NHẬP HÀNG VỚI CÔNG THỨC GIÁ BÌNH QUÂN
    $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $quantity_imported = mysqli_real_escape_string($conn, $_POST['quantity_imported']);
    $import_price = mysqli_real_escape_string($conn, $_POST['import_price']);
    $profit_margin = mysqli_real_escape_string($conn, $_POST['profit_margin']);
    $note = mysqli_real_escape_string($conn, $_POST['note']);
    $admin_id = $_SESSION['auth_user']['id'];

    // Lấy thông tin sản phẩm hiện tại
    $product_query = "SELECT qty, original_price FROM products WHERE id='$product_id'";
    $product_result = mysqli_query($conn, $product_query);

    if (mysqli_num_rows($product_result) > 0) {
        $product = mysqli_fetch_array($product_result);
        $old_quantity = $product['qty'];
        $old_original_price = $product['original_price'];

        // CÔNG THỨC GIÁ BÌNH QUÂN (theo yêu cầu thầy):
        // Giá nhập mới = (SL tồn × Giá nhập cũ + SL nhập × Giá nhập mới) / (SL tồn + SL nhập)
        $new_total_quantity = $old_quantity + $quantity_imported;
        $new_average_price = ($old_quantity * $old_original_price + $quantity_imported * $import_price) / $new_total_quantity;

        // Giá bán = Giá nhập × (100% + Tỷ lệ lợi nhuận)
        $new_selling_price = $new_average_price * (1 + $profit_margin / 100);

        // Cập nhật thông tin sản phẩm
        $update_product_query = "UPDATE products SET 
            qty = '$new_total_quantity',
            original_price = '$new_average_price',
            selling_price = '$new_selling_price',
            profit_margin = '$profit_margin'
            WHERE id = '$product_id'";

        $update_product_run = mysqli_query($conn, $update_product_query);

        if ($update_product_run) {
            // Lưu lịch sử nhập hàng
            $insert_history_query = "INSERT INTO import_history 
                (product_id, quantity_imported, import_price, old_quantity, old_original_price, 
                 new_average_price, new_selling_price, profit_margin, admin_id, note) 
                VALUES 
                ('$product_id', '$quantity_imported', '$import_price', '$old_quantity', '$old_original_price',
                 '$new_average_price', '$new_selling_price', '$profit_margin', '$admin_id', '$note')";

            $insert_history_run = mysqli_query($conn, $insert_history_query);

            if ($insert_history_run) {
                redirect("import-history.php", "Nhập hàng thành công! Giá bình quân mới: " . number_format($new_average_price, 2) . " $");
            } else {
                redirect("import-stock.php", "Lỗi khi lưu lịch sử nhập hàng");
            }
        } else {
            redirect("import-stock.php", "Lỗi khi cập nhật sản phẩm");
        }
    } else {
        redirect("import-stock.php", "Không tìm thấy sản phẩm");
    }
} {
    header('Location: ./index.php');
}
