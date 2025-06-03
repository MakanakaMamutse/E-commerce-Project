<?php


session_start();

if (!empty($_SESSION['cart']) &&  isset($_POST['checkout'])) {



    $cart = $_SESSION['cart'];
} 

else {
    header("Location: index.php");
}
?>

<?php include('layouts/header.php'); ?>
  
    <!--Checkout-->
  <section class="my-5 py-5">
    <div class="container mt-5 py-5">
        <div class="row">
            <div class="row-col-lg-6 col-md-6 col-sm-12 mx-auto">
                <h2 class="text-center">Checkout</h2>
                <hr class="mx-auto">

                <form action="server/place_order.php" method="POST" class="shadow-lg p-4" id="checkout">
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
                    <button type="submit" name="place_order"  id="place_order" class="btn btn-primary w-100">Complete Purchase</button>
                    <div class="mt-3 text-center">
                        <small>Have questions about your order? <a href="contact.php">Contact support</a></small>
                    </div>
                </form>
            </div>
        </div>
     </div>
  </section>
  
<?php include('layouts/footer.php'); ?>