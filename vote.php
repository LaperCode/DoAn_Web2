<?php 
include("./includes/header.php");
include_once("./functions/userfunctions.php");


if (!isset($_SESSION['auth_user']['id'])) {
    die("Từ chối truy cập <a href='./login'>Đăng nhập ngay</a> ");
}

// Lấy ID sản phẩm và ép kiểu để tránh lỗi SQL Injection
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die("Không tìm thấy sản phẩm! <a href='./cart-status.php'>Trở lại</a>");
}

// Lấy thông tin sản phẩm
$product = getMyOrderVote($id);
?>
<link rel="stylesheet" href="./assets/css/vote.css">
<body>
    <div class="bg-main">
        <div class="container">
            <div class="box">
                <div class="breadcumb">
                    <a href="/">Trang chủ</a>
                    <span><i class='bx bxs-chevrons-right'></i></span>
                    <a href="./cart.php">Giỏ hàng của tôi</a>
                    <span><i class='bx bxs-chevrons-right'></i></span>
                    <a href="./cart-status.php">Giỏ hàng đã mua</a>
                    <span><i class='bx bxs-chevrons-right'></i></span>
                    <a href="#">Đánh giá</a>
                </div>
            </div>

            <div class="box" style="padding: 0 40px">
                <div class="product-info">
                <?php if (!$product) { 
                    http_response_code(404); // Trả về mã lỗi 404 nếu không tìm thấy
                ?>
                    <p style="font-size: 20px; text-align: center;">
                        Mặt hàng này chưa thể vote <a style="color: blue; text-decoration: underline" href="./cart-status.php">Trở lại</a>  
                    </p>
                <?php } else { ?>
                    <div class="box" style="padding: 0 100px">
                        <div class="product-info">
                            <h1><?= htmlspecialchars($product['name']) ?></h1>
                            <p class="product-description">
                                <?= nl2br(htmlspecialchars($product['small_description'])) ?>
                            </p>
                        </div>
                        <br>
                        <div class="product-img" id="product-img">
                            <img src="./images/<?= htmlspecialchars($product['image']) ?>" alt="Hình ảnh sản phẩm">
                        </div>
                        <br>
                        <div class="product-detail-description">
                            <?= nl2br(htmlspecialchars($product['description'])) ?>
                        </div>
                    </div>

                    <form class="form-vote" method="post" action="./functions/ordercode.php">
                        <div class="rate-form">
                            <input type="hidden" name="rate" value="true">
                            <input type="hidden" name="id" value="<?= $id ?>">
                            <fieldset class="rating">
                                <?php 
                                $currentRate = $product['rate'] ?? 0;
                                for ($i = 5; $i >= 1; $i--) {
                                    $checked = ($currentRate == $i) ? 'checked' : '';
                                    echo "<input type='radio' id='star$i' name='rating' value='$i' $checked />";
                                    echo "<label class='full' for='star$i' title='Đánh giá $i sao'></label>";
                                }
                                ?>
                            </fieldset>
                        </div>
                        <br>
                        <div class="comment">
                            <textarea name="comment"><?= htmlspecialchars($product['comment'] ?? '') ?></textarea>
                        </div>
                        <br>
                        <div class="comment">
                            <button class="btn-vote">Đánh giá</button>
                        </div>
                    </form>
                <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <?php include("./includes/footer.php") ?>
    <script src="./assets/js/app.js"></script>
    <script src="./assets/js/index.js"></script>
</body>
</html>
