<?php

// Starting session to access cart data and user session
session_start();

// Checking if user has items in cart and came from proper checkout flow
if (!empty($_SESSION['cart']) && isset($_POST['checkout'])) {
    // Cart exists and user clicked checkout - proceeding with order
    $cart = $_SESSION['cart'];
} 
else {
    // No cart found or invalid access - redirecting back to homepage
    header("Location: index.php");
    exit(); // Adding exit to prevent further execution
}
?>

<?php include('layouts/header.php'); ?>
  
    <!-- Main checkout form section where customers enter shipping details -->
  <section class="my-5 py-5">
    <div class="container mt-5 py-5">
        <div class="row">
            <div class="row-col-lg-6 col-md-6 col-sm-12 mx-auto">
                <h2 class="text-center">Checkout</h2>
                <hr class="mx-auto">

                <!-- Checkout form posting to order processing script -->
                <form action="server/place_order.php" method="POST" class="shadow-lg p-4" id="checkout">
                    
                    <!-- Customer's full name for shipping -->
                    <div class="mb-3">
                        <label for="checkout-name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="checkout-name" name="checkout-name" maxlength="100" required>
                    </div>
                    
                    <!-- Email address for order confirmation -->
                    <div class="mb-3">
                        <label for="checkout-email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="checkout-email" name="checkout-email" maxlength="150" required>
                    </div>
                    
                    <!-- Phone number for delivery coordination -->
                    <div class="mb-3">
                        <label for="checkout-phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="checkout-phone" name="checkout-phone" maxlength="20" required>
                    </div>
                    
                    <!-- Primary shipping address -->
                    <div class="mb-3">
                        <label for="checkout-address" class="form-label">Shipping Address</label>
                        <input type="text" class="form-control" id="checkout-address" name="checkout-address" maxlength="200" required>
                    </div>
                    
                    <!-- City for shipping destination -->
                    <div class="mb-3">
                        <label for="checkout-city" class="form-label">City</label>
                        <input type="text" class="form-control" id="checkout-city" name="checkout-city" maxlength="100" required>
                    </div>
                    
                    <!-- State and postal code in split layout -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="checkout-state" class="form-label">State/Province</label>
                            <input type="text" class="form-control" id="checkout-state" name="checkout-state" maxlength="100" required>
                        </div>
                        <div class="col-md-6">
                            <label for="checkout-zip" class="form-label">Zip/Postal Code</label>
                            <input type="text" class="form-control" id="checkout-zip" name="checkout-zip" maxlength="20" required>
                        </div>
                    </div>
                    
                    <!-- Country selection dropdown -->
                    <div class="mb-3">
                        <label for="checkout-country" class="form-label">Country</label>
                        <select class="form-select" id="checkout-country" name="checkout-country" required>
                            <option value="" selected disabled>Select Country</option>
                            <option value="ZA">South Africa</option>
                            <option value="US">United States</option>
                            <option value="GB">United Kingdom</option>
                            <option value="AU">Australia</option>
                            <!-- Will expand country list based on shipping requirements -->
                        </select>
                    </div>

                    <!-- Order summary displaying total amount from cart session -->
                     <div class="mb-5 mt-5">
                        <div class="card p-3 bg-light">
                            <h4 class="card-title text-center fw-bold">Order Summary</h4>
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total Amount:</span>
                                <span id="order_total" class="fw-bold fs-5">R<?php echo isset($_SESSION['cart_total']) ? htmlspecialchars(number_format($_SESSION['cart_total'], 2), ENT_QUOTES, 'UTF-8') : '0.00'; ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment method selection - currently supporting card and PayPal -->
                    <div class="mb-4">
                        <label for="payment-method" class="form-label">Payment Method</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment-method" id="checkout-creditcard" value="credit-card" checked>
                            <label class="form-check-label" for="checkout-creditcard">
                                Credit/Debit Card
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment-method" id="checkout-paypal" value="paypal">
                            <label class="form-check-label" for="checkout-paypal">
                                PayPal
                            </label>
                        </div>
                    </div>
                    
                    <!-- Final submit button to process the order -->
                    <button type="submit" name="place_order" id="place_order" class="btn btn-primary w-100">Complete Purchase</button>
                    
                    <!-- Support link for customer assistance -->
                    <div class="mt-3 text-center">
                        <small>Have questions about your order? <a href="contact.php">Contact support</a></small>
                    </div>
                </form>
            </div>
        </div>
     </div>
  </section>
  
<?php include('layouts/footer.html'); ?>