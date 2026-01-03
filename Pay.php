<?php 
include("./includes/header.php");
if (!isset($_SESSION['auth_user']['id'])){
    die("Từ Chối truy cập <a href='./login'>Đăng nhập ngay</a>");
}

$id = $_SESSION['auth_user']['id'];

$users = getByID("users",$id);                              
$data= mysqli_fetch_array($users);
?>;

<style>
    th,td{
        padding: 5px;
        text-align: center;
    }
    .input-number{
        width: 100%;
        font-size: 20px;
        outline: none;
        border: none;
    }
    .btn-buy{
        border: none;
        outline: none;
        font-size: 17px;
        padding: 2px 6px;
        border-radius: 2px;
        background-color: #59e1ff;
        display: inline-block;
    }
</style>

<body>
    <!-- product-detail content -->
    <div class="bg-main">
        <div class="container">
            <div class="box">
                <div class="breadcumb">
                    <a href="index.php">Trang chủ</a>
                    <span><i class='bx bxs-chevrons-right'></i></span>
                    <a href="./cart.php">Giỏ hàng của tôi</a>
                    <span><i class='bx bxs-chevrons-right'></i></span>
                    <a href="#">Thanh toán</a>
                </div>
            </div>

            <div class="box" style="padding: 0 40px">
                <div class="product-info">
                    <?php include("PayInclude.php");?>
                <br>
                <br>
                </div>
            </div>
        </div>
    </div>
    <!-- end product-detail content -->
    <?php include("./includes/footer.php") ?>
    <script src="./assets/js/app.js"></script>
    <script src="./assets/js/index.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- <script src="./assets/font/jquery/jquery-3.6.1.js"></script> -->
    <script type="text/javascript" src="./assets/js/Wn3.js"></script>
</body>
    <script>
        $(document).ready(function () {
            $('.input-number').on('change', function (e) {
                if (e.target.value == 0){
                    e.target.value = 1;
                }
                const node      = $(this).parent().parent();
                const price     = parseInt(node.find('.product-price').val());
                let total_order = parseInt(e.target.value);
                let total_price = price * total_order;
                node.find('.total-price').html(total_price);
            })
        });
    </script>

<script>
$(document).ready(function() {
  $(".btn-buy").click(function(event) {
    event.preventDefault();

    let addtional = $("#order_comments").val();
    let name = $("#name").val();
    let phone = $("#phone").val();
    let address = $("#address").val();

    // Chuyển đổi form thành mảng các đối tượng thuộc tính và giá trị
    var formDataArray = $("form").serializeArray();

    // Kiểm tra radio button đã được chọn
    if ($("#payment_method_bacs").is(":checked")) {
      let payment_method_bacs = $("#payment_method_bacs").val();
      formDataArray.push({ name: "payment_method", value: 0 });
    }else{
      let payment_method_cod = $("#payment_method_cod").val();
      formDataArray.push({ name: "payment_method", value: 1 });
    }

    // Thêm biến tùy chỉnh vào mảng formDataArray
    formDataArray.push({ name: "addtional", value: addtional });
    formDataArray.push({ name: "name", value: name });
    formDataArray.push({ name: "phone", value: phone });
    formDataArray.push({ name: "address", value: address });

    // Chuyển đổi mảng formDataArray thành chuỗi dữ liệu
    var formData = $.param(formDataArray);
    console.log(formData);
    // Gửi dữ liệu form thông qua AJAX
    $.ajax({
      url: "./functions/ordercode.php",
      type: "post",
      data: formData,
      success: function(response) {
        if (response == 1) {
            console.log(response);
            window.location.href = "cart-status.php";
        }
      },
      error: function(xhr, status, error) {
        console.error(error);
      }
    });
  });
});
</script>
</html>