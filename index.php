<?php include('layouts/header.php'); ?>

  <!-- Main hero section with rotating carousel banners -->
  <section id="home">
        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
            <!-- Navigation dots for the carousel slides -->
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
            </div>

            <div class="carousel-inner">
                <!-- First slide showcasing new arrivals and seasonal pricing -->
                <div class="carousel-item active">
                    <img src="/assets/images/homeBG.jpg" class="d-block w-100 carousel-bg-image" alt="New Arrivals Background">
                    <div class="carousel-caption d-flex flex-column justify-content-center align-items-center h-100">
                        <h5>NEW ARRIVALS</h5>
                        <h1>Best Prices This season</h1>
                        <p>Get 50% off on selected items</p>
                        <button class="text-uppercase">Shop Now</button>
                    </div>
                </div>

                <!-- Second slide highlighting limited footwear deals -->
                <div class="carousel-item">
                    <img src="/assets/images/heroBG4.jpg" class="d-block w-100 carousel-bg-image" alt="Special Offer Background">
                    <div class="carousel-caption d-flex flex-column justify-content-center align-items-center h-100">
                        <h5>LIMITED TIME OFFER</h5>
                        <h1>Flash Sale on Footwear</h1>
                        <p>Up to 70% off on sneakers and boots!</p>
                        <button class="text-uppercase">View Deals</button>
                    </div>
                </div>

                <!-- Third slide promoting summer collection -->
                <div class="carousel-item">
                    <img src="/assets/images/heroBG3.jpg" class="d-block w-100 carousel-bg-image" alt="New Collection Background">
                    <div class="carousel-caption d-flex flex-column justify-content-center align-items-center h-100">
                        <h5>SUMMER COLLECTION</h5>
                        <h1>Fresh Styles for the Season</h1>
                        <p>Explore our latest apparel and accessories</p>
                        <button class="text-uppercase">Discover More</button>
                    </div>
                </div>
             </div>
      </div>
  </section>

    <!-- Brand showcase section displaying partner logos -->
    <section id="brand">
      <div class="container">
        <div class="row">
          <img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/images/nike.png" alt="Nike Brand Logo"/>

          <img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/images/adidas.png" alt="Adidas Brand Logo"/>

          <img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/images/puma.png" alt="Puma Brand Logo"/>

          <img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/images/newBalance.png" alt="New Balance Brand Logo"/>
        </div>
      </div>
    </section>

    <!-- Three-column promotional section featuring different product categories -->
    <section id="new" class="w-100">
      <div class="row p-0 m-0">
        <!-- First column promoting footwear collection -->
        <div class="one col-lg-4 col-md-12 col-sm-12 p-0">
          <img class="img-fluid" src="assets/images/boots1.jpg" alt="Premium Boots Collection"/>
            <div class="details">
              <h2>Best Shoes on Market</h2>
              <button class="text-uppercase">Shop Now</button>
            </div>
        </div>
        <!-- Second column highlighting shirt collection -->
        <div class="one col-lg-4 col-md-12 col-sm-12 p-0">
          <img class="img-fluid" src="assets/images/shirts1.jpg" alt="Premium Shirts Collection"/>
            <div class="details">
              <h2>Best Shirts on the Market</h2>
              <button class="text-uppercase">Shop Now</button>
            </div>
        </div>
        <!-- Third column showcasing gear with discount -->
        <div class="one col-lg-4 col-md-12 col-sm-12 p-0">
          <img class="img-fluid" src="assets/images/gear1.jpg" alt="Sports Gear Collection"/>
            <div class="details">
              <h2>50% Off Gear</h2>
              <button class="text-uppercase">Shop Now</button>
            </div>
        </div>
      </div>
    </section>

    <!-- Featured products section pulling from database -->
    <section id="featured" class="my-3 pb-3">
      <div class="container text-center mt-4 py-4">
        <h3>Featured Products</h3>
        <hr class="mx-auto">
        <p>Here you can check out our featured products</p>
      </div>

      <div class="row mx-auto container-fluid">

      <?php include('server/get_featured_products.php'); ?>

      <!-- Loop through each featured product from database -->
      <?php while($row = $featured_products->fetch_assoc()) { ?>

        <div class="product text-center col-lg-3 col-md-6 col-sm-12">
         <img class="img-fluid mb-3" src="assets/<?php echo htmlspecialchars($row['image_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8'); ?>" onerror="this.onerror=null; this.src='assets/images/Placeholder.png';"/>

          <!-- Five-star rating display (static for now) -->
          <div class="star">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
          </div>

          <h5 class="p-name mt-1"><?php echo htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8'); ?></h5>
          <h4 class="p-price">$<?php echo htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8'); ?></h4>
          <p>Get the best shirts on the market</p>
          <a href="singleProduct.php?product_id=<?php echo (int)$row['product_id']; ?>" class="buy-btn">Buy Now</a>
        </div>

        <?php } ?>
      </div>
    </section>

    <!-- Clearance sale banner promoting seasonal discounts -->
    <section id="banner" class="my-1">
      <div class="container">
        <h4>CLEARANCE SALE</h4>
        <h1>2024/25 SEASON <br> Up to 50% Off</h1>
        <button class="">Shop Now</button>
        </div>
      </div>
    </section>

    <!-- Boot collection section displaying footwear products -->
    <section id="Boot Locker" class="my-4 pb-3">
      <div class="container text-center">
        <h3>Boot Locker</h3>
        <hr class="mx-auto">
        <p>Here you can check out more of our boots</p>
      </div>

      <div class="row mx-auto container-fluid">

      <?php include('server/get_boots.php'); ?>

      <!-- Iterating through boot products retrieved from database -->
      <?php while($row = $boots_products->fetch_assoc()) { ?>

        <div class="product text-center col-lg-3 col-md-6 col-sm-12">
          <img class="img-fluid mb-3" src="assets/<?php echo htmlspecialchars($row['image_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8'); ?>" onerror="this.onerror=null; this.src='assets/images/Placeholder.png';"/>
          <div class="star">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
          </div>
          <h5 class="p-name"><?php echo htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8'); ?></h5>
          <h4 class="p-price">R <?php echo htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8'); ?></h4>
          <p>Get the best shoes on the market</p>
          <a href="singleProduct.php?product_id=<?php echo (int)$row['product_id']; ?>" class="buy-btn">Buy Now</a>
        </div>

        <?php } ?>
      </div>
    </section>

    <!-- Shirt collection section showcasing apparel products -->
    <section id="Shirt Locker" class="my-5 pb-3">
      <div class="container text-center mt-4 py-4">
        <h3>Shirt Locker</h3>
        <hr class="mx-auto">
        <p>Here you can check out more of our shirts</p>
      </div>

      <div class="row mx-auto container-fluid">

        <?php include('server/get_shirts.php'); ?>

        <!-- Processing each shirt product from the database query -->
        <?php while($row = $shirt_products->fetch_assoc()) { ?>
            <div class="product text-center col-lg-3 col-md-6 col-sm-12">
              <img class="img-fluid mb-3" src="assets/<?php echo htmlspecialchars($row['image_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8'); ?>" onerror="this.onerror=null; this.src='assets/images/Placeholder.png';"/>
              <div class="star">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
              </div>
              <h5 class="p-name"><?php echo htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8'); ?></h5>
              <h4 class="p-price">$ <?php echo htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8'); ?></h4>
              <p>Get the best shirts on the market</p>
              <a href="singleProduct.php?product_id=<?php echo (int)$row['product_id']; ?>" class="buy-btn">Buy Now</a>
            </div>
          <?php } ?>
      </div>
    </section>
          
<?php include('layouts/footer.html'); ?>