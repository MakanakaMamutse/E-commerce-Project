<?php
    // Include database connection
    include('../server/connection.php');

    session_start();

    // Check if the user is logged in
    if (!isset($_SESSION['login_status'])) {
        // User is not logged in, redirect to the login page
        header("Location: login.php");
        exit();
    }
    
    // Fetch product details by ID
    $productId = $_GET['id'] ?? null;
    $sql = "SELECT * FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        die("Product not found.");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin/edit_product.css">
</head>

<body>
    <!-- Top Navigation -->
    <nav class="top-nav">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="admin_dashboard.php">
                                    <i class="fas fa-home me-1"></i>Dashboard
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="products.php">
                                    <i class="fas fa-box me-1"></i>Products
                                </a>
                            </li>
                            <li class="breadcrumb-item active">Edit Product</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="products.php" class="nav-link">
                        <i class="fas fa-arrow-left me-2"></i>Back to Products
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Success Alert -->
                <div class="alert alert-success d-flex align-items-center mb-4 d-none" role="alert" id="successAlert">
                    <i class="fas fa-check-circle me-3"></i>
                    <div id="successMessage">Product has been updated successfully!</div>
                    <button type="button" class="btn-close ms-auto" onclick="hideAlert()"></button>
                </div>

                <!-- Error Alert -->
                <div class="alert alert-danger d-flex align-items-center mb-4 d-none" role="alert" id="errorAlert">
                    <i class="fas fa-exclamation-circle me-3"></i>
                    <div id="errorMessage">An error occurred while updating the product.</div>
                    <button type="button" class="btn-close ms-auto" onclick="hideErrorAlert()"></button>
                </div>

                <!-- Edit Product Form -->
                <div class="main-card">
                    <div class="card-header text-center">
                        <h2 class="mb-0">
                            <i class="fas fa-edit me-2"></i>Edit Product
                        </h2>
                        <p class="mb-0 mt-2 opacity-75">Update product information</p>
                    </div>
                    
                    <div class="card-body p-4">
                        <form id="editProductForm" onsubmit="updateProduct(event)">
                            <div class="row">
                                <!-- Product ID (Read-only) -->
                                <div class="col-md-6">
                                    <label for="productId" class="form-label">
                                        <i class="fas fa-hashtag me-1"></i>Product ID
                                    </label>
                                    <div class="product-id-display" id="productId"><?php echo $product['product_id']; ?></div>
                                </div>

                                <!-- Seller ID (Read-only) -->
                                <div class="col-md-6">
                                    <label for="sellerId" class="form-label">
                                        <i class="fas fa-user me-1"></i>Seller ID
                                    </label>
                                    <div class="product-id-display" id="sellerId"><?php echo $product['seller_id']; ?></div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Category ID -->
                                <div class="col-md-6">
                                    <label for="categoryId" class="form-label">
                                        <i class="fas fa-tags me-1"></i>Category
                                    </label>
                                    <select class="form-select" id="categoryId" required>
                                        <option value="">Select Category</option>
                                        <option value="1" <?php echo ($product['category_id'] == 1) ? 'selected' : ''; ?>>Club Shirts</option>
                                        <option value="2" <?php echo ($product['category_id'] == 2) ? 'selected' : ''; ?>>National Team Shirts</option>
                                        <option value="3" <?php echo ($product['category_id'] == 3) ? 'selected' : ''; ?>>Footballs</option>
                                        <option value="4" <?php echo ($product['category_id'] == 4) ? 'selected' : ''; ?>>Gear</option>
                                        <option value="5" <?php echo ($product['category_id'] == 5) ? 'selected' : ''; ?>>Football Boots</option>
                                    </select>
                                </div>

                                <!-- Price -->
                                <div class="col-md-6">
                                    <label for="price" class="form-label">
                                        <i class="fas fa-dollar-sign me-1"></i>Price
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="price" step="0.01" min="0" value="<?php echo $product['price']; ?>" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Product Name -->
                            <div class="mb-3">
                                <label for="productName" class="form-label">
                                    <i class="fas fa-box me-1"></i>Product Name
                                </label>
                                <input type="text" class="form-control" id="productName" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left me-1"></i>Description
                                </label>
                                <textarea class="form-control" id="description" rows="4" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between align-items-center">
                                <span>
                                    <strong>Last Updated:</strong> 
                                    <span class="text-muted" id="lastUpdated">
                                        <?php echo date('Y-m-d H:i:s', strtotime($product['updated_at'])); ?>
                                    </span>
                                </span>
                                <div class="d-flex gap-3">
                                    <a href="products.php" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="updateBtn">
                                        <i class="fas fa-save me-2"></i>Update Product
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        async function updateProduct(event) {
            event.preventDefault();
            
            // Show loading state
            const updateBtn = document.getElementById('updateBtn');
            const originalBtnText = updateBtn.innerHTML;
            updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
            updateBtn.disabled = true;
            
            // Get form data
            const formData = {
                productId: parseInt(document.getElementById('productId').textContent.trim()),
                sellerId: parseInt(document.getElementById('sellerId').textContent.trim()),
                categoryId: parseInt(document.getElementById('categoryId').value),
                productName: document.getElementById('productName').value.trim(),
                description: document.getElementById('description').value.trim(),
                price: parseFloat(document.getElementById('price').value)
            };
            
            // Validate form
            if (!formData.categoryId || !formData.productName || 
                !formData.description || !formData.price) {
                showErrorAlert('Please fill in all required fields.');
                resetButton(updateBtn, originalBtnText);
                return;
            }
            
            try {
                // Send AJAX request to PHP backend
                const response = await fetch('update_product.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showSuccessAlert(result.message);
                    //Redirect after success
                    setTimeout(() => {
                        window.location.href = 'products.php';
                    }, 6000);
                } else {
                    showErrorAlert(result.message || 'Failed to update product.');
                }
                
            } catch (error) {
                console.error('Error:', error);
                showErrorAlert('Network error occurred. Please try again.');
            } finally {
                resetButton(updateBtn, originalBtnText);
            }
        }
        
        function resetButton(button, originalText) {
            button.innerHTML = originalText;
            button.disabled = false;
        }
        
        function showSuccessAlert(message) {
            hideErrorAlert();
            const alert = document.getElementById('successAlert');
            const messageDiv = document.getElementById('successMessage');
            messageDiv.textContent = message;
            alert.classList.remove('d-none');
            alert.style.display = 'flex';
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                hideAlert();
            }, 5000);
        }
        
        function showErrorAlert(message) {
            hideAlert();
            const alert = document.getElementById('errorAlert');
            const messageDiv = document.getElementById('errorMessage');
            messageDiv.textContent = message;
            alert.classList.remove('d-none'); // Removing the hiding class
            alert.style.display = 'flex';
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                hideErrorAlert();
            }, 5000);
        }
        
        function hideAlert() {
            const alert = document.getElementById('successAlert');
            alert.style.transition = 'opacity 0.7s';
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.classList.add('d-none'); // Adding the hiding class back
                //alert.style.display = 'none';
                alert.style.opacity = '1';
            }, 500);
        }
        
        function hideErrorAlert() {
            const alert = document.getElementById('errorAlert');
            alert.style.transition = 'opacity 0.7s';
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.classList.add('d-none'); // Add the hiding class back
                //alert.style.display = 'none';
                alert.style.opacity = '1';
            }, 500);
        }
        
        // Auto-resize textarea
        const textarea = document.getElementById('description');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
        
        // Initialize with proper height
        textarea.style.height = textarea.scrollHeight + 'px';
    </script>

    <script src="js/active_sidebar.js"></script>

</body>
</html>