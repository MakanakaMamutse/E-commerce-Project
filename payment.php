<?php
session_start();

// Figuring out where the user came from to get the correct total amount
if (isset($_SESSION['total_cost'])) {
    // User came directly from shopping cart checkout
    $total_cost = $_SESSION['total_cost'];
} elseif (isset($_GET['order_id']) && is_numeric($_GET['order_id'])) {
    // User accessed payment page from order history - need to fetch total from database
    include('server/connection.php');
    
    $order_id = $_GET['order_id'];
    
    // Retrieving the total cost from our orders table
    $sql_order_total = "SELECT total_amount FROM orders WHERE order_id = ?";
    $stmt_order_total = $conn->prepare($sql_order_total);
    
    if ($stmt_order_total) {
        $stmt_order_total->bind_param("i", $order_id);
        $stmt_order_total->execute();
        $result = $stmt_order_total->get_result();
        
        if ($order_data = $result->fetch_assoc()) {
            $total_cost = $order_data['total_amount'];
        } else {
            // Order not found in database, setting safe default
            $total_cost = '0.00';
        }
        
        $stmt_order_total->close();
    } else {
        // Database query failed, using fallback value
        $total_cost = '0.00';
    }
} else {
    // No valid way to determine total cost, defaulting to zero
    $total_cost = '0.00';
}

// Sanitizing order status for safe display
$order_status = isset($_GET['order_status']) ? htmlspecialchars($_GET['order_status'], ENT_QUOTES, 'UTF-8') : 'unknown';
?>

<?php include('layouts/header.php'); ?>
   <!--Payment Processing Section-->
  <section class="my-5 py-5">
    <div class="container mt-5 py-5">
        <div class="row">
            <div class="row-col-lg-6 col-md-6 col-sm-12 mx-auto text-center fw-bold">
                <h2 class="text-center fw-bold">Payment</h2>
                <hr class="mx-auto">
                <!-- Displaying current order status safely -->
                <p>Your order is currently <?php echo $order_status; ?>, proceed below</p>
                <!-- Showing formatted total cost with proper escaping -->
                <p><?php echo "Total payment is: R" . htmlspecialchars(number_format($total_cost, 2), ENT_QUOTES, 'UTF-8'); ?> (incl. Shipping)</p>
                <button type="submit">Pay Now</button>
            </div>
        </div>
    </div>
  </section>
<?php include('layouts/footer.html'); ?>