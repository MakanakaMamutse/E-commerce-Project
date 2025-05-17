<?php
include('connection.php');

// Fetch boots products with their category name and the URL of one image,
// joining the products, categories, and product_images tables.
$sql = " SELECT
    p.*,
    c.category_name,
    pi.image_url
    FROM
        products p
    JOIN
        categories c ON p.category_id = c.category_id
    LEFT JOIN
        product_images pi ON p.product_id = pi.product_id
    WHERE
        p.category_id = 5
    GROUP BY
        p.product_id
    LIMIT 4";

$stmt = $conn->prepare($sql);
$stmt->execute();

$boots_products = $stmt->get_result();
?>