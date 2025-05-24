<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous"/>

    <link rel="stylesheet" href="assets/css/style.css"/>

    <style>
      .product img{
        width: 100%; 
        height: 350px; /* Fixed height for images */
        object-fit: contain; /* Ensures entire image is visible */
        object-position: center; /* Centers the image */
      }
    </style>
</head>

<body>
    
    <!--Navbar-->
    <nav class="navbar navbar-expand-lg navbar-light bg-white py-3 fixed-top">
        <div class="container-fluid">
          <img class="logo" src="assets/images/mLogo.png" alt="My shop">
          <h2 class="brand">M&M Sports</h2>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse nav-buttons" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
              
              <li class="nav-item">
                <a class="nav-link" href="index.html">Home</a>
              </li>

              <li class="nav-item">
                <a class="nav-link" href="shop.html">Shop</a>
              </li>

              <li class="nav-item">
                <a class="nav-link" href="#">Blog</a>
              </li>

              <li class="nav-item">
                <a class="nav-link" href="#">Contact Us</a>
              </li>
     
              <li class="nav-item">
                <a href="cart.php"><i class="fas fa-shopping-bag"></i></a>
                <a href="account.php"><i class="fas fa-user"></i></a>
              </li>


            </ul>
          </div>
        </div>
    </nav>

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
          <h4 class="p-price">R <?php echo $row['price']; ?> </h4>
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
        <div class="product text-center col-lg-3 col-md-6 col-sm-12">
          <img class="img-fluid mb-3" src="assets/images/boot1.png"/>
          <div class="star">
            <i class="fas fa-star"></i>
          </div>

          <h5 class="p-name">Best Shoes</h5>
          <h4 class="p-price">R350</h4>
          <p>Get the best shoes on the market</p>
          <a href="singleProduct.php?product_id=<?php echo $row['product_id']; ?>" class="buy-btn">Buy Now</a>
        </div>

        <div class="product text-center col-lg-3 col-md-6 col-sm-12">
          <img class="img-fluid mb-3" src="assets/images/boot2.png"/>
          <div class="star">
            <i class="fas fa-star"></i>
          </div>

          <h5 class="p-name">Best Shoes</h5>
          <h4 class="p-price">R350</h4>
          <p>Get the best shoes on the market</p>
          <button>Buy Now</button>
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
          <img class="img-fluid mb-3" src="assets/images/boot5.png"/>
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
          
      <!--Footer-->
    <footer class="mt-5 py-5">

      <div class="row container mx-auto py-5">
        <div class="footer-one col-lg-3 col-md-6 col-sm-12">
          <img src="assets/images/yy.png"/>
          <p class="pt-3">We provide the best products</p>
        </div>

        <div class="footer-one col-lg-3 col-md-6 col-sm-12">
          <h5 class="pt-2">Featured</h5>
          <ul class="text-uppercase">
            <li><a href="#">Shoes</a></li>
            <li><a href="#">Shirts</a></li>
            <li><a href="#">Gear</a></li>
          </ul>
        </div>

        <div class="footer-one col-lg-3 col-md-6 col-sm-12">
          <h5 class="pt-2">Contact Us</h5>
          <div> 
            <h6 class="text-uppercase">Address</h6>
            <p>1234 Street Name</p>
          </div>
          <div> 
            <h6 class="text-uppercase">Phone</h6>
            <p>00 00 XXX</p>
          </div>
          <div> 
            <h6 class="text-uppercase">Email</h6>
            <p>dummy@mail.com</p>
          </div>>
        </div>

        <div class="footer-one col-lg-3 col-md-6 col-sm-12">
          <h5 class="pb-2">Instagram</h5>
          <div class="row">
            <img class="img-fluid w-25 h-100 m-2" src="assets/images/featured1.jpg"/>
            <img class="img-fluid w-25 h-100 m-2" src="assets/images/featured2.jpg"/>
            <img class="img-fluid w-25 h-100 m-2" src="assets/images/featured3.jpg"/>

        </div>
      </div>

      <div class="copyright mt-5">
        <div class ="row container mx-auto">
          <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <img src="assets/images/payment.png" alt="My Shop" height="40"> <!--Payjpg-->
          </div>
          <div class="col-lg-3 col-md-6 col-sm-12 mb-4 text-nowrap mb-2">
            <p>&copy; 2025 My Shop. All Rights Reserved</p>
          </div>
          <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
          </div>
        </div>
      </div>

    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>