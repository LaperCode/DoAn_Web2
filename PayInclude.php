<link rel="stylesheet" href="./assets/font/fontawesome-free-6.2.0-web/css/all.css">
<link rel="stylesheet" href="./assets/css/Pay.css">

<div class="slider">
    <div class="form-left">
        <div class="information">
            <div class="information-bill">
                <h3 class="billing">Thông tin thanh toán</h3>
                <div class="input-information">
                    <p class="name">
                        <label>
                            <font>Họ và tên&nbsp;</font>
                            <font>*</font>
                        </label>
                        <span>
                            <input class="form-control" id="name" required type="text" name="name" value="<?= $data['name']?>" ><br>
                        </span>
                    </p>
                    <p class="address">
                        <label>
                            <font>Địa chỉ&nbsp;</font>
                            <font>*</font>
                        </label>
                        <span>
                            <input class="form-control" id="address" required type="text" name="address" value="<?= $data['address']?>" ><br>
                        </span>
                    </p>
                    <p class="phone-number">
                        <label>
                            <font>Số điện thoại&nbsp;</font>
                            <font>*</font>
                        </label>
                        <span>
                            <input class="form-control" id="phone" required type="text" name="phone" value="<?= $data['phone']?>"><br>
                        </span>
                    </p>
                    <p class="email-address">
                        <label>
                            <font>Địa chỉ Email&nbsp;</font>
                            <font>*</font>
                        </label>
                        <span>
                            <input readonly class="form-control" required type="text" name="email" value="<?= $data['email']?>" ><br>
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
                        <tr >
                            <th class="product-name">sản phẩm</th>
                            <th class="product-total">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $products = getMyOrders();
                        $total_price = 0;
                        if (mysqli_num_rows($products) == 0){
                    ?><?php } else { ?>
                        <?php foreach ($products as $product){ ?>
                            <tr class="pro-item">
                                <td class="product-name">
                                    <?= $product['name']?>&nbsp;<strong class="product-quantity">×&nbsp;<?= $product['quantity']?></strong>
                                </td>
                                <td class="product-total">
                                    <span class="price-amount"><?= $product['selling_price']?>&nbsp;<span class="price-currencySymbol">$</span></span>
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
                            <td><span class="price-amount"><?=$total_price?>&nbsp;<span class="price-currencySymbol">$</span></span></td>
                        </tr>
                        <tr class="order-total">
                            <th>Tổng</th>
                            <td><strong><span class="price-amount"><?=$total_price?>&nbsp;<span class="price-currencySymbol">$</span></span></strong></td>
                        </tr>
                    </tfoot>
                </table>
                <div class="payment">
                    <ul class="payment-list">
                        <li class="payment-bank">
                            <input type="radio" id="payment_method_bacs" checked name="option-payment" value="bacs" data-oder_button_text>
                            <label for="payment_method_bacs">Chuyển khoản ngân hàng</label>
                            <div class="payment-text">
                                <p>Thực hiện thanh toán vào tài khoản ngân hàng của chúng tôi ngay lập tức. Đơn hàng sẽ được giao sau khi thanh toán được thực hiện.
                                </p>
                            </div>
                        </li>
                        <li class="payment-cash" >
                            <input type="radio" id="payment_method_cod" value="cod" name="option-payment" data-oder_button_text>
                            <label for="payment_method_cod">Cod</label>
                            <div class="payment-text">
                                <p >Thanh toán khi giao hàng</p>
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
                            <!-- <p style="display: block;">Tổng tiền: $<?=$total_price?></p> -->
                            <button class="btn-order-click btn-buy" style="float: right;">Đặt hàng</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

