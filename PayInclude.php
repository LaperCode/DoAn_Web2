<link rel="stylesheet" href="./assets/font/fontawesome-free-6.2.0-web/css/all.css">
<link rel="stylesheet" href="./assets/css/Pay.css">

<div class="slider">
    <div class="form-left">
        <div class="information">
            <div class="information-bill">
                <h3 class="billing">Thông tin thanh toán</h3>
                <div class="input-information">
                    <?php
                    // Get user addresses
                    $user_id = $_SESSION['auth_user']['id'];
                    $addresses_query = "SELECT * FROM user_addresses WHERE user_id='$user_id' ORDER BY is_default DESC, created_at DESC";
                    $addresses_result = mysqli_query($conn, $addresses_query);
                    $has_addresses = mysqli_num_rows($addresses_result) > 0;
                    ?>

                    <?php if ($has_addresses) { ?>
                        <!-- Chọn địa chỉ từ danh sách -->
                        <p class="saved-address" style="margin-bottom: 15px;">
                            <label>
                                <font>Chọn địa chỉ giao hàng&nbsp;</font>
                                <font>*</font>
                            </label>
                            <span>
                                <select class="form-control" id="saved_address_select" onchange="fillAddressFromSaved()">
                                    <option value="">-- Chọn địa chỉ có sẵn --</option>
                                    <?php while ($addr = mysqli_fetch_array($addresses_result)) { ?>
                                        <option value="<?= $addr['id'] ?>"
                                            <?= $addr['is_default'] ? 'selected' : '' ?>
                                            data-name="<?= htmlspecialchars($addr['recipient_name']) ?>"
                                            data-phone="<?= htmlspecialchars($addr['phone']) ?>"
                                            data-address="<?= htmlspecialchars($addr['address'] . ($addr['district'] ? ', ' . $addr['district'] : '') . ($addr['city'] ? ', ' . $addr['city'] : '')) ?>">
                                            <?= htmlspecialchars($addr['address_name']) ?>
                                            <?= $addr['is_default'] ? '(Mặc định)' : '' ?>
                                        </option>
                                    <?php } ?>
                                    <option value="manual">✏️ Nhập địa chỉ mới</option>
                                </select>
                            </span>
                            <small style="display: block; margin-top: 5px; color: #F39C12;">
                                <a href="manage-addresses.php" style="color: #F39C12; text-decoration: underline;">Quản lý địa chỉ của bạn</a>
                            </small>
                        </p>
                    <?php } else { ?>
                        <!-- Không có địa chỉ - hiển thị link thêm -->
                        <div style="background: #FFF3CD; padding: 12px; border-radius: 8px; margin-bottom: 15px; border-left: 4px solid #F39C12;">
                            <p style="margin: 0; color: #856404;">
                                <strong>💡 Mẹo:</strong> Bạn chưa có địa chỉ giao hàng nào.
                                <a href="manage-addresses.php" style="color: #F39C12; text-decoration: underline; font-weight: 600;">
                                    Thêm địa chỉ ngay
                                </a> để thanh toán nhanh hơn lần sau!
                            </p>
                        </div>
                    <?php } ?>

                    <!-- Các trường nhập thủ công -->
                    <p class="name">
                        <label>
                            <font>Họ và tên&nbsp;</font>
                            <font>*</font>
                        </label>
                        <span>
                            <input class="form-control" id="name" required type="text" name="name" value="<?= $data['name'] ?>"><br>
                        </span>
                    </p>
                    <p class="address">
                        <label>
                            <font>Địa chỉ&nbsp;</font>
                            <font>*</font>
                        </label>
                        <span>
                            <input class="form-control" id="address" required type="text" name="address" value="<?= $data['address'] ?>"><br>
                        </span>
                    </p>
                    <p class="phone-number">
                        <label>
                            <font>Số điện thoại&nbsp;</font>
                            <font>*</font>
                        </label>
                        <span>
                            <input class="form-control" id="phone" required type="text" name="phone" value="<?= $data['phone'] ?>"><br>
                        </span>
                    </p>
                    <p class="email-address">
                        <label>
                            <font>Địa chỉ Email&nbsp;</font>
                            <font>*</font>
                        </label>
                        <span>
                            <input readonly class="form-control" required type="text" name="email" value="<?= $data['email'] ?>"><br>
                        </span>
                    </p>
                </div>
            </div>
            <div class="addtional-fill">
                <h3>Thông tin bổ sung</h3>
                <div>
                    <p class="order-option">
                        <label for="">
                            Ghi chú đặt hàng
                            <span class="optional">(tùy chọn)</span>
                        </label>
                        <span style="width: 100%; height: 100%;">
                            <textarea class="input-text" id="order_comments" placeholder="Ghi chú đặt hàng, ví dụ, thời gian hoặc địa điểm giao hàng chi tiết hơn." rows="2" cols="5"></textarea>
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="form-right">
        <div class="order">
            <h3 class="your-oder">Đơn hàng của bạn</h3>
            <div class="oder-review">
                <table class="product-provisinal">
                    <thead>
                        <tr>
                            <th class="product-name">sản phẩm</th>
                            <th class="product-total">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $products = getMyOrders();
                        $total_price = 0;
                        if (mysqli_num_rows($products) == 0) {
                        ?><?php } else { ?>
                        <?php foreach ($products as $product) { ?>
                            <tr class="pro-item">
                                <td class="product-name">
                                    <?= $product['name'] ?>&nbsp;<strong class="product-quantity">×&nbsp;<?= $product['quantity'] ?></strong>
                                </td>
                                <td class="product-total">
                                    <span class="price-amount"><?= fmt_price($product['selling_price']) ?>&nbsp;<span class="price-currencySymbol">$</span></span>
                                </td>
                            </tr>
                        <?php
                                $total_price +=  $product['selling_price'] * $product['quantity'];
                            }
                        ?>
                    <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr class="cart-subtotal">
                            <th>Thuế (VAT)</th>
                            <td><span class="price-amount">0&nbsp;<span class="price-currencySymbol">$</span></span></td>
                        </tr>
                        <tr class="cart-subtotal">
                            <th>Tạm tính</th>
                            <td><span class="price-amount"><?= fmt_price($total_price) ?>&nbsp;<span class="price-currencySymbol">$</span></span></td>
                        </tr>
                        <tr class="order-total">
                            <th>Tổng</th>
                            <td><strong><span class="price-amount"><?= fmt_price($total_price) ?>&nbsp;<span class="price-currencySymbol">$</span></span></strong></td>
                        </tr>
                    </tfoot>
                </table>
                <div class="payment">
                    <ul class="payment-list">
                        <!-- Chuyển khoản ngân hàng -->
                        <li class="payment-bank">
                            <input type="radio" id="payment_method_bacs" checked name="option-payment" value="bacs" data-oder_button_text>
                            <label for="payment_method_bacs">Chuyển khoản ngân hàng</label>
                            <div class="payment-text" id="bank-info">
                                <p style="margin-bottom: 10px;">Thực hiện thanh toán vào tài khoản ngân hàng của chúng tôi ngay lập tức. Đơn hàng sẽ được giao sau khi thanh toán được xác nhận.</p>
                                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 4px solid #F39C12;">
                                    <p style="margin: 5px 0;"><strong>🏦 Ngân hàng:</strong> Vietcombank</p>
                                    <p style="margin: 5px 0;"><strong>📝 Chủ tài khoản:</strong> ZBOOKS BOOKSTORE</p>
                                    <p style="margin: 5px 0;"><strong>💳 Số tài khoản:</strong> 1234567890</p>
                                    <p style="margin: 5px 0;"><strong>📱 Chi nhánh:</strong> Hà Nội</p>
                                    <p style="margin: 10px 0 5px 0; color: #e74c3c;"><strong>✍️ Nội dung chuyển khoản:</strong></p>
                                    <p style="margin: 0; font-weight: 600; color: #2C3E50; font-size: 15px;">
                                        DH[Mã đơn hàng] - [Họ tên] - [SĐT]
                                    </p>
                                    <p style="margin: 5px 0 0 0; font-size: 13px; color: #7f8c8d;">
                                        Ví dụ: DH12345 - Nguyen Van A - 0123456789
                                    </p>
                                </div>
                            </div>
                        </li>

                        <!-- Thanh toán COD -->
                        <li class="payment-cash">
                            <input type="radio" id="payment_method_cod" value="cod" name="option-payment" data-oder_button_text>
                            <label for="payment_method_cod">COD (Thanh toán khi nhận hàng)</label>
                            <div class="payment-text">
                                <p>Thanh toán bằng tiền mặt khi nhận hàng.</p>
                            </div>
                        </li>


                    </ul>
                    <div class="btn-order">
                        <!-- <a href="../Html/Cart.html" class="btn-order-link">
                            <button class="btn-order-click">
                                Đặt hàng
                            </button>
                        </a> -->
                        <form action="./functions/ordercode.php" method="post">
                            <input type="hidden" name="buy_product" value="true">
                            <!-- <p style="display: block;">Tổng tiền: $<?= $total_price ?></p> -->
                            <button class="btn-order-click btn-buy" style="float: right;">Đặt hàng</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>