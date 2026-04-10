<?php
include("../admin/includes/header.php");

// Lấy danh sách sản phẩm
$products = getAll("products");
$selected_product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$productData = [];
if (mysqli_num_rows($products) > 0) {
    foreach ($products as $item) {
        $productData[] = [
            'id' => (int)$item['id'],
            'name' => $item['name'],
            'qty' => (float)$item['qty'],
            'price' => (float)$item['original_price'],
            'selling' => (float)$item['selling_price'],
            'margin' => (float)$item['profit_margin']
        ];
    }
}
?>

<body>
    <style>
        .btn-remove-item {
            background: #fff;
            color: #e53935;
            border: 1.5px solid #ef5350;
            padding: 6px 14px;
            border-radius: 999px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all .2s ease;
        }

        .btn-remove-item:hover {
            background: #ffebee;
            border-color: #e53935;
            color: #c62828;
        }

        .btn-remove-item:focus {
            box-shadow: 0 0 0 0.2rem rgba(229, 57, 53, 0.2);
        }
    </style>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);">
                        <h4 style="color: white; margin: 0;">
                            <i class="material-icons" style="vertical-align: middle;">inventory</i>
                            Thêm phiếu nhập
                            <a href="import-manage.php" class="btn btn-light btn-sm float-end" style="margin-left: 10px;">
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

                        <form id="import-receipt-form" action="code.php" method="POST">
                            <input type="hidden" name="import_receipt" value="true">

                            <div id="items-container">
                                <div class="import-item mb-4" data-index="0" style="border: 1px solid #FFE0B2; border-radius: 10px; padding: 16px; background: #fff;">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label"><b>Chọn sản phẩm <span style="color: red;">*</span></b></label>
                                            <div class="product-search-wrap" style="position:relative;">
                                                <input type="text" class="form-control product-input" placeholder="Gõ để tìm sản phẩm..." required autocomplete="off">
                                                <input type="hidden" name="product_id[]" class="product-id-input" value="">
                                                <div class="product-dropdown" style="position:absolute;left:0;right:0;top:calc(100% + 6px);background:#fff;border:1px solid #E2E8F0;border-radius:10px;box-shadow:0 10px 20px rgba(0,0,0,0.08);max-height:220px;overflow:auto;z-index:20;display:none;"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label"><b>Số lượng <span style="color: red;">*</span></b></label>
                                            <input type="number" name="quantity_imported[]" class="form-control quantity-input" min="1" step="1" required placeholder="SL">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label"><b>Giá nhập/sp <span style="color: red;">*</span></b></label>
                                            <input type="number" name="import_price[]" class="form-control import-price-input" min="0" step="0.01" required placeholder="Giá">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label"><b>Tỷ lệ LN (%)</b></label>
                                            <input type="hidden" name="profit_margin[]" class="margin-input" value="20">
                                            <div class="form-control bg-light margin-display" style="height: 38px; display: flex; align-items: center;">20%</div>
                                        </div>
                                        <div class="col-md-2 text-end">
                                            <button type="button" class="btn btn-remove-item remove-item" style="display: none;">
                                                <i class="fa fa-trash"></i> Xóa
                                            </button>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <label class="form-label"><b>Thông tin hiện tại</b></label>
                                            <div class="current-info" style="background: linear-gradient(135deg, #FFF3E0 0%, #FFE0B2 100%); padding: 15px; border-radius: 8px; border-left: 4px solid #FF9800; color: #E65100; min-height: 110px;">
                                                <small style="color: #8B5A00;">Chọn sản phẩm để xem thông tin</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label"><b>Kết quả tính toán</b></label>
                                            <div class="calc-result" style="display: none; background: linear-gradient(135deg, #E8F5E9 0%, #C8E6C9 100%); padding: 15px; border-radius: 8px; border-left: 4px solid #4CAF50;">
                                                <p style="color: #1B5E20; font-size: 15px; margin-bottom: 6px;">
                                                    <strong>Giá nhập bình quân mới:</strong>
                                                    <span class="new-avg-price" style="color: #FF6F00; font-weight: 700; font-size: 16px;">0</span> $
                                                </p>
                                                <p style="color: #1B5E20; font-size: 15px; margin-bottom: 0;">
                                                    <strong>Giá bán mới:</strong>
                                                    <span class="new-selling-price" style="color: #388E3C; font-weight: 700; font-size: 16px;">0</span> $
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-outline-warning" id="add-item">
                                        <i class="fa fa-plus"></i> Thêm sản phẩm
                                    </button>
                                </div>
                            </div>

                            <!-- Ngày nhập & Ghi chú -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label"><b>Ngày nhập <span style="color: red;">*</span></b></label>
                                        <input type="date" name="import_date" class="form-control" required
                                            value="<?= date('Y-m-d') ?>">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label"><b>Ghi chú</b></label>
                                        <textarea name="note" class="form-control" rows="3"
                                            placeholder="Nhập ghi chú cho phiếu nhập (tùy chọn)"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> Lưu phiếu nhập
                                    </button>
                                    <a href="import-manage.php" class="btn btn-secondary">
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
        const productData = <?= json_encode($productData) ?>;
        const preselectedProductId = <?= (int)$selected_product_id ?>;

        // JavaScript để tính toán tự động
        document.addEventListener('DOMContentLoaded', function() {
            const itemsContainer = document.getElementById('items-container');
            const addItemBtn = document.getElementById('add-item');

            function bindRow(row) {
                const productInput = row.querySelector('.product-input');
                const productIdInput = row.querySelector('.product-id-input');
                const dropdown = row.querySelector('.product-dropdown');
                const quantityInput = row.querySelector('.quantity-input');
                const importPriceInput = row.querySelector('.import-price-input');
                const profitMarginInput = row.querySelector('.margin-input');
                const currentInfo = row.querySelector('.current-info');
                const calculationResult = row.querySelector('.calc-result');
                const newAvgPrice = row.querySelector('.new-avg-price');
                const newSellingPrice = row.querySelector('.new-selling-price');
                const marginDisplay = row.querySelector('.margin-display');
                const removeBtn = row.querySelector('.remove-item');

                let currentQty = 0;
                let currentPrice = 0;
                let currentSelling = 0;

                function updateProductInfo(product) {
                    if (!product) {
                        productIdInput.value = '';
                        currentInfo.innerHTML = '<small style="color: #8B5A00;">Chọn sản phẩm để xem thông tin</small>';
                        calculationResult.style.display = 'none';
                        return;
                    }

                    productIdInput.value = product.id;
                    currentQty = parseFloat(product.qty);
                    currentPrice = parseFloat(product.price);
                    currentSelling = parseFloat(product.selling);
                    const currentMargin = parseFloat(product.margin);

                    currentInfo.innerHTML = `
                        <div style="color: #E65100;">
                            <p style="margin: 5px 0;"><strong style="color: #8B5A00;">📦 Số lượng tồn:</strong> <span style="font-weight: 600; color: #F57C00;">${currentQty} sp</span></p>
                            <p style="margin: 5px 0;"><strong style="color: #8B5A00;">💰 Giá nhập hiện tại:</strong> <span style="font-weight: 600; color: #F57C00;">${currentPrice.toFixed(2)} $</span></p>
                            <p style="margin: 5px 0;"><strong style="color: #8B5A00;">💵 Giá bán hiện tại:</strong> <span style="font-weight: 600; color: #388E3C;">${currentSelling.toFixed(2)} $</span></p>
                            <p style="margin: 5px 0;"><strong style="color: #8B5A00;">📊 Tỷ lệ lợi nhuận:</strong> <span style="font-weight: 600; color: #1976D2;">${currentMargin.toFixed(2)}%</span></p>
                        </div>
                    `;
                    profitMarginInput.value = currentMargin;
                    if (marginDisplay) {
                        marginDisplay.textContent = `${currentMargin.toFixed(2)}%`;
                    }
                    calculate();
                }

                function normalizeText(value) {
                    return value
                        .toLowerCase()
                        .normalize('NFD')
                        .replace(/[\u0300-\u036f]/g, '')
                        .replace(/đ/g, 'd')
                        .replace(/[^a-z0-9\s]/g, ' ')
                        .replace(/\s+/g, ' ')
                        .trim();
                }

                function hasDiacritics(value) {
                    return normalizeText(value) !== value.toLowerCase();
                }

                function hasDiacritics(value) {
                    return normalizeText(value) !== value.toLowerCase();
                }

                let activeIndex = -1;

                function renderDropdown(matches) {
                    if (!dropdown) {
                        return;
                    }
                    if (!matches.length) {
                        dropdown.style.display = 'none';
                        dropdown.innerHTML = '';
                        activeIndex = -1;
                        return;
                    }
                    if (activeIndex >= matches.length) {
                        activeIndex = matches.length - 1;
                    }
                    dropdown.innerHTML = matches.map((item, index) => `
                        <div class="dropdown-item" data-id="${item.id}" data-index="${index}" style="padding:8px 12px;cursor:pointer;border-bottom:1px solid #F1F5F9;font-size:14px;background:${index === activeIndex ? '#EEF2FF' : 'transparent'};font-weight:${index === activeIndex ? '600' : '400'};">
                            ${item.name}
                        </div>
                    `).join('');
                    dropdown.style.display = 'block';
                }

                function getMatches(keywordRaw) {
                    const keyword = keywordRaw.trim();
                    if (!keyword) {
                        return productData; // Hiển thị tất cả khi chưa gõ
                    }
                    if (hasDiacritics(keyword)) {
                        const lower = keyword.toLowerCase();
                        return productData.filter(p => p.name.toLowerCase().includes(lower)).slice(0, 12);
                    }
                    const normalized = normalizeText(keyword);
                    return productData.filter(p => normalizeText(p.name).includes(normalized)).slice(0, 12);
                }

                function handleInput() {
                    const keywordRaw = productInput.value.trim();
                    const matches = getMatches(keywordRaw);
                    activeIndex = matches.length ? 0 : -1;
                    renderDropdown(matches);

                    let exact = null;
                    if (keywordRaw) {
                        exact = hasDiacritics(keywordRaw) ?
                            productData.find(p => p.name.toLowerCase() === keywordRaw.toLowerCase()) :
                            productData.find(p => normalizeText(p.name) === normalizeText(keywordRaw));
                    }
                    updateProductInfo(exact);
                }

                function chooseByIndex(index) {
                    const keywordRaw = productInput.value.trim();
                    const matches = getMatches(keywordRaw);
                    if (!matches.length || index < 0 || index >= matches.length) {
                        return;
                    }
                    const selected = matches[index];
                    productInput.value = selected.name;
                    updateProductInfo(selected);
                    dropdown.style.display = 'none';
                }

                if (productInput) {
                    productInput.addEventListener('input', handleInput);
                    productInput.addEventListener('focus', handleInput);
                    productInput.addEventListener('keydown', function(e) {
                        const keywordRaw = productInput.value.trim();
                        const matches = getMatches(keywordRaw);
                        if (!matches.length) {
                            return;
                        }

                        if (e.key === 'ArrowDown') {
                            e.preventDefault();
                            activeIndex = (activeIndex + 1) % matches.length;
                            renderDropdown(matches);
                        } else if (e.key === 'ArrowUp') {
                            e.preventDefault();
                            activeIndex = (activeIndex - 1 + matches.length) % matches.length;
                            renderDropdown(matches);
                        } else if (e.key === 'Enter') {
                            e.preventDefault();
                            chooseByIndex(activeIndex >= 0 ? activeIndex : 0);
                        }
                    });
                }

                if (dropdown) {
                    dropdown.addEventListener('mousedown', function(e) {
                        const item = e.target.closest('.dropdown-item');
                        if (!item) return;
                        const selected = productData.find(p => p.id === parseInt(item.dataset.id, 10));
                        if (selected) {
                            productInput.value = selected.name;
                            updateProductInfo(selected);
                            dropdown.style.display = 'none';
                        }
                    });
                }

                document.addEventListener('click', function(e) {
                    if (!row.contains(e.target) && dropdown) {
                        dropdown.style.display = 'none';
                    }
                });

                if (preselectedProductId && !productIdInput.value) {
                    const matched = productData.find(p => p.id === preselectedProductId);
                    if (matched) {
                        productInput.value = matched.name;
                        updateProductInfo(matched);
                    }
                }

                [quantityInput, importPriceInput, profitMarginInput].forEach(input => {
                    input.addEventListener('input', calculate);
                });

                function calculate() {
                    if (!productIdInput.value || !quantityInput.value || !importPriceInput.value || !profitMarginInput.value) {
                        calculationResult.style.display = 'none';
                        return;
                    }
                    const qtyImported = parseFloat(quantityInput.value);
                    const priceImported = parseFloat(importPriceInput.value);
                    const profitMargin = parseFloat(profitMarginInput.value || 20);
                    const totalQty = currentQty + qtyImported;
                    const avgPrice = (currentQty * currentPrice + qtyImported * priceImported) / totalQty;
                    const sellingPrice = avgPrice * (1 + profitMargin / 100);
                    newAvgPrice.textContent = avgPrice.toFixed(2);
                    newSellingPrice.textContent = sellingPrice.toFixed(2);
                    calculationResult.style.display = 'block';
                }

                if (removeBtn) {
                    removeBtn.onclick = function() {
                        row.remove();
                        refreshRemoveButtons();
                    };
                }
            }

            function refreshRemoveButtons() {
                const rows = itemsContainer.querySelectorAll('.import-item');
                rows.forEach((row, index) => {
                    const removeBtn = row.querySelector('.remove-item');
                    if (removeBtn) {
                        removeBtn.style.display = rows.length > 1 ? 'inline-block' : 'none';
                    }
                    row.setAttribute('data-index', index);
                });
            }

            function addRow() {
                const row = document.createElement('div');
                const listId = `product-list-${Date.now()}-${Math.floor(Math.random() * 10000)}`;
                row.className = 'import-item mb-4';
                row.style.border = '1px solid #FFE0B2';
                row.style.borderRadius = '10px';
                row.style.padding = '16px';
                row.style.background = '#fff';
                row.innerHTML = `
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label"><b>Chọn sản phẩm <span style=\"color: red;\">*</span></b></label>
                            <div class="product-search-wrap" style="position:relative;">
                                <input type="text" class="form-control product-input" placeholder="Gõ để tìm sản phẩm..." required autocomplete="off">
                                <input type="hidden" name="product_id[]" class="product-id-input" value="">
                                <div class="product-dropdown" style="position:absolute;left:0;right:0;top:calc(100% + 6px);background:#fff;border:1px solid #E2E8F0;border-radius:10px;box-shadow:0 10px 20px rgba(0,0,0,0.08);max-height:220px;overflow:auto;z-index:20;display:none;"></div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label"><b>Số lượng <span style=\"color: red;\">*</span></b></label>
                            <input type="number" name="quantity_imported[]" class="form-control quantity-input" min="1" step="1" required placeholder="SL">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label"><b>Giá nhập/sp <span style=\"color: red;\">*</span></b></label>
                            <input type="number" name="import_price[]" class="form-control import-price-input" min="0" step="0.01" required placeholder="Giá">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label"><b>Tỷ lệ LN (%)</b></label>
                            <input type="hidden" name="profit_margin[]" class="margin-input" value="20">
                            <div class="form-control bg-light margin-display" style="height: 38px; display: flex; align-items: center;">20%</div>
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="button" class="btn btn-remove-item remove-item">
                                <i class="fa fa-trash"></i> Xóa
                            </button>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label"><b>Thông tin hiện tại</b></label>
                            <div class="current-info" style="background: linear-gradient(135deg, #FFF3E0 0%, #FFE0B2 100%); padding: 15px; border-radius: 8px; border-left: 4px solid #FF9800; color: #E65100; min-height: 110px;">
                                <small style="color: #8B5A00;">Chọn sản phẩm để xem thông tin</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><b>Kết quả tính toán</b></label>
                            <div class="calc-result" style="display: none; background: linear-gradient(135deg, #E8F5E9 0%, #C8E6C9 100%); padding: 15px; border-radius: 8px; border-left: 4px solid #4CAF50;">
                                <p style="color: #1B5E20; font-size: 15px; margin-bottom: 6px;">
                                    <strong>Giá nhập bình quân mới:</strong>
                                    <span class="new-avg-price" style="color: #FF6F00; font-weight: 700; font-size: 16px;">0</span> $
                                </p>
                                <p style="color: #1B5E20; font-size: 15px; margin-bottom: 0;">
                                    <strong>Giá bán mới:</strong>
                                    <span class="new-selling-price" style="color: #388E3C; font-weight: 700; font-size: 16px;">0</span> $
                                </p>
                            </div>
                        </div>
                    </div>
                `;

                itemsContainer.appendChild(row);
                bindRow(row);
                refreshRemoveButtons();
            }

            bindRow(itemsContainer.querySelector('.import-item'));
            refreshRemoveButtons();

            addItemBtn.addEventListener('click', addRow);
        });
    </script>
</body>

<?php include("../admin/includes/footer.php"); ?>