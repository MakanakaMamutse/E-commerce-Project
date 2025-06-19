<?php
// Include the database connection file
include('server/connection.php');

if(isset($_GET['product_id'])) {
    
  $product_id = $_GET['product_id'];
    
  $sql = 
      "SELECT 
          p.*, 
          pi.image_url, 
          u.full_name as seller_name,
          c.category_name
      FROM 
          products p
      LEFT JOIN 
          product_images pi ON p.product_id = pi.product_id
      LEFT JOIN 
          categories c ON p.category_id = c.category_id
      LEFT JOIN 
            users u ON p.seller_id = u.user_id
      WHERE 
          p.product_id = ?";

  $stmt = $conn->prepare($sql);

  $stmt->bind_param("i", $product_id);

  $stmt->execute();

  $product = $stmt->get_result(); //Will return an array for me with 1 single product


    // Get the product data first to extract category_id
    $product_data = $product->fetch_assoc();
    
    if($product_data) {
        // For demonstration purposes, create array of same product images (different angles)
        // In a real application, you would query for multiple images of the same product
        // Example query would be: SELECT image_url FROM product_images WHERE product_id = ? ORDER BY image_order
        $same_product_images = [
            $product_data['image_url'], // Current main image (same as big display)
            'images/Placeholder.png', // Main product - different angle 2  
            'images/Placeholder.png', // Main product - different angle 3
            'images/Placeholder.png'  // Main product - different angle 4
        ];

        // These would represent different views of the same product:
        // - Front view, back view, side view, detail shot, etc.     
        $small_images = $same_product_images;
    }
} 
 
else {
    // Handle the case where product_id is not provided - Re-directs to index.php
    header("Location: index.php");
    die("Product ID not provided.");
}

?>

<?php include('layouts/header.php'); ?>

<!--Single Product-->
<section class="container single-product my-5 pt-5">
    <div class="row mt-5">
        <?php if($product_data) { ?>
            <div class="col-lg-5 col-md-6 col-sm-12">
                <img class="img-fluid w-100 pb-1" 
                     src="assets/<?php echo $product_data['image_url']; ?>" 
                     alt="Product Image" 
                     id="mainImg" 
                     onerror="this.onerror=null; this.src='assets/images/Placeholder.png';"/>
                
                <!-- Small images showing different angles/views of the same product -->
                <div class="small-img-group">
                    <?php 
                    // Loop through the same product images (different angles)
                    // In a real live app, these would be actual different photos of the same item
                    for($i = 0; $i < 4; $i++) { 
                    ?>
                        <div class="small-img-col">
                            <!-- Each image represents a different angle/view of the current product -->
                            <!-- Currently using placeholders for demonstration -->
                            <img src="assets/<?php echo $small_images[$i]; ?>" 
                                 width="100%" 
                                 class="small-img" 
                                 alt="Product Image - View <?php echo ($i + 1); ?>"
                                 title="Click to view this angle"
                                 onerror="this.onerror=null; this.src='assets/images/Placeholder.png';"/>
                        </div>
                    <?php } ?>
                </div>
            </div>
            
            <div class="col-lg-7 col-md-6 col-sm-12">
                <h6><?php echo $product_data['category_name']; ?></h6>
                <h3 class="py-4"><?php echo $product_data['product_name']; ?></h3>
                <h2>$<?php echo $product_data['price']; ?></h2>
                
                 <!-- Add to Cart Form -->
                <form id="addToCartForm" method="POST" action="cart.php">
                    <input type="hidden" name="product_id" value="<?php echo $product_data['product_id']; ?>">
                    <input type="hidden" name="product_name" value="<?php echo $product_data['product_name']; ?>">
                    <input type="hidden" name="product_price" value="<?php echo $product_data['price']; ?>">
                    <input type="hidden" name="product_image" value="<?php echo $product_data['image_url']; ?>">
                    
                    <input type="number" name="product_quantity" value="1" min="1" max="10" id="quantity"/>
                    <button type="submit" class="add-to-cart-btn" name="add_to_cart" id="addToCartBtn">Add To Cart</button>
                </form>
                
                <h4 class="mt-5 mb-5">Product Details</h4>
                <span><?php echo $product_data['description']; ?></span>

                <!-- Seller Information Section -->
                <div class="seller-info mt-4 p-4 border-0 rounded-3 shadow-sm" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-left: 8px solid #007bff !important; max-width: 300px;">
                    <div class="d-flex align-items-center">
                        <div class="seller-icon-wrapper me-3 d-flex align-items-center justify-content-center rounded-circle" 
                             style="width: 50px; height: 50px; background: linear-gradient(135deg, #007bff, #0056b3); box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);">
                            <i class="fas fa-store text-white" style="font-size: 1.2rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 text-muted" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Sold by</h6>
                            <p class="mb-0 fw-bold" style="font-size: 1.1rem; color: #343a40;"><?php $seller_name = (!empty($product_data['seller_name'])) ? $product_data['seller_name'] : 'Individual Seller';echo htmlspecialchars($seller_name, ENT_QUOTES, 'UTF-8'); ?></p>
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1" style="color: #28a745;"></i>
                                Verified Seller
                            </small>
                        </div>
                    </div>
                </div>
                
            </div>
        <?php } else { ?>
            <div class="col-12">
                <h3>Product not found</h3>
                <a href="index.php" class="btn btn-primary">Go Back to Home</a>
            </div>
        <?php } ?>
    </div>
</section>


      <!--Related Products-->
    <section id="related-products" class="my-5 pb-3">
        <div class="container text-center mt-5 py-5">
          <h3>Related Products</h3>
          <hr class="mx-auto">
          <p>Here you can check out other items you may like</p>
        </div>

        <div class="row mx-auto container-fluid">
          <?php include('server/get_shopItems.php'); ?>

          <?php while($row=$products->fetch_assoc()) { ?>
              <div class="product text-center col-lg-3 col-md-6 col-sm-12">
                <img class="img-fluid mb-3" src="assets/<?php echo $row['image_url']; ?>" alt="<?php echo $row['product_name']; ?>" onerror="this.onerror=null; this.src='assets/images/Placeholder.png';"/>
                <div class="star">
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                </div>

                <h5 class="p-name"><?php echo $row['product_name']; ?></h5>
                <h4 class="p-price"><?php echo $row['price']; ?></h4>
                <p>Simply the best on the market</p>
                <a href="singleProduct.php?product_id=<?php echo $row['product_id']; ?>" class="buy-btn">Buy Now</a>
              </div>
          <?php } ?>
        </div>
    </section>

    
    <script>
      // JavaScript for changing main image when small image is clicked
      // This allows users to view different angles/views of the same product
      document.addEventListener('DOMContentLoaded', function() {
          const smallImages = document.querySelectorAll('.small-img');
          const mainImg = document.getElementById('mainImg');
          
          // Add click event to each small image
          smallImages.forEach(function(img, index) {
              img.addEventListener('click', function() {
                  // Change the main image to show the clicked angle/view
                  mainImg.src = this.src;
                  
                  // Add visual feedback to show which image is selected
                  smallImages.forEach(function(otherImg) {
                      otherImg.style.opacity = '0.7'; // Dim all images at first
                  });
                  this.style.opacity = '1'; // Highlight selected image
              });
              
              // Set first image as active by default
              if(index === 0) {
                  img.style.opacity = '1';
              } else {
                  img.style.opacity = '0.7';
              }
          });
      });
    </script>

<?php include('layouts/footer.html'); ?>