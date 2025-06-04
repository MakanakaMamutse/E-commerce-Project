<?php
include('connection.php');

// Fetching all products 
$sql = "SELECT
    p.*,
    pi.image_url
    FROM
        products p
    JOIN
        product_images pi ON p.product_id = pi.product_id
    ORDER BY
        RAND()
    LIMIT 4";

$stmt = $conn->prepare($sql);
$stmt->execute();

$products = $stmt->get_result();

?>