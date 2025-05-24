<?php
session_start();

// Include database connection
include('server/connection.php');

//Stops logged in users from registering again/ accessing the register page
  if (isset($_SESSION['login_status']) && $_SESSION['login_status'] === true) {
      // User is already logged in, redirect to account page
      header("Location: account.php");
      exit();
  }

// Initialize variables for form data and errors
$errors = [];
$form_data = [];

// Check if registration form was submitted - You clicked the register button
if (isset($_POST['register'])) {
    
    // Sanitize and store form input
    $form_data['name'] = trim($_POST['name']);
    $form_data['username'] = trim($_POST['username']);
    $form_data['email'] = trim($_POST['email']);
    $form_data['phone_number'] = trim($_POST['phone-number']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];
    
    // Validate form input
    
    // Check if passwords match
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match.";
    }
    
    // Check password length
    if (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters long.";
    }
    
    // Check if email already exists in database
    if (empty($errors)) {
        $check_email_sql = "SELECT COUNT(*) FROM users WHERE email = ?";
        $check_stmt = $conn->prepare($check_email_sql);
        $check_stmt->bind_param("s", $form_data['email']);
        $check_stmt->execute();
        $check_stmt->bind_result($email_count);
        $check_stmt->fetch();
        $check_stmt->close();
        
        if ($email_count > 0) {
            $errors['email'] = "An account with this email already exists.";
        }
    }
    
    // Check if username already exists in database
    if (empty($errors)) {
        $check_username_sql = "SELECT COUNT(*) FROM users WHERE username = ?";
        $check_stmt = $conn->prepare($check_username_sql);
        $check_stmt->bind_param("s", $form_data['username']);
        $check_stmt->execute();
        $check_stmt->bind_result($username_count);
        $check_stmt->fetch();
        $check_stmt->close();
        
        if ($username_count > 0) {
            $errors['username'] = "This username is already taken.";
        }
    }

    // Validate phone number format
    if (!preg_match('/^[0-9]{10}$/', $form_data['phone_number'])) {
        $errors['phone_number'] = "Phone number must be 10 digits.";
    }
    
    // If no validation errors, proceed with registration
    if (empty($errors)) {
        
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $registration_date = date("Y-m-d H:i:s");
        
        // Prepare SQL statement to insert new user
        $insert_sql = "INSERT INTO users (full_name, username, email, phone_number, password, registration_date) 
                       VALUES (?, ?, ?, ?, ?, ?)";
        
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ssssss", 
            $form_data['name'], 
            $form_data['username'], 
            $form_data['email'], 
            $form_data['phone_number'], 
            $hashed_password, 
            $registration_date
        );
        
        // Execute the insert statement
        if ($insert_stmt->execute()) {
            
            $user_id = $conn->insert_id; // Get the newly created user ID from the database
            
            // Storung user ID in session
            $_SESSION['user_id'] = $user_id;
            
            // Registration successful - set session variables
            $_SESSION['username'] = $form_data['username'];
            $_SESSION['user_email'] = $form_data['email'];
            $_SESSION['login_status'] = true;
            
            // Redirect to account page
            header("Location: account.php");
            exit();
            
        } else {
            // Database error occurred
            $errors['general'] = "Registration failed. Please try again.";
        }
        
        $insert_stmt->close();
    }
    
    $conn->close();
} 


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous"/>

    <link rel="stylesheet" href="assets/css/style.css"/>

</head>

<body>

    <!--Navbar-->
    <nav class="navbar navbar-expand-lg navbar-light bg-white py-3 fixed-top">
      <div class="container-fluid">
        <img class="logo" src="assets/images/mLogo.png" alt="My shop">
        <h2 class="brand">M&M Sports</h2>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse nav-buttons" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            
            <li class="nav-item">
              <a class="nav-link" href="index.html">Home</a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="shop.html">Shop</a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="#">Blog</a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="#">Contact Us</a>
            </li>
   
            <li class="nav-item">
              <a href="cart.html"><i class="fas fa-shopping-bag"></i></a>
              <a href="account.html"><i class="fas fa-user"></i></a>
            </li>


          </ul>
        </div>
      </div>
    </nav>


    <!--Register-->
<section class="my-5 py-5">
    <div class="container mt-5 py-5">
        <div class="row">
            <div class="row-col-lg-6 col-md-6 col-sm-12 mx-auto">
                <h2 class="text-center">Register</h2>
                <hr class="mx-auto">
                <form action="register.php" method="POST" class="shadow-lg p-4">
                    
                    <!-- General error message -->
                    <?php if (isset($errors['general'])): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($errors['general']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Full Name Field -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" 
                               class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
                               id="name" 
                               name="name" 
                               value="<?php echo isset($form_data['name']) ? htmlspecialchars($form_data['name']) : ''; ?>"
                               required>
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback">
                                <?php echo htmlspecialchars($errors['name']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Username Field -->
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" 
                               class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" 
                               id="username" 
                               name="username" 
                               value="<?php echo isset($form_data['username']) ? htmlspecialchars($form_data['username']) : ''; ?>"
                               required>
                        <?php if (isset($errors['username'])): ?>
                            <div class="invalid-feedback">
                                <?php echo htmlspecialchars($errors['username']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Email Field -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" 
                               class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                               id="email" 
                               name="email" 
                               value="<?php echo isset($form_data['email']) ? htmlspecialchars($form_data['email']) : ''; ?>"
                               required>
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback">
                                <?php echo htmlspecialchars($errors['email']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Phone Number Field -->
                    <div class="mb-3">
                        <label for="phone-number" class="form-label">Phone Number</label>
                        <input type="tel" 
                               class="form-control <?php echo isset($errors['phone_number']) ? 'is-invalid' : ''; ?>" 
                               id="phone-number" 
                               name="phone-number" 
                               value="<?php echo isset($form_data['phone_number']) ? htmlspecialchars($form_data['phone_number']) : ''; ?>"
                               required>
                        <?php if (isset($errors['phone_number'])): ?>
                            <div class="invalid-feedback">
                                <?php echo htmlspecialchars($errors['phone_number']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Password Field -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" 
                               class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                               id="password" 
                               name="password" 
                               required>
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback">
                                <?php echo htmlspecialchars($errors['password']); ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-text">Password must be at least 8 characters long.</div>
                    </div>
                    
                    <!-- Confirm Password Field -->
                    <div class="mb-3">
                        <label for="confirm-password" class="form-label">Confirm Password</label>
                        <input type="password" 
                               class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" 
                               id="confirm-password" 
                               name="confirm-password" 
                               required>
                        <?php if (isset($errors['confirm_password'])): ?>
                            <div class="invalid-feedback">
                                <?php echo htmlspecialchars($errors['confirm_password']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" class="register-btn" id="register-btn" name="register">Register</button>
                    
                    <div class="mt-3 text-center">
                        <small>Do you have an account? <a href="login.php">Login here</a></small>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>











    <!--Footer-->
    <footer class="mt-5 py-5">

        <div class="row container mx-auto py-5">
          <div class="footer-one col-lg-3 col-md-6 col-sm-12">
            <img src="assets/images/yy.png"/>
            <p class="pt-3">We provide the best products</p>
          </div>
  
          <div class="footer-one col-lg-3 col-md-6 col-sm-12">
            <h5 class="pt-2">Featured</h5>
            <ul class="text-uppercase">
              <li><a href="#">Shoes</a></li>
              <li><a href="#">Shirts</a></li>
              <li><a href="#">Gear</a></li>
            </ul>
          </div>
  
          <div class="footer-one col-lg-3 col-md-6 col-sm-12">
            <h5 class="pt-2">Contact Us</h5>
            <div> 
              <h6 class="text-uppercase">Address</h6>
              <p>1234 Street Name</p>
            </div>
            <div> 
              <h6 class="text-uppercase">Phone</h6>
              <p>00 00 XXX</p>
            </div>
            <div> 
              <h6 class="text-uppercase">Email</h6>
              <p>dummy@mail.com</p>
            </div>>
          </div>
  
          <div class="footer-one col-lg-3 col-md-6 col-sm-12">
            <h5 class="pb-2">Instagram</h5>
            <div class="row">
              <img class="img-fluid w-25 h-100 m-2" src="assets/images/featured1.jpg"/>
              <img class="img-fluid w-25 h-100 m-2" src="assets/images/featured2.jpg"/>
              <img class="img-fluid w-25 h-100 m-2" src="assets/images/featured3.jpg"/>
  
          </div>
        </div>
  
        <div class="copyright mt-5">
          <div class ="row container mx-auto">
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
              <img src="assets/images/payment.png" alt="My Shop" height="40"> <!--Payjpg-->
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4 text-nowrap mb-2">
              <p>&copy; 2025 My Shop. All Rights Reserved</p>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
              <a href="#"><i class="fab fa-facebook"></i></a>
              <a href="#"><i class="fab fa-instagram"></i></a>
              <a href="#"><i class="fab fa-twitter"></i></a>
            </div>
          </div>
        </div>
  
      </footer>
  
  
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  
  </body>
  </html>