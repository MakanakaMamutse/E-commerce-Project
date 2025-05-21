<?php

session_start();

// Include database connection
include_once 'connection.php';


    if(isset($_POST['place_order'])) {

        // --- Get User Info and Order Details from Form and Session ---
        // Mapping $_POST data to PHP variables, aligning with database column names.
        // Updated to match the 'name' attributes in your HTML form (e.g., 'checkout-name').
        $full_name        = $_POST['checkout-name']; // Matches HTML <input name="checkout-name">
        $email            = $_POST['checkout-email']; // Matches HTML <input name="checkout-email">
        $phone_number     = $_POST['checkout-phone']; // Matches HTML <input name="checkout-phone">
        $shipping_address = $_POST['checkout-address'];
        $city             = $_POST['checkout-city'];
        $state_province   = $_POST['checkout-state'];
        $zip_postal_code  = $_POST['checkout-zip'];
        $country          = $_POST['checkout-country'];
        $payment_method   = $_POST['payment-method']; // Matches HTML <input name="payment-method">

        // Hardcoded IDs for now (will be dynamic in a full application).
        $customer_id = 1;
        $seller_id   = 2; // Required as 'seller_id' is NOT nullable in your DB.
        $payment_id  = null; // Set to null as payment processing is not yet implemented (DB column is nullable).


        // --- Calculate Order Financials ---
        // Assuming $_SESSION['cart_total'] holds the base product subtotal.
        $subtotal_amount = $_SESSION['cart_total'];

        // Calculate shipping cost and round to two decimal places.
        $shipping_cost = round($subtotal_amount * 0.0825, 2);

        // Calculate the grand total for the order.
        $total_cost = $subtotal_amount + $shipping_cost;
        $_SESSION['total_cost'] = $total_cost;


        // --- Set Other Order Specific Details ---
        $order_status = "pending"; // Default status for a new order.
        $order_date   = date("Y-m-d H:i:s"); // Current date and time of the order.


        // --- Prepare SQL INSERT Statement ---
        // All relevant columns from your 'orders' table are included.
        // The order of columns must match the order of '?' placeholders and bound variables.
        $sql = "INSERT INTO orders (
                    customer_id,
                    seller_id,
                    payment_id,
                    full_name,
                    email,
                    phone_number,
                    country,
                    city,
                    state_province,
                    zip_postal_code,
                    order_status,
                    order_date,
                    payment_method,
                    shipping_address,
                    shipping_cost,
                    subtotal,
                    total_amount
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                )";

        $stmt = $conn->prepare($sql);

        // Basic error handling for prepare() method.
        if ($stmt === false) {
            error_log("Failed to prepare statement: " . $conn->error);
            die("An internal error occurred. Please try again later.");
        }

    //     // --- DIAGNOSTIC STEP: Check variable count right before bind_param ---
    // $bind_variables = [
    //     $customer_id,
    //     $seller_id,
    //     $payment_id,
    //     $full_name,
    //     $email,
    //     $phone_number,
    //     $country,
    //     $city,
    //     $state_province,
    //     $zip_postal_code,
    //     $order_status,
    //     $order_date,
    //     $payment_method,
    //     $shipping_address,
    //     $shipping_cost,
    //     $subtotal_amount,
    //     $total_cost
    // ];

    // echo "Debug: Number of variables to bind: " . count($bind_variables) . "<br>";
    // echo "Debug: Type string length: " . strlen("iiisssssssssssddd") . "<br>";
    // echo "Debug: Variables and their types:<pre>";
    // foreach ($bind_variables as $key => $val) {
    //     echo "[$key]: " . (is_null($val) ? 'NULL' : (is_string($val) ? '"' . $val . '"' : $val)) . " (Type: " . gettype($val) . ")\n";
    // }
    // echo "</pre>";
    // END DIAGNOSTIC STEP

        // --- Bind Parameters ---
        // The type string "iiissssssssssddd" corresponds to the data types of variables:
        // i=integer, s=string, d=double (for decimal/float).
        $stmt->bind_param("iiisssssssssssidd",
            $customer_id,
            $seller_id,
            $payment_id,
            $full_name,
            $email,
            $phone_number,
            $country,
            $city,
            $state_province,
            $zip_postal_code,
            $order_status,
            $order_date,
            $payment_method,
            $shipping_address,
            $shipping_cost,
            $subtotal_amount,
            $total_cost
        );

        // --- Execute the Statement ---
        $stmt->execute();

        $order_id = $stmt->insert_id; // Get the last inserted order ID. In the order table, this is the primary key and auto-incremented.

        // Close the statement
        $stmt->close();



        //2 get products from the cart from the session
        foreach($_SESSION['cart'] as $key => $value) {
            
            // Assuming each cart item has 'product_id' and 'quantity'.
            $product = $_SESSION['cart'][$key]; //  [] the array of products in the cart at a specific index with $key storing the product_id and and product_name columns -- and value the actual data
            $product_id = $value['product_id'];
            $product_name = $value['product_name'];
            $product_price = $value['product_price'];
            $product_quantity = $value['product_quantity'];
            $item_subtotal = $product_quantity * $product_price; // Calculation in PHP
// ... then $item_subtotal is bound and inserted into the 'subtotal' column

            // --- Prepare SQL INSERT Statement for Order Items ---
            $sql_order_items = "INSERT INTO order_items (
                order_id,
                product_id,
                quantity,
                price_at_purchase,
                subtotal
            ) VALUES (?, ?, ?, ?, ?)";

            $stmt_order_items = $conn->prepare($sql_order_items);

             // Basic error handling for prepare() method.
            if ($stmt_order_items === false) {
                error_log("Failed to prepare statement: " . $conn->error);
                die("An internal error occurred. Please try again later.");
            }

                // Bind parameters for order items
            // The type string "iiidd" matches:
            // i = order_id (int)
            // i = product_id (int)
            // i = quantity (int)
            // d = price_at_purchase (decimal/double)
            // d = subtotal (decimal/double)

            $stmt_order_items->bind_param("iiidd",
                $order_id, 
                $product_id,
                $product_quantity,
                $product_price,
                $item_subtotal
            );

            // Execute the statement
            $stmt_order_items->execute();

            // Close the statement
            $stmt_order_items->close();
        }

        // --- Clear the Cart Session ---     
        //Unset the cart session variable to clear the cart after order placement.


        //Inform the user that the order was placed successfully
        header('location: ../payment.php?order_status=Order Placed Successfully');

    } else {
        // If the script was accessed without a form submission (e.g., direct URL access).
        // Consider redirecting to the checkout page.
        // header("Location: checkout.php");
        // exit();
    }
    
    
    
    
    
    
    
    
    
    // $order_id = $_POST['order_id'];
    // $user_id = $_POST['user_id'];
    // $product_id = $_POST['product_id'];
    // $quantity = $_POST['quantity'];
    // $total_price = $_POST['total_price'];

    // // Database connection
    // $conn = new mysqli('localhost', 'username', 'password', 'database');

    // // Check connection
    // if ($conn->connect_error) {
    //     die("Connection failed: " . $conn->connect_error);
    // }

    // // Insert order into database
    // $sql = "INSERT INTO orders (order_id, user_id, product_id, quantity, total_price) VALUES ('$order_id', '$user_id', '$product_id', '$quantity', '$total_price')";

    // if ($conn->query($sql) === TRUE) {
    //     echo "Order placed successfully";
    // } else {
    //     echo "Error: " . $sql . "<br>" . $conn->error;
    // }

    // // Close connection
    // $conn->close();


?>