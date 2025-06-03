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


// ========================================
// GET CATEGORY COUNTS FOR FILTER SIDEBAR
// ========================================

// Initializing am empty array to store category counts
$category_count = [];

// SQL Query: Get count of active products for each category
$category_query = " SELECT 
        c.category_name, 
        COUNT(p.product_id) as product_count 
    FROM categories c 
    LEFT JOIN products p ON c.category_id = p.category_id 
    GROUP BY c.category_id, c.category_name
    ORDER BY c.category_name
";

// Execute the category count query
$category_stmt = $conn->prepare($category_query);
$category_stmt->execute();
$category_result = $category_stmt->get_result();

// Loop through results and store in our array
while($row = $category_result->fetch_assoc()) {
    $category_count[$row['category_name']] = $row['product_count'];
}
$category_stmt->close();

// Get total count of all products
$total_query = "SELECT COUNT(*) as total FROM products";
$total_stmt = $conn->prepare($total_query);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_count = $total_result->fetch_assoc()['total'];
$total_stmt->close();


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
                                <span class="product-count"><?php echo $total_count; ?></span>
                            </label>
                        </div>
                        <div class="filter-option">
                            <input type="checkbox" id="football-boots">
                            <label for="football-boots">
                                <span>Football Boots</span>
                                <span class="product-count"><?php echo $category_count['Football Boots'] ?? 0; ?></span>
                            </label>
                        </div>
                        <div class="filter-option">
                            <input type="checkbox" id="club-shirts">
                            <label for="club-shirts">
                                <span>Club Shirts</span>
                                <span class="product-count"><?php echo $category_count['Club Shirts'] ?? 0; ?></span>
                            </label>
                        </div>
                        <div class="filter-option">
                            <input type="checkbox" id="national-team">
                            <label for="national-team">
                                <span>National Kits</span>
                                <span class="product-count"><?php echo $category_count['National Team Shirts'] ?? 0; ?></span>
                            </label>
                        </div>
                        <div class="filter-option">
                            <input type="checkbox" id="footballs">
                            <label for="footballs">
                                <span>Footballs</span>
                                <span class="product-count"><?php echo $category_count['Footballs'] ?? 0; ?></span>
                            </label>
                        </div>
                        <div class="filter-option">
                            <input type="checkbox" id="training-gear">
                            <label for="training-gear">
                                <span>Training Gear</span>
                                <span class="product-count"><?php echo $category_count['Gear'] ?? 0; ?></span>
                            </label>
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div class="filter-section">
                        <h6 class="filter-title">Price Range</h6>
                        <div class="price-range">
                            <input type="range" class="price-slider" id="priceRange" min="0" max="500" value="500">
                            <div class="price-display">
                                <span>$0</span>
                                <span id="maxPrice">$500</span>
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
                        <div class="product-card" onclick="window.location.href='singleProduct.php?product_id=<?php echo htmlspecialchars($row['product_id']); ?>';">
                            <div class="product-image-container">
                                <img class="product-image" src="assets/<?php echo $row['image_url']; ?>"  alt="<?php echo htmlspecialchars($row['product_name']); ?>" onerror="this.onerror=null; this.src='assets/images/Placeholder.png';"/>
                                <div class="product-badge">New</div>
                                <button class="wishlist-btn" onclick="event.stopPropagation();">
                                    <i class="far fa-heart"></i>
                                </button>
                            </div>
                            <div class="product-info">
                                <div class="product-category"><?php echo $row['category_name']; ?></div>
                                <h5 class="product-name"><?php echo htmlspecialchars($row['product_name']); ?></h5>
                                <span class="description mb-1"><?php echo $row['description']; ?></span>
                                <div class="product-price">$ <?php echo htmlspecialchars(number_format($row['price'], 2)); ?></div>
                                <div class="product-actions">
                                    <button class="btn btn-primary-custom" onclick="event.stopPropagation(); window.location.href='singleProduct.php?product_id=<?php echo htmlspecialchars($row['product_id']); ?>';">Buy Now</button>
                                    <button class="add-to-cart btn btn-outline-custom" 
                                            onclick="event.stopPropagation(); addToCart(this, '<?php echo $row['product_id']; ?>', '<?php echo htmlspecialchars($row['product_name']); ?>', '<?php echo $row['price']; ?>', '<?php echo $row['image_url']; ?>');">
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
                                            </div> End of unused products-grid, was hardcoded-->
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
// Wait for DOM to be fully loaded, important for elements to be available else stabilty issues

document.addEventListener('DOMContentLoaded', function() {
    
    // Price range slider functionality
    const priceSlider = document.getElementById('priceRange');
    const maxPriceDisplay = document.getElementById('maxPrice');

    if (priceSlider && maxPriceDisplay) {
        priceSlider.addEventListener('input', function() {
            maxPriceDisplay.textContent = '$' + this.value;
        });
    } //When you drag the price slider, the displayed price updates in real-time

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const productCards = document.querySelectorAll('.product-card'); //Find ALL product cards on the page (returns a list)

    if (searchInput) { //Check if search input exists
        searchInput.addEventListener('input', function() {
            filterProducts(); // Call filterProducts whenever the search input changes
        });
    }

    // Category filter functionality
    const categoryCheckboxes = document.querySelectorAll('input[type="checkbox"]');
    const allProductsCheckbox = document.getElementById('all-products');

    categoryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.id === 'all-products') {//using ids to identify the checkboxes
                if (this.checked) {
                    // Uncheck all other categories if all products is checked
                    categoryCheckboxes.forEach(cb => {
                        if (cb.id !== 'all-products') {
                            cb.checked = false;
                        }
                    });
                }
            } else {
                // If any specific category is checked, uncheck "All Products"
                if (this.checked && allProductsCheckbox) {
                    allProductsCheckbox.checked = false;
                }
            }
            
            filterProducts();
        });
    });

    // Brand filter functionality
    const brandCheckboxes = document.querySelectorAll('#nike, #adidas, #puma'); //Finding the three brand checkboxes specifically by using their ids
    brandCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            filterProducts(); //listen for changes and run the filter
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

        //Gather all current filter settings (what categories are selected, what brands, what price, what search term)
        const selectedCategories = getSelectedCategories();
        const selectedBrands = getSelectedBrands();
        const maxPrice = parseInt(priceSlider ? priceSlider.value : 500);
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        

        productCards.forEach(card => { //looping Loop through every product card
            const productName = card.querySelector('.product-name');
            const productCategory = card.querySelector('.product-category');
            const productPriceElement = card.querySelector('.product-price');
            
            if (!productName || !productCategory || !productPriceElement) return; //Safety check - if any element is missing, skip this product
            

            // Get text content and format it for comparison
            const nameText = productName.textContent.toLowerCase();
            const categoryText = productCategory.textContent.toLowerCase();
            const priceText = productPriceElement.textContent.replace('$', '').trim();
            const price = parseFloat(priceText); //convert price to a number


            if (isNaN(price)) return; // If price is not a number, skip this product


            // Check search term
            const matchesSearch = searchTerm === '' || nameText.includes(searchTerm) || categoryText.includes(searchTerm); //Product matches search if: no search term OR name contains search term OR category contains search term - bugged to be fixed
            
            // Check category filter
            let matchesCategory = true;
            if (!allProductsCheckbox?.checked && selectedCategories.length > 0) { // Only filter by category if "All Products" is NOT checked AND some categories are selected
                matchesCategory = selectedCategories.some(cat => {
                    // Map category names to match your database values
                    const categoryMap = {
                        'football boots': ['football boots', 'boots'],
                        'club shirts': ['club shirts', 'shirts', 'club'],
                        'national kits': ['national kits', 'national team', 'kits'],
                        'footballs': ['footballs', 'ball'],
                        'training gear': ['gear', 'training gear', 'training']
                    };
                    
                    const searchTerms = categoryMap[cat.toLowerCase()] || [cat.toLowerCase()]; // e.g searchTerms = ['gear', 'training gear', 'training'], with fallback to just the category name if not found
                    return searchTerms.some(term => categoryText.includes(term)); //term = 'gear': does 'gear'.includes('gear')? YES
                });
            }
            
            // Check brand filter
            let matchesBrand = true;
            if (selectedBrands.length > 0) {
                matchesBrand = selectedBrands.some(brand => nameText.includes(brand.toLowerCase()));
            }//Check if product name contains any selected brand names
            
            // Check price filter
            const matchesPrice = isNaN(price) || price <= maxPrice;
            



             //Product is shown ONLY if it passes ALL filters
            if (matchesSearch && matchesCategory && matchesBrand && matchesPrice) {
                card.style.display = 'flex'; //showing the product card
            } else {
                card.style.display = 'none'; //hiding the product card
            }
        });
        
        updateProductCount(); // Update the product count after filtering || Showing X of Y products
    }

    // Helper functions to get selected categories and brands

    function getSelectedCategories() {
        const categories = []; //Creating an empty list to store selected categories
        const categoryMappings = { //Mapping checkbox IDs to category names
            'football-boots': 'Football Boots',
            'club-shirts': 'Club Shirts', 
            'national-team': 'National Kits',
            'footballs': 'Footballs',
            'training-gear': 'Training Gear'
        };
        
        Object.keys(categoryMappings).forEach(id => {
            const checkbox = document.getElementById(id); 
            if (checkbox && checkbox.checked) {
                categories.push(categoryMappings[id]);
            } //If checkbox exists and is checked, add the corresponding category name to the list
        });
        
        return categories; // Return the list of selected categories
    }

    function getSelectedBrands() {
        const brands = [];
        const brandIds = ['nike', 'adidas', 'puma'];
        
        brandIds.forEach(id => { // Loop through each brand ID
            const checkbox = document.getElementById(id);
            if (checkbox && checkbox.checked) { // If the checkbox exists and is checked add it to the list
                brands.push(id);
            }
        });
        
        return brands;
    }

    // Update product count display
    function updateProductCount() {
    // Count how many products are currently visible (not hidden)
        const visibleCount = Array.from(productCards).filter(card => 
            card.style.display !== 'none'
        ).length;
        
         // Find the text element that shows the count, in html it is the products-meta class
        const productsMeta = document.querySelector('.products-meta');
        if (productsMeta) {   // Update the text with current numbers
            productsMeta.textContent = `Showing ${visibleCount} of ${productCards.length} products`;
        }
    }

    // Sort functionality, Looking for an element with class 'sort-dropdown'
    const sortDropdown = document.querySelector('.sort-dropdown');

     //Check if the dropdown exists before adding functionality
    if (sortDropdown) {
        sortDropdown.addEventListener('change', function() { //he 'change' event fires when a new option is selected from the dropdown
            const sortValue = this.value; //'this' refers to the dropdown element, 'value' gets the selected option's value
            const productsGrid = document.getElementById('productsGrid');
            const productArray = Array.from(productCards); //Array.from() converts it to an array
            

            // Sort the product cards based on the selected option, This targets my <h5 class="product-name"> elements inside each product card
            productArray.sort((a, b) => {
                const aName = a.querySelector('.product-name')?.textContent || '';
                const bName = b.querySelector('.product-name')?.textContent || '';
                const aPriceText = a.querySelector('.product-price')?.textContent.replace('$', '').trim() || '0';
                const bPriceText = b.querySelector('.product-price')?.textContent.replace('$', '').trim() || '0';
                const aPrice = parseFloat(aPriceText);
                const bPrice = parseFloat(bPriceText);
                
                // Determining sorting logic based on the selected dropdown value
                switch(sortValue) {
                    case 'Price: Low to High':
                        return aPrice - bPrice; //                // Subtract aPrice from bPrice: negative = a comes first, positive = b comes first
                    case 'Price: High to Low':
                        return bPrice - aPrice; // Reversing the low-to-high logic
                    case 'Name: A to Z':
                        return aName.localeCompare(bName); // localeCompare() handles proper alphabetical sorting including special characters
                    default: /// Default case: no sorting (maintain original order) 
                        return 0;// Return 0 means elements are considered equal
                }
            });
            
            // Re-append sorted products by update the DOM with the newly sorted product cards
            if (productsGrid) {
                productArray.forEach(card => productsGrid.appendChild(card));
            }
        });
    }

    // This function resets all filter controls and shows all products

    window.clearAllFilters = function() {
        // Clear the search input field
        if (searchInput) {
            searchInput.value = '';
        }
        
        // Reset price slider
        if (priceSlider && maxPriceDisplay) {
            priceSlider.value = 500;
            maxPriceDisplay.textContent = '$500';
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
    };

    // Initialize
    updateProductCount();

}); // End of DOMContentLoaded

// The code is essentially a bunch of "listeners" that watch for changes, and one main function that decides which products to show or hide based on all the current filter settings.



        // Function to add product to cart in the backgorund with me having to reload the page, it will then also have pop up a message to tell the user that the product has been added to the cart



        function addToCart(button, productId, productName, productPrice, productImage) {
                    // Created form data object to send product details, works like an invisble HTML form submission just <form method "POST">
            const formData = new FormData();
            formData.append('add_to_cart', '1'); // Indicate that this is an add to cart request - 1 just a flag so that formData can be sent to cart.php
            formData.append('product_id', productId);
            formData.append('product_name', productName);
            formData.append('product_price', productPrice);
            formData.append('product_image', productImage);
            formData.append('product_quantity', '1'); // Default quantity since its 1 item.
            
            // Send to cart.php
            fetch('cart.php', { //Fetch API to send data to cart.php
                method: 'POST',
                body: formData
            })
            .then(response => response.text()) //Cart/php wont send anything back, so we wait for the process to finish
            .then(data => {
                // Shows success message to the user
            // alert('Product added to cart!'); //forces me to click ok to continue, annoying
                
                showCardNotification(button, 'Added to cart!');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error adding to cart');
            });
        }

        // The Simple notification function // To be worked on later
            function showCardNotification(button, message) {
                // Create notification
                const notification = document.createElement('div');
                notification.textContent = message;
                notification.style.cssText = `
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    background: #28a745;
                    color: white;
                    padding: 12px;
                    border-radius: 5px;
                    font-size: 14px;
                    font-weight: 500;
                    text-align: center;
                    z-index: 1000;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
                `;
                
                // Find the product-actions div (the container with both buttons)
                const productActions = button.closest('.product-actions');
                
                // Make sure it has relative positioning
                productActions.style.position = 'relative';
                
                // Add notification to cover the buttons area
                productActions.appendChild(notification);
                
                // Remove after 2 seconds
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.parentElement.removeChild(notification);
                    }
                }, 2000);
            }

   </script>

    </body>
</html>