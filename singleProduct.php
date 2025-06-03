<?php
// Include the database connection file
include('server/connection.php');

if(isset($_GET['product_id'])) {
    
  $product_id = $_GET['product_id'];
    
  $sql = 
      "SELECT 
          p.*, 
          pi.image_url, 
          c.category_name
      FROM 
          products p
      LEFT JOIN 
          product_images pi ON p.product_id = pi.product_id
      LEFT JOIN 
          categories c ON p.category_id = c.category_id
      WHERE 
          p.product_id = ?";

  $stmt = $conn->prepare($sql);

  $stmt->bind_param("i", $product_id);

  $stmt->execute();

  $product = $stmt->get_result(); //Will return an array for me with 1 single product
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

          <?php while($row = $product->fetch_assoc()) { ?>


            <div class="col-lg-5 col-md-6 col-sm-12">
                <img class="img-fluid w-100 pb-1" src="assets/<?php echo $row['image_url']; ?>" alt="Product Image" id="mainImg" 
                onerror="this.onerror=null; this.src='assets/images/Placeholder.png';"/> 
                <div class="small-img-group">
                    <div class="small-img-col">
                        <img src="assets/images/top2.png" width="100%" class="small-img" alt="Product Image"/>
                    </div>

                    <div class="small-img-col">
                        <img src="assets/images/top4.png" width="100%" class="small-img" alt="Product Image"/>
                    </div>

                    <div class="small-img-col">
                        <img src="assets/images/top5.png" width="100%" class="small-img" alt="Product Image"/>
                    </div>

                    <div class="small-img-col">
                        <img src="assets/images/top3.png" width="100%" class="small-img" alt="Product Image"/>
                    </div>
                 </div>
            </div>

        

            <div class="col-lg-6 col-md-12 col-12">
              <h6><?php echo $row['category_name']; ?></h6>
              <h3 class="py-4"><?php echo $row['product_name']; ?></h3>
              <h2>R <?php echo $row['price']; ?></h2>

              <form method="POST" action="cart.php">
                <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>"/>
                <input type="hidden" name="product_image" value="<?php echo $row['image_url']; ?>"/>
                <input type="hidden" name="product_name" value="<?php echo $row['product_name']; ?>"/>
                <input type="hidden" name="product_price" value="<?php echo $row['price']; ?>"/>

                  <input type="number" name="product_quantity" value="1" min="1"/>
                  <button class="buy-btn" type="submit" name="add_to_cart">Add to Cart</button>
             </form>

              <h4 class="mt-5 mb-5">Product Details</h4>
              <span><?php echo $row['description']; ?></span>
            </div>

        

          <?php } ?>
        </div>
    </section>


      <!--Related Products-->
    <section id="related-products" class="my-5 pb-5">
        <div class="container text-center mt-5 py-5">
          <h3>Related Products</h3>
          <hr class="mx-auto">
          <p>Here you can check out our fetured products</p>
        </div>

        <div class="row mx-auto container-fluid">
          <div class="product text-center col-lg-3 col-md-6 col-sm-12">
            <img class="img-fluid mb-3" src="assets/images/boot1.png"/>
            <div class="star">
              <i class="fas fa-star"></i>
            </div>

            <h5 class="p-name">Best Shoes</h5>
            <h4 class="p-price">R350</h4>
            <p>Get the best shoes on the market</p>
            <button class="buy-btn">Buy Now</button>
          </div>

          <div class="product text-center col-lg-3 col-md-6 col-sm-12">
            <img class="img-fluid mb-3" src="assets/images/top1.png"/>
            <div class="star">
              <i class="fas fa-star"></i>
            </div>

            <h5 class="p-name">Best Shoes</h5>
            <h4 class="p-price">R350</h4>
            <p>Get the best shoes on the market</p>
            <button class="buy-btn">Buy Now</button>
          </div>

          <div class="product text-center col-lg-3 col-md-6 col-sm-12">
            <img class="img-fluid mb-3" src="assets/images/boot4.png"/>
            <div class="star">
              <i class="fas fa-star"></i>
            </div>

            <h5 class="p-name">Best Shoes</h5>
            <h4 class="p-price">R350</h4>
            <p>Get the best shoes on the market</p>
            <button class="buy-btn">Buy Now</button>
          </div>

          <div class="product text-center col-lg-3 col-md-6 col-sm-12">
            <img class="img-fluid mb-3" src="assets/images/equip2.png"/>
            <div class="star">
              <i class="fas fa-star"></i>
            </div>

            <h5 class="p-name">Best Shoes</h5>
            <h4 class="p-price">R350</h4>
            <p>Get the best shoes on the market</p>
            <button class="buy-btn">Buy Now</button>
          </div>

        </div>
    </section>


    <script>
      var mainImg = document.getElementById("mainImg");
      var smallImg = document.getElementsByClassName("small-img");

      for(let i=0; i<4; i++){
        smallImg[i].onclick = function(){
          mainImg.src = smallImg[i].src;
        }
      }
    </script>

<?php include('layouts/footer.php'); ?>