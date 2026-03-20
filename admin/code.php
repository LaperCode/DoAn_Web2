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
    $original_price = 0;
    $selling_price = 0;
    $status = isset($_POST['status']) ? '1' : '0';
    $qty = 0;
    $profit_margin = 0;
    $image = $_FILES['image']['name'];

    $path = "../images";
    $image_ext = pathinfo($image, PATHINFO_EXTENSION);
    $filename = time() . '.' . $image_ext;

    // Validate input fields
    if ($name != "" && $slug != "" && $description != "") {
        $product_query = "INSERT INTO products (category_id,name,slug,small_description,description,original_price,selling_price,profit_margin,image,qty,status) VALUES 
    ('$category_id','$name','$slug','$small_description','$description','$original_price','$selling_price','$profit_margin','$filename','$qty','$status')";

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
    $status = isset($_POST['status']) ? '1' : '0';

    $path = "../images";

    $new_image = $_FILES['image']['name'];
    $old_image = $_POST['old_image'];

    if ($new_image != "") {
        $image_ext = pathinfo($new_image, PATHINFO_EXTENSION);
        $update_filename = time() . '.' . $image_ext;
    } else {
        $update_filename = $old_image;
    }

    // Đọc lại giá trị hiện tại để không bao giờ ghi đè original_price, selling_price, qty
    $current_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT original_price, selling_price, qty FROM products WHERE id='$product_id'"));
    $keep_original_price = $current_data['original_price'];
    $keep_selling_price  = $current_data['selling_price'];
    $keep_qty            = $current_data['qty'];

    $update_product_query = "UPDATE products SET name='$name', category_id='$category_id', slug='$slug', small_description='$small_description', description='$description',
    status='$status', image='$update_filename',
    original_price='$keep_original_price', selling_price='$keep_selling_price', qty='$keep_qty'
    WHERE id='$product_id'";
    $update_product_query_run = mysqli_query($conn, $update_product_query);

    if ($update_product_query_run) {
        if ($_FILES['image']['name'] != "") {
            move_uploaded_file($_FILES['image']['tmp_name'], $path . '/' . $update_filename);
            if (file_exists("../images/" . $old_image)) {
                unlink("../images/" . $old_image);
            }
        }
        $back_params = http_build_query(array_filter([
            'tensanpham' => $_POST['back_tensanpham'] ?? '',
            'loaisanpham' => $_POST['back_loaisanpham'] ?? '',
            'qtymin' => $_POST['back_qtymin'] ?? '',
            'qtymax' => $_POST['back_qtymax'] ?? '',
            'giamin' => $_POST['back_giamin'] ?? '',
            'giamax' => $_POST['back_giamax'] ?? '',
            'trangthai' => $_POST['back_trangthai'] ?? '',
            'sapxep' => $_POST['back_sapxep'] ?? '1',
            'theocot' => $_POST['back_theocot'] ?? 'id',
            'page' => $_POST['back_page'] ?? '1',
        ]));
        redirect("edit-product.php?id=$product_id&$back_params", "Cập nhật sản phẩm thành công");
    } else {
        $back_params = http_build_query(array_filter([
            'tensanpham' => $_POST['back_tensanpham'] ?? '',
            'loaisanpham' => $_POST['back_loaisanpham'] ?? '',
            'qtymin' => $_POST['back_qtymin'] ?? '',
            'qtymax' => $_POST['back_qtymax'] ?? '',
            'giamin' => $_POST['back_giamin'] ?? '',
            'giamax' => $_POST['back_giamax'] ?? '',
            'trangthai' => $_POST['back_trangthai'] ?? '',
            'sapxep' => $_POST['back_sapxep'] ?? '1',
            'theocot' => $_POST['back_theocot'] ?? 'id',
            'page' => $_POST['back_page'] ?? '1',
        ]));
        redirect("edit-product.php?id=$product_id&$back_params", "Đã xảy ra lỗi");
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
    $current_status = null;
    $status_result = mysqli_query($conn, "SELECT `status` FROM `orders` WHERE `id` = '$order_id'");
    if ($status_result && mysqli_num_rows($status_result) > 0) {
        $current_status = mysqli_fetch_assoc($status_result)['status'];
    }

    if ($type == 5 && $current_status !== '5') {
        $restore_query = "UPDATE `products` p
                          JOIN `order_detail` od ON p.id = od.product_id
                          SET p.qty = p.qty + od.quantity
                          WHERE od.order_id = '$order_id'";
        mysqli_query($conn, $restore_query);
    }
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
                redirect("import-manage.php", "Nhập hàng thành công! Giá bình quân mới: " . number_format($new_average_price, 2) . " $");
            } else {
                redirect("import-stock.php", "Lỗi khi lưu lịch sử nhập hàng");
            }
        } else {
            redirect("import-stock.php", "Lỗi khi cập nhật sản phẩm");
        }
    } else {
        redirect("import-stock.php", "Không tìm thấy sản phẩm");
    }
} else if (isset($_POST['import_receipt'])) {  // NHẬP HÀNG NHIỀU SẢN PHẨM THEO PHIẾU
    $product_ids = isset($_POST['product_id']) ? $_POST['product_id'] : [];
    $quantities = isset($_POST['quantity_imported']) ? $_POST['quantity_imported'] : [];
    $import_prices = isset($_POST['import_price']) ? $_POST['import_price'] : [];
    $profit_margins = isset($_POST['profit_margin']) ? $_POST['profit_margin'] : [];
    $note = isset($_POST['note']) ? mysqli_real_escape_string($conn, trim($_POST['note'])) : '';
    $admin_id = $_SESSION['auth_user']['id'];
    // Ngày nhập do người dùng chọn, fallback về ngày hiện tại
    $import_date = isset($_POST['import_date']) && !empty($_POST['import_date'])
        ? mysqli_real_escape_string($conn, $_POST['import_date'])
        : date('Y-m-d');

    $items = [];
    foreach ($product_ids as $index => $product_id) {
        $product_id = (int)$product_id;
        $quantity = isset($quantities[$index]) ? (int)$quantities[$index] : 0;
        $import_price = isset($import_prices[$index]) ? (float)$import_prices[$index] : 0;
        $profit_margin = isset($profit_margins[$index]) ? (float)$profit_margins[$index] : 0;

        if ($product_id > 0 && $quantity > 0 && $import_price >= 0) {
            $items[] = [
                'product_id' => $product_id,
                'quantity' => $quantity,
                'import_price' => $import_price,
                'profit_margin' => $profit_margin
            ];
        }
    }

    if (count($items) === 0) {
        redirect("import-stock.php", "Vui lòng chọn ít nhất 1 sản phẩm để nhập");
    }

    mysqli_begin_transaction($conn);

    try {
        $total_value = 0;
        $total_quantity = 0;
        $total_items = count($items);

        foreach ($items as $item) {
            $total_value += $item['quantity'] * $item['import_price'];
            $total_quantity += $item['quantity'];
        }

        $insert_receipt_query = "INSERT INTO import_receipts (code, admin_id, note, total_value, total_quantity, total_items, import_date) 
            VALUES ('', '$admin_id', '$note', '$total_value', '$total_quantity', '$total_items', '$import_date')";

        if (!mysqli_query($conn, $insert_receipt_query)) {
            throw new Exception('Không thể tạo phiếu nhập');
        }

        $receipt_id = mysqli_insert_id($conn);
        $receipt_code = 'PN' . str_pad($receipt_id, 3, '0', STR_PAD_LEFT);
        mysqli_query($conn, "UPDATE import_receipts SET code = '$receipt_code' WHERE id = '$receipt_id'");

        foreach ($items as $item) {
            $product_id = $item['product_id'];
            $quantity_imported = $item['quantity'];
            $import_price = $item['import_price'];
            $profit_margin = $item['profit_margin'];

            $product_query = "SELECT qty, original_price FROM products WHERE id = '$product_id'";
            $product_result = mysqli_query($conn, $product_query);

            if (!$product_result || mysqli_num_rows($product_result) === 0) {
                throw new Exception('Không tìm thấy sản phẩm');
            }

            $product = mysqli_fetch_array($product_result);
            $old_quantity = $product['qty'];
            $old_original_price = $product['original_price'];

            $new_total_quantity = $old_quantity + $quantity_imported;
            $new_average_price = ($old_quantity * $old_original_price + $quantity_imported * $import_price) / $new_total_quantity;
            $new_selling_price = $new_average_price * (1 + $profit_margin / 100);

            $update_product_query = "UPDATE products SET 
                qty = '$new_total_quantity',
                original_price = '$new_average_price',
                selling_price = '$new_selling_price',
                profit_margin = '$profit_margin'
                WHERE id = '$product_id'";

            if (!mysqli_query($conn, $update_product_query)) {
                throw new Exception('Lỗi khi cập nhật sản phẩm');
            }

            $insert_history_query = "INSERT INTO import_history 
                (receipt_id, product_id, quantity_imported, import_price, old_quantity, old_original_price, 
                 new_average_price, new_selling_price, profit_margin, admin_id, note) 
                VALUES 
                ('$receipt_id', '$product_id', '$quantity_imported', '$import_price', '$old_quantity', '$old_original_price',
                 '$new_average_price', '$new_selling_price', '$profit_margin', '$admin_id', '$note')";

            if (!mysqli_query($conn, $insert_history_query)) {
                throw new Exception('Lỗi khi lưu lịch sử nhập hàng');
            }
        }

        mysqli_commit($conn);
        redirect("import-manage.php", "Tạo phiếu nhập thành công (#$receipt_code)");
    } catch (Exception $e) {
        mysqli_rollback($conn);
        redirect("import-stock.php", "Có lỗi xảy ra: " . $e->getMessage());
    }
} {
    header('Location: ./index.php');
}
