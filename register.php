<?php
session_start();

// Including database connection file
include('server/connection.php');

// Preventing already logged-in users from accessing registration page
// If someone's already logged in, we redirect them to their account page
if (isset($_SESSION['login_status']) && $_SESSION['login_status'] === true) {
    header("Location: account.php");
    exit();
}

// Setting up arrays to store form data and validation errors
$errors = [];
$form_data = [];

// Checking if the registration form was submitted
if (isset($_POST['register'])) {
    
    // Cleaning up form input by removing extra whitespace
    $form_data['name'] = trim($_POST['name']);
    $form_data['username'] = trim($_POST['username']);
    $form_data['email'] = trim($_POST['email']);
    $form_data['phone_number'] = trim($_POST['phone-number']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];
    
    // Starting validation process
    
    // Making sure both password fields match
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match.";
    }
    
    // Ensuring password meets minimum length requirement
    if (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters long.";
    }
    
    // Checking if email is already registered in our system
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
    
    // Verifying username isn't taken by another user
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

    // Validating phone number format - expecting exactly 10 digits
    if (!preg_match('/^[0-9]{10}$/', $form_data['phone_number'])) {
        $errors['phone_number'] = "Phone number must be 10 digits.";
    }
    
    // Processing registration if all validation passed
    if (empty($errors)) {
        
        // Hashing password for secure storage
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $registration_date = date("Y-m-d H:i:s");
        
        // Preparing SQL statement to create new user account
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
        
        // Attempting to create the user account
        if ($insert_stmt->execute()) {
            
            // Getting the newly created user's ID from database
            $user_id = $conn->insert_id;

            // Assigning default customer role to new user
            $role_sql = "INSERT INTO user_roles (user_id, role_type) VALUES (?, 'customer')";
            $role_stmt = $conn->prepare($role_sql);
            $role_stmt->bind_param("i", $user_id);
            $role_stmt->execute();
            $role_stmt->close();

            // Storing user information in session for automatic login
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $form_data['username'];
            $_SESSION['email'] = $form_data['email'];
            $_SESSION['login_status'] = true;
            
            // Redirecting to account page after successful registration
            header("Location: account.php");
            exit();
            
        } else {
            // Handling database insertion errors
            $errors['general'] = "Registration failed. Please try again.";
        }
        
        $insert_stmt->close();
    }
    
    $conn->close();
} 

?>

<?php include('layouts/header.php'); ?>
      <!--Registration Form Section-->
    <section class="my-5 py-5">
        <div class="container mt-5 py-5">
            <div class="row">
                <div class="row-col-lg-6 col-md-6 col-sm-12 mx-auto">
                    <h2 class="text-center">Register</h2>
                    <hr class="mx-auto">
                    <form action="register.php" method="POST" class="shadow-lg p-4">
                        
                        <!-- Displaying general error messages if registration failed -->
                        <?php if (isset($errors['general'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($errors['general'], ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Full Name Input Field -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" 
                                  class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
                                  id="name" 
                                  name="name" 
                                  value="<?php echo isset($form_data['name']) ? htmlspecialchars($form_data['name'], ENT_QUOTES, 'UTF-8') : ''; ?>"
                                  required>
                            <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Username Input Field -->
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" 
                                  class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" 
                                  id="username" 
                                  name="username" 
                                  value="<?php echo isset($form_data['username']) ? htmlspecialchars($form_data['username'], ENT_QUOTES, 'UTF-8') : ''; ?>"
                                  required>
                            <?php if (isset($errors['username'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($errors['username'], ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Email Address Input Field -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" 
                                  class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                  id="email" 
                                  name="email" 
                                  value="<?php echo isset($form_data['email']) ? htmlspecialchars($form_data['email'], ENT_QUOTES, 'UTF-8') : ''; ?>"
                                  required>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Phone Number Input Field -->
                        <div class="mb-3">
                            <label for="phone-number" class="form-label">Phone Number</label>
                            <input type="tel" 
                                  class="form-control <?php echo isset($errors['phone_number']) ? 'is-invalid' : ''; ?>" 
                                  id="phone-number" 
                                  name="phone-number" 
                                  value="<?php echo isset($form_data['phone_number']) ? htmlspecialchars($form_data['phone_number'], ENT_QUOTES, 'UTF-8') : ''; ?>"
                                  required>
                            <?php if (isset($errors['phone_number'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($errors['phone_number'], ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Password Input Field -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" 
                                  class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                                  id="password" 
                                  name="password" 
                                  required>
                            <?php if (isset($errors['password'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-text">Password must be at least 8 characters long.</div>
                        </div>
                        
                        <!-- Password Confirmation Field -->
                        <div class="mb-3">
                            <label for="confirm-password" class="form-label">Confirm Password</label>
                            <input type="password" 
                                  class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" 
                                  id="confirm-password" 
                                  name="confirm-password" 
                                  required>
                            <?php if (isset($errors['confirm_password'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($errors['confirm_password'], ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <button type="submit" class="register-btn" id="register-btn" name="register">Register</button>
                        
                        <!-- Link to login page for existing users -->
                        <div class="mt-3 text-center">
                            <small>Do you have an account? <a href="login.php">Login here</a></small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>


<?php include('layouts/footer.html'); ?>