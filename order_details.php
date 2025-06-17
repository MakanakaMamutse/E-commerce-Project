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

<?php include('layouts/header.php'); ?>


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
                            <img src="assets/<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" onerror="this.onerror=null; this.src='assets/images/Placeholder.png';">
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

<?php include('layouts/footer.html') ?>