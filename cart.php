<?php
// Include the database connection file
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
// session_destroy();   // Destroys the session
// session_start();     // Start a fresh session

// To be converted to an empty cart funtion / stops session from being corrupted
if(isset($_POST['reset_session'])) {
    // Clear only the cart data, not the entire session
    unset($_SESSION['cart']);
    unset($_SESSION['cart_total']);
    // Redirect to prevent resubmission
    header("Location: cart.php");
    exit();
}



  if(isset($_POST['add_to_cart'])) {
      
    //if user has already added items to the cart
    if(isset($_SESSION['cart'])) {

      //Checking if the product is already in the cart - will return array of those product ids
      $product_array_ids = array_column($_SESSION['cart'], 'product_id');

      if(!in_array($_POST['product_id'], $product_array_ids)) {
        //If the product is not in the cart, add it
        $product_id = $_POST['product_id'];
        $product_name = $_POST['product_name'];
        $product_price = $_POST['product_price']; 
        $product_image = $_POST['product_image'];
        $product_quantity = $_POST['product_quantity'];

        $product_array = array(
            'product_id' => $product_id,
            'product_name' => $product_name,
            'product_price' => $product_price,
            'product_image' => $product_image,
            'product_quantity' => $product_quantity
        );

        $_SESSION['cart'][$_POST['product_id']] = $product_array;
      }
      
      else {
        //If the product is already in the cart, update the quantity
        $product_id = $_POST['product_id'];
        $product_quantity = $_POST['product_quantity'];
        $_SESSION['cart'][$product_id]['product_quantity'] += $product_quantity;

        // Add later!
        //echo "<script>alert('Product is already in the cart. Quantity updated.')</script>";
        //echo "<script>window.location = 'index.php'</script>";
      }

    } //if user has not added items to the cart
    else {
        $product_id = $_POST['product_id'];
        $product_name = $_POST['product_name'];
        $product_price = $_POST['product_price']; 
        $product_image = $_POST['product_image'];
        $product_quantity = $_POST['product_quantity'];

        $product_array = array(
            'product_id' => $product_id,
            'product_name' => $product_name,
            'product_price' => $product_price,
            'product_image' => $product_image,
            'product_quantity' => $product_quantity
        );


        $_SESSION['cart'][$product_id] = $product_array;
        //Making key value pairs with array 
        // [ 2=>[] 3=>[] 4=>[] ]     42 => [ 'product_id'=>42, 'product_name'=>'Widget', ... ]
    }
    
    //Updating the cart total
    getCartTotal();
  
  } 

  //Remove product from cart
  else if(isset($_POST['remove_product'])) {
    // If the user wants to remove a product from the cart
    $product_id = $_POST['product_id'];
    unset($_SESSION['cart'][$product_id]);

    //Updating the cart total
    getCartTotal();
  } 

  else if(isset($_POST['edit_quantity'])) {
    // If the user wants to edit the quantity of a product in the cart
    // 🛒 Handle quantity update for a product in the cart
    // Get the product ID from the submitted form
    $product_id = $_POST['product_id'];

    // Get the new quantity input by the user
    $new_quantity = $_POST['product_quantity'];
    
    // Update the quantity of the specified product in the session cart - accesing the session variable/data
    $_SESSION['cart'][$product_id]['product_quantity'] = $new_quantity;

    //Updating the cart total
    getCartTotal();

    
  }


  /*
  else if(isset($_POST['checkout'])) {
    // If the user wants to checkout
    header("Location: checkout.php");
    exit();
  }
*/


  else{
    // If the form is not submitted, redirect to the shop page     ------- Later make it shop a cart with a messages of no items in the cart , add more logic
    // header("Location: login.php");
    // exit();
    
  }





  function getCartTotal() {
    // Calculate the total price of all items in the cart
    $total = 0;
    // Loop through each item in the cart and calculate the total
    foreach($_SESSION['cart'] as $key => $value) {
        $total += $value['product_price'] * $value['product_quantity'];
    }
    // Store the total in the session for later use
    // This is optional, but can be useful if you want to display the total in multiple places
    $_SESSION['cart_total'] = $total;
    return $total;
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

     <!--Cart Section-->  
     <section class="cart container my-5 py-5">
        <div class="container mt-5">
            <h2 class="font-weight-bolde">Your Cart</h2>
            <hr>
        </div>
        <table class="mt-5 pt-5">
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>

            <?php foreach($_SESSION['cart'] as $key => $value) { ?>


            <tr>
                <td>
                    <div class="product-info">
                        <img src="assets/<?php echo $value['product_image']; ?>" alt="Product Image" 
                        onerror="this.onerror=null; this.src='assets/images/Placeholder.png';">
                        <div>
                            <p><?php echo $value['product_name']; ?></p>
                            <small>Price: $<?php echo $value['product_price']; ?></small><br>

                            <form method="POST" action="cart.php">
                              <input type="hidden" name="product_id" value="<?php echo $value['product_id']; ?>">
                              <input type="submit" name="remove_product" class="remove-btn" value="Remove">

                            </form>
                            <!--<a href="#">Remove</a>-->
                        </div>
                    </div>
                </td>

               <!-- Quantity -->
                <td>
                  <form method="POST" action="cart.php">
                    <input type="hidden" name="product_id" value="<?php echo $value['product_id']; ?>">
                    <input type="number" name="product_quantity" value="<?php echo $value['product_quantity']; ?>" min="1">
                    <input type="submit" name="edit_quantity" class="edit-btn" value="Edit">
                  </form>
                </td>

                <!-- Total Price-->
                <td>
                  <span>$</span>
                  <span class="product-price"><?php echo $value['product_price'] * $value['product_quantity']; ?></span>
                </td>
            </tr>

            <?php } ?>


        </table>

        <div class="cart-total">
          <table>
            <tr>
              <td>Subtotal</td>
              <td> <?php echo "$ " . number_format($_SESSION['cart_total'], 2);?> </td>
            </tr>
            <tr>
              <td>Shipping</td>
              <td><?php echo "$ " . number_format($_SESSION['cart_total'] * 0.0825, 2);?> </td>  
          </table>
        </div>

        <div class="checkout-container">
          <form method="POST" action="checkout.php">
            <input type="hidden" name="cart_total" value="<?php echo $_SESSION['cart_total']; ?>">
            <input type="submit" name="checkout" class="checkout-btn" value="Checkout">
          </form>
          <!-- <button class="checkout-btn">Checkout</button> -->

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


  <!-- <form method="POST" action="cart.php">
    <input type="submit" name="reset_session" value="Reset Cart" class="btn btn-warning">
  </form> --> 
  <!-- to be added later as cart reset button -->