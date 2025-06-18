<?php
include('../server/connection.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['login_status'])) {
    header("Location: login.php");
    exit();
}

// Check if product ID is provided
if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Product ID is required']);
    exit();
}

$product_id = intval($_POST['product_id']);
$user_id = $_SESSION['user_id'];
$role_type = $_SESSION['role_type'];

try {
    // Start transaction
    $conn->begin_transaction();
    
    // Check if user has permission to delete this product
    if ($role_type == 'seller') {
        // Sellers can only delete their own products
        $check_owner_query = "SELECT seller_id FROM products WHERE product_id = ?";
        $check_stmt = $conn->prepare($check_owner_query);
        $check_stmt->bind_param("i", $product_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows == 0) {
            throw new Exception("Product not found");
        }
        
        $product_data = $check_result->fetch_assoc();
        if ($product_data['seller_id'] != $user_id) {
            throw new Exception("You don't have permission to delete this product");
        }
        $check_stmt->close();
    }
    // Admin can delete any product (no additional check needed)
    
    // Get image URLs before deleting (for file cleanup)
    $get_images_query = "SELECT image_url FROM product_images WHERE product_id = ?";
    $images_stmt = $conn->prepare($get_images_query);
    $images_stmt->bind_param("i", $product_id);
    $images_stmt->execute();
    $images_result = $images_stmt->get_result();
    
    $image_urls = [];
    while ($row = $images_result->fetch_assoc()) {
        $image_urls[] = $row['image_url'];
    }
    $images_stmt->close();
    
    // Delete from product_images table first (child table)
    $delete_images_query = "DELETE FROM product_images WHERE product_id = ?";
    $delete_images_stmt = $conn->prepare($delete_images_query);
    $delete_images_stmt->bind_param("i", $product_id);
    
    if (!$delete_images_stmt->execute()) {
        throw new Exception("Failed to delete product images");
    }
    $delete_images_stmt->close();
    
    // Delete from products table (parent table)
    $delete_product_query = "DELETE FROM products WHERE product_id = ?";
    $delete_product_stmt = $conn->prepare($delete_product_query);
    $delete_product_stmt->bind_param("i", $product_id);
    
    if (!$delete_product_stmt->execute()) {
        throw new Exception("Failed to delete product");
    }
    
    // Check if any rows were actually deleted
    if ($delete_product_stmt->affected_rows == 0) {
        throw new Exception("Product not found or already deleted");
    }
    
    $delete_product_stmt->close();
    
    //Delete physical image files from server
    foreach ($image_urls as $image_url) {
        if (!empty($image_url) && $image_url != 'Placeholder.png') {
            $file_path = '../assets/' . $image_url;
            if (file_exists($file_path)) {
                unlink($file_path); // Delete the physical file
            }
        }
    }
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Product deleted successfully',
        'deleted_images' => count($image_urls)
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>