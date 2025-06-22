<?php

// Include database connection
  include('../server/connection.php');
session_start();

  // If the Admin is already logged in, redirect them to the admin dashboard
// This prevents unauthorized access to the login page, else seller must re-login
if (isset($_SESSION['login_status']) && $_SESSION['login_status'] === true && $_SESSION['user_id'] == 1) {

    // Admin is already logged in, but we need to ensure all session variables are set
    // Check if critical session variables are missing
    if (!isset($_SESSION['full_name']) || !isset($_SESSION['username']) || !isset($_SESSION['email']) || 
        !isset($_SESSION['phone_number']) || !isset($_SESSION['registration_date']) || !isset($_SESSION['role_type'])) {
        
        // Fetch complete user data from database to populate missing session variables
        $user_sql = "SELECT u.*, ur.role_type
                     FROM users u 
                     LEFT JOIN user_roles ur ON u.user_id = ur.user_id 
                     WHERE u.user_id = ? LIMIT 1";
        
        $stmt = $conn->prepare($user_sql);
        $stmt->bind_param("i", $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            
            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                
                // Set all required session variables
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['phone_number'] = $user['phone_number'];
                $_SESSION['registration_date'] = $user['registration_date'];
                $_SESSION['role_type'] = $user['role_type'];
            }
        }
        
        $stmt->close();
    }
    
    // Now redirecting to admin dashboard with all session variables properly set
    header("Location: admin_dashboard.php");
    exit();
}

  // Initialize arrays to store form data and error messages
  $errors = [];
  $form_data = [];

  // Process login attempt when form is submitted
  if (isset($_POST['login'])) {
      
      // Capture and clean user input from the form
      $form_data['email'] = trim($_POST['email']);
      $plain_password = $_POST['password']; // Keep password plain text for verification - won't hash it yet!
      
      // Perform basic input validation before hitting the database
      if (empty($form_data['email'])) {
          $errors['email'] = "Email is required.";
      }
      
      if (empty($plain_password)) {
          $errors['password'] = "Password is required.";
      }
      
      // Only proceed with database operations if initial validation passes
      if (empty($errors)) {
          
           // Prepare SQL query to find user and their role
    // Join users table with user_roles table to get role information
    $login_sql = "SELECT u.*, ur.role_type
                  FROM users u 
                  LEFT JOIN user_roles ur ON u.user_id = ur.user_id 
                  WHERE u.email = ? LIMIT 1";
          
          // Prepare statement to prevent SQL injection attacks
          $stmt = $conn->prepare($login_sql);
          $stmt->bind_param("s", $form_data['email']);
          
          // Execute the database query
          if ($stmt->execute()) {
              // Fetch the result
              // This will return a result set containing the user data
              $result = $stmt->get_result();
              
              // Check if we found exactly one user with this email
              if ($result->num_rows == 1) {
                  // User exists - fetch their data from database
                  $user = $result->fetch_assoc();
                  
                  // Now verify the plain text password against the stored hash
                  // This is the secure way to check passwords - never store plain text passwords!
                  if (password_verify($plain_password, $user['password'])) {
                      // Check user role for admin dashboard access
                     if ($user['role_type'] == 'admin' || $user['role_type'] == 'seller') {
                        // Authentication successful! Create user session
                        $_SESSION['user_id'] = $user['user_id'];
                        $_SESSION['full_name'] = $user['full_name'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['phone_number'] = $user['phone_number'];
                        $_SESSION['registration_date'] = $user['registration_date'];
                        $_SESSION['role_type'] = $user['role_type']; // Store role in session
                        $_SESSION['login_status'] = true;
                        
                        // Redirect to admin dashboard
                        header("Location: admin_dashboard.php"); // Where admin area is
                        exit();
                    
                        } else {
                            // Customer role or no role - not allowed in admin area
                            $errors['general'] = "Access denied. Admin or seller privileges required.";
                        }
                  } 
                  else {
                      // Password verification failed - wrong password entered
                      $errors['password'] = "Invalid password.";
                  }
                  
              } else {
                  // No user found with this email address in our database
                  $errors['email'] = "No account found with this email address.";
              }
              
          } else {
              // Database query execution failed - could be server issue
              $errors['general'] = "Login failed. Please try again.";
          }
          
          // Clean up prepared statement
          $stmt->close();
      }
      
      // Close database connection
      $conn->close();
  }
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .admin-title {
            font-size: 3rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 2rem;
        }
        .form-control {
            border-radius: 15px;
            padding: 15px 20px;
            border: 2px solid #e9ecef;
            font-size: 16px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-admin {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            border-radius: 15px;
            padding: 15px;
            font-size: 18px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .icon-wrapper {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="login-card p-5">
                        <div class="text-center">
                            <div class="icon-wrapper">
                                <img class="logo" src="../assets/images/mLogo.png" alt="My shop" style="max-width: 100%; height: auto;">
                            </div>
                            <h1 class="admin-title">ADMIN</h1>
                            <p class="text-muted mb-4">Access your dashboard</p>
                        </div>
                        
                          <form action="login.php" method="POST">
                            <div class="mb-4">
                                <label for="email" class="form-label fw-bold">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0" style="border-radius: 15px 0 0 15px; border: 2px solid #e9ecef; border-right: none;">
                                        <i class="fas fa-user text-muted"></i>
                                    </span>
                                    <input type="email" class="form-control border-start-0" id="email" name="email" placeholder="Enter your email address" required style="border-radius: 0 15px 15px 0;">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label fw-bold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0" style="border-radius: 15px 0 0 15px; border: 2px solid #e9ecef; border-right: none;">
                                        <i class="fas fa-lock text-muted"></i>
                                    </span>
                                    <input type="password" class="form-control border-start-0" id="password" name="password" placeholder="Enter your password" required style="border-radius: 0 15px 15px 0;">
                                </div>
                            </div>
                            
                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>
                            
                            <button type="submit" class="btn btn-admin text-white w-100 mb-3" name="login">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Sign In
                            </button>
                            
                            <div class="text-center">
                                <a href="#" class="text-decoration-none">Forgot password?</a>
                            </div>
                         </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>