<?php
include("../admin/includes/header.php");
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
?>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <h4 class="mb-0">Danh mục</h4>
                            <div class="d-flex align-items-center gap-2 flex-nowrap">
                                <form method="GET" class="d-flex align-items-center gap-2 flex-nowrap" style="margin:0;">
                                    <div class="input-group" style="min-width: 260px; max-width: 320px; height: 38px; align-items: center;">
                                        <span class="input-group-text bg-white" style="height: 38px; align-items: center;"><i class="fa fa-search"></i></span>
                                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                                            class="form-control" placeholder="Tìm danh mục..." style="height: 38px; line-height: 38px; padding-top: 0; padding-bottom: 0;">
                                    </div>
                                    <button class="btn btn-primary" type="submit"
                                        style="height: 38px; min-width: 72px; padding: 0 16px; white-space: nowrap; display: inline-flex; align-items: center; justify-content: center;">Tìm</button>
                                    <?php if ($search !== ''): ?>
                                        <a class="btn btn-outline-secondary" href="category.php"
                                            style="height: 38px; min-width: 88px; padding: 0 14px; white-space: nowrap; display: inline-flex; align-items: center; justify-content: center;">Xóa lọc</a>
                                    <?php endif; ?>
                                </form>
                                <a class="btn btn-success" href="add-category.php" style="height: 38px; display: inline-flex; align-items: center;">
                                    <i class="fa fa-plus"></i> Thêm danh mục
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên</th>
                                    <th>Hình ảnh</th>
                                    <th>Trạng thái</th>
                                    <th>Sửa</th>
                                    <th>Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($search !== '') {
                                    $safe_search = mysqli_real_escape_string($conn, $search);
                                    $category = mysqli_query($conn, "SELECT * FROM categories WHERE name LIKE '%$safe_search%' ORDER BY id DESC");
                                } else {
                                    $category = getAll("categories");
                                }

                                if (mysqli_num_rows($category) > 0) {
                                    foreach ($category as $item) {
                                ?>
                                        <tr>
                                            <td><?= $item['id']; ?> </td>
                                            <td><?= $item['name']; ?></td>
                                            <td>
                                                <img src="../images/<?= $item['image']; ?>" width="50px" height="50px" alt="<?= $item['name']; ?>">
                                            <td>
                                                <?= $item['status'] == '0' ? "Hiển thị" : "Ẩn" ?>
                                            </td>
                                            <td>
                                                <a href="edit-category.php?id=<?= $item['id']; ?>" class="btn btn-primary">Sửa</a>
                                            </td>
                                            <td>
                                                <form action="code.php" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này không?');">
                                                    <input type="hidden" name="category_id" value="<?= $item['id']; ?>">
                                                    <button type="submit" name="delete_category_btn" class="btn btn-danger">Xóa</button>
                                                </form>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                } else {
                                    echo "Không tìm thấy danh mục phù hợp";
                                }
                                ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<?php include("../admin/includes/footer.php"); ?>