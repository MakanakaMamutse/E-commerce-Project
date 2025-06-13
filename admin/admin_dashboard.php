<?php

    include('../server/connection.php');

    session_start();

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
        // Prepare SQL statement to fetch user orders
            $orderQuery = "SELECT * FROM orders ORDER BY order_date DESC";
            $stmt = $conn->prepare($orderQuery);

        // Count total orders for the dashboard card
            $total_orders_query = "SELECT COUNT(*) as total FROM orders"; 
            $total_orders_stmt = $conn->prepare($total_orders_query); 
            $total_orders_stmt->execute(); 
            $total_orders_result = $total_orders_stmt->get_result(); 
            $total_orders = $total_orders_result->fetch_assoc()['total']; 
            $total_orders_stmt->close(); 

            // Count all products in the system
            $total_products_query = "SELECT COUNT(*) as total FROM products"; 
            $total_products_stmt = $conn->prepare($total_products_query); 
            $total_products_stmt->execute(); 
            $total_products_result = $total_products_stmt->get_result(); 
            $total_products = $total_products_result->fetch_assoc()['total']; 
            $total_products_stmt->close();
            
            // Count customers and sellers together (basically everyone who's not admin)
            $total_customers_query = "
                SELECT COUNT(DISTINCT u.user_id) as total 
                FROM users u 
                INNER JOIN user_roles ur ON u.user_id = ur.user_id 
                WHERE ur.role_type IN ('customer', 'seller')
            "; 
            $total_customers_stmt = $conn->prepare($total_customers_query); 
            $total_customers_stmt->execute(); 
            $total_customers_result = $total_customers_stmt->get_result(); 
            $total_customers = $total_customers_result->fetch_assoc()['total']; 
            $total_customers_stmt->close();

            // Calculate total revenue - using subtotal (product revenue without shipping)
            $total_revenue_query = "SELECT SUM(subtotal) as revenue FROM orders"; 
            $total_revenue_stmt = $conn->prepare($total_revenue_query); 
            $total_revenue_stmt->execute(); 
            $total_revenue_result = $total_revenue_stmt->get_result(); 
            $total_revenue = $total_revenue_result->fetch_assoc()['revenue'] ?? 0; 
            $total_revenue_stmt->close();
            
            // Check if the statement was prepared successfully
            if ($stmt) {
                $stmt->execute();
                // Fetch results
                $orders = $stmt->get_result();
                // Close the statement
                $stmt->close();
            } else {
                $errors['general'] = "Failed to retrieve orders. Please try again.";
            }
        }
        else if($_SESSION['role_type'] == 'seller') {

            // Prepare SQL statement to fetch user orders
            $seller_id = $_SESSION['user_id'];
            $orderQuery = "SELECT * FROM orders WHERE seller_id = ? ORDER BY order_date DESC";
            $stmt = $conn->prepare($orderQuery);

            // Count total orders for this seller
            $seller_orders_query = "SELECT COUNT(*) as total FROM orders WHERE seller_id = ?";
            $seller_orders_stmt = $conn->prepare($seller_orders_query);
            $seller_orders_stmt->bind_param("i", $seller_id);
            $seller_orders_stmt->execute();
            $seller_orders_result = $seller_orders_stmt->get_result();
            $seller_total_orders = $seller_orders_result->fetch_assoc()['total'] ?? 0;
            $seller_orders_stmt->close();

            // Calculate seller's total revenue, this is pure revenue from products sold before shipping costs
            $seller_revenue_query = "SELECT SUM(subtotal) as revenue FROM orders WHERE seller_id = ?";
            $seller_revenue_stmt = $conn->prepare($seller_revenue_query);
            $seller_revenue_stmt->bind_param("i", $seller_id);
            $seller_revenue_stmt->execute();
            $seller_revenue_result = $seller_revenue_stmt->get_result();
            $seller_revenue = $seller_revenue_result->fetch_assoc()['revenue'] ?? 0;
            $seller_revenue_stmt->close();

            // Count seller's total products
            $seller_products_query = "SELECT COUNT(*) as total FROM products WHERE seller_id = ?";
            $seller_products_stmt = $conn->prepare($seller_products_query);
            $seller_products_stmt->bind_param("i", $seller_id);
            $seller_products_stmt->execute();
            $seller_products_result = $seller_products_stmt->get_result();
            $seller_total_products = $seller_products_result->fetch_assoc()['total'] ?? 0;
            $seller_products_stmt->close();
            
                // Check if the statement was prepared successfully
                if ($stmt) {
                    // Bind parameters and execute query
                    $stmt->bind_param("s", $seller_id);
                    $stmt->execute();
                    
                    // Fetch results
                    $orders = $stmt->get_result();
                    
                    // Close the statement
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
    <title>Admin Dashboard</title>
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
    </style>
</head>
<body class="bg-light">
    
    <?php include('sidemenu.php'); ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title mb-0">Dashboard</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active">Product  Information</li>
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
                        <li><a class="dropdown-item" href="admin_dashboard.php?logout=1" id="logout-btn"><i class="fas fa-sign-out-alt me-2"></i>Sign out</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="content-wrapper">
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card text-white" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Total Orders</h6>
                                    <h3 class="mb-0">
                                        <?php
                                            if($_SESSION['role_type'] == 'admin') {
                                                echo $total_orders;
                                            } else {
                                                echo $seller_total_orders;
                                            }
                                        ?>
                                    </h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-shopping-cart fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card text-white" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Products</h6>
                                    <h3 class="mb-0">
                                        <?php
                                            if($_SESSION['role_type'] == 'admin') {
                                                echo $total_products;
                                            } else {
                                                echo $seller_total_products; 
                                            }
                                        ?>
                                    </h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-box fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card text-white" style="background: linear-gradient(135deg, #4facfe, #00f2fe);">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Customers</h6>
                                    <h3 class="mb-0">
                                        
                                        <?php
                                            if($_SESSION['role_type'] == 'admin') {
                                                echo $total_customers;
                                            } else {
                                                // For sellers, we can show the total orders as customers, i will change this later to show individual customers
                                                echo $seller_total_orders; 
                                            }
                                        ?>
                                    </h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card text-white" style="background: linear-gradient(135deg, #fa709a, #fee140);">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Revenue</h6>
                                    <h3 class="mb-0">
                                        <?php
                                            if($_SESSION['role_type'] == 'admin') {
                                                echo 'R' . number_format($total_revenue, 2);
                                            } else {
                                                echo 'R' . number_format($seller_revenue, 2);
                                            }
                                        ?>
                                    </h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">Latest Orders</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Order Status</th>
                                    <th>Order Date</th>
                                    <th>Order Destination</th>
                                    <th>Payment Method</th>
                                    <th>Shipping Cost</th>
                                    <th>Order Total</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if ($orders && $orders->num_rows > 0): ?>
                                <?php while ($order = $orders->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $order['order_id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['order_status']); ?></td>
                                    <td><?php echo date('M j, Y • h:i A', strtotime($order['order_date'])); ?></td>
                                    <td>
                                        <span class="badge bg-success">
                                            <?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['country']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                                    <td>R<?php echo number_format($order['shipping_cost'], 2); ?></td>
                                    <td>R<?php echo number_format($order['total_amount'], 2); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>