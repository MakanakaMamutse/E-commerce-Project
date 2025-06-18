<?php

include('../server/connection.php');

session_start();

 // Check if the user is logged in
    if (!isset($_SESSION['login_status'])) {
        // User is not logged in, redirect to the login page
        header("Location: login.php");
        exit();
    }

// Handle user logout functionality
if (isset($_GET['logout'])) {
    if (isset($_SESSION['login_status'])) {
        unset($_SESSION['login_status']);
        unset($_SESSION['username']);
        unset($_SESSION['email']);
        unset($_SESSION['user_id']);
        unset($_SESSION['role_type']);

        // Redirect to login page after logout
        header("Location: login.php");
        exit();
    }
}



    if($_SESSION['role_type'] == 'admin') {
        // Prepare SQL statement to fetch ALL products for admin
        $productsQuery = " SELECT 
            p.*,
            c.category_name,
            u.username as seller_name,
            pi.image_url
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN users u ON p.seller_id = u.user_id
            LEFT JOIN product_images pi ON p.product_id = pi.product_id
            ORDER BY p.product_id ASC";
        $stmt = $conn->prepare($productsQuery);

        // Count all products in the system
        $total_products_query = "SELECT COUNT(*) as total FROM products"; 
        $total_products_stmt = $conn->prepare($total_products_query); 
        $total_products_stmt->execute(); 
        $total_products_result = $total_products_stmt->get_result(); 
        $total_products = $total_products_result->fetch_assoc()['total']; 
        $total_products_stmt->close();

            // Check if the statement was prepared successfully
            if ($stmt) {
                $stmt->execute();
                // Fetch results and store in $products array
                $result = $stmt->get_result();
                $products = [];
                while ($row = $result->fetch_assoc()) {
                    $products[] = $row;
                }
                $stmt->close();
            } else {
                $errors['general'] = "Failed to retrieve products. Please try again.";
            }
    }
    else if($_SESSION['role_type'] == 'seller') {
        $seller_id = $_SESSION['user_id'];
        
        // Fetch seller's products
        $seller_products_query = " SELECT 
            p.*,
            c.category_name,
            u.username as seller_name,
            pi.image_url
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN users u ON p.seller_id = u.user_id
            LEFT JOIN product_images pi ON p.product_id = pi.product_id
            WHERE p.seller_id = ?
            ORDER BY p.product_id DESC";
        $seller_products_stmt = $conn->prepare($seller_products_query);
        
        // Count seller's total products
        $count_products_query = "SELECT COUNT(*) as total FROM products WHERE seller_id = ?";
        $count_products_stmt = $conn->prepare($count_products_query);
        $count_products_stmt->bind_param("i", $seller_id);
        $count_products_stmt->execute();
        $count_products_result = $count_products_stmt->get_result();
        $seller_total_products = $count_products_result->fetch_assoc()['total'] ?? 0;
        $count_products_stmt->close();
        
            // Check if the statement was prepared successfully
            if ($seller_products_stmt) {
                $seller_products_stmt->bind_param("i", $seller_id);
                $seller_products_stmt->execute();
                $seller_products_result = $seller_products_stmt->get_result();
                
                // Store results in $products array (same variable name as admin)
                $products = [];
                while ($row = $seller_products_result->fetch_assoc()) {
                    $products[] = $row;
                }
                
                $seller_products_stmt->close();
            } else {
                $errors['general'] = "Failed to retrieve your products.";
            }
    }


?>









<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Admin Dashboard</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
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
        
        .table {
            border-radius: 15px;
            overflow: hidden;
        }
        
        .table thead th {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            font-weight: 600;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .badge {
            font-size: 0.75rem;
            padding: 0.5rem 1rem;
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
        
        .form-check-input:checked {
            background-color: #3498db;
            border-color: #3498db;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #3498db, #2980b9);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #2980b9, #21618c);
        }
        
        .btn-outline-primary {
            border-color: #3498db;
            color: #3498db;
        }
        
        .btn-outline-primary:hover {
            background-color: #3498db;
            border-color: #3498db;
        }
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <?php include('sidemenu.php'); ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title mb-0">Products</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="admin_dashboard.html" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active">Products</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-primary">
                    <i class="fas fa-download me-2"></i>Export
                </button>
                <a href="add_product.html" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Product
                </a>
                <div class="dropdown">
                    <button class="btn btn-link text-dark dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle fa-lg me-2"></i>Admin
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="account.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="products.php?logout=1"><i class="fas fa-sign-out-alt me-2"></i>Sign out</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="content-wrapper">
            <!-- Filter and Search -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" placeholder="Search products..." id="searchInput">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="categoryFilter">
                                <option value="">All Categories</option>
                                <option value="electronics">Electronics</option>
                                <option value="clothing">Clothing</option>
                                <option value="books">Books</option>
                                <option value="home">Home & Garden</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="out-of-stock">Out of Stock</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary w-100" onclick="applyFilters()">
                                <i class="fas fa-filter me-2"></i>Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Table -->
            <div class="card">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Product Inventory</h5>
                    <span class="badge bg-primary">
                        <?php 
                        if($_SESSION['role_type'] == 'admin') {
                            echo $total_products . ' Products';
                        } else {
                            echo $seller_total_products . ' Products';
                        }
                        ?>
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th width="80">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                        </div>
                                    </th>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th>Seller</th>
                                    <th>Price</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input row-checkbox" type="checkbox">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded me-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                                    <img class="logo" src="../assets/<?php echo htmlspecialchars($product['image_url']); ?>" alt="Product Image" style="max-width: 100%; height: auto;" onerror="this.onerror=null; this.src='../assets/images/Placeholder.png';">
                                                    
                                                </div>
                                                <div>
                                                    <h6 class="mb-0"><?php echo htmlspecialchars($product['product_name']); ?></h6>
                                                    <small class="text-muted"><?php echo htmlspecialchars($product['product_id']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($product['category_name'] ?? 'No Category'); ?></td>
                                        <td>
                                            <span class="badge bg-secondary" title="<?php echo htmlspecialchars($product['description']); ?>"
                                                style="display: inline-block; max-width: 550px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                <?php echo htmlspecialchars($product['description']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success"><?php echo htmlspecialchars($product['seller_name'] ?? 'Unknown Seller'); ?></span>
                                        </td>
                                        <td><strong>$<?php echo number_format($product['price'], 2); ?></strong></td>

                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" title="Edit" onclick="editProduct(<?php echo $product['product_id']; ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-outline-danger" title="Delete" onclick="deleteProduct(<?php echo $product['product_id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Showing 1 to 5 of 567 entries</small>
                        </div>
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1">Previous</a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Toggle select all checkboxes
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');
            
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
        }

        // Apply filters function
        function applyFilters() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const categoryFilter = document.getElementById('categoryFilter').value;
            const statusFilter = document.getElementById('statusFilter').value;
            
            // In a real application, you would implement actual filtering logic here
            console.log('Applying filters:', { searchTerm, categoryFilter, statusFilter });
            
            // Show a toast or notification that filters have been applied
            alert('Filters applied! (This would filter the products in a real application)');
        }

        // Edit product function
        function editProduct(productID) {
            // Redirect to edit product page with the product ID as a parameter
            console.log("Editing product with ID:", productID);
            window.location.href = 'edit_product.php?id=' + productID;
        }

        // Delete product function
        function deleteProduct(productID) {
            // Show confirmation dialog before proceeding
            if (confirm(`Are you sure you want to delete product ID ${productID}? This action cannot be undone.`)) {
                
                // Get the delete button and show loading state
                const deleteButton = document.querySelector(`button[onclick="deleteProduct(${productID})"]`);
                const originalHTML = deleteButton.innerHTML;
                deleteButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                deleteButton.disabled = true;
                
                // Prepare form data to send to server
                const formData = new FormData();
                formData.append('product_id', productID);
                
                // Send AJAX request to delete_product.php
                fetch('delete_product.php', {
                    method: 'POST',
                    body: formData
                })
                // Convert PHP response from JSON string to JavaScript object
                .then(response => response.json())
                .then(data => {
                    // Handle successful or failed deletion based on PHP response
                    if (data.success) {
                        // Deletion successful - show message and remove row
                        alert(data.message);
                        
                        // Animate row removal with fade effect
                        const row = deleteButton.closest('tr');
                        row.style.transition = 'opacity 0.3s ease';
                        row.style.opacity = '0';
                        
                        // Remove row from DOM after animation completes
                        setTimeout(() => {
                            row.remove();
                            
                            // Update product count in header badge
                            const badge = document.querySelector('.badge.bg-primary');
                            if (badge) {
                                const currentCount = parseInt(badge.textContent.match(/\d+/)[0]);
                                badge.textContent = `${currentCount - 1} Products`;
                            }
                        }, 300);
                        
                    } else {
                        // Deletion failed - show error and restore button
                        alert('Error: ' + data.message);
                        deleteButton.innerHTML = originalHTML;
                        deleteButton.disabled = false;
                    }
                })
                // Handle network errors or other issues with the request
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the product. Please try again.');
                    
                    // Restore button to original state
                    deleteButton.innerHTML = originalHTML;
                    deleteButton.disabled = false;
                });
            }
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });

        // Update select all checkbox based on individual checkboxes
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('row-checkbox')) {
                const rowCheckboxes = document.querySelectorAll('.row-checkbox');
                const selectAll = document.getElementById('selectAll');
                const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
                
                if (checkedCount === 0) {
                    selectAll.indeterminate = false;
                    selectAll.checked = false;
                } else if (checkedCount === rowCheckboxes.length) {
                    selectAll.indeterminate = false;
                    selectAll.checked = true;
                } else {
                    selectAll.indeterminate = true;
                }
            }
        });
    </script>

    <script src="js/active_sidebar.js"></script>

</body>
</html>
