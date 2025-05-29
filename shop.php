<?php

// Start the session
session_start();

include('server/connection.php');

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
          categories c ON p.category_id = c.category_id";

$stmt = $conn->prepare($sql);
$stmt->execute();
$product = $stmt->get_result(); //this puts all products in the database into $result as an array
//$products = $result->fetch_all(MYSQLI_ASSOC);  Fetch all products as an associative array



// Close the statement      
$stmt->close();



?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous"/>

    <link rel="stylesheet" href="assets/css/style.css"/>


</head>

<body class="shop-page">

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
              <a class="nav-link" href="index.php">Home</a>
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

     <!--Shop Section-->
    <div class="container-fluid py-4">
        <div class="row">
            <!-- Filters Sidebar - Narrower I will only use 2/12ths of the parent div (row) -->
            <div class="col-lg-2 col-md-3"> 
                <div class="filters-sidebar">
                    <h5 class="filter-title mb-3">
                        <i class="fas fa-filter me-2"></i>Filter
                    </h5>

                    <!-- Search Box -->
                    <div class="filter-section">
                        <div class="search-box">
                            <input type="text" class="search-input" placeholder="Search..." id="searchInput">
                            <i class="fas fa-search search-icon"></i>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="filter-section">
                        <h6 class="filter-title">Categories</h6>
                        <div class="filter-option">
                            <input type="checkbox" id="all-products" checked>
                            <label for="all-products">
                                <span>All Products</span>
                                <span class="product-count">24</span>
                            </label>
                        </div>
                        <div class="filter-option">
                            <input type="checkbox" id="football-boots">
                            <label for="football-boots">
                                <span>Football Boots</span>
                                <span class="product-count">8</span>
                            </label>
                        </div>
                        <div class="filter-option">
                            <input type="checkbox" id="club-shirts">
                            <label for="club-shirts">
                                <span>Club Shirts</span>
                                <span class="product-count">6</span>
                            </label>
                        </div>
                        <div class="filter-option">
                            <input type="checkbox" id="national-team">
                            <label for="national-team">
                                <span>National Kits</span>
                                <span class="product-count">4</span>
                            </label>
                        </div>
                        <div class="filter-option">
                            <input type="checkbox" id="training-gear">
                            <label for="training-gear">
                                <span>Training Gear</span>
                                <span class="product-count">6</span>
                            </label>
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div class="filter-section">
                        <h6 class="filter-title">Price Range</h6>
                        <div class="price-range">
                            <input type="range" class="price-slider" id="priceRange" min="0" max="1000" value="500">
                            <div class="price-display">
                                <span>R0</span>
                                <span id="maxPrice">R500</span>
                            </div>
                        </div>
                    </div>

                    <!-- Brands -->
                    <div class="filter-section">
                        <h6 class="filter-title">Brands</h6>
                        <div class="filter-option">
                            <input type="checkbox" id="nike">
                            <label for="nike">
                                <span>Nike</span>
                                <span class="product-count">8</span>
                            </label>
                        </div>
                        <div class="filter-option">
                            <input type="checkbox" id="adidas">
                            <label for="adidas">
                                <span>Adidas</span>
                                <span class="product-count">7</span>
                            </label>
                        </div>
                        <div class="filter-option">
                            <input type="checkbox" id="puma">
                            <label for="puma">
                                <span>Puma</span>
                                <span class="product-count">5</span>
                            </label>
                        </div>
                    </div>

                    <!-- Clear Filters -->
                    <button class="clear-filters" onclick="clearAllFilters()">
                        <i class="fas fa-times me-1"></i>Clear Filters
                    </button>
                </div>
            </div>

            <!-- Products Section - More Space -->
            <div class="col-lg-10 col-md-9">
                <div class="products-section">
                    <div class="section-header">
                        <div>
                            <h2 class="section-title">Product Locker</h2>
                            <p class="products-meta">Showing 16 of 24 products</p> <!--Calc done in JS below-->
                        </div>
                        <select class="sort-dropdown">
                            <option>Sort by: Default</option>
                            <option>Price: Low to High</option>
                            <option>Price: High to Low</option>
                            <option>Name: A to Z</option>
                            <option>Newest First</option>
                            <option>Best Sellers</option>
                        </select>
                    </div>

                    <!-- Products Grid -->
                    <div class="products-grid" id="productsGrid">

                    <?php while ($row = $product->fetch_assoc()) { ?>

                        <!-- Product Card 1 -->
                        <div class="product-card" onclick="window.location.href='singleProduct.php';">
                            <div class="product-image-container">
                                <img class="product-image" src="assets/<?php echo $row['image_url']; ?>" alt="<?php echo $row['product_name']; ?>" onerror="this.onerror=null; this.src='assets/images/Placeholder.png';"/>
                                <div class="product-badge">New</div>
                                <button class="wishlist-btn" onclick="event.stopPropagation();">
                                    <i class="far fa-heart"></i>
                                </button>
                            </div>
                            <div class="product-info">
                                <div class="product-category"><?php echo $row['category_name']; ?></div>
                                <h5 class="product-name"><?php echo $row['product_name']; ?></h5>
                                <span class="description mb-1"><?php echo $row['description']; ?></span>
                                <div class="product-price">$  <?php echo $row['price']; ?></div>
                                <div class="product-actions">
                                    <button class="btn btn-primary-custom" onclick="event.stopPropagation();">Buy Now</button>
                                    <button class="btn btn-outline-custom" onclick="event.stopPropagation();">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php } ?>



                                           <!-- <div class="unused-product-card">
                                                     Product Card 2 
                                                    <div class="product-card" onclick="window.location.href='singleProduct.html';">
                                                        <div class="product-image-container">
                                                            <img class="product-image" src="assets/images/chelsea-home-2023.png" alt="Club Jersey">
                                                            <button class="wishlist-btn" onclick="event.stopPropagation();">
                                                                <i class="far fa-heart"></i>
                                                            </button>
                                                        </div>
                                                        <div class="product-info">
                                                            <div class="product-category">Club Shirts</div>
                                                            <h5 class="product-name">Chelsea Home Jersey 2024</h5>
                                                            <div class="product-price">R650</div>
                                                            <div class="product-actions">
                                                                <button class="btn btn-primary-custom" onclick="event.stopPropagation();">Buy Now</button>
                                                                <button class="btn btn-outline-custom" onclick="event.stopPropagation();">
                                                                    <i class="fas fa-shopping-cart"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    Product Card 3 
                                                    <div class="product-card" onclick="window.location.href='singleProduct.html';">
                                                        <div class="product-image-container">
                                                            <img class="product-image" src="assets/images/equip2.png" alt="Training Equipment">
                                                            <div class="product-badge">Sale</div>
                                                            <button class="wishlist-btn" onclick="event.stopPropagation();">
                                                                <i class="far fa-heart"></i>
                                                            </button>
                                                        </div>
                                                        <div class="product-info">
                                                            <div class="product-category">Training Equipment</div>
                                                            <h5 class="product-name">Professional Training Cones</h5>
                                                            <div class="product-price">R180</div>
                                                            <div class="product-actions">
                                                                <button class="btn btn-primary-custom" onclick="event.stopPropagation();">Buy Now</button>
                                                                <button class="btn btn-outline-custom" onclick="event.stopPropagation();">
                                                                    <i class="fas fa-shopping-cart"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                     Product Card 4 
                                                    <div class="product-card" onclick="window.location.href='singleProduct.html';">
                                                        <div class="product-image-container">
                                                            <img class="product-image" src="assets/images/top4.png" alt="Training Top">
                                                            <button class="wishlist-btn" onclick="event.stopPropagation();">
                                                                <i class="far fa-heart"></i>
                                                            </button>
                                                        </div>
                                                        <div class="product-info">
                                                            <div class="product-category">Training Wear</div>
                                                            <h5 class="product-name">Elite Training Top</h5>
                                                            <div class="product-price">R320</div>
                                                            <div class="product-actions">
                                                                <button class="btn btn-primary-custom" onclick="event.stopPropagation();">Buy Now</button>
                                                                <button class="btn btn-outline-custom" onclick="event.stopPropagation();">
                                                                    <i class="fas fa-shopping-cart"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                     Product Card 5 
                                                    <div class="product-card" onclick="window.location.href='singleProduct.html';">
                                                        <div class="product-image-container">
                                                            <img class="product-image" src="assets/images/boot3.png" alt="Football Boots">
                                                            <button class="wishlist-btn" onclick="event.stopPropagation();">
                                                                <i class="far fa-heart"></i>
                                                            </button>
                                                        </div>
                                                        <div class="product-info">
                                                            <div class="product-category">Football Boots</div>
                                                            <h5 class="product-name">Speed Demon Boots</h5>
                                                            <div class="product-price">R580</div>
                                                            <div class="product-actions">
                                                                <button class="btn btn-primary-custom" onclick="event.stopPropagation();">Buy Now</button>
                                                                <button class="btn btn-outline-custom" onclick="event.stopPropagation();">
                                                                    <i class="fas fa-shopping-cart"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                     Product Card 6 
                                                    <div class="product-card" onclick="window.location.href='singleProduct.html';">
                                                        <div class="product-image-container">
                                                            <img class="product-image" src="assets/images/top1.png" alt="National Kit">
                                                            <div class="product-badge">Limited</div>
                                                            <button class="wishlist-btn" onclick="event.stopPropagation();">
                                                                <i class="far fa-heart"></i>
                                                            </button>
                                                        </div>
                                                        <div class="product-info">
                                                            <div class="product-category">National Team</div>
                                                            <h5 class="product-name">South Africa National Kit</h5>
                                                            <div class="product-price">R750</div>
                                                            <div class="product-actions">
                                                                <button class="btn btn-primary-custom" onclick="event.stopPropagation();">Buy Now</button>
                                                                <button class="btn btn-outline-custom" onclick="event.stopPropagation();">
                                                                    <i class="fas fa-shopping-cart"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                     Product Card 7 
                                                    <div class="product-card" onclick="window.location.href='singleProduct.html';">
                                                        <div class="product-image-container">
                                                            <img class="product-image" src="assets/images/boot4.png" alt="Football Boots">
                                                            <button class="wishlist-btn" onclick="event.stopPropagation();">
                                                                <i class="far fa-heart"></i>
                                                            </button>
                                                        </div>
                                                        <div class="product-info">
                                                            <div class="product-category">Football Boots</div>
                                                            <h5 class="product-name">Classic Football Boots</h5>
                                                            <div class="product-price">R390</div>
                                                            <div class="product-actions">
                                                                <button class="btn btn-primary-custom" onclick="event.stopPropagation();">Buy Now</button>
                                                                <button class="btn btn-outline-custom" onclick="event.stopPropagation();">
                                                                    <i class="fas fa-shopping-cart"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                     Product Card 8 
                                                    <div class="product-card" onclick="window.location.href='singleProduct.html';">
                                                        <div class="product-image-container">
                                                            <img class="product-image" src="assets/images/equip1.png" alt="Training Equipment">
                                                            <button class="wishlist-btn" onclick="event.stopPropagation();">
                                                                <i class="far fa-heart"></i>
                                                            </button>
                                                        </div>
                                                        <div class="product-info">
                                                            <div class="product-category">Training Equipment</div>
                                                            <h5 class="product-name">Training Ball Set</h5>
                                                            <div class="product-price">R250</div>
                                                            <div class="product-actions">
                                                                <button class="btn btn-primary-custom" onclick="event.stopPropagation();">Buy Now</button>
                                                                <button class="btn btn-outline-custom" onclick="event.stopPropagation();">
                                                                    <i class="fas fa-shopping-cart"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> End of unused products-grid -->
                        </div>

                    <!-- Pagination -->
                    <div class="pagination-wrapper">
                        <nav aria-label="Product pagination">
                            <ul class="pagination">
                                <li class="page-item">
                                    <a class="page-link" href="#" aria-label="Previous">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                                <li class="page-item active">
                                    <a class="page-link" href="#">1</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">2</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">3</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#" aria-label="Next">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
      

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- Custom JavaScript for functionality -->
    <script>
        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            
        // Price range slider functionality
        const priceSlider = document.getElementById('priceRange');
        const maxPriceDisplay = document.getElementById('maxPrice');

        if (priceSlider && maxPriceDisplay) {
            priceSlider.addEventListener('input', function() {
                maxPriceDisplay.textContent = 'R' + this.value;
            });
        }

        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const productCards = document.querySelectorAll('.product-card');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                productCards.forEach(card => {
                    const productName = card.querySelector('.product-name');
                    const productCategory = card.querySelector('.product-category');
                    
                    if (productName && productCategory) {
                        const nameText = productName.textContent.toLowerCase();
                        const categoryText = productCategory.textContent.toLowerCase();
                        
                        if (nameText.includes(searchTerm) || categoryText.includes(searchTerm)) {
                            card.style.display = 'flex';
                        } else {
                            card.style.display = 'none';
                        }
                    }
                });
                
                updateProductCount();
            });
        }

        // Category filter functionality
        const categoryCheckboxes = document.querySelectorAll('input[type="checkbox"]');
        const allProductsCheckbox = document.getElementById('all-products');

        categoryCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (this.id === 'all-products') {
                    if (this.checked) {
                        // Uncheck all other categories
                        categoryCheckboxes.forEach(cb => {
                            if (cb.id !== 'all-products') {
                                cb.checked = false;
                            }
                        });
                        // Show all products
                        productCards.forEach(card => {
                            card.style.display = 'flex';
                        });
                    }
                } else {
                    // If any specific category is checked, uncheck "All Products"
                    if (this.checked && allProductsCheckbox) {
                        allProductsCheckbox.checked = false;
                    }
                    
                    // Filter products based on selected categories
                    filterByCategory();
                }
                
                updateProductCount();
            });
        });

        // Brand filter functionality
        const brandCheckboxes = document.querySelectorAll('#nike, #adidas, #puma');

        brandCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                filterProducts();
            });
        });

        // Price filter functionality
        if (priceSlider) {
            priceSlider.addEventListener('input', function() {
                filterProducts();
            });
        }

        // Combined filter function
        function filterProducts() {
            const selectedCategories = getSelectedCategories();
            const selectedBrands = getSelectedBrands();
            const maxPrice = parseInt(priceSlider ? priceSlider.value : 1000);
            const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
            
            productCards.forEach(card => {
                const productName = card.querySelector('.product-name');
                const productCategory = card.querySelector('.product-category');
                const productPriceElement = card.querySelector('.product-price');
                
                if (!productName || !productCategory || !productPriceElement) return;
                
                const nameText = productName.textContent.toLowerCase();
                const categoryText = productCategory.textContent.toLowerCase();
                const priceText = productPriceElement.textContent.replace('$', '');
                const price = parseInt(priceText);
                
                // Check search term
                const matchesSearch = nameText.includes(searchTerm) || categoryText.includes(searchTerm);
                
                // Check category filter
                const matchesCategory = selectedCategories.length === 0 || 
                                      allProductsCheckbox?.checked || 
                                      selectedCategories.some(cat => categoryText.includes(cat.toLowerCase()));
                
                // Check brand filter (you'll need to add brand info to your HTML or determine from product name)
                const matchesBrand = selectedBrands.length === 0 || checkProductBrand(nameText, selectedBrands);
                
                // Check price filter
                const matchesPrice = price <= maxPrice;
                
                if (matchesSearch && matchesCategory && matchesBrand && matchesPrice) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
            
            updateProductCount();
        }

        function getSelectedCategories() {
            const categories = [];
            const categoryIds = ['football-boots', 'club-shirts', 'national-team', 'training-gear'];
            
            categoryIds.forEach(id => {
                const checkbox = document.getElementById(id);
                if (checkbox && checkbox.checked) {
                    const label = checkbox.nextElementSibling;
                    if (label) {
                        const spanText = label.querySelector('span');
                        if (spanText) {
                            categories.push(spanText.textContent.trim());
                        }
                    }
                }
            });
            
            return categories;
        }

        function getSelectedBrands() {
            const brands = [];
            const brandIds = ['nike', 'adidas', 'puma'];
            
            brandIds.forEach(id => {
                const checkbox = document.getElementById(id);
                if (checkbox && checkbox.checked) {
                    brands.push(id);
                }
            });
            
            return brands;
        }

        function checkProductBrand(productName, selectedBrands) {
            return selectedBrands.some(brand => productName.includes(brand));
        }

        function filterByCategory() {
            const selectedCategories = getSelectedCategories();
            
            productCards.forEach(card => {
                const productCategory = card.querySelector('.product-category');
                
                if (productCategory) {
                    const categoryText = productCategory.textContent.toLowerCase();
                    
                    if (selectedCategories.length === 0 || 
                        selectedCategories.some(cat => categoryText.includes(cat.toLowerCase()))) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                }
            });
        }

        // Update product count display
        function updateProductCount() {
            const visibleProducts = document.querySelectorAll('.product-card[style*="flex"], .product-card:not([style*="none"])');
            const productsMeta = document.querySelector('.products-meta');
            
            if (productsMeta) {
                const visibleCount = Array.from(productCards).filter(card => 
                    card.style.display !== 'none'
                ).length;
                productsMeta.textContent = `Showing ${visibleCount} of ${productCards.length} products`;
            }
        }

        // Sort functionality
        const sortDropdown = document.querySelector('.sort-dropdown');

        if (sortDropdown) {
            sortDropdown.addEventListener('change', function() {
                const sortValue = this.value;
                const productsGrid = document.getElementById('productsGrid');
                const productArray = Array.from(productCards);
                
                productArray.sort((a, b) => {
                    const aName = a.querySelector('.product-name')?.textContent || '';
                    const bName = b.querySelector('.product-name')?.textContent || '';
                    const aPrice = parseInt(a.querySelector('.product-price')?.textContent.replace('R', '') || '0');
                    const bPrice = parseInt(b.querySelector('.product-price')?.textContent.replace('R', '') || '0');
                    
                    switch(sortValue) {
                        case 'Price: Low to High':
                            return aPrice - bPrice;
                        case 'Price: High to Low':
                            return bPrice - aPrice;
                        case 'Name: A to Z':
                            return aName.localeCompare(bName);
                        default:
                            return 0;
                    }
                });
                
                // Re-append sorted products
                if (productsGrid) {
                    productArray.forEach(card => productsGrid.appendChild(card));
                }
            });
        }

        // Clear all filters function
        function clearAllFilters() {
            // Clear search
            if (searchInput) {
                searchInput.value = '';
            }
            
            // Reset price slider
            if (priceSlider && maxPriceDisplay) {
                priceSlider.value = 500;
                maxPriceDisplay.textContent = 'R500';
            }
            
            // Reset checkboxes
            categoryCheckboxes.forEach(checkbox => {
                if (checkbox.id === 'all-products') {
                    checkbox.checked = true;
                } else {
                    checkbox.checked = false;
                }
            });
            
            // Show all products
            productCards.forEach(card => {
                card.style.display = 'flex';
            });
            
            // Reset sort dropdown
            if (sortDropdown) {
                sortDropdown.value = 'Sort by: Default';
            }
            
            updateProductCount();
        }

        // Initialize
        updateProductCount();

        }); // End of DOMContentLoaded

   </script>

    </body>
</html>