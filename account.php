<?php
/**
 * Account Management Page
 * Handles user authentication, logout, and password changes
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
    $orderQuery = "SELECT * FROM orders WHERE customer_id = ? ORDER BY order_date DESC";  // Bugged to be fixed, as im saving the email in the session, but order can have a different email, maybe use customer ID instead
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
          <div class="text-center mt-3 pt-5 col-lg-6 col-md-12 col-sm-12">
              <h3>Account Info</h3>
              <hr class="mx-auto">
              <div class="account-info">
                  <p>Name: <span> <?php echo htmlspecialchars($_SESSION['username']); ?> </span></p>
                  <p>Email: <span> <?php echo htmlspecialchars($_SESSION['email']); ?> </span></p>
                  <p><a href="#orders" id="order-btn">Your Orders</a></p>
                  <p><a href="account.php?logout=1" id="logout-btn">Logout</a></p>
              </div>
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
              
              <form id="account-form" method="POST" action="account.php">
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
                      <span>$</span>
                      <span class="product-price"><?php echo $row['total_amount']; ?></span>
                    </td>
                </tr>

            <?php } ?>

            <!-- Repeat for other products

            <tr>
              <td>
                  <div class="product-info">
                      <img src="assets/images/top4.png" alt="Product Image">
                      <div>
                          <p>Footbal Shirt</p>
                          <small>Price: $50.00</small><br>
                          <a href="#">Remove</a>
                      </div>
                  </div>
              </td>
              <td>
                <input type="number" value="1" min="1">
                <a class="edit-btn" href="#">Edit</a>
              </td>

              <td>
                <span>$</span>
                <span class="product-price">50.00</span>
              </td>
          </tr>

          Repeat for other products 

          <tr>
            <td>
                <div class="product-info">
                    <img src="assets/images/top4.png" alt="Product Image">
                    <div>
                        <p>Footbal Shirt</p>
                        <small>Price: $50.00</small><br>
                        <a href="#">Remove</a>
                    </div>
                </div>
            </td>
            <td>
              <input type="number" value="1" min="1">
              <a class="edit-btn" href="#">Edit</a>
            </td>

            <td>
              <span>$</span>
              <span class="product-price">50.00</span>
            </td>
        </tr> -->

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

<?php include('layouts/footer.php'); ?>