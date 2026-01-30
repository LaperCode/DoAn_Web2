<?php
include("../admin/includes/header.php");

// Lấy danh sách sản phẩm
$products = getAll("products");
?>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);">
                        <h4 style="color: white; margin: 0;">
                            <i class="material-icons" style="vertical-align: middle;">inventory</i>
                            Nhập hàng vào kho
                            <a href="import-history.php" class="btn btn-light btn-sm float-end" style="margin-left: 10px;">
                                <i class="fa fa-history"></i> Lịch sử nhập
                            </a>
                            <a href="products.php" class="btn btn-danger btn-sm float-end">
                                <i class="fa fa-arrow-left"></i> Quay lại
                            </a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Thông báo công thức -->
                        <div style="background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 100%); padding: 20px; border-radius: 8px; border-left: 4px solid #2196F3;">
                            <h5 style="color: #1565C0; margin-bottom: 12px;">
                                <i class="fa fa-calculator"></i> <strong>Công thức tính giá:</strong>
                            </h5>
                            <ul style="margin-bottom: 0; line-height: 1.8; color: #1976D2;">
                                <li><strong>Giá nhập bình quân mới</strong> = (SL tồn × Giá nhập cũ + SL nhập × Giá nhập mới) / (SL tồn + SL nhập)</li>
                                <li><strong>Giá bán mới</strong> = Giá nhập bình quân × (100% + Tỷ lệ lợi nhuận%)</li>
                            </ul>
                        </div>

                        <form id="import-stock-form" action="code.php" method="POST">
                            <input type="hidden" name="import_stock" value="true">

                            <div class="row">
                                <!-- Chọn sản phẩm -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><b>Chọn sản phẩm <span style="color: red;">*</span></b></label>
                                        <select name="product_id" id="product_id" class="form-select" required>
                                            <option value="">-- Chọn sản phẩm --</option>
                                            <?php
                                            if (mysqli_num_rows($products) > 0) {
                                                foreach ($products as $item) {
                                            ?>
                                                    <option
                                                        value="<?= $item['id'] ?>"
                                                        data-qty="<?= $item['qty'] ?>"
                                                        data-price="<?= $item['original_price'] ?>"
                                                        data-selling="<?= $item['selling_price'] ?>"
                                                        data-margin="<?= $item['profit_margin'] ?>">
                                                        <?= $item['name'] ?>
                                                    </option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Thông tin sản phẩm hiện tại -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><b>Thông tin hiện tại</b></label>
                                        <div id="current-info" style="background: linear-gradient(135deg, #FFF3E0 0%, #FFE0B2 100%); padding: 15px; border-radius: 8px; border-left: 4px solid #FF9800; color: #E65100; min-height: 120px;">
                                            <small style="color: #8B5A00;">Chọn sản phẩm để xem thông tin</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Số lượng nhập -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label"><b>Số lượng nhập <span style="color: red;">*</span></b></label>
                                        <input type="number" name="quantity_imported" id="quantity_imported"
                                            class="form-control" min="1" step="1" required
                                            placeholder="Nhập số lượng">
                                    </div>
                                </div>

                                <!-- Giá nhập -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label"><b>Giá nhập/sp <span style="color: red;">*</span></b></label>
                                        <input type="number" name="import_price" id="import_price"
                                            class="form-control" min="0" step="0.01" required
                                            placeholder="Nhập giá">
                                    </div>
                                </div>

                                <!-- Tỷ lệ lợi nhuận -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label"><b>Tỷ lệ lợi nhuận (%) <span style="color: red;">*</span></b></label>
                                        <input type="number" name="profit_margin" id="profit_margin"
                                            class="form-control" min="0" max="100" step="0.01"
                                            value="20" required
                                            placeholder="Ví dụ: 20">
                                    </div>
                                </div>
                            </div>

                            <!-- Kết quả tính toán tự động -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div id="calculation-result" style="display: none; background: linear-gradient(135deg, #E8F5E9 0%, #C8E6C9 100%); padding: 20px; border-radius: 8px; border-left: 4px solid #4CAF50;">
                                        <h5 style="color: #2E7D32; margin-bottom: 15px;">
                                            <i class="fa fa-check-circle"></i> <strong>Kết quả tính toán:</strong>
                                        </h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p style="color: #1B5E20; font-size: 16px; margin-bottom: 8px;">
                                                    <strong>Giá nhập bình quân mới:</strong>
                                                    <span id="new-avg-price" style="color: #FF6F00; font-weight: 700; font-size: 18px;">0</span> $
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <p style="color: #1B5E20; font-size: 16px; margin-bottom: 8px;">
                                                    <strong>Giá bán mới:</strong>
                                                    <span id="new-selling-price" style="color: #388E3C; font-weight: 700; font-size: 18px;">0</span> $
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Ghi chú -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label"><b>Ghi chú</b></label>
                                        <textarea name="note" class="form-control" rows="3"
                                            placeholder="Nhập ghi chú về lần nhập hàng này (tùy chọn)"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> Xác nhận nhập hàng
                                    </button>
                                    <a href="products.php" class="btn btn-secondary">
                                        <i class="fa fa-times"></i> Hủy
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // JavaScript để tính toán tự động
        document.addEventListener('DOMContentLoaded', function() {
            const productSelect = document.getElementById('product_id');
            const quantityInput = document.getElementById('quantity_imported');
            const importPriceInput = document.getElementById('import_price');
            const profitMarginInput = document.getElementById('profit_margin');
            const currentInfo = document.getElementById('current-info');
            const calculationResult = document.getElementById('calculation-result');
            const newAvgPrice = document.getElementById('new-avg-price');
            const newSellingPrice = document.getElementById('new-selling-price');

            let currentQty = 0;
            let currentPrice = 0;

            // Hiển thị thông tin sản phẩm hiện tại
            productSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (this.value) {
                    currentQty = parseFloat(selectedOption.getAttribute('data-qty'));
                    currentPrice = parseFloat(selectedOption.getAttribute('data-price'));
                    const currentSelling = parseFloat(selectedOption.getAttribute('data-selling'));
                    const currentMargin = parseFloat(selectedOption.getAttribute('data-margin'));

                    currentInfo.innerHTML = `
                        <div style="color: #E65100;">
                            <p style="margin: 5px 0;"><strong style="color: #8B5A00;">📦 Số lượng tồn:</strong> <span style="font-weight: 600; color: #F57C00;">${currentQty} sp</span></p>
                            <p style="margin: 5px 0;"><strong style="color: #8B5A00;">💰 Giá nhập hiện tại:</strong> <span style="font-weight: 600; color: #F57C00;">${currentPrice.toFixed(2)} $</span></p>
                            <p style="margin: 5px 0;"><strong style="color: #8B5A00;">💵 Giá bán hiện tại:</strong> <span style="font-weight: 600; color: #388E3C;">${currentSelling.toFixed(2)} $</span></p>
                            <p style="margin: 5px 0;"><strong style="color: #8B5A00;">📊 Tỷ lệ lợi nhuận:</strong> <span style="font-weight: 600; color: #1976D2;">${currentMargin.toFixed(2)}%</span></p>
                        </div>
                    `;

                    // Set profit margin mặc định
                    profitMarginInput.value = currentMargin;

                    calculate();
                } else {
                    currentInfo.innerHTML = '<small style="color: #8B5A00;">Chọn sản phẩm để xem thông tin</small>';
                    calculationResult.style.display = 'none';
                }
            });

            // Tính toán khi thay đổi input
            [quantityInput, importPriceInput, profitMarginInput].forEach(input => {
                input.addEventListener('input', calculate);
            });

            function calculate() {
                if (!productSelect.value || !quantityInput.value || !importPriceInput.value || !profitMarginInput.value) {
                    calculationResult.style.display = 'none';
                    return;
                }

                const qtyImported = parseFloat(quantityInput.value);
                const priceImported = parseFloat(importPriceInput.value);
                const profitMargin = parseFloat(profitMarginInput.value);

                // Công thức giá bình quân
                const totalQty = currentQty + qtyImported;
                const avgPrice = (currentQty * currentPrice + qtyImported * priceImported) / totalQty;

                // Công thức giá bán
                const sellingPrice = avgPrice * (1 + profitMargin / 100);

                newAvgPrice.textContent = avgPrice.toFixed(2);
                newSellingPrice.textContent = sellingPrice.toFixed(2);
                calculationResult.style.display = 'block';
            }
        });
    </script>
</body>

<?php include("../admin/includes/footer.php"); ?>