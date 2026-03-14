<?php
include("../admin/includes/header.php");
?>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Thêm sản phẩm
                            <a href="products.php" class="btn btn-danger btn-sm float-end">
                                <i class="fa fa-arrow-left"></i> Quay lại
                            </a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form id="productForm" action="code.php" method="POST" enctype="multipart/form-data"><!-- Uploads image -->
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="mb-0"><b>Chọn loại sản phẩm</b></label>
                                    <select name="category_id" class="form-select mb-2">
                                        <option selected>Chọn...</option>
                                        <?php
                                        $categories = getAll("categories");
                                        if (mysqli_num_rows($categories) > 0) {
                                            foreach ($categories as $item) {
                                        ?>
                                                <option value="<?= $item['id']; ?>"> <?= $item['name'] ?></option>
                                        <?php
                                            }
                                        } else {
                                            echo "No Category available";
                                        }

                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <br>
                                    <label class="mb-0"><b>Tên</b></label>
                                    <input type="text" id="full-name" required name="name" placeholder="Nhập tên sản phẩm" class="form-control mb-2 ">
                                </div>
                                <div class="col-md-6">
                                    <br>
                                    <label class="mb-0"><b>Slug</b></label>
                                    <input type="text" id="slug-name" required name="slug" placeholder="Nhập slug" class="form-control mb-2">
                                </div>
                                <div class="col-md-12">
                                    <br>
                                    <label class="mb-0"><b>Tóm tắt</b></label>
                                    <textarea type="text" required name="small_description" placeholder="Nội dung cụ thể" class="form-control mb-2"></textarea>
                                </div>
                                <div class="col-md-12">
                                    <br>
                                    <label class="mb-0"><b>Mô tả</b></label>
                                    <textarea type="text" required name="description" placeholder="Nhập mô tả" class="form-control mb-2"></textarea>
                                </div>
                                <div class="col-md-12">
                                    <br>
                                    <label class="mb-0"><b>Hình ảnh</b></label>
                                    <input type="file" name="image" class="form-control mb-2" id="imageInput">
                                    <img id="imagePreview" style="max-width: 30%; margin-top: 10px; display: none;">
                                </div>
                                <div class="col-md-12">
                                    <br>
                                    <button type="submit" class="btn btn-primary" name="add_product_btn">Thêm</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
</body>
<script type="text/javascript" src="./assets/js/StringConvertToSlug.js"></script>
<script>
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');

    imageInput.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (file) {
            imagePreview.src = URL.createObjectURL(file);
            imagePreview.style.display = 'block';
        } else {
            imagePreview.style.display = 'none';
        }
    });
</script>

<?php include("../admin/includes/footer.php"); ?>