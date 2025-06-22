<?php 
// Telling the browser we're sending JSON data back, not HTML or plain text
header('Content-Type: application/json'); 
// Allowing requests from any domain - should be more specific in production for security
header('Access-Control-Allow-Origin: *'); 
// Only accepting POST requests - blocking GET, PUT, DELETE etc.
header('Access-Control-Allow-Methods: POST'); 
// Letting the browser send Content-Type headers so we know what format the data is in
header('Access-Control-Allow-Headers: Content-Type'); 
 
// Getting our database connection ready
include('../server/connection.php'); 
 
// Making sure we're only accepting POST requests - keeps things secure
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { 
    http_response_code(405); 
    echo json_encode(['success' => false, 'message' => 'Method not allowed']); 
    exit; 
} 
 
// Grabbing the JSON data from the request body
$input = json_decode(file_get_contents('php://input'), true); 
 
// Checking that all the important stuff is there - can't update without these
if (!isset($input['productId']) || !isset($input['categoryId']) ||  
    !isset($input['productName']) || !isset($input['description']) ||  
    !isset($input['price'])) { 
    http_response_code(400); 
    echo json_encode(['success' => false, 'message' => 'Missing required fields']); 
    exit; 
} 
 
// Cleaning up the input data and making sure it's the right type
$productId = intval($input['productId']); 
$categoryId = intval($input['categoryId']); 
$productName = trim($input['productName']); 
$description = trim($input['description']); 
$price = floatval($input['price']); 
 
// Double-checking everything looks reasonable before we touch the database
if ($productId <= 0 || $categoryId <= 0 || empty($productName) ||  
    empty($description) || $price < 0) { 
    http_response_code(400); 
    echo json_encode(['success' => false, 'message' => 'Invalid input data']); 
    exit; 
} 
 
try { 
    // Building our update query with placeholders - this prevents SQL injection attacks
    $sql = "UPDATE products SET  
            category_id = ?,  
            product_name = ?,  
            description = ?,  
            price = ?, 
            updated_at = NOW() 
            WHERE product_id = ?"; 
     
    $stmt = $conn->prepare($sql); 
     
    // Making sure the statement prepared correctly
    if (!$stmt) { 
        throw new Exception('Prepare statement failed: ' . $conn->error); 
    } 
     
    // Escaping HTML special characters to prevent XSS attacks - this is crucial for security
    $safeProductName = htmlspecialchars($productName, ENT_QUOTES, 'UTF-8');
    $safeDescription = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
     
    // Binding our cleaned parameters to the query (i = integer, s = string, d = double/float) 
    $stmt->bind_param("issdi", $categoryId, $safeProductName, $safeDescription, $price, $productId); 
     
    // Running the update and checking if it worked
    if ($stmt->execute()) { 
        if ($stmt->affected_rows > 0) { 
            // Success! The product was actually updated
            echo json_encode([ 
                'success' => true,  
                'message' => 'Product updated successfully', 
                'affected_rows' => $stmt->affected_rows 
            ]); 
        } else { 
            // Query ran but nothing changed - either product doesn't exist or no changes were made
            echo json_encode([ 
                'success' => false,  
                'message' => 'No changes made or product not found' 
            ]); 
        } 
    } else { 
        // Something went wrong during execution
        throw new Exception('Execute failed: ' . $stmt->error); 
    } 
     
    // Cleaning up the prepared statement
    $stmt->close(); 
     
} catch (Exception $e) { 
    // Handling any database errors that might have occurred
    http_response_code(500); 
    echo json_encode([ 
        'success' => false,  
        'message' => 'Database error: ' . $e->getMessage() 
    ]); 
} finally { 
    // Making sure we always close the database connection, even if something went wrong
    if (isset($conn)) { 
        $conn->close(); 
    } 
} 
?>