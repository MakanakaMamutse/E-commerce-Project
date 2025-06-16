<?php

session_start();
// Check if user is logged in
// Database connection
include('../server/connection.php');

$message = "";
$messageType = "";

// Get categories for dropdown
$categories = [];
$result = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_id");
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $product_name = $_POST['product_name'];
    $category_id = $_POST['category_id'];
    $product_description = $_POST['product_description'];
    $product_price = $_POST['product_price'];
    $seller_id = $_SESSION['user_id']; 

    // Get category name for folder structure
    $category_name = "";
    foreach($categories as $cat) {
        if($cat['category_id'] == $category_id) {
            $category_name = $cat['category_name'];
            break;
        }
    }
    
    // Handle image upload
    $image_url = "";
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $relative_path = "images/" . strtolower(str_replace(' ', '-', $category_name)) . "/"; // This is what's saved to DB
        $target_dir = "../assets/" . $relative_path; // Full upload path on disk
        
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true); //Sets full permissions (read/write/execute for all)
        }
        
        $file_extension = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
        $safe_filename = strtolower(str_replace(' ', '-', $product_name)) . '.' . $file_extension;
        $target_file = $target_dir . $safe_filename;
        
        // Check if image file is actual image
        $check = getimagesize($_FILES['product_image']['tmp_name']);
        if ($check !== false) {
            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
                $image_url = $relative_path . $safe_filename; // Save relative path to DB
            } else {
                $message = "Sorry, there was an error uploading your file.";
                $messageType = "danger";
            }
        } else {
            $message = "File is not an image.";
            $messageType = "danger";
        }
    }
    
    // Insert into database if no errors
    if (empty($message)) {
        // Insert into products table without image_url
        $stmt = $conn->prepare("INSERT INTO products (product_name, category_id, description, price, seller_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sisdi", $product_name, $category_id, $product_description, $product_price, $seller_id);

        if ($stmt->execute()) {
            $product_id = $conn->insert_id; // Get inserted product ID

            // ✅ Now insert into product_images table
            if (!empty($image_url)) {
                $imgStmt = $conn->prepare("INSERT INTO product_images (product_id, image_url) VALUES (?, ?)");
                $imgStmt->bind_param("is", $product_id, $image_url);
                $imgStmt->execute();
                $imgStmt->close();
            }

            $message = "Product has been added successfully!";
            $messageType = "success";
        } else {
            $message = "Error: " . $stmt->error;
            $messageType = "danger";
        }

        $stmt->close();
    }
}
$conn->close();
?>

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
        .image-preview {
            max-width: 200px;
            max-height: 200px;
            border-radius: 10px;
            margin: 10px 0;
            border: 2px solid #dee2e6;
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
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <i class="fas fa-<?php echo $messageType == 'success' ? 'check-circle' : 'exclamation-circle'; ?> me-2"></i>
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form id="addProductForm" method="POST" enctype="multipart/form-data">
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
                                    <select class="form-select" id="productCategory" name="category_id" required>
                                        <option value="">Select Category</option>
                                        <?php foreach($categories as $category): ?>
                                            <option value="<?php echo $category['category_id']; ?>"><?php echo $category['category_name']; ?></option>
                                        <?php endforeach; ?>
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
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="productPrice" name="product_price" placeholder="0.00" step="0.01" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="productSalePrice" class="form-label fw-bold">Sale Price</label>
                                    <div class="input-group">
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

                        <!-- Product Image -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-image me-2"></i>Product Image
                            </h5>
                            <div class="image-upload-area" onclick="document.getElementById('productImage').click()">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Click to Upload Image</h5>
                                <p class="text-muted mb-3">JPG, PNG, GIF up to 5MB</p>
                                <input type="file" id="productImage" name="product_image" accept="image/*" class="d-none" onchange="previewImage(this)">
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
                                <button type="button" class="btn btn-outline-danger" onclick="resetForm()">
                                    <i class="fas fa-undo me-2"></i>Reset Form
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Image preview functionality
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" class="image-preview" alt="Product Image Preview">`;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Reset form
        function resetForm() {
            if (confirm('Are you sure you want to reset the form? All data will be lost.')) {
                document.getElementById('addProductForm').reset();
                document.getElementById('imagePreview').innerHTML = '';
            }
        }

        // Form validation
        document.getElementById('addProductForm').addEventListener('submit', function(e) {
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
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    </script>
</body>
</html>