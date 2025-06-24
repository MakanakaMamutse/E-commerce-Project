<?php

  session_start();
  // Connecting to our database
  include('server/connection.php');

  // Making sure we have a valid order ID from the URL and it's actually a number
  if (isset($_GET['order_id']) && is_numeric($_GET['order_id'])) {
      $order_id = $_GET['order_id'];

      // Getting basic order information from the database
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

      // Checking if we actually found an order with this ID
      if (!$order_info) {
          echo "<h1>Order not found.</h1>";
          exit();
      }

      // Grabbing the current order status for later use
      $order_status = $order_info['order_status'];

      // Building complex query to get all item details with product info and images
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

      // Executing the query to get all items in this order
      $stmt_order_items->bind_param("i", $order_id);
      $stmt_order_items->execute();
      $order_items_result = $stmt_order_items->get_result();
      $stmt_order_items->close();
  }

?>

<?php include('layouts/header.php'); ?>

<!--Order Details Display Section-->  
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
        // Looping through each item in the order and displaying it safely
        if ($order_items_result->num_rows > 0) {
            while ($item = $order_items_result->fetch_assoc()) { ?>
                <tr>
                    <td>
                        <div class="product-info">
                            <!-- Displaying product image with fallback and proper escaping -->
                            <img src="assets/<?php echo htmlspecialchars($item['image_url'], ENT_QUOTES, 'UTF-8'); ?>" 
                                 alt="<?php echo htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8'); ?>" 
                                 onerror="this.onerror=null; this.src='assets/images/Placeholder.png';">
                            <div>
                                <!-- Product name with XSS protection -->
                                <p><?php echo htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <!-- Price at time of purchase, formatted for currency display -->
                                <small>Price: R<?php echo htmlspecialchars(number_format($item['price_at_purchase'], 2), ENT_QUOTES, 'UTF-8'); ?></small><br>
                            </div>
                        </div>
                    </td>
                    <td>
                        <!-- Quantity ordered, safely escaped -->
                        <span class="product-quantity"><?php echo htmlspecialchars($item['quantity'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </td>
                    <td>
                        <!-- Individual item price at time of purchase -->
                        <span class="price-at-purchase">R<?php echo htmlspecialchars(number_format($item['price_at_purchase'], 2), ENT_QUOTES, 'UTF-8'); ?></span>
                    </td>
                    <td>
                        <!-- Total cost for this line item (quantity × price) -->
                        <span class="item-total-price">R<?php echo htmlspecialchars(number_format($item['item_subtotal'], 2), ENT_QUOTES, 'UTF-8'); ?></span>
                    </td>
                </tr>
            <?php }
        } else { ?>
            <!-- Fallback message when no items are found -->
            <tr>
                <td colspan="4">No items found for this order.</td>
            </tr>
        <?php } ?>
    </table>

  <!-- Payment button section - only showing if order hasn't been paid yet -->
    <?php if ($order_info['order_status'] === 'Unpaid'): ?>
      <div class="mt-4" style="text-align: right;">
          <!-- Secure payment link with properly escaped parameters -->
          <a href="payment.php?order_id=<?php echo htmlspecialchars($order_id, ENT_QUOTES, 'UTF-8'); ?>&order_status=<?php echo htmlspecialchars($order_info['order_status'], ENT_QUOTES, 'UTF-8'); ?>" 
            class="btn btn-primary btn-lg">Pay Now</a>
      </div>
    <?php endif; ?>

</section>

<?php include('layouts/footer.html') ?>