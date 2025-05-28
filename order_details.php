<?php

  session_start();
  // Database connection
  include('server/connection.php');

  // Check if the GET parameter is set to prevent errors and making sure it's numeric
  if (isset($_GET['order_id']) && is_numeric($_GET['order_id'])) {
      $order_id = $_GET['order_id'];

      // Fetch general order details
      $sql_order_details = "SELECT * FROM orders WHERE order_id = ?";
      $stmt_order_details = $conn->prepare($sql_order_details);

      if ($stmt_order_details === false) {
          error_log("Failed to prepare order details statement: " . $conn->error);
          die("An internal error occurred. Please try again later.");
      }

      $stmt_order_details->bind_param("i", $order_id);
      $stmt_order_details->execute();
      $order_info = $stmt_order_details->get_result()->fetch_assoc();
      $stmt_order_details->close();

      // Check if order exists for this ID
      if (!$order_info) {
          echo "<h1>Order not found.</h1>";
          exit();
      }

      // Extract order status
      $order_status = $order_info['order_status'];

      // Prepare the SQL statement for order items with joins
      $sql_order_items_details = " SELECT
              oi.quantity,
              p.product_name,
              p.price AS current_product_price,
              oi.price_at_purchase,
              oi.subtotal AS item_subtotal,
              pi.image_url
          FROM
              order_items oi
          INNER JOIN
              products p ON oi.product_id = p.product_id
          LEFT JOIN
              product_images pi ON p.product_id = pi.product_id
          WHERE
              oi.order_id = ?;
      ";

      $stmt_order_items = $conn->prepare($sql_order_items_details);

      if ($stmt_order_items === false) {
          error_log("Failed to prepare order items details statement: " . $conn->error);
          die("An internal error occurred while fetching order items. Please try again later.");
      }

      // Fixed: Use $order_id instead of undefined $order_id_to_display
      $stmt_order_items->bind_param("i", $order_id);
      $stmt_order_items->execute();
      $order_items_result = $stmt_order_items->get_result();
      $stmt_order_items->close();
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


<!--Orders Details Section-->  
<section id="order" class="cart container my-5 py-5">
    <div class="container mt-5">
        <h2 class="font-weight-bold">Order Details</h2>
        <hr>
    </div>
    
    <table class="mt-5 pt-5">
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Price @Purchase</th>
            <th>Total Price</th>
        </tr>

        <?php
        if ($order_items_result->num_rows > 0) {
            while ($item = $order_items_result->fetch_assoc()) { ?>
                <tr>
                    <td>
                        <div class="product-info">
                            <img src="assets/<?php echo htmlspecialchars($item['image_url']); ?>" alt="Product Image">
                            <div>
                                <p><?php echo htmlspecialchars($item['product_name']); ?></p>
                                <small>Price: $<?php echo number_format($item['price_at_purchase'], 2); ?></small><br>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="product-quantity"><?php echo htmlspecialchars($item['quantity']); ?></span>
                    </td>
                    <td>
                        <span class="price-at-purchase">$<?php echo number_format($item['price_at_purchase'], 2); ?></span>
                    </td>
                    <td>
                        <span class="item-total-price">$<?php echo number_format($item['item_subtotal'], 2); ?></span>
                    </td>
                </tr>
            <?php }
        } else { ?>
            <tr>
                <td colspan="4">No items found for this order.</td>
            </tr>
        <?php } ?>
    </table>

  <!-- Display additional order information -->
    <?php if ($order_info['order_status'] === 'Unpaid'): ?>
      <div class="mt-4" style="text-align: right;">
          <a href="payment.php?order_id=<?php echo htmlspecialchars($order_id); ?>&order_status=<?php echo htmlspecialchars($order_info['order_status']); ?>" 
            class="btn btn-primary btn-lg">Pay Now</a>
      </div>
    <?php endif; ?>

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