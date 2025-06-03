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

<?php include('layouts/header.php'); ?>
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


  <!-- Javascript to handle login required alert - uses SweetAlert2 for better UX -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      if (new URLSearchParams(window.location.search).get('login_required')) {
          Swal.fire({
              icon: 'warning',
              title: 'Login Required',
              text: 'Please login to complete your order',
              confirmButtonText: 'OK'
          });
      }
    </script>

<?php include('layouts/footer.php'); ?>
  