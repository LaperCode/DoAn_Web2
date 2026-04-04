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
                        <h4>Quản lý bài viết</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" style="display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 10px; flex-wrap: wrap; align-items:center;">
                            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                                class="form-control" placeholder="Tìm theo tiêu đề bài viết..." style="max-width: 320px;">
                            <button type="submit" class="btn btn-primary">Tìm</button>
                            <a class="btn btn-success" href="add-blog.php">
                                <i class="fa fa-plus"></i> Thêm bài viết
                            </a>
                        </form>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Bài viết</th>
                                    <th>Hình ảnh</th>
                                    <th>Ngày đăng</th>
                                    <th>Sửa</th>
                                    <th>Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($search !== '') {
                                    $keyword = mysqli_real_escape_string($conn, $search);
                                    $blog = mysqli_query($conn, "SELECT * FROM blog WHERE title LIKE '%$keyword%' ORDER BY id DESC");
                                } else {
                                    $blog = getAll("blog");
                                }

                                if (mysqli_num_rows($blog) > 0) {
                                    foreach ($blog as $item) {
                                ?>
                                        <tr>
                                            <td><?= $item['id']; ?> </td>
                                            <td><?= $item['title']; ?></td>
                                            <td>
                                                <img src="../images/<?= $item['img']; ?>" width="100" height="50px">
                                            <td>
                                                <?= date('d-m-Y', strtotime($item['created_at'])); ?>
                                            </td>
                                            <td>
                                                <a href="edit-blog.php?id=<?= $item['id']; ?>" class="btn btn-primary">Sửa</a>
                                            </td>
                                            <td>
                                                <form action="code.php" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này không?');">
                                                    <input type="hidden" name="blog_id" value="<?= $item['id']; ?>">
                                                    <button type="submit" name="delete_blog_btn" class="btn btn-danger">Xóa</button>
                                                </form>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                } else {
                                    echo "No records found";
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