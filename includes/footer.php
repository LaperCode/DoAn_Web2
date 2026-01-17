<footer class="bg-second">
    <div class="container">
        <div class="row">
            <div class="col-5 col-md-6 ">
                <h3 class="footer-head">Công Ty ZBooks</h3>
                <ul class="menu">
                    <li><a href="#"> Điện thoại: (+84) 927723622</a></li>
                    <li><a href="#">Email: ZBooks@mail.com</a></li>
                    <li><a href="#">Địa chỉ: 273 Đ. An D. Vương, Phường 3, Quận 5, Thành phố Hồ Chí Minh</a></li>
                    <li><a href="#">Thời gian làm việc: 8:00 - 18:00 Thứ 2 - Chủ Nhật</a></li>
                </ul>
            </div>

            <div class="col-5 col-md-6">
                <h3 class="footer-head">Thông Tin Và Chính Sách</h3>
                <ul class="menu">
                    <li><a href="#">Liên hệ chúng tôi</a></li>
                    <li><a href="#">Chương trình ưu đãi thu game cũ</a></li>
                    <li><a href="#">Chính sách đổi trả game</a></li>
                    <li><a href="#">Chính sách bảo mật thông tin</a></li>
                </ul>
            </div>
            <div class="col-2 col-md-6 col-sm-12">
                <div class="contact">
                    <h3 class="contact-header">
                        GameHub
                    </h3>
                    <ul class="contact-socials">
                        <li><a href="#">
                                <i class='bx bxl-facebook-circle'></i>
                            </a></li>
                        <li><a href="#">
                                <i class='bx bxl-instagram-alt'></i>
                            </a></li>
                        <li><a href="#">
                                <i class='bx bxl-youtube'></i>
                            </a></li>
                        <li><a href="#">
                                <i class='bx bxl-twitter'></i>
                            </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- app js -->
<script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<script>
    <?php if (isset($_SESSION['message'])) {
    ?>
        alertify.set('notifier', 'position', 'top-right');
        alertify.success('<?= $_SESSION['message'] ?>');
    <?php
        unset($_SESSION['message']);
    }
    ?>
</script>