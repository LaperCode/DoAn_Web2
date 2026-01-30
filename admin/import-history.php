<?php
include("../admin/includes/header.php");

// Lấy lịch sử nhập hàng
$import_history_query = "SELECT 
    ih.id,
    ih.product_id,
    p.name as product_name,
    ih.quantity_imported,
    ih.import_price,
    ih.old_quantity,
    ih.old_original_price,
    ih.new_average_price,
    ih.new_selling_price,
    ih.profit_margin,
    u.name as admin_name,
    ih.note,
    ih.created_at
FROM import_history ih
INNER JOIN products p ON ih.product_id = p.id
INNER JOIN users u ON ih.admin_id = u.id
ORDER BY ih.created_at DESC";

$import_history = mysqli_query($conn, $import_history_query);
?>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);">
                        <h4 style="color: white; margin: 0;">
                            <i class="material-icons" style="vertical-align: middle;">history</i>
                            Lịch sử nhập hàng
                            <a href="import-stock.php" class="btn btn-danger btn-sm float-end">
                                <i class="fa fa-arrow-left"></i> Quay lại
                            </a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($import_history) > 0) { ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover table-striped">
                                    <thead style="background: linear-gradient(135deg, #FFB74D 0%, #FFA726 100%); color: white;">
                                        <tr>
                                            <th style="width: 50px;">ID</th>
                                            <th>Sản phẩm</th>
                                            <th style="width: 100px;">SL nhập</th>
                                            <th style="width: 120px;">Giá nhập/sp</th>
                                            <th style="width: 100px;">SL tồn cũ</th>
                                            <th style="width: 120px;">Giá nhập cũ</th>
                                            <th style="width: 140px;">Giá BQ mới</th>
                                            <th style="width: 130px;">Giá bán mới</th>
                                            <th style="width: 90px;">% LN</th>
                                            <th style="width: 130px;">Admin</th>
                                            <th style="width: 150px;">Thời gian</th>
                                            <th style="width: 100px;">Chi tiết</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($import_history as $item) {
                                            // Tính tổng số lượng sau khi nhập
                                            $total_qty = $item['old_quantity'] + $item['quantity_imported'];
                                        ?>
                                            <tr>
                                                <td class="text-center"><?= $item['id'] ?></td>
                                                <td>
                                                    <a href="edit-product.php?id=<?= $item['product_id'] ?>"
                                                        style="color: #F57C00; font-weight: 500;">
                                                        <?= $item['product_name'] ?>
                                                    </a>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary" style="font-size: 14px;">
                                                        <?= $item['quantity_imported'] ?>
                                                    </span>
                                                </td>
                                                <td class="text-end"><?= number_format($item['import_price'], 2) ?> $</td>
                                                <td class="text-center"><?= $item['old_quantity'] ?></td>
                                                <td class="text-end"><?= number_format($item['old_original_price'], 2) ?> $</td>
                                                <td class="text-end">
                                                    <strong style="color: #FF9800;">
                                                        <?= number_format($item['new_average_price'], 2) ?> $
                                                    </strong>
                                                </td>
                                                <td class="text-end">
                                                    <strong style="color: #4CAF50;">
                                                        <?= number_format($item['new_selling_price'], 2) ?> $
                                                    </strong>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-success">
                                                        <?= number_format($item['profit_margin'], 2) ?>%
                                                    </span>
                                                </td>
                                                <td><?= $item['admin_name'] ?></td>
                                                <td class="text-center">
                                                    <small><?= date('d/m/Y H:i', strtotime($item['created_at'])) ?></small>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button"
                                                        class="btn btn-sm btn-info"
                                                        onclick="showDetail(<?= htmlspecialchars(json_encode($item)) ?>)">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Thống kê tổng quan -->
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div style="background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 100%); padding: 20px; border-radius: 8px; border-left: 4px solid #2196F3;">
                                        <h5 style="color: #1565C0; margin-bottom: 12px;">
                                            <i class="fa fa-chart-bar"></i> <strong>Thống kê:</strong>
                                        </h5>
                                        <p style="margin-bottom: 0; color: #1976D2; font-size: 16px;">
                                            <strong>Tổng số lần nhập:</strong> <span style="font-weight: 700; color: #0D47A1;"><?= mysqli_num_rows($import_history) ?> lần</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-warning text-center">
                                <h5><i class="fa fa-exclamation-triangle"></i> Chưa có lịch sử nhập hàng</h5>
                                <p>Hãy <a href="import-stock.php" style="color: #FF9800; font-weight: bold;">nhập hàng mới</a> để bắt đầu.</p>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal chi tiết -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%); color: white;">
                    <h5 class="modal-title">
                        <i class="fa fa-info-circle"></i> Chi tiết lần nhập hàng
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailContent">
                    <!-- Content will be filled by JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showDetail(item) {
            const totalQty = parseInt(item.old_quantity) + parseInt(item.quantity_imported);

            const content = `
                <div class="row">
                    <div class="col-md-6">
                        <h6><strong>Thông tin sản phẩm:</strong></h6>
                        <p><strong>Tên sản phẩm:</strong> ${item.product_name}</p>
                        <p><strong>ID sản phẩm:</strong> #${item.product_id}</p>
                    </div>
                    <div class="col-md-6">
                        <h6><strong>Thông tin nhập hàng:</strong></h6>
                        <p><strong>Số lượng nhập:</strong> ${item.quantity_imported} sp</p>
                        <p><strong>Giá nhập/sp:</strong> ${parseFloat(item.import_price).toFixed(2)} $</p>
                        <p><strong>Tỷ lệ lợi nhuận:</strong> ${parseFloat(item.profit_margin).toFixed(2)}%</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h6><strong>Trước khi nhập:</strong></h6>
                        <p><strong>Số lượng tồn:</strong> ${item.old_quantity} sp</p>
                        <p><strong>Giá nhập cũ:</strong> ${parseFloat(item.old_original_price).toFixed(2)} $</p>
                    </div>
                    <div class="col-md-6">
                        <h6><strong>Sau khi nhập:</strong></h6>
                        <p><strong>Số lượng mới:</strong> ${totalQty} sp</p>
                        <p><strong>Giá nhập bình quân:</strong> <span style="color: #FF9800; font-weight: bold;">${parseFloat(item.new_average_price).toFixed(2)} $</span></p>
                        <p><strong>Giá bán mới:</strong> <span style="color: #4CAF50; font-weight: bold;">${parseFloat(item.new_selling_price).toFixed(2)} $</span></p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <h6><strong>Công thức tính toán:</strong></h6>
                        <div style="background: linear-gradient(135deg, #FFF3E0 0%, #FFE0B2 100%); padding: 15px; border-radius: 8px; border-left: 4px solid #FF9800; color: #E65100;">
                            <p style="margin: 8px 0;"><strong style="color: #8B5A00;">📐 Giá bình quân:</strong> (${item.old_quantity} × ${parseFloat(item.old_original_price).toFixed(2)} + ${item.quantity_imported} × ${parseFloat(item.import_price).toFixed(2)}) / ${totalQty} = <strong style="color: #F57C00;">${parseFloat(item.new_average_price).toFixed(2)} $</strong></p>
                            <p style="margin: 8px 0 0 0;"><strong style="color: #8B5A00;">💵 Giá bán:</strong> ${parseFloat(item.new_average_price).toFixed(2)} × (1 + ${parseFloat(item.profit_margin).toFixed(2)}%) = <strong style="color: #388E3C;">${parseFloat(item.new_selling_price).toFixed(2)} $</strong></p>
                        </div>
                    </div>
                </div>
                ${item.note ? `
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <h6><strong>Ghi chú:</strong></h6>
                        <p>${item.note}</p>
                    </div>
                </div>
                ` : ''}
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <p><strong>Admin nhập:</strong> ${item.admin_name}</p>
                        <p><strong>Thời gian:</strong> ${new Date(item.created_at).toLocaleString('vi-VN')}</p>
                    </div>
                </div>
            `;

            document.getElementById('detailContent').innerHTML = content;
            const modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();
        }
    </script>
</body>

<?php include("../admin/includes/footer.php"); ?>