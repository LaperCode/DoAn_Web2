<?php
include("./includes/header.php");
?>

<style>
    /* Contact Page Styling */
    .contact-page {
        padding: 60px 0;
    }

    .contact-header-section {
        text-align: center;
        margin-bottom: 60px;
    }

    .contact-header-section h1 {
        font-size: 48px;
        font-weight: 700;
        color: #2C3E50;
        margin-bottom: 15px;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    .contact-header-section p {
        font-size: 18px;
        color: #7F8C8D;
        max-width: 600px;
        margin: 0 auto;
    }

    .contact-container {
        display: flex;
        gap: 40px;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .contact-info-section {
        flex: 1;
    }

    .contact-form-section {
        flex: 1.2;
    }

    /* Contact Info Cards */
    .contact-info-card {
        background: white;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 25px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border-left: 4px solid #F39C12;
    }

    .contact-info-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(243, 156, 18, 0.2);
    }

    .contact-info-card .icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #F39C12 0%, #E67E22 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
    }

    .contact-info-card .icon i {
        font-size: 24px;
        color: white;
    }

    .contact-info-card h3 {
        font-size: 20px;
        font-weight: 600;
        color: #2C3E50;
        margin-bottom: 10px;
    }

    .contact-info-card p {
        color: #7F8C8D;
        font-size: 15px;
        line-height: 1.6;
        margin: 0;
    }

    .contact-info-card a {
        color: #F39C12;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .contact-info-card a:hover {
        color: #E67E22;
    }

    /* Contact Form */
    .contact-form {
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .contact-form h2 {
        font-size: 28px;
        font-weight: 700;
        color: #2C3E50;
        margin-bottom: 10px;
    }

    .contact-form .form-subtitle {
        color: #7F8C8D;
        margin-bottom: 30px;
        font-size: 15px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        color: #2C3E50;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #ecf0f1;
        border-radius: 8px;
        font-size: 15px;
        transition: all 0.3s ease;
        outline: none;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        border-color: #F39C12;
        box-shadow: 0 0 0 3px rgba(243, 156, 18, 0.1);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 150px;
    }

    .submit-btn {
        width: 100%;
        padding: 15px 30px;
        background: linear-gradient(135deg, #F39C12 0%, #E67E22 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        text-transform: uppercase;
        cursor: pointer;
        transition: all 0.3s ease;
        letter-spacing: 1px;
    }

    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(243, 156, 18, 0.4);
    }

    /* Social Links */
    .social-links {
        display: flex;
        gap: 15px;
        margin-top: 20px;
    }

    .social-link {
        width: 45px;
        height: 45px;
        background: #34495E;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .social-link i {
        color: white;
        font-size: 20px;
    }

    .social-link:hover {
        background: #F39C12;
        transform: translateY(-3px);
    }

    /* Map Section */
    .map-section {
        margin-top: 80px;
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .map-section h2 {
        font-size: 28px;
        font-weight: 700;
        color: #2C3E50;
        margin-bottom: 25px;
        text-align: center;
    }

    .map-container {
        width: 100%;
        height: 400px;
        border-radius: 12px;
        overflow: hidden;
    }

    .map-container iframe {
        width: 100%;
        height: 100%;
        border: 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .contact-container {
            flex-direction: column;
        }

        .contact-header-section h1 {
            font-size: 36px;
        }

        .contact-form {
            padding: 30px 20px;
        }
    }
</style>

<body>
    <div class="bg-main">
        <div class="container">
            <!-- Breadcrumb -->
            <div class="box">
                <div class="breadcumb">
                    <a href="index.php">Trang chủ</a>
                    <span><i class='bx bxs-chevrons-right'></i></span>
                    <a href="#">Liên lạc</a>
                </div>
            </div>

            <!-- Contact Page -->
            <div class="contact-page">
                <!-- Header Section -->
                <div class="contact-header-section">
                    <h1>Liên hệ với chúng tôi</h1>
                    <p>Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn. Hãy để lại thông tin và chúng tôi sẽ phản hồi trong thời gian sớm nhất!</p>
                </div>

                <!-- Main Content -->
                <div class="contact-container">
                    <!-- Contact Info Section -->
                    <div class="contact-info-section">
                        <!-- Address Card -->
                        <div class="contact-info-card">
                            <div class="icon">
                                <i class='bx bxs-map'></i>
                            </div>
                            <h3>Địa chỉ</h3>
                            <p>273 Đ. An Dương Vương, Phường 3, Quận 5, Thành Phố Hồ Chí Minh</p>
                        </div>

                        <!-- Phone Card -->
                        <div class="contact-info-card">
                            <div class="icon">
                                <i class='bx bxs-phone'></i>
                            </div>
                            <h3>Điện thoại</h3>
                            <p>
                                <a href="tel:+84977723622">(+84) 977 723 622</a>
                            </p>
                        </div>

                        <!-- Email Card -->
                        <div class="contact-info-card">
                            <div class="icon">
                                <i class='bx bxs-envelope'></i>
                            </div>
                            <h3>Email</h3>
                            <p>
                                <a href="mailto:ZBooks@Mail.Com">ZBooks@Mail.Com</a>
                            </p>
                        </div>

                        <!-- Working Hours Card -->
                        <div class="contact-info-card">
                            <div class="icon">
                                <i class='bx bxs-time'></i>
                            </div>
                            <h3>Giờ làm việc</h3>
                            <p>Thứ 2 - Thứ 6: 8:00 - 18:00<br>
                                Thứ 7: 8:00 - 12:00<br>
                                Chủ nhật: Nghỉ</p>
                        </div>

                        <!-- Social Links -->
                        <div class="contact-info-card">
                            <h3>Kết nối với chúng tôi</h3>
                            <div class="social-links">
                                <a href="#" class="social-link">
                                    <i class='bx bxl-facebook'></i>
                                </a>
                                <a href="#" class="social-link">
                                    <i class='bx bxl-instagram'></i>
                                </a>
                                <a href="#" class="social-link">
                                    <i class='bx bxl-youtube'></i>
                                </a>
                                <a href="#" class="social-link">
                                    <i class='bx bxl-twitter'></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form Section -->
                    <div class="contact-form-section">
                        <form class="contact-form" method="post" action="#">
                            <h2>Gửi tin nhắn cho chúng tôi</h2>
                            <p class="form-subtitle">Điền thông tin của bạn và chúng tôi sẽ liên hệ lại sớm nhất có thể.</p>

                            <div class="form-group">
                                <label for="name">Họ và tên *</label>
                                <input type="text" id="name" name="name" required placeholder="Nhập họ và tên của bạn">
                            </div>

                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" required placeholder="Nhập email của bạn">
                            </div>

                            <div class="form-group">
                                <label for="phone">Số điện thoại</label>
                                <input type="tel" id="phone" name="phone" placeholder="Nhập số điện thoại của bạn">
                            </div>

                            <div class="form-group">
                                <label for="subject">Tiêu đề *</label>
                                <input type="text" id="subject" name="subject" required placeholder="Tiêu đề tin nhắn">
                            </div>

                            <div class="form-group">
                                <label for="message">Nội dung *</label>
                                <textarea id="message" name="message" required placeholder="Nhập nội dung tin nhắn của bạn..."></textarea>
                            </div>

                            <button type="submit" class="submit-btn">Gửi tin nhắn</button>
                        </form>
                    </div>
                </div>

                <!-- Map Section -->
                <div class="map-section">
                    <h2>Vị trí của chúng tôi</h2>
                    <div class="map-container">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.6306188859313!2d106.67971897451687!3d10.759676159489263!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f1c06f4e1dd%3A0x43900f1d4539a3d!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBTxrAgcGjhuqFtIEvhu7kgdGh1bOG6rXQgVFAuSENN!5e0!3m2!1svi!2s!4v1705653600000!5m2!1svi!2s"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include("./includes/footer.php") ?>
    <script src="./assets/js/app.js"></script>
</body>

</html>