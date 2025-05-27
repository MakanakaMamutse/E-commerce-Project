<?php
include('connection.php');


// Fetch products and their main image (e.g., front image) using LEFT JOIN
$sql = " SELECT 
    p.product_id,
    p.product_name,
    p.price,
    pi.image_url
FROM 
    products p
LEFT JOIN 
    product_images pi ON p.product_id = pi.product_id
GROUP BY 
    p.product_id
LIMIT 4 ";

$stmt = $conn->prepare($sql);
$stmt->execute();

$featured_products = $stmt->get_result();
?>
