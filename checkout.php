<?php


session_start();

if (!empty($_SESSION['cart']) &&  isset($_POST['checkout'])) {



    $cart = $_SESSION['cart'];
} 

else {
    header("Location: index.php");
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

    <style>
      .product img{
        width: 100%; 
        height: 350px; /* Fixed height for images */
        object-fit: contain; /* Ensures entire image is visible */
        object-position: center; /* Centers the image */
      }
    </style>
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

  <!--Checkout-->
  <section class="my-5 py-5">
    <div class="container mt-5 py-5">
        <div class="row">
            <div class="row-col-lg-6 col-md-6 col-sm-12 mx-auto">
                <h2 class="text-center">Checkout</h2>
                <hr class="mx-auto">

                <form action="checkout.php" method="POST" class="shadow-lg p-4" id="checkout">
                    <div class="mb-3">
                        <label for="checkout-name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="checkout-name" name="checkout-name" required>
                    </div>
                    <div class="mb-3">
                        <label for="checkout-email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="checkout-email" name="checkout-email" required>
                    </div>
                    <div class="mb-3">
                        <label for="checkout-phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="checkout-phone" name="checkout-phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="checkout-address" class="form-label">Shipping Address</label>
                        <input type="text" class="form-control" id="checkout-address" name="checkout-address" required>
                    </div>
                    <div class="mb-3">
                        <label for="checkout-city" class="form-label">City</label>
                        <input type="text" class="form-control" id="checkout-city" name="checkout-city" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="checkout-state" class="form-label">State/Province</label>
                            <input type="text" class="form-control" id="checkout-state" name="checkout-state" required>
                        </div>
                        <div class="col-md-6">
                            <label for="checkout-zip" class="form-label">Zip/Postal Code</label>
                            <input type="text" class="form-control" id="checkout-zip" name="checkout-zip" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="checkout-country" class="form-label">Country</label>
                        <select class="form-select" id="checkout-country" name="checkout-country" required>
                            <option value="" selected disabled>Select Country</option>
                            <option value="ZA">South Africa</option>
                            <option value="CA">United States</option>
                            <option value="UK">United Kingdom</option>
                            <option value="AU">Australia</option>
                            <!-- I'll add more countires as needed -->
                        </select>
                    </div>

                     <div class="mb-5 mt-5">
                        <div class="card p-3 bg-light">
                            <h4 class="card-title text-center fw-bold">Order Summary</h4>
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total Amount:</span>
                                <span id="order_total"  class="fw-bold fs-5" >$<?php echo isset($_SESSION['cart_total']) ? number_format($_SESSION['cart_total'], 2) : '0.00'; ?></span>
                            </div>
                        </div>
                    </div>


                    <div class="mb-4">
                        <label for="checkout-payment" class="form-label">Payment Method</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="checkout-payment" id="checkout-creditcard" value="credit-card" checked>
                            <label class="form-check-label" for="checkout-creditcard">
                                Credit Card
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="checkout-payment" id="checkout-paypal" value="paypal">
                            <label class="form-check-label" for="checkout-paypal">
                                PayPal
                            </label>
                        </div>
                    </div>
                    <button type="submit" name="place_order"  id="place_order" class="btn btn-primary w-100">Complete Purchase</button>
                    <div class="mt-3 text-center">
                        <small>Have questions about your order? <a href="contact.php">Contact support</a></small>
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