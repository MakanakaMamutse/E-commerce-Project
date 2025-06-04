<?php
// Include the database connection file
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Initialize cart session variables if they don't exist
if(!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}
if(!isset($_SESSION['cart_total'])) {
    $_SESSION['cart_total'] = 0;
}

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
    if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {

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
        $product_price = floatval($_POST['product_price']); 
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
    
    // Check if cart exists and is not empty
    if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        // Loop through each item in the cart and calculate the total
        foreach($_SESSION['cart'] as $key => $value) {
            // Convert price and quantity to proper numeric types before calculation
            $price = floatval($value['product_price']);
            $quantity = intval($value['product_quantity']);
            
            $total += $price * $quantity;
        }
    }
    
    // Store the total in the session for later use
    // This is optional, but can be useful if you want to display the total in multiple places
    $_SESSION['cart_total'] = $total;
    return $total;
}
  ?>

<?php include('layouts/header.php'); ?>

     <!--Cart Section-->  
     <section class="cart container my-5 py-5">
        <div class="container mt-5">
            <h2 class="font-weight-bolder">Your Cart</h2>
            <hr>
        </div>

        <?php if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) { ?>
        <!-- Cart has items - show the table -->
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
                    <input type="number" name="product_quantity" value="<?php echo $value['product_quantity']; ?>" min="1" max="10">
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
        </div>
        <?php } else { ?>
            <!-- Cart is empty - show empty cart message -->
            <div class="empty-cart mt-5 pt-5 text-center">
                <h4>Your cart is empty</h4>
                <p>Looks like you haven't added any items to your cart yet.</p>
                <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        <?php } ?>

     </section>

<?php include('layouts/footer.php'); ?>

  <!-- <form method="POST" action="cart.php">
    <input type="submit" name="reset_session" value="Reset Cart" class="btn btn-warning">
  </form> --> 
  <!-- to be added later as cart reset button -->