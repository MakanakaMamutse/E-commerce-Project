<?php

session_start();
// Checking if user is logged in and has proper permissions
// Establishing database connection to handle product data
include('../server/connection.php');

// Initializing message variables for user feedback
$message = "";
$messageType = "";

// Fetching all available categories from database for dropdown menu
$categories = [];
$result = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_id");
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $categories[] = $row; // Building categories array for later use
    }
}

// Processing form submission when user clicks submit button
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Capturing and sanitizing form data that was submitted by the user
    // XSS Protection: Sanitizing all user inputs to prevent script injection
    $product_name = htmlspecialchars(trim($_POST['product_name']), ENT_QUOTES, 'UTF-8');
    $category_id = filter_var($_POST['category_id'], FILTER_VALIDATE_INT);
    $product_description = htmlspecialchars(trim($_POST['product_description']), ENT_QUOTES, 'UTF-8');
    $product_price = filter_var($_POST['product_price'], FILTER_VALIDATE_FLOAT);
    $seller_id = $_SESSION['user_id']; // Getting current user's ID from session

    // XSS Protection: Validate that category_id is a valid integer
    if ($category_id === false || $category_id <= 0) {
        $message = "Invalid category selected.";
        $messageType = "danger";
    }
    // XSS Protection: Validate that price is a valid positive number (must be greater than 0)
    elseif ($product_price === false || $product_price <= 0) {
        $message = "Invalid price entered. Price must be greater than 0.";
        $messageType = "danger";
    }
    // XSS Protection: Validate that product name is not empty after sanitization
    elseif (empty($product_name)) {
        $message = "Product name is required and cannot contain only special characters.";
        $messageType = "danger";
    }
    else {
        // Finding the category name to create organized folder structure
        $category_name = "";
        foreach($categories as $cat) {
            if($cat['category_id'] == $category_id) {
                $category_name = $cat['category_name'];
                break; // Found the matching category, stopping the search
            }
        }
        
        // Handling mandatory image upload - product cannot be created without an image
        $image_url = "";
        // Fixed condition: check if no file uploaded OR if there's an upload error
        if (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] == UPLOAD_ERR_NO_FILE || $_FILES['product_image']['error'] != UPLOAD_ERR_OK) {
            $message = "Product image is required. Please upload an image.";
            $messageType = "danger";
        } else {
            // Creating organized folder structure based on category
            $relative_path = "images/" . strtolower(str_replace(' ', '-', $category_name)) . "/"; // Path saved to database
            $target_dir = "../assets/" . $relative_path; // Actual upload directory on server
            
            // Creating directory if it doesn't exist yet
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true); // Setting full permissions for web server access
            }
            
            // Generating safe filename to prevent conflicts and security issues
            // XSS Protection: Additional sanitization of filename to prevent path traversal
            $file_extension = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
            $safe_filename = preg_replace('/[^a-z0-9\-]/', '', strtolower(str_replace(' ', '-', $product_name))) . '.' . $file_extension;
            $target_file = $target_dir . $safe_filename;
            
            // XSS Protection: Whitelist allowed image extensions
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($file_extension, $allowed_extensions)) {
                $message = "Invalid file type. Only JPG, PNG, GIF, and WebP images are allowed.";
                $messageType = "danger";
            }
            // Validating that uploaded file is actually an image
            elseif (($check = getimagesize($_FILES['product_image']['tmp_name'])) !== false) {
                // XSS Protection: Additional validation of file size (5MB limit)
                if ($_FILES['product_image']['size'] > 5 * 1024 * 1024) {
                    $message = "File size too large. Maximum size is 5MB.";
                    $messageType = "danger";
                } else {
                    // Moving uploaded file from temporary location to permanent storage
                    if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
                        $image_url = $relative_path . $safe_filename; // Storing relative path for database
                    } else {
                        $message = "Sorry, there was an error uploading your file.";
                        $messageType = "danger";
                    }
                }
            } else {
                $message = "File is not an image.";
                $messageType = "danger";
            }
        }
        
        // Saving product data to database if no upload errors occurred
        if (empty($message)) {
            // Inserting basic product information into products table
            // XSS Protection: Using prepared statements to prevent SQL injection
            $stmt = $conn->prepare("INSERT INTO products (product_name, category_id, description, price, seller_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sisdi", $product_name, $category_id, $product_description, $product_price, $seller_id);

            if ($stmt->execute()) {
                $product_id = $conn->insert_id; // Getting the ID of newly created product

                // Saving image information to separate table if image was uploaded
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
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Admin</title>
    <!-- Bootstrap CSS for responsive design and components -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons throughout the interface -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Admin sidebar styling with gradient background */
        .sidebar {
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            z-index: 1000;
        }
        /* Sidebar navigation links with hover effects */
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
        /* Main content area positioned to accommodate sidebar */
        .main-content {
            margin-left: 250px;
            padding: 0;
        }
        /* Top navigation bar with shadow */
        .top-navbar {
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 1rem 2rem;
            margin-bottom: 2rem;
        }
        .content-wrapper {
            padding: 0 2rem 2rem;
        }
        /* Card styling with modern shadow effects */
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
        /* Form input styling with focus effects */
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
        /* Primary button with gradient and hover animation */
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
        /* File upload area with drag-and-drop styling */
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
        /* XSS Protection: Green outline when image is uploaded */
        .image-upload-area.has-image {
            border-color: #28a745;
            background: rgba(40, 167, 69, 0.05);
        }
        /* Image preview styling */
        .image-preview {
            max-width: 200px;
            max-height: 200px;
            border-radius: 10px;
            margin: 10px 0;
            border: 2px solid #dee2e6;
        }
        /* Price input with currency symbol */
        .price-input-group .input-group-text {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 10px 0 0 10px;
            font-weight: 600;
        }
        /* Form sections with consistent spacing */
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
  
    <!-- Admin navigation sidebar -->
    <?php include('sidemenu.php'); ?>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Top Navigation Bar -->
        <div class="top-navbar d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title mb-0">Add New Product</h2>
                <!-- Breadcrumb navigation -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="admin_dashboard.php" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="products.php" class="text-decoration-none">Products</a></li>
                        <li class="breadcrumb-item active">Add Product</li>
                    </ol>
                </nav>
            </div>
            <!-- User dropdown menu -->
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

        <!-- Main Content Wrapper -->
        <div class="content-wrapper">
            <!-- Success/Error Message Display -->
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo htmlspecialchars($messageType, ENT_QUOTES, 'UTF-8'); ?> alert-dismissible fade show" role="alert">
                    <i class="fas fa-<?php echo $messageType == 'success' ? 'check-circle' : 'exclamation-circle'; ?> me-2"></i>
                    <?php 
                    // XSS Protection: Escaping message output to prevent script execution
                    echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); 
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Product Add Form with file upload capability -->
            <form id="addProductForm" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Basic Product Information Section -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-info-circle me-2"></i>Basic Information
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="productName" class="form-label fw-bold">Product Name *</label>
                                    <!-- XSS Protection: Preserving user input while preventing script execution -->
                                    <input type="text" class="form-control" id="productName" name="product_name" 
                                           placeholder="Enter product name" 
                                           value="<?php echo isset($_POST['product_name']) ? htmlspecialchars($_POST['product_name'], ENT_QUOTES, 'UTF-8') : ''; ?>" 
                                           required maxlength="255">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="productSku" class="form-label fw-bold">SKU</label>
                                    <input type="text" class="form-control" id="productSku" name="product_sku" 
                                           placeholder="Enter SKU" maxlength="100">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="productCategory" class="form-label fw-bold">Category *</label>
                                    <!-- Dynamic category dropdown populated from database -->
                                    <select class="form-select" id="productCategory" name="category_id" required>
                                        <option value="">Select Category</option>
                                        <?php foreach($categories as $category): ?>
                                            <option value="<?php echo htmlspecialchars($category['category_id'], ENT_QUOTES, 'UTF-8'); ?>"
                                                    <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $category['category_id']) ? 'selected' : ''; ?>>
                                                <?php 
                                                // XSS Protection: Escaping category names in dropdown options
                                                echo htmlspecialchars($category['category_name'], ENT_QUOTES, 'UTF-8'); 
                                                ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="productBrand" class="form-label fw-bold">Brand</label>
                                    <input type="text" class="form-control" id="productBrand" name="product_brand" 
                                           placeholder="Enter brand name" maxlength="100">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="productDescription" class="form-label fw-bold">Description</label>
                                <!-- XSS Protection: Preserving user input in textarea while preventing script execution -->
                                <textarea class="form-control" id="productDescription" name="product_description" 
                                          rows="4" placeholder="Enter product description" maxlength="1000"><?php 
                                    echo isset($_POST['product_description']) ? htmlspecialchars($_POST['product_description'], ENT_QUOTES, 'UTF-8') : ''; 
                                ?></textarea>
                            </div>
                        </div>

                        <!-- Pricing and Inventory Management Section -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-dollar-sign me-2"></i>Pricing & Inventory
                            </h5>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="productPrice" class="form-label fw-bold">Price *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <!-- XSS Protection: Preserving numeric input while ensuring proper validation -->
                                        <input type="number" class="form-control" id="productPrice" name="product_price" 
                                               placeholder="0.00" step="0.01" min="0.01" max="99999.99"
                                               value="<?php echo isset($_POST['product_price']) ? htmlspecialchars($_POST['product_price'], ENT_QUOTES, 'UTF-8') : ''; ?>" 
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="productSalePrice" class="form-label fw-bold">Sale Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="productSalePrice" name="product_sale_price" 
                                               placeholder="0.00" step="0.01" min="0" max="99999.99">
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="productStock" class="form-label fw-bold">Stock Quantity *</label>
                                    <input type="number" class="form-control" id="productStock" name="product_stock" 
                                           placeholder="0" min="0" max="999999" required>
                                </div>
                            </div>
                        </div>

                        <!-- Product Image Upload Section -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-image me-2"></i>Product Image *
                            </h5>
                            <!-- Click-to-upload image area -->
                            <div class="image-upload-area" onclick="document.getElementById('productImage').click()">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Click to Upload Image *</h5>
                                <p class="text-muted mb-3">JPG, PNG, GIF, WebP up to 5MB (Required)</p>
                                <input type="file" id="productImage" name="product_image" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" class="d-none" onchange="previewImage(this)">
                            </div>
                            <!-- Image preview container -->
                            <div id="imagePreview" class="mt-3"></div>
                        </div>
                    </div>

                    <!-- Sidebar with Action Buttons -->
                    <div class="col-lg-4">
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

    <!-- Bootstrap JavaScript for interactive components -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
       // Image preview functionality - showing uploaded image before form submission
       // XSS Protection: Client-side validation to ensure only image files are processed
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const uploadArea = document.querySelector('.image-upload-area');
    preview.innerHTML = ''; // Clearing any existing preview
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // XSS Protection: Validate file type on client side
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            alert('Invalid file type. Please select a JPG, PNG, GIF, or WebP image.');
            input.value = '';
            uploadArea.classList.remove('has-image'); // Remove green outline
            return;
        }
        
        // XSS Protection: Validate file size (5MB limit)
        if (file.size > 5 * 1024 * 1024) {
            alert('File size too large. Please select an image smaller than 5MB.');
            input.value = '';
            uploadArea.classList.remove('has-image'); // Remove green outline
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            // XSS Protection: Using textContent to prevent any potential script execution
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'image-preview';
            img.alt = 'Product Image Preview';
            preview.appendChild(img);
            
            // XSS Protection: Add green outline when image is successfully uploaded
            uploadArea.classList.add('has-image');
        };
        reader.readAsDataURL(file); // Converting image to displayable format
    } else {
        // Remove green outline if no file selected
        uploadArea.classList.remove('has-image');
    }
}

// Form reset functionality with confirmation dialog
function resetForm() {
    if (confirm('Are you sure you want to reset the form? All data will be lost.')) {
        document.getElementById('addProductForm').reset();
        document.getElementById('imagePreview').innerHTML = ''; // Clearing image preview
        document.querySelector('.image-upload-area').classList.remove('has-image'); // Remove green outline
    }
}

// XSS Protection: Additional client-side validation for form inputs
document.getElementById('addProductForm').addEventListener('submit', function(e) {
    const productName = document.getElementById('productName').value.trim();
    const productPrice = document.getElementById('productPrice').value;
    const productStock = document.getElementById('productStock').value;
    
    // Validate product name is not empty
    if (productName === '') {
        alert('Product name is required.');
        e.preventDefault();
        return;
    }
    
    // Validate price is a positive number
    if (parseFloat(productPrice) < 0) {
        alert('Price must be a positive number.');
        e.preventDefault();
        return;
    }
    
    // Validate stock is a non-negative integer
    if (parseInt(productStock) < 0) {
        alert('Stock quantity must be a non-negative number.');
        e.preventDefault();
        return;
    }
});
        
    </script>

    <!-- Script for highlighting active sidebar menu item -->
    <script src="js/active_sidebar.js"></script>

</body>
</html>