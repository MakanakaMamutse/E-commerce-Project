<?php
session_start();

// Check if coming from cart (session variable exists) or from order history (GET parameter)
if (isset($_SESSION['total_cost'])) {
    // Coming from cart
    $total_cost = $_SESSION['total_cost'];
} elseif (isset($_GET['order_id']) && is_numeric($_GET['order_id'])) {
    // Coming from order history - need to get total from database
    include('server/connection.php');
    
    $order_id = $_GET['order_id'];
    
    // Get the total cost from the orders table
    $sql_order_total = "SELECT total_amount FROM orders WHERE order_id = ?";
    $stmt_order_total = $conn->prepare($sql_order_total);
    
    if ($stmt_order_total) {
        $stmt_order_total->bind_param("i", $order_id);
        $stmt_order_total->execute();
        $result = $stmt_order_total->get_result();
        
        if ($order_data = $result->fetch_assoc()) {
            $total_cost = $order_data['total_amount'];
        } else {
            $total_cost = '0.00';
        }
        
        $stmt_order_total->close();
    } else {
        $total_cost = '0.00';
    }
} else {
    // No valid source for total cost
    $total_cost = '0.00';
}
?>

<?php include('layouts/header.php'); ?>
   <!--Checkout-->
  <section class="my-5 py-5">
    <div class="container mt-5 py-5">
        <div class="row">
            <div class="row-col-lg-6 col-md-6 col-sm-12 mx-auto text-center fw-bold">
                <h2 class="text-center fw-bold">Payment</h2>
                <hr class="mx-auto">
                <p>Your order is currently <?php echo $_GET['order_status']; ?>, proceed below</p>
                <p> <?php echo "Total payment is: $" . htmlspecialchars(number_format($total_cost, 2)); ?> (inclu. Shipping) </p>
                <button type="submit">Pay Now</button>
            </div>
        </div>
    </div>
  </section>
<?php include('layouts/footer.html'); ?>