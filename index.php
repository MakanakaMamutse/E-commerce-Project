<?php include('layouts/header.php'); ?>

    <!--Home-->
    <section id="home">

      <div class="">
        <h5>NEW ARRIVALS</h5>
        <h1>Best Prices This season</h1>
        <p>Get 50% off on selected items</p>
        <button>Shop Now</button>
      </div>
    </section>

    <!--Brand-->
    <section id="brand">
      <div class="container">
        <div class="row">
          <img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/images/nike.png"/>

          <img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/images/adidas.png"/>

          <img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/images/puma.png"/>

          <img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/images/newBalance.png"/>
        </div>
      </div>
    </section>

    <!--New-->
    <section id="new" class="w-100">
      <div class="row p-0 m-0">
        <!--First-->
        <div class="one col-lg-4 col-md-12 col-sm-12 p-0">
          <img class="img-fluid" src="assets/images/boots1.jpg"/>
            <div class="details">
              <h2>Best Shoes on Market</h2>
              <button class="text-uppercase">Shop Now</button>
            </div>
        </div>
        <!--Second-->
        <div class="one col-lg-4 col-md-12 col-sm-12 p-0">
          <img class="img-fluid" src="assets/images/shirts1.jpg"/>
            <div class="details">
              <h2>Best Shirts on the Market</h2>
              <button class="text-uppercase">Shop Now</button>
            </div>
        </div>
        <!--Third-->
        <div class="one col-lg-4 col-md-12 col-sm-12 p-0">
          <img class="img-fluid" src="assets/images/gear1.jpg"/>
            <div class="details">
              <h2>50% Off Gear</h2>
              <button class="text-uppercase">Shop Now</button>
            </div>
        </div>
      </div>
    </section>

    <!--Featured-->
    <section id="featured" class="my-5 pb-5">
      <div class="container text-center mt-5 py-5">
        <h3>Featured Products</h3>
        <hr class="mx-auto">
        <p>Here you can check out our fetured products</p>
      </div>

      <div class="row mx-auto container-fluid">

      <?php include('server/get_featured_products.php'); ?>


      <?php while($row=$featured_products->fetch_assoc()) { ?>

        <div class="product text-center col-lg-3 col-md-6 col-sm-12">
         <img class="img-fluid mb-3" src="assets/<?php echo $row['image_url']; ?>" alt="<?php echo $row['product_name']; ?>" onerror="this.onerror=null; this.src='assets/images/Placeholder.png';"/>

          <div class="star">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
          </div>

          <h5 class="p-name"> <?php echo $row['product_name']; ?> </h5>
          <h4 class="p-price">$ <?php echo $row['price']; ?> </h4>
          <p>Get the best shoes on the market</p>
          <a href="singleProduct.php?product_id=<?php echo $row['product_id']; ?>" class="buy-btn">Buy Now</a>
        </div>

        <?php } ?>
      </div>
    </section>

    <!--Banner-->
    <section id="banner" class="my-5 py5">
      <div class="container">
        <h4>CLEARANCE SALE</h4>
        <h1>2024/25 SEASON <br> Up to 50% Off</h1>
        <button class="">Shop Now</button>
        </div>
      </div>
    </section>

    <!--Boot Locker-->
    <section id="Boot Locker" class="my-5">
      <div class="container text-center mt-5 py-5">
        <h3>Boot Locker</h3>
        <hr class="mx-auto">
        <p>Here you can check out more of our boots</p>
      </div>

      <div class="row mx-auto container-fluid">

      <?php include('server/get_boots.php'); ?>

      <?php while($row=$boots_products->fetch_assoc()) { ?>

        <div class="product text-center col-lg-3 col-md-6 col-sm-12">
          <img class="img-fluid mb-3" src="assets/<?php echo $row['image_url']; ?>" alt="<?php echo $row['product_name']; ?>" onerror="this.onerror=null; this.src='assets/images/Placeholder.png';"/>
          <div class="star">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
          </div>
          <h5 class="p-name"> <?php echo $row['product_name']; ?> </h5>
          <h4 class="p-price">R <?php echo $row['price']; ?> </h4>
          <p>Get the best shoes on the market</p>
          <a href="singleProduct.php?product_id=<?php echo $row['product_id']; ?>" class="buy-btn">Buy Now</a>
        </div>

        <?php } ?>
      </div>
    </section>

    <!--Shirt Locker-->
    <section id="Shirt Locker" class="my-5">
      <div class="container text-center mt-5 py-5">
        <h3>Shirt Locker</h3>
        <hr class="mx-auto">
        <p>Here you can check out more of our shirts</p>
      </div>

      <div class="row mx-auto container-fluid">

        <?php include('server/get_shirts.php'); ?>

        <?php while($row=$shirt_products->fetch_assoc()) { ?>
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
              <h4 class="p-price">$ <?php echo $row['price']; ?></h4>
              <p>Get the best shirts on the market</p>
              <a href="singleProduct.php?product_id=<?php echo $row['product_id']; ?>" class="buy-btn">Buy Now</a>
            </div>
          <?php } ?>
      </div>
    </section>
          
<?php include('layouts/footer.php'); ?>