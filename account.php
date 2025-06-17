<?php
/**
 * Account Management Page
 * Handles user authentication, logout, password changes, and seller upgrades
 */

// Start the session
session_start();

// Include database connection file
include('server/connection.php');

// Check if the user is logged in
if (!isset($_SESSION['login_status'])) {
    // User is not logged in, redirect to the login page
    header("Location: login.php");
    exit();
}

// Initialize variables for form data and errors
$errors = [];
$success_message = '';

// Handle user logout functionality
if (isset($_GET['logout'])) {
    if(isset($_SESSION['login_status'])) {
        // Unset specific session variables instead of destroying entire session
        unset($_SESSION['login_status']);
        unset($_SESSION['username']);
        unset($_SESSION['useremail']);
        unset($_SESSION['email']);

        // Redirect to login page after logout
        header("Location: login.php");
        exit();
    }
}

// Get user's current role
$user_role = 'customer'; // Default role
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $roleQuery = "SELECT ur.role_type FROM user_roles ur JOIN users u ON ur.user_id = u.user_id WHERE u.user_id = ?";
    $stmt = $conn->prepare($roleQuery);
    
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $user_role = $row['role_type'];
        }
        $stmt->close();
    }
}

/**
 * Handle Seller Upgrade Request
 */
if (isset($_POST['upgrade-to-seller'])) {
    $user_id = $_SESSION['user_id'];
    
    // Check if user is already a seller or admin
    if ($user_role === 'seller' || $user_role === 'admin') {
        $errors['upgrade'] = "You are already registered as a " . ucfirst($user_role) . ".";
    } else {
        // Update user role to seller
        $upgradeQuery = "UPDATE user_roles SET role_type = 'seller' WHERE user_id = ?";
        $stmt = $conn->prepare($upgradeQuery);
        
        if ($stmt) {
            $stmt->bind_param("i", $user_id);
            
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $success_message = "Congratulations! Your account has been upgraded to Seller status.";
                    $user_role = 'seller'; // Update the current role variable
                } else {
                    $errors['upgrade'] = "Failed to upgrade account. Please try again.";
                }
            } else {
                $errors['upgrade'] = "Database error occurred during upgrade. Please try again.";
            }
            $stmt->close();
        } else {
            $errors['upgrade'] = "Failed to prepare upgrade query. Please try again.";
        }
    }
}

/**
 * Password Change Form Processing
 * Validates input and updates user password in database
 */
if (isset($_POST['change-password'])) {
    // Sanitize input data
    $new_password = trim($_POST['new-password']);
    $confirm_password = trim($_POST['confirm-password']);
    $user_email = $_SESSION['email'];

    // Validate new password field
    if (empty($new_password)) {
        $errors['new_password'] = "New password is required.";
    }
    
    if (strlen($new_password) < 8) {
        $errors['new_password'] = "Password must be at least 8 characters long.";
    }
    
    // Check for password complexity (at least one number and one letter)
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)/', $new_password)) {
        $errors['new_password'] = "Password must contain at least one letter and one number.";
    }
    
    // Validate confirm password field
    if (empty($confirm_password)) {
        $errors['confirm_password'] = "Please confirm your password.";
    }
    
    if ($new_password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match.";
    }

    // Process password update if no validation errors
    if (empty($errors)) {
        // Hash the new password securely
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Prepare SQL statement to prevent SQL injection
        $updatePasswordQuery = "UPDATE users SET password = ? WHERE email = ?";
        $stmt = $conn->prepare($updatePasswordQuery);
        
        // Check if the statement was prepared successfully
        if ($stmt) {
            // Bind parameters and execute query
            $stmt->bind_param("ss", $hashed_password, $user_email);
            
            if ($stmt->execute()) {
                // Check if any rows were actually updated
                if ($stmt->affected_rows > 0) {
                    $success_message = "Password changed successfully!";
                    // Clear POST data to prevent form resubmission issues
                    $_POST = [];
                } 
                else {
                    $errors['general'] = "No changes were made. Please try again.";
                }
            }
             else {
                $errors['general'] = "Database error occurred. Please try again.";
            }
            $stmt->close();
        } 
        else {
            $errors['general'] = "Failed to prepare database query. Please try again.";
        }
    }
}

// Check if the user is logged in and then retrieve order history
if (isset($_SESSION['login_status'])) {
    // Prepare SQL statement to fetch user orders
    $customer_id = $_SESSION['user_id'];
    $orderQuery = "SELECT * FROM orders WHERE customer_id = ? ORDER BY order_date DESC";
    $stmt = $conn->prepare($orderQuery);
    
    // Check if the statement was prepared successfully
    if ($stmt) {
        // Bind parameters and execute query
        $stmt->bind_param("s", $customer_id);
        $stmt->execute();
        
        // Fetch results
        $orders = $stmt->get_result();
        
        // Close the statement
        $stmt->close();
    } else {
        $errors['general'] = "Failed to retrieve orders. Please try again.";
    }
} else {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

?>



<?php include('layouts/header.php'); ?>

    <!--Account Page-->
  <section class="my-5 py-5">
      <div class="row container mx-auto">
          <div class="col-lg-6 col-md-12 col-sm-12">
              <!-- Account Info Section -->
              <div class="text-center mt-3 pt-5">
                  <h3>Account Info</h3>
                  <hr class="mx-auto">
                  <div class="account-info">
                      <p>Name: <span> <?php echo htmlspecialchars($_SESSION['username']); ?> </span></p>
                      <p>Email: <span> <?php echo htmlspecialchars($_SESSION['email']); ?> </span></p>
                      <p>Role: <span class="badge bg-<?php echo ($user_role === 'admin') ? 'danger' : (($user_role === 'seller') ? 'warning' : 'secondary'); ?>">
                          <?php echo ucfirst($user_role); ?>
                      </span></p>
                      
                      <?php if ($user_role === 'seller' || $user_role === 'admin'): ?>
                          <p><a href="admin/login.php" class="btn btn-sm btn-outline-primary" target="_blank">
                              <i class="fas fa-cog"></i> Admin Panel
                          </a></p>
                      <?php endif; ?>
                      
                      <p><a href="#orders" id="order-btn">Your Orders</a></p>
                      <p><a href="account.php?logout=1" id="logout-btn">Logout</a></p>
                  </div>
              </div>
              
              <!-- Seller Upgrade Section (in the white space) -->
              <?php if ($user_role === 'customer'): ?>
              <div class="mt-4">
                  <div class="card">
                      <div class="card-header bg-warning text-dark">
                          <h5 class="mb-0"><i class="fas fa-store"></i> Become a Seller</h5>
                      </div>
                      <div class="card-body">
                          <p class="card-text">
                              Want to start selling on our platform? Upgrade your account to seller status and start managing your own products!
                          </p>
                          
                          <!-- Upgrade Error Message Display -->
                          <?php if (isset($errors['upgrade'])): ?>
                              <div class="alert alert-danger" role="alert">
                                  <strong>Error:</strong> <?php echo htmlspecialchars($errors['upgrade']); ?>
                              </div>
                          <?php endif; ?>
                          
                          <form method="POST" action="account.php" class="d-inline">
                              <button type="submit" 
                                      name="upgrade-to-seller" 
                                      class="btn btn-warning"
                                      onclick="return confirm('Are you sure you want to upgrade to a seller account? This action cannot be undone.')">
                                  <i class="fas fa-arrow-up"></i> Upgrade to Seller
                              </button>
                          </form>
                          
                          <div class="mt-2">
                              <small class="text-muted">
                                  <strong>Benefits of being a seller:</strong><br>
                                  • Add and manage your own products<br>
                                  • Access to seller dashboard<br>
                                  • Track your sales and inventory<br>
                                  • Connect with customers directly
                              </small>
                          </div>
                      </div>
                  </div>
              </div>
              <?php endif; ?>
          </div>

          <div class="col-lg-6 col-md-12 col-sm-12">
              <!-- Success Message Display -->
              <?php if (!empty($success_message)): ?>
                  <div class="alert alert-success text-center" role="alert">
                      <strong>Success!</strong> <?php echo htmlspecialchars($success_message); ?>
                  </div>
              <?php endif; ?>
              
              <!-- General Error Message Display -->
              <?php if (isset($errors['general'])): ?>
                  <div class="alert alert-danger text-center" role="alert">
                      <strong>Error:</strong> <?php echo htmlspecialchars($errors['general']); ?>
                  </div>
              <?php endif; ?>
              
              <!-- Password Change Form -->
              <form id="account-form" method="POST" action="account.php" class="mt-3 pt-5">
                  <h3>Change Password</h3>
                  <hr class="mx-auto">
                  
                  <!-- New Password Field -->
                  <div class="mb-3">
                      <label for="new-password" class="form-label">New Password</label>
                      <input type="password" 
                            class="form-control <?php echo isset($errors['new_password']) ? 'is-invalid' : ''; ?>" 
                            id="new-password" 
                            name="new-password" 
                            placeholder="Enter new password"
                            required>
                      <?php if (isset($errors['new_password'])): ?>
                          <div class="invalid-feedback">
                              <?php echo htmlspecialchars($errors['new_password']); ?>
                          </div>
                      <?php endif; ?>
                      <div class="form-text">
                          Password must be at least 8 characters with letters and numbers.
                      </div>
                  </div>

                  <!-- Confirm Password Field -->
                  <div class="mb-3">
                      <label for="account-password-confirm" class="form-label">Confirm Password</label>
                      <input type="password" 
                            class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" 
                            id="account-password-confirm" 
                            name="confirm-password" 
                            placeholder="Confirm new password"
                            required>
                      <?php if (isset($errors['confirm_password'])): ?>
                          <div class="invalid-feedback">
                              <?php echo htmlspecialchars($errors['confirm_password']); ?>
                          </div>
                      <?php endif; ?>
                  </div>
                  
                  <div class="form-group mt-2">
                      <input type="submit" 
                            class="btn btn-primary" 
                            id="change-password-btn" 
                            name="change-password" 
                            value="Change Password">
                  </div>
              </form>
          </div>
      </div>
  </section>



  <!--Orders Section-->  
  <section id="order" class="cart container my-5 py-5">
        <div class="container mt-5">
            <h2 class="font-weight-bold">Your Orders</h2>
            <hr>
        </div>
        <table class="mt-5 pt-5">
            <tr>
                <th>OrderID</th>
                <th>Order Status</th>
                <th>Date</th>
                <th>Quantity</th>
                <th>Order Cost</th>
            </tr>

            <?php while ($row = $orders->fetch_assoc()) { ?>
                <tr>
                    <td>
                        <div class="product-info">
                          <img src="assets\images\OrderID.png" alt="Product Image">
                          <div class="font-weight-bold">
                              <br>
                              <p> <?php echo $row['order_id']; ?> </p>
                              <a href="order_details.php?order_id=<?php echo htmlspecialchars($row['order_id']); ?>">Details</a>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span class="order-date"> <?php echo $row['order_status']; ?> </span>
                    </td>
                    <td>
                      <span class="order-date"> <?php echo $row['order_date']; ?> </span>
                    </td>
                    <td>
                      <input type="number" value="1" min="1">
                      <a class="edit-btn" href="#">Edit</a>
                    </td>
                    <td>
                      <span class="product-price">$<?php echo $row['total_amount']; ?></span>
                    </td>
                </tr>

            <?php } ?>

        </table>

        <div class="cart-total">
          <table>
            <tr>
              <td>Subtotal</td>
              <td>R150.00</td>
            </tr>
            <tr>
              <td>Shipping</td>
              <td>R50.00</td>
          </table>
        </div>

        <div class="checkout-container">
          <button class="checkout-btn">Checkout</button>

        </div>
  </section>

<?php include('layouts/footer.html'); ?>