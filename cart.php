<?php

// Starting session to manage user cart data
session_start();

// Making sure cart session variables exist to prevent undefined errors
if(!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}
if(!isset($_SESSION['cart_total'])) {
    $_SESSION['cart_total'] = 0;
}

// Emergency cart reset function - clearing corrupted cart data
if(isset($_POST['reset_session'])) {
    // Removing only cart-related session data, keeping user login intact
    unset($_SESSION['cart']);
    unset($_SESSION['cart_total']);
    // Redirecting to prevent form resubmission on page refresh
    header("Location: cart.php");
    exit();
}

// Processing new item additions to the shopping cart
if(isset($_POST['add_to_cart'])) {
    
    // Sanitizing all incoming product data to prevent XSS attacks
    $product_id = filter_var($_POST['product_id'], FILTER_VALIDATE_INT);
    $product_name = htmlspecialchars(trim($_POST['product_name']), ENT_QUOTES, 'UTF-8');
    $product_price = filter_var($_POST['product_price'], FILTER_VALIDATE_FLOAT);
    $product_image = htmlspecialchars(trim($_POST['product_image']), ENT_QUOTES, 'UTF-8');
    $product_quantity = filter_var($_POST['product_quantity'], FILTER_VALIDATE_INT);
    
    // Validating that all required data is present and valid
    if($product_id === false || $product_price === false || $product_quantity === false || 
       empty($product_name) || empty($product_image) || $product_quantity < 1 || $product_quantity > 10) {
        // Invalid data - redirecting back with error
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
      
    // Checking if user already has items in their cart
    if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {

        // Getting list of product IDs already in cart for duplicate checking
        $product_array_ids = array_column($_SESSION['cart'], 'product_id');

        if(!in_array($product_id, $product_array_ids)) {
            // Product isn't in cart yet - adding as new item
            $product_array = array(
                'product_id' => $product_id,
                'product_name' => $product_name,
                'product_price' => $product_price,
                'product_image' => $product_image,
                'product_quantity' => $product_quantity
            );

            $_SESSION['cart'][$product_id] = $product_array;
        }
        else {
            // Product already exists - updating quantity instead of duplicating
            $_SESSION['cart'][$product_id]['product_quantity'] += $product_quantity;
            
            // Future enhancement: adding user notification about quantity update
            //echo "<script>alert('Product is already in the cart. Quantity updated.')</script>";
            //echo "<script>window.location = 'index.php'</script>";
        }

    } 
    else {
        // First item being added to empty cart
        $product_array = array(
            'product_id' => $product_id,
            'product_name' => $product_name,
            'product_price' => $product_price,
            'product_image' => $product_image,
            'product_quantity' => $product_quantity
        );

        $_SESSION['cart'][$product_id] = $product_array;
        // Creating key-value pairs: [ 2=>[] 3=>[] 4=>[] ] where 42 => [ 'product_id'=>42, 'product_name'=>'Widget', ... ]
    }
    
    // Recalculating total after adding item
    getCartTotal();
  
} 

// Handling product removal from cart
else if(isset($_POST['remove_product'])) {
    // Validating product ID before removal
    $product_id = filter_var($_POST['product_id'], FILTER_VALIDATE_INT);
    
    if($product_id !== false && isset($_SESSION['cart'][$product_id])) {
        // Removing the specified product from cart session
        unset($_SESSION['cart'][$product_id]);
        
        // Updating cart total after removal
        getCartTotal();
    }
} 

// Processing quantity changes for existing cart items
else if(isset($_POST['edit_quantity'])) {
    // Getting and validating the product ID and new quantity
    $product_id = filter_var($_POST['product_id'], FILTER_VALIDATE_INT);
    $new_quantity = filter_var($_POST['product_quantity'], FILTER_VALIDATE_INT);
    
    // Ensuring valid data and reasonable quantity limits
    if($product_id !== false && $new_quantity !== false && $new_quantity >= 1 && $new_quantity <= 10 && isset($_SESSION['cart'][$product_id])) {
        // Updating the quantity for the specified product in cart
        $_SESSION['cart'][$product_id]['product_quantity'] = $new_quantity;
        
        // Recalculating total with new quantity
        getCartTotal();
    }
}

/*
// Placeholder for future checkout processing
else if(isset($_POST['checkout'])) {
    // Redirecting to checkout page when user proceeds to payment
    header("Location: checkout.php");
    exit();
}
*/

else {
    // No valid form submission detected - could redirect to shop page
    // Future enhancement: redirect with "no items in cart" message
    // header("Location: login.php");
    // exit();
}

// Function calculating total price of all items in shopping cart
function getCartTotal() {
    // Starting with zero total
    $total = 0;
    
    // Making sure cart exists before processing
    if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        // Looping through each cart item to calculate running total
        foreach($_SESSION['cart'] as $key => $value) {
            // Converting price and quantity to proper numeric types for calculation
            $price = floatval($value['product_price']);
            $quantity = intval($value['product_quantity']);
            
            $total += $price * $quantity;
        }
    }
    
    // Storing calculated total in session for use across pages
    $_SESSION['cart_total'] = $total;
    return $total;
}
?>

<?php include('layouts/header.php'); ?>

     <!-- Shopping cart display section showing user's selected items -->  
     <section class="cart container my-5 py-5">
        <div class="container mt-5">
            <h2 class="font-weight-bolder">Your Cart</h2>
            <hr>
        </div>

        <?php if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) { ?>
          <!-- Cart contains items - displaying full cart table -->
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
                          <img src="assets/<?php echo htmlspecialchars($value['product_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($value['product_name'], ENT_QUOTES, 'UTF-8'); ?>" 
                          onerror="this.onerror=null; this.src='assets/images/Placeholder.png';">
                          <div>
                              <p><?php echo htmlspecialchars($value['product_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                              <small>Price: $<?php echo htmlspecialchars(number_format($value['product_price'], 2), ENT_QUOTES, 'UTF-8'); ?></small><br>

                              <!-- Form for removing individual products from cart -->
                              <form method="POST" action="cart.php">
                                <input type="hidden" name="product_id" value="<?php echo (int)$value['product_id']; ?>">
                                <input type="submit" name="remove_product" class="remove-btn" value="Remove">
                              </form>
                          </div>
                      </div>
                  </td>

                <!-- Quantity adjustment controls -->
                  <td>
                    <form method="POST" action="cart.php">
                      <input type="hidden" name="product_id" value="<?php echo (int)$value['product_id']; ?>">
                      <input type="number" name="product_quantity" value="<?php echo (int)$value['product_quantity']; ?>" min="1" max="10">
                      <input type="submit" name="edit_quantity" class="edit-btn" value="Edit">
                    </form>
                  </td>

                  <!-- Calculated line total for this product -->
                  <td>
                    <span class="product-price">$<?php echo htmlspecialchars(number_format($value['product_price'] * $value['product_quantity'], 2), ENT_QUOTES, 'UTF-8'); ?></span>
                  </td>
              </tr>

              <?php } ?>

          </table>

          <!-- Cart totals breakdown showing subtotal and shipping costs -->
          <div class="cart-total">
            <table>
              <tr>
                <td>Subtotal</td>
                <td>$<?php echo htmlspecialchars(number_format($_SESSION['cart_total'], 2), ENT_QUOTES, 'UTF-8'); ?></td>
              </tr>
              <tr>
                <td>Shipping</td>
                <td>$<?php echo htmlspecialchars(number_format($_SESSION['cart_total'] * 0.0825, 2), ENT_QUOTES, 'UTF-8'); ?></td>  
              </tr>
            </table>
          </div>

          <!-- Checkout button leading to payment processing -->
          <div class="checkout-container">
            <form method="POST" action="checkout.php">
              <input type="hidden" name="cart_total" value="<?php echo htmlspecialchars($_SESSION['cart_total'], ENT_QUOTES, 'UTF-8'); ?>">
              <input type="submit" name="checkout" class="checkout-btn" value="Checkout">
            </form>
          </div>
        <?php } else { ?>
            <!-- Empty cart state - encouraging users to browse products -->
            <div class="empty-cart mt-5 pt-5 text-center">
                <h4>Your cart is empty</h4>
                <p>Looks like you haven't added any items to your cart yet.</p>
                <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        <?php } ?>

     </section>

<?php include('layouts/footer.html'); ?>

  <!-- Emergency cart reset functionality - hidden for production use -->
  <!-- <form method="POST" action="cart.php">
    <input type="submit" name="reset_session" value="Reset Cart" class="btn btn-warning">
  </form> --> 
  <!-- Future feature: adding cart reset button for user convenience -->