<?php
include("./includes/header.php");

// Check login
if (!isset($_SESSION['auth_user']['id'])) {
    redirect("login.php", "Vui lòng đăng nhập để quản lý địa chỉ");
    exit();
}

$user_id = $_SESSION['auth_user']['id'];

// Lấy danh sách địa chỉ
$addresses_query = "SELECT * FROM user_addresses WHERE user_id='$user_id' ORDER BY is_default DESC, created_at DESC";
$addresses = mysqli_query($conn, $addresses_query);
?>

<style>
    .addresses-container {
        max-width: 1200px;
        margin: 50px auto;
        padding: 0 20px;
    }

    .page-title {
        text-align: center;
        margin-bottom: 40px;
        color: #2C3E50;
    }

    .page-title h1 {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .addresses-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 30px;
    }

    @media (max-width: 768px) {
        .addresses-grid {
            grid-template-columns: 1fr;
        }
    }

    .addresses-list {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .addresses-list h2 {
        font-size: 20px;
        font-weight: 700;
        color: #2C3E50;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 3px solid #F39C12;
    }

    .address-card {
        background: #f8f9fa;
        border: 2px solid #e3e3e3;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
        position: relative;
    }

    .address-card.default {
        border-color: #F39C12;
        background: linear-gradient(135deg, #FFF8E1 0%, #FFECB3 100%);
    }

    .address-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .address-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: #F39C12;
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .address-name {
        font-size: 18px;
        font-weight: 700;
        color: #2C3E50;
        margin-bottom: 10px;
    }

    .address-info {
        font-size: 14px;
        color: #555;
        line-height: 1.6;
        margin-bottom: 15px;
    }

    .address-info p {
        margin: 5px 0;
    }

    .address-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-address {
        padding: 8px 16px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-edit {
        background: #2C3E50;
        color: white;
    }

    .btn-edit:hover {
        background: #34495E;
        transform: translateY(-2px);
    }

    .btn-default {
        background: #4CAF50;
        color: white;
    }

    .btn-default:hover {
        background: #45a049;
        transform: translateY(-2px);
    }

    .btn-delete {
        background: #e74c3c;
        color: white;
    }

    .btn-delete:hover {
        background: #c0392b;
        transform: translateY(-2px);
    }

    .address-form {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .address-form h2 {
        font-size: 20px;
        font-weight: 700;
        color: #2C3E50;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 3px solid #F39C12;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #2C3E50;
        font-size: 14px;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e3e3e3;
        border-radius: 8px;
        font-size: 15px;
        transition: all 0.3s ease;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #F39C12;
        box-shadow: 0 0 0 3px rgba(243, 156, 18, 0.1);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 80px;
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .checkbox-group input[type="checkbox"] {
        width: auto;
        margin: 0;
        cursor: pointer;
    }

    .btn-submit {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, #F39C12 0%, #E67E22 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
    }

    .btn-submit:hover {
        background: linear-gradient(135deg, #E67E22 0%, #D35400 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(243, 156, 18, 0.4);
    }

    .btn-cancel {
        width: 100%;
        padding: 14px;
        background: #95a5a6;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        margin-top: 10px;
    }

    .btn-cancel:hover {
        background: #7f8c8d;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #7f8c8d;
    }

    .empty-state i {
        font-size: 64px;
        margin-bottom: 20px;
        color: #bdc3c7;
    }
</style>

<body>
    <div class="bg-main" style="min-height: 100vh; padding: 30px 0;">
        <div class="addresses-container">
            <div class="page-title">
                <h1>Quản lý địa chỉ giao hàng</h1>
                <p style="color: #7f8c8d;">Quản lý các địa chỉ giao hàng của bạn</p>
            </div>

            <div class="addresses-grid">
                <!-- Left: Danh sách địa chỉ -->
                <div class="addresses-list">
                    <h2>Địa chỉ của tôi</h2>

                    <?php if (mysqli_num_rows($addresses) > 0) { ?>
                        <?php while ($address = mysqli_fetch_array($addresses)) { ?>
                            <div class="address-card <?= $address['is_default'] ? 'default' : '' ?>">
                                <?php if ($address['is_default']) { ?>
                                    <span class="address-badge">Mặc định</span>
                                <?php } ?>

                                <div class="address-name"><?= htmlspecialchars($address['address_name']) ?></div>
                                <div class="address-info">
                                    <p><strong>Người nhận:</strong> <?= htmlspecialchars($address['recipient_name']) ?></p>
                                    <p><strong>Điện thoại:</strong> <?= htmlspecialchars($address['phone']) ?></p>
                                    <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($address['address']) ?></p>
                                    <?php if ($address['district'] || $address['city']) { ?>
                                        <p><strong>Khu vực:</strong>
                                            <?= htmlspecialchars($address['district']) ?><?= $address['district'] && $address['city'] ? ', ' : '' ?><?= htmlspecialchars($address['city']) ?>
                                        </p>
                                    <?php } ?>
                                </div>

                                <div class="address-actions">
                                    <button class="btn-address btn-edit" onclick="editAddress(<?= $address['id'] ?>)">
                                        <i class='bx bx-edit'></i> Sửa
                                    </button>
                                    <?php if (!$address['is_default']) { ?>
                                        <form method="POST" action="functions/address-handler.php" style="display: inline;">
                                            <input type="hidden" name="action" value="set_default">
                                            <input type="hidden" name="address_id" value="<?= $address['id'] ?>">
                                            <button type="submit" class="btn-address btn-default">
                                                <i class='bx bx-check-circle'></i> Đặt làm mặc định
                                            </button>
                                        </form>
                                        <form method="POST" action="functions/address-handler.php" style="display: inline;"
                                            onsubmit="return confirm('Bạn có chắc muốn xóa địa chỉ này?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="address_id" value="<?= $address['id'] ?>">
                                            <button type="submit" class="btn-address btn-delete">
                                                <i class='bx bx-trash'></i> Xóa
                                            </button>
                                        </form>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="empty-state">
                            <i class='bx bx-map'></i>
                            <p>Bạn chưa có địa chỉ nào</p>
                            <p>Thêm địa chỉ giao hàng đầu tiên của bạn</p>
                        </div>
                    <?php } ?>
                </div>

                <!-- Right: Form thêm/sửa địa chỉ -->
                <div class="address-form">
                    <h2 id="form-title">Thêm địa chỉ mới</h2>

                    <form method="POST" action="functions/address-handler.php" id="address-form">
                        <input type="hidden" name="action" value="add" id="form-action">
                        <input type="hidden" name="address_id" value="" id="address_id">

                        <div class="form-group">
                            <label>Tên địa chỉ <span style="color: red;">*</span></label>
                            <input type="text" name="address_name" id="address_name" required
                                placeholder="Ví dụ: Nhà riêng, Văn phòng, ...">
                        </div>

                        <div class="form-group">
                            <label>Tên người nhận <span style="color: red;">*</span></label>
                            <input type="text" name="recipient_name" id="recipient_name" required
                                placeholder="Nhập tên người nhận">
                        </div>

                        <div class="form-group">
                            <label>Số điện thoại <span style="color: red;">*</span></label>
                            <input type="tel" name="phone" id="phone" required
                                placeholder="Nhập số điện thoại">
                        </div>

                        <div class="form-group">
                            <label>Địa chỉ chi tiết <span style="color: red;">*</span></label>
                            <textarea name="address" id="address" required
                                placeholder="Số nhà, tên đường, ..."></textarea>
                        </div>

                        <div class="form-group">
                            <label>Quận/Huyện</label>
                            <input type="text" name="district" id="district"
                                placeholder="Nhập quận/huyện">
                        </div>

                        <div class="form-group">
                            <label>Tỉnh/Thành phố</label>
                            <input type="text" name="city" id="city"
                                placeholder="Nhập tỉnh/thành phố">
                        </div>

                        <div class="form-group">
                            <div class="checkbox-group">
                                <input type="checkbox" name="is_default" id="is_default" value="1">
                                <label for="is_default" style="margin: 0;">Đặt làm địa chỉ mặc định</label>
                            </div>
                        </div>

                        <button type="submit" class="btn-submit">
                            <i class='bx bx-save'></i> Lưu địa chỉ
                        </button>

                        <button type="button" class="btn-cancel" id="btn-cancel" style="display: none;" onclick="cancelEdit()">
                            Hủy chỉnh sửa
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include("./includes/footer.php") ?>

    <script>
        // Data for edit
        const addressData = <?= json_encode(mysqli_fetch_all($addresses, MYSQLI_ASSOC)) ?>;

        function editAddress(addressId) {
            const address = addressData.find(a => a.id == addressId);
            if (!address) return;

            // Update form
            document.getElementById('form-title').textContent = 'Chỉnh sửa địa chỉ';
            document.getElementById('form-action').value = 'edit';
            document.getElementById('address_id').value = address.id;
            document.getElementById('address_name').value = address.address_name;
            document.getElementById('recipient_name').value = address.recipient_name;
            document.getElementById('phone').value = address.phone;
            document.getElementById('address').value = address.address;
            document.getElementById('district').value = address.district || '';
            document.getElementById('city').value = address.city || '';
            document.getElementById('is_default').checked = address.is_default == 1;
            document.getElementById('btn-cancel').style.display = 'block';

            // Scroll to form
            document.querySelector('.address-form').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }

        function cancelEdit() {
            document.getElementById('form-title').textContent = 'Thêm địa chỉ mới';
            document.getElementById('form-action').value = 'add';
            document.getElementById('address_id').value = '';
            document.getElementById('address-form').reset();
            document.getElementById('btn-cancel').style.display = 'none';
        }
    </script>
</body>

</html>