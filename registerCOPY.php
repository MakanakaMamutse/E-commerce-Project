<?php

session_start();

// Database connection
include('server/connection.php');


  if(isset($_POST['register'])) {

      // Get user input
      $name = $_POST['name'];
      $username = $_POST['username'];
      $email = $_POST['email'];
      $phone_number = $_POST['phone-number'];
      $password = $_POST['password'];
      $confirm_password = $_POST['confirm-password'];

      $order_date   = date("Y-m-d H:i:s");

      if($password !== $confirm_password) {
          echo "Passwords do not match.";
          exit();
      }

      else if(strlen($password) < 8) {
          //echo "Password must be at least 8 characters long.";
          header("Location: register.php?error=Password must be at least 8 characters long");
          //exit();
      }

      //if there are no errors, proceed with registration
      else {
            // Hash the password
          $hashed_password = password_hash($password, PASSWORD_DEFAULT);

          //Check if user already exists
          $sql = "SELECT COUNT(*) FROM users WHERE email = ?";
          $stmt = $conn->prepare($sql);
          $stmt->bind_param("s", $email);
          $stmt->execute();
          $stmt->bind_result($num_rows);
          $stmt->store_result();
          $stmt->fetch();
          
          //if user already exists, display error message
          if($num_rows != 0) {
              echo "User already exists.";
              exit();
          }//no user exists with that email, proceed with registration
          else {
          // Insert into database
          $sql = "INSERT INTO users (full_name, username, email, phone_number, password, registration_date) 
                    VALUES (?, ?, ?, ?, ?, ?)";

          $stmt = $conn->prepare($sql);

          $stmt->bind_param("ssssss", $name, $username, $email, $phone_number, $hashed_password, $order_date);

          //when accoutn created succesfully
          if($stmt->execute()) {

            //Store user data in the session
            $_SESSION['user_name']= $name;
            $_SESSION['user_email']= $email;
            $_SESSION['login_status']= true;
              echo "Registration successful!";
              header("Location: account.php");
              exit();
          } //account not created
          else {
              header("Location: register.php?error=Account not created");
              echo "Error: " . $stmt->error;
          }

          $stmt->close();
          $conn->close();
        }
      }

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
                      <p style="color: red;"><?php 
                        if(isset($_GET['error'])){echo $_GET['error']; }; ?></p>
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone-number" class="form-label">Phone Number</label>
                            <input type="phone" class="form-control" id="phone-number" name="phone-number" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="password" name="confirm-password" required>
                        </div>
                        <button type="submit" class="register-btn" id="register-btn" name="register">Register</button>
                        <div class="mt-3 text-center">
                            <small>Do you have an account? <a href="login.html">Login here</a></small>
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