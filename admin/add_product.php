<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            z-index: 1000;
        }
        .sidebar .nav-link {
            color: #bdc3c7;
            padding: 15px 25px;
            border-radius: 0;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(52, 152, 219, 0.1);
            color: #3498db;
            border-left-color: #3498db;
        }
        .main-content {
            margin-left: 250px;
            padding: 0;
        }
        .top-navbar {
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 1rem 2rem;
            margin-bottom: 2rem;
        }
        .content-wrapper {
            padding: 0 2rem 2rem;
        }
        .card {
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border-radius: 15px;
        }
        .page-title {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .breadcrumb {
            background: none;
            padding: 0;
        }
        .form-control, .form-select {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        .btn-secondary {
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
        }
        .image-upload-area {
            border: 3px dashed #dee2e6;
            border-radius: 15px;
            padding: 60px 20px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            background: #f8f9fa;
        }
        .image-upload-area:hover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.05);
        }
        .image-upload-area.dragover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.1);
        }
        .image-preview {
            max-width: 200px;
            max-height: 200px;
            border-radius: 10px;
            margin: 10px;
            border: 2px solid #dee2e6;
        }
        .image-container {
            position: relative;
            display: inline-block;
        }
        .remove-image {
            position: absolute;
            top: -10px;
            right: -10px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
        }
        .price-input-group {
            position: relative;
        }
        .price-input-group .input-group-text {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 10px 0 0 10px;
            font-weight: 600;
        }
        .form-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .section-title {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }
    </style>
</head>
<body class="bg-light">
  
    <!-- Sidebar -->
    <?php include('sidemenu.php'); ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title mb-0">Add New Product</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="admin_dashboard.php" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="products.php" class="text-decoration-none">Products</a></li>
                        <li class="breadcrumb-item active">Add Product</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn btn-link text-dark dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle fa-lg me-2"></i>Admin
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="account.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="login.php"><i class="fas fa-sign-out-alt me-2"></i>Sign out</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="content-wrapper">
            <form id="addProductForm" action="add_product.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Basic Information -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-info-circle me-2"></i>Basic Information
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="productName" class="form-label fw-bold">Product Name *</label>
                                    <input type="text" class="form-control" id="productName" name="product_name" placeholder="Enter product name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="productSku" class="form-label fw-bold">SKU</label>
                                    <input type="text" class="form-control" id="productSku" name="product_sku" placeholder="Enter SKU">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="productCategory" class="form-label fw-bold">Category *</label>
                                    <select class="form-select" id="productCategory" name="product_category" required>
                                        <option value="">Select Category</option>
                                        <option value="electronics">Club Shirts</option>
                                        <option value="clothing">National Team Shirts</option>
                                        <option value="books">Footballs</option>
                                        <option value="home">Gear</option>
                                        <option value="sports">Football Boots</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="productBrand" class="form-label fw-bold">Brand</label>
                                    <input type="text" class="form-control" id="productBrand" name="product_brand" placeholder="Enter brand name">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="productDescription" class="form-label fw-bold">Description</label>
                                <textarea class="form-control" id="productDescription" name="product_description" rows="4" placeholder="Enter product description"></textarea>
                            </div>
                        </div>

                        <!-- Pricing & Inventory -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-dollar-sign me-2"></i>Pricing & Inventory
                            </h5>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="productPrice" class="form-label fw-bold">Price *</label>
                                    <div class="input-group price-input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="productPrice" name="product_price" placeholder="0.00" step="0.01" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="productSalePrice" class="form-label fw-bold">Sale Price</label>
                                    <div class="input-group price-input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="productSalePrice" name="product_sale_price" placeholder="0.00" step="0.01" min="0">
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="productStock" class="form-label fw-bold">Stock Quantity *</label>
                                    <input type="number" class="form-control" id="productStock" name="product_stock" placeholder="0" min="0" required>
                                </div>
                            </div>
                        </div>

                        <!-- Product Images -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-images me-2"></i>Product Images
                            </h5>
                            <div class="image-upload-area" id="imageUploadArea">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Drag & Drop Images Here</h5>
                                <p class="text-muted mb-3">or click to browse</p>
                                <input type="file" id="productImages" name="product_images[]" multiple accept="image/*" class="d-none">
                                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('productImages').click()">
                                    <i class="fas fa-plus me-2"></i>Add Images
                                </button>
                            </div>
                            <div id="imagePreview" class="mt-3"></div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Quick Actions -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-cogs me-2"></i>Actions
                            </h5>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Product
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="saveDraft()">
                                    <i class="fas fa-file-alt me-2"></i>Save as Draft
                                </button>
                                <button type="button" class="btn btn-outline-danger" onclick="resetForm()">
                                    <i class="fas fa-undo me-2"></i>Reset Form
                                </button>
                            </div>
                        </div>

                        <!-- SEO & Meta -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-search me-2"></i>SEO & Meta
                            </h5>
                            <div class="mb-3">
                                <label for="metaTitle" class="form-label fw-bold">Meta Title</label>
                                <input type="text" class="form-control" id="metaTitle" name="meta_title" placeholder="SEO title">
                            </div>
                            <div class="mb-3">
                                <label for="metaDescription" class="form-label fw-bold">Meta Description</label>
                                <textarea class="form-control" id="metaDescription" name="meta_description" rows="3" placeholder="SEO description"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="productTags" class="form-label fw-bold">Tags</label>
                                <input type="text" class="form-control" id="productTags" name="product_tags" placeholder="Tag1, Tag2, Tag3">
                                <small class="text-muted">Separate tags with commas</small>
                            </div>
                        </div>

                        <!-- Product Specifications -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-list-ul me-2"></i>Specifications
                            </h5>
                            <div id="specifications">
                                <div class="row mb-2">
                                    <div class="col-5">
                                        <input type="text" class="form-control form-control-sm" name="spec_name[]" placeholder="Specification">
                                    </div>
                                    <div class="col-5">
                                        <input type="text" class="form-control form-control-sm" name="spec_value[]" placeholder="Value">
                                    </div>
                                    <div class="col-2">
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeSpec(this)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addSpec()">
                                <i class="fas fa-plus me-1"></i>Add Specification
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Image upload functionality
        const imageUploadArea = document.getElementById('imageUploadArea');
        const imageInput = document.getElementById('productImages');
        const imagePreview = document.getElementById('imagePreview');
        let uploadedImages = [];

        // Drag and drop functionality
        imageUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            imageUploadArea.classList.add('dragover');
        });

        imageUploadArea.addEventListener('dragleave', () => {
            imageUploadArea.classList.remove('dragover');
        });

        imageUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            imageUploadArea.classList.remove('dragover');
            const files = e.dataTransfer.files;
            handleFiles(files);
        });

        imageUploadArea.addEventListener('click', () => {
            imageInput.click();
        });

        imageInput.addEventListener('change', (e) => {
            handleFiles(e.target.files);
        });

        function handleFiles(files) {
            for (let file of files) {
                if (file.type.startsWith('image/')) {
                    uploadedImages.push(file);
                    displayImage(file);
                }
            }
        }

        function displayImage(file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const imageContainer = document.createElement('div');
                imageContainer.className = 'image-container';
                imageContainer.innerHTML = `
                    <img src="${e.target.result}" class="image-preview" alt="Product Image">
                    <button type="button" class="remove-image" onclick="removeImage(this, '${file.name}')">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                imagePreview.appendChild(imageContainer);
            };
            reader.readAsDataURL(file);
        }

        function removeImage(button, fileName) {
            uploadedImages = uploadedImages.filter(file => file.name !== fileName);
            button.parentElement.remove();
        }

        // Specifications functionality
        function addSpec() {
            const specsContainer = document.getElementById('specifications');
            const newSpec = document.createElement('div');
            newSpec.className = 'row mb-2';
            newSpec.innerHTML = `
                <div class="col-5">
                    <input type="text" class="form-control form-control-sm" name="spec_name[]" placeholder="Specification">
                </div>
                <div class="col-5">
                    <input type="text" class="form-control form-control-sm" name="spec_value[]" placeholder="Value">
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeSpec(this)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            specsContainer.appendChild(newSpec);
        }

        function removeSpec(button) {
            button.closest('.row').remove();
        }

        // Form actions
        function saveDraft() {
            document.getElementById('productStatus').value = 'draft';
            document.getElementById('addProductForm').submit();
        }

        function resetForm() {
            if (confirm('Are you sure you want to reset the form? All data will be lost.')) {
                document.getElementById('addProductForm').reset();
                imagePreview.innerHTML = '';
                uploadedImages = [];
            }
        }

        // Form validation
        document.getElementById('addProductForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Basic validation
            const requiredFields = ['productName', 'productCategory', 'productPrice', 'productStock'];
            let isValid = true;
            
            requiredFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (isValid) {
                // Show success message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success alert-dismissible fade show';
                alertDiv.innerHTML = `
                    <i class="fas fa-check-circle me-2"></i>
                    Product has been added successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.querySelector('.content-wrapper').insertBefore(alertDiv, document.querySelector('.row'));
                
                // Here you would normally submit the form
                // this.submit();
            }
        });
    </script>
</body>
</html>