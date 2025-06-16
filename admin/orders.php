<?php

// Database connection
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
        // Admin sees all orders
        $orderQuery = "SELECT 
            o.order_id,
            o.order_status,
            o.order_date,
            o.full_name as customer_name,
            o.phone_number,
            o.shipping_address,
            o.city,
            o.state_province,
            o.zip_postal_code,
            o.payment_method,
            oi.product_id,
            oi.quantity,
            p.product_name,
            p.seller_id,
            u.email as seller_email
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN products p ON oi.product_id = p.product_id
        JOIN users u ON p.seller_id = u.user_id
        ORDER BY o.order_date DESC";
        
        $stmt = $conn->prepare($orderQuery);
        
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            // Fetch all results into an array
            while ($row = $result->fetch_assoc()) {
                $orders[] = $row;
            }
            $stmt->close();
        } else {
            $errors['general'] = "Failed to retrieve orders. Please try again.";
        }
        
    } else if($_SESSION['role_type'] == 'seller') {
        // Seller sees only their orders
        $orderQuery = "SELECT 
            o.order_id,
            o.order_status,
            o.order_date,
            o.full_name as customer_name,
            o.phone_number,
            o.shipping_address,
            o.city,
            o.state_province,
            o.zip_postal_code,
            o.payment_method,
            oi.product_id,
            oi.quantity,
            p.product_name,
            p.seller_id,
            u.email as seller_email
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN products p ON oi.product_id = p.product_id
        JOIN users u ON p.seller_id = u.user_id
        WHERE p.seller_id = ?
        ORDER BY o.order_date DESC";
        
        $stmt = $conn->prepare($orderQuery);
        
        if ($stmt) {
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();

            // Fetch all results into an array
            while ($row = $result->fetch_assoc()) {
                $orders[] = $row;
            }
            $stmt->close();
        } else {
            $errors['general'] = "Failed to retrieve orders. Please try again.";
        }
    }




?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders</title>
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
        .btn-edit {
            background: #3498db;
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .btn-edit:hover {
            background: #2980b9;
            transform: translateY(-1px);
            color: white;
        }
        .btn-delete {
            background: #e74c3c;
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .btn-delete:hover {
            background: #c0392b;
            transform: translateY(-1px);
            color: white;
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-delivered {
            background: #d4edda;
            color: #155724;
        }
        .status-not-paid {
            background: #f8d7da;
            color: #721c24;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-processing {
            background: #cce7ff;
            color: #004085;
        }
        .alert-success {
            border-radius: 10px;
            border: none;
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
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
        .pagination .page-link {
            border-radius: 8px;
            margin: 0 2px;
            border: none;
            color: #667eea;
        }
        .pagination .page-link:hover {
            background: #667eea;
            color: white;
        }
        .pagination .page-item.active .page-link {
            background: #667eea;
            border-color: #667eea;
        }
    </style>
</head>
<body class="bg-light">

    <!-- Sidebar Navigation -->
    <?php include('sidemenu.php'); ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title mb-0">Orders</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.html" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active">Orders</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn btn-link text-dark dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle fa-lg me-2"></i>Admin
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="orders.php?logout=1"><i class="fas fa-sign-out-alt me-2"></i>Sign out</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="content-wrapper">
            <!-- Success Alert -->
            <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                <i class="fas fa-check-circle me-3"></i>
                <div>Order has been updated successfully</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>

            <!-- Orders Table -->
            <div class="card">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Orders Management</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                        <button class="btn btn-outline-success btn-sm">
                            <i class="fas fa-download me-2"></i>Export
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Order Id</th>
                                    <th>Product</th>
                                    <th>Order Status</th>
                                    <th>Customer Name</th>
                                    <th>Order Date</th>
                                    <th>User Phone</th>
                                    <th>User Address</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($orders)): ?>
                                    <?php foreach ($orders as $order): ?>   
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($order['order_id']); ?></strong></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-light rounded me-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                                        <img class="logo" src="../assets/<?php echo htmlspecialchars($product['image_url']); ?>" alt="Product Image" style="max-width: 100%; height: auto;" onerror="this.onerror=null; this.src='../assets/images/Placeholder.png';">
                                                        
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0"><?php echo htmlspecialchars($order['product_name']); ?></h6>
                                                        <small class="text-muted"><?php echo htmlspecialchars($order['product_id']); ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="status-badge status-not-paid"><?php echo htmlspecialchars($order['order_status']); ?></span></td>
                                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                            <td><?php echo date('Y-m-d H:i', strtotime($order['order_date'])); ?></td>
                                            <td><?php echo htmlspecialchars($order['phone_number']); ?></td>
                                            <td><?php echo $order['state_province']; ?></td> 
                                            <td>
                                                <button class="btn btn-edit">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </button>
                                            </td>
                                            <td>
                                                <button class="btn btn-delete" onclick="confirmDelete(5)">
                                                    <i class="fas fa-trash me-1"></i>Delete
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="10" class="text-center">No orders found</td>
                                    </tr>
                                <?php endif; ?>

                                <!-- <tr> 
                                    <td><strong>6</strong></td>
                                    <td><span class="status-badge status-delivered">delivered</span></td>
                                    <td>1</td>
                                    <td>2029-08-15 12:59:52</td>
                                    <td>123456789</td>
                                    <td>Bloemfontein</td>
                                    <td>
                                        <button class="btn btn-edit">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </button>
                                    </td>
                                    <td>
                                        <button class="btn btn-delete" onclick="confirmDelete(6)">
                                            <i class="fas fa-trash me-1"></i>Delete
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>7</strong></td>
                                    <td><span class="status-badge status-pending">pending</span></td>
                                    <td>2</td>
                                    <td>2029-08-16 09:30:15</td>
                                    <td>987654321</td>
                                    <td>Bloemfontein</td>
                                    <td>
                                        <button class="btn btn-edit">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </button>
                                    </td>
                                    <td>
                                        <button class="btn btn-delete" onclick="confirmDelete(7)">
                                            <i class="fas fa-trash me-1"></i>Delete
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>8</strong></td>
                                    <td><span class="status-badge status-processing">processing</span></td>
                                    <td>3</td>
                                    <td>2029-08-16 14:20:30</td>
                                    <td>555123456</td>
                                    <td>Bloemfontein</td>
                                    <td>
                                        <button class="btn btn-edit">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </button>
                                    </td>
                                    <td>
                                        <button class="btn btn-delete" onclick="confirmDelete(8)">
                                            <i class="fas fa-trash me-1"></i>Delete
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>9</strong></td>
                                    <td><span class="status-badge status-delivered">delivered</span></td>
                                    <td>4</td>
                                    <td>2029-08-17 11:45:22</td>
                                    <td>444555666</td>
                                    <td>Bloemfontein</td>
                                    <td>
                                        <button class="btn btn-edit">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </button>
                                    </td>
                                    <td>
                                        <button class="btn btn-delete" onclick="confirmDelete(9)">
                                            <i class="fas fa-trash me-1"></i>Delete
                                        </button>
                                    </td>
                                </tr> -->
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Pagination -->
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Showing 1 to 5 of 25 entries
                        </div>
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1">Previous</a>
                                </li>
                                <li class="page-item active">
                                    <a class="page-link" href="#">1</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">2</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">3</a>
                                </li>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        function confirmDelete(orderId) {
            if (confirm('Are you sure you want to delete order #' + orderId + '?')) {
                // Here you would typically make an AJAX call to delete the order
                console.log('Deleting order:', orderId);
                // For demo purposes, just show an alert
                alert('Order #' + orderId + ' has been deleted successfully!');
                // In a real application, you would reload the table data or remove the row
            }
        }

        // Auto-hide success alert after 5 seconds
        setTimeout(function() {
            const alert = document.querySelector('.alert-success');
            if (alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 5000);

        // Add click handlers for edit buttons
        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                const orderId = row.querySelector('td:first-child strong').textContent;
                console.log('Editing order:', orderId);
                // Here you would typically redirect to an edit page or open a modal
                alert('Edit functionality for order #' + orderId + ' would be implemented here.');
            });
        });
    </script>
</body>
</html>