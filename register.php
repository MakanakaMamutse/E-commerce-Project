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

<?php include('layouts/header.php'); ?>
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

    
<?php include('layouts/footer.php'); ?>