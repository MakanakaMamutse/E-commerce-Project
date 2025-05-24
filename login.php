<?php


session_start();

  // Include database connection
  include('server/connection.php');

  //Stops logged in users from login again/ accessing the login page
  if (isset($_SESSION['login_status']) && $_SESSION['login_status'] === true) {
      // User is already logged in, redirect to account page
      header("Location: account.php");
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
          
          // Prepare SQL query to find user by email address only
          // We don't include password in WHERE clause because we need to verify it separately
          $login_sql = "SELECT user_id, username, email, password FROM users WHERE email = ? LIMIT 1";
          
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
                      
                      // Authentication successful! Create user session to keep them logged in
                      $_SESSION['user_id'] = $user['user_id'];
                      $_SESSION['username'] = $user['username'];
                      $_SESSION['email'] = $user['email'];
                      $_SESSION['login_status'] = true;
                      
                      // Redirect user to main page after successful login
                      header("Location: index.php");
                      exit(); // Stop script execution after redirect
                      
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
              <a class="nav-link" href="index.php">Home</a>
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
              <a href="cart.php"><i class="fas fa-shopping-bag"></i></a>
              <a href="account.php"><i class="fas fa-user"></i></a>
            </li>


          </ul>
        </div>
      </div>
    </nav>


  <!--Login-->
  <section class="my-5 py-5">
      <div class="container mt-5 py-5">
          <div class="row">
              <div class="row-col-lg-6 col-md-6 col-sm-12 mx-auto">
                  <h2 class="text-center">Login</h2>
                  <hr class="mx-auto">
                  <form action="login.php" method="POST" class="shadow-lg p-4">
                      
                      <!-- General error message -->
                      <?php if (isset($errors['general'])): ?>
                          <div class="alert alert-danger" role="alert">
                              <?php echo htmlspecialchars($errors['general']); ?>
                          </div>
                      <?php endif; ?>
                      
                      <!-- Email Field -->
                      <div class="mb-3">
                          <label for="email" class="form-label">Email address</label>
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
                      </div>
                      
                      <button type="submit" class="login-btn" name="login" id="login-btn">Login</button>
                      
                      <div class="mt-3 text-center">
                          <small>Don't have an account? <a href="register.php">Register here</a></small>
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