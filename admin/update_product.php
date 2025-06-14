<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Include database connection
include('../server/connection.php');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (!isset($input['productId']) || !isset($input['categoryId']) || 
    !isset($input['productName']) || !isset($input['description']) || 
    !isset($input['price'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Sanitize and validate input
$productId = intval($input['productId']);
$categoryId = intval($input['categoryId']);
$productName = trim($input['productName']);
$description = trim($input['description']);
$price = floatval($input['price']);

// Additional validation
if ($productId <= 0 || $categoryId <= 0 || empty($productName) || 
    empty($description) || $price < 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    exit;
}

try {
    // Prepare SQL update statement
    $sql = "UPDATE products SET 
            category_id = ?, 
            product_name = ?, 
            description = ?, 
            price = ?,
            updated_at = NOW()
            WHERE product_id = ?";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Prepare statement failed: ' . $conn->error);
    }
    
    // Bind parameters (i = integer, s = string, d = double/float)
    $stmt->bind_param("issdi", $categoryId, $productName, $description, $price, $productId);
    
    // Execute the statement
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                'success' => true, 
                'message' => 'Product updated successfully',
                'affected_rows' => $stmt->affected_rows
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'No changes made or product not found'
            ]);
        }
    } else {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>