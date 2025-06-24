<?php

// Starting up the session to track user data
session_start();

// Bringing in our database connection
include('server/connection.php');

// Building our main query to grab all products with their images and categories
// Using LEFT JOINs here so we don't lose products that might be missing images or categories
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
$product = $stmt->get_result();
$stmt->close();

// ========================================
// GRABBING CATEGORY COUNTS FOR THE SIDEBAR
// ========================================

// Setting up an array to hold our category counts
$category_count = [];

// This query counts how many products we have in each category
// Pretty handy for showing users what's actually available
$category_query = " SELECT 
        c.category_name, 
        COUNT(p.product_id) as product_count 
    FROM categories c 
    LEFT JOIN products p ON c.category_id = p.category_id 
    GROUP BY c.category_id, c.category_name
    ORDER BY c.category_name
";

$category_stmt = $conn->prepare($category_query);
$category_stmt->execute();
$category_result = $category_stmt->get_result();

// Looping through and storing the counts in our array
while($row = $category_result->fetch_assoc()) {
    $category_count[$row['category_name']] = $row['product_count'];
}
$category_stmt->close();

// Getting the total count of all products for the "All Products" filter
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

<?php include('layouts/header.php'); ?>

<div class="container-fluid py-4">
    <div class="row">
        <!-- Filter sidebar on the left -->
        <div class="col-lg-2 col-md-3"> 
            <div class="filters-sidebar">
                <h5 class="filter-title mb-3">
                    <i class="fas fa-filter me-2"></i>Filter
                </h5>

                <!-- Search box for finding specific products -->
                <div class="filter-section">
                    <div class="search-box">
                        <input type="text" class="search-input" placeholder="Search..." id="searchInput">
                        <i class="fas fa-search search-icon"></i>
                    </div>
                </div>

                <!-- Category filters with product counts -->
                <div class="filter-section">
                    <h6 class="filter-title">Categories</h6>
                    <div class="filter-option">
                        <input type="checkbox" id="all-products" checked>
                        <label for="all-products">
                            <span>All Products</span>
                            <span class="product-count"><?php echo htmlspecialchars($total_count, ENT_QUOTES, 'UTF-8'); ?></span>
                        </label>
                    </div>
                    <div class="filter-option">
                        <input type="checkbox" id="football-boots">
                        <label for="football-boots">
                            <span>Football Boots</span>
                            <span class="product-count"><?php echo htmlspecialchars($category_count['Football Boots'] ?? 0, ENT_QUOTES, 'UTF-8'); ?></span>
                        </label>
                    </div>
                    <div class="filter-option">
                        <input type="checkbox" id="club-shirts">
                        <label for="club-shirts">
                            <span>Club Shirts</span>
                            <span class="product-count"><?php echo htmlspecialchars($category_count['Club Shirts'] ?? 0, ENT_QUOTES, 'UTF-8'); ?></span>
                        </label>
                    </div>
                    <div class="filter-option">
                        <input type="checkbox" id="national-team">
                        <label for="national-team">
                            <span>National Kits</span>
                            <span class="product-count"><?php echo htmlspecialchars($category_count['National Team Shirts'] ?? 0, ENT_QUOTES, 'UTF-8'); ?></span>
                        </label>
                    </div>
                    <div class="filter-option">
                        <input type="checkbox" id="footballs">
                        <label for="footballs">
                            <span>Footballs</span>
                            <span class="product-count"><?php echo htmlspecialchars($category_count['Footballs'] ?? 0, ENT_QUOTES, 'UTF-8'); ?></span>
                        </label>
                    </div>
                    <div class="filter-option">
                        <input type="checkbox" id="training-gear">
                        <label for="training-gear">
                            <span>Training Gear</span>
                            <span class="product-count"><?php echo htmlspecialchars($category_count['Gear'] ?? 0, ENT_QUOTES, 'UTF-8'); ?></span>
                        </label>
                    </div>
                </div>

                <!-- Price range slider - updated for Rands -->
                <div class="filter-section">
                    <h6 class="filter-title">Price Range</h6>
                    <div class="price-range">
                        <input type="range" class="price-slider" id="priceRange" min="0" max="6000" value="6000">
                        <div class="price-display">
                            <span>R0</span>
                            <span id="maxPrice">R6000</span>
                        </div>
                    </div>
                </div>

                <!-- Brand filters with hardcoded counts for now -->
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

                <!-- Button to reset all filters back to default -->
                <button class="clear-filters" onclick="clearAllFilters()">
                    <i class="fas fa-times me-1"></i>Clear Filters
                </button>
            </div>
        </div>

        <!-- Main products display area -->
        <div class="col-lg-10 col-md-9">
            <div class="products-section">
                <div class="section-header">
                    <div>
                        <h2 class="section-title">Product Locker</h2>
                        <p class="products-meta">Showing 16 of 24 products</p>
                    </div>
                    <!-- Dropdown for sorting products -->
                    <select class="sort-dropdown">
                        <option>Sort by: Default</option>
                        <option>Price: Low to High</option>
                        <option>Price: High to Low</option>
                        <option>Name: A to Z</option>
                        <option>Newest First</option>
                        <option>Best Sellers</option>
                    </select>
                </div>

                <!-- Grid layout for displaying all products -->
                <div class="products-grid" id="productsGrid">
                    <?php while ($row = $product->fetch_assoc()) { ?>
                        <!-- Individual product card - made clickable to go to product details -->
                        <div class="product-card" onclick="window.location.href='singleProduct.php?product_id=<?php echo urlencode($row['product_id']); ?>';">
                            <div class="product-image-container">
                                <!-- Product image with fallback to placeholder if image fails to load -->
                                <img class="product-image" 
                                     src="assets/<?php echo htmlspecialchars($row['image_url'], ENT_QUOTES, 'UTF-8'); ?>" 
                                     alt="<?php echo htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8'); ?>" 
                                     onerror="this.onerror=null; this.src='assets/images/Placeholder.png';"/>
                                <div class="product-badge">New</div>
                                <!-- Wishlist button that doesn't trigger the card click -->
                                <button class="wishlist-btn" onclick="event.stopPropagation();">
                                    <i class="far fa-heart"></i>
                                </button>
                            </div>
                            <div class="product-info">
                                <div class="product-category"><?php echo htmlspecialchars($row['category_name'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <h5 class="product-name"><?php echo htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8'); ?></h5>
                                <span class="description mb-1"><?php echo htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <!-- Price display updated to show Rands -->
                                <div class="product-price">R<?php echo htmlspecialchars(number_format($row['price'], 2), ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="product-actions">
                                    <!-- Buy now button -->
                                    <button class="btn btn-primary-custom" 
                                            onclick="event.stopPropagation(); window.location.href='singleProduct.php?product_id=<?php echo urlencode($row['product_id']); ?>';">
                                        Buy Now
                                    </button>
                                    <!-- Add to cart button with all the product data passed securely -->
                                    <button class="add-to-cart btn btn-outline-custom" 
                                            onclick="event.stopPropagation(); addToCart(this, '<?php echo htmlspecialchars($row['product_id'], ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($row['image_url'], ENT_QUOTES, 'UTF-8'); ?>');">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <!-- Pagination controls at the bottom -->
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

<!-- All the JavaScript functionality that makes the page interactive and dynamic -->
<script>
// Waiting for the entire page to load before running any of our JavaScript
// This prevents trying to interact with elements that haven't been created yet
document.addEventListener('DOMContentLoaded', function() {
    
    // Setting up the price range slider that lets users filter by cost
    // We're grabbing both the slider itself and the display that shows the current max price
    const priceSlider = document.getElementById('priceRange');
    const maxPriceDisplay = document.getElementById('maxPrice');

    // Making sure both elements exist before trying to work with them
    if (priceSlider && maxPriceDisplay) {
        // Every time someone drags the slider, we update the display to show the new price
        // This gives immediate visual feedback about what price range they're selecting
        priceSlider.addEventListener('input', function() {
            maxPriceDisplay.textContent = 'R' + this.value;
        });
    }

    // Setting up the search box that filters products as users type
    // This creates a live search experience where results appear instantly
    const searchInput = document.getElementById('searchInput');
    const productCards = document.querySelectorAll('.product-card');

    if (searchInput) {
        // Triggering the filter every time someone types in the search box
        // Using 'input' event instead of 'keyup' catches paste events and other input methods
        searchInput.addEventListener('input', function() {
            filterProducts();
        });
    }

    // Managing all the category filter checkboxes in the sidebar
    // These let users narrow down products by type like boots, shirts, etc.
    const categoryCheckboxes = document.querySelectorAll('input[type="checkbox"]');
    const allProductsCheckbox = document.getElementById('all-products');

    categoryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Special logic for the "All Products" checkbox
            // When someone checks this, we want to uncheck all the specific categories
            if (this.id === 'all-products') {
                if (this.checked) {
                    categoryCheckboxes.forEach(cb => {
                        if (cb.id !== 'all-products') {
                            cb.checked = false;
                        }
                    });
                }
            } else {
                // If someone selects a specific category, we automatically uncheck "All Products"
                // This makes the filtering logic cleaner and more intuitive for users
                if (this.checked && allProductsCheckbox) {
                    allProductsCheckbox.checked = false;
                }
            }
            
            // After updating checkboxes, we need to refilter all the products
            filterProducts();
        });
    });

    // Setting up brand filtering for Nike, Adidas, and Puma
    // Users can select multiple brands at once to see products from those manufacturers
    const brandCheckboxes = document.querySelectorAll('#nike, #adidas, #puma');
    brandCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Every time a brand checkbox changes, we refilter the products
            filterProducts();
        });
    });

    // Connecting the price slider to our filtering system
    // This ensures price changes immediately affect what products are visible
    if (priceSlider) {
        priceSlider.addEventListener('input', function() {
            filterProducts();
        });
    }

    // This is the main filtering engine that decides which products to show
    // It combines search terms, categories, brands, and price ranges into one decision
    function filterProducts() {
        // Gathering all the current filter settings from the interface
        const selectedCategories = getSelectedCategories();
        const selectedBrands = getSelectedBrands();
        const maxPrice = parseInt(priceSlider ? priceSlider.value : 6000);
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        
        // Going through each product card and deciding whether to show or hide it
        productCards.forEach(card => {
            // Extracting the key information from each product card
            const productName = card.querySelector('.product-name');
            const productCategory = card.querySelector('.product-category');
            const productPriceElement = card.querySelector('.product-price');
            
            // Safety check - if any of these elements are missing, skip this card
            if (!productName || !productCategory || !productPriceElement) return;
            
            // Converting text to lowercase for case-insensitive searching
            const nameText = productName.textContent.toLowerCase();
            const categoryText = productCategory.textContent.toLowerCase();
            
            // Extracting the numeric price by removing the 'R' symbol and any whitespace
            const priceText = productPriceElement.textContent
                .replace('R', '')        // Remove R symbol
                .replace(/,/g, '')       // Remove commas
                .replace(/\s/g, '')      // Remove all whitespace
                .trim();
            const price = parseFloat(priceText);

            // If we can't parse the price, skip this product to avoid errors
            if (isNaN(price)) return;

            // Testing if the product matches the search term
            // We check both the product name and category for matches
            const matchesSearch = searchTerm === '' || nameText.includes(searchTerm) || categoryText.includes(searchTerm);
            
            // Testing if the product matches the selected categories
            let matchesCategory = true;
            // Only apply category filtering if "All Products" isn't checked and we have specific categories selected
            if (!allProductsCheckbox?.checked && selectedCategories.length > 0) {
                matchesCategory = selectedCategories.some(cat => {
                    // Creating a mapping of filter names to possible category text variations
                    // This handles cases where the display name might be slightly different from the filter name
                    const categoryMap = {
                        'football boots': ['football boots', 'boots'],
                        'club shirts': ['club shirts', 'club'],
                        'national kits': ['national kits', 'national team'],
                        'footballs': ['footballs', 'ball'],
                        'training gear': ['gear', 'training gear', 'training']
                    };
                    
                    // Getting all possible search terms for this category
                    const searchTerms = categoryMap[cat.toLowerCase()] || [cat.toLowerCase()];
                    
                    // Checking if any of the search terms match the product's category
                    return searchTerms.some(term => categoryText.includes(term));
                });
            }
            
            // Testing if the product matches the selected brands
            let matchesBrand = true;
            // Only filter by brand if the user has actually selected some brands
            if (selectedBrands.length > 0) {
                // Checking if the product name contains any of the selected brand names
                matchesBrand = selectedBrands.some(brand => nameText.includes(brand.toLowerCase()));
            }
            
            // Testing if the product is within the selected price range
            const matchesPrice = isNaN(price) || price <= maxPrice;
            
            // Final decision: show the product only if it passes ALL filter criteria
            if (matchesSearch && matchesCategory && matchesBrand && matchesPrice) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
        
        // After filtering, update the count display to show how many products are visible
        updateProductCount();
    }

    // Helper function that figures out which categories the user has selected
    // Returns an array of category names that we can use for filtering
    function getSelectedCategories() {
        const categories = [];
        
        // Mapping checkbox IDs to their corresponding category names in the database
        const categoryMappings = {
            'football-boots': 'Football Boots',
            'club-shirts': 'Club Shirts', 
            'national-team': 'National Kits',
            'footballs': 'Footballs',
            'training-gear': 'Training Gear'
        };
        
        // Going through each category mapping and checking if its checkbox is selected
        Object.keys(categoryMappings).forEach(id => {
            const checkbox = document.getElementById(id); 
            if (checkbox && checkbox.checked) {
                categories.push(categoryMappings[id]);
            }
        });
        
        return categories;
    }

    // Helper function that builds a list of selected brands
    // Similar to categories but simpler since brand names match their IDs
    function getSelectedBrands() {
        const brands = [];
        const brandIds = ['nike', 'adidas', 'puma'];
        
        // Checking each brand checkbox and adding it to the list if selected
        brandIds.forEach(id => {
            const checkbox = document.getElementById(id);
            if (checkbox && checkbox.checked) {
                brands.push(id);
            }
        });
        
        return brands;
    }

    // Function that updates the "Showing X of Y products" text after filtering
    // This gives users feedback about how their filters are affecting the results
    function updateProductCount() {
        // Counting how many product cards are currently visible
        const visibleCount = Array.from(productCards).filter(card => 
            card.style.display !== 'none'
        ).length;
        
        // Finding the element that displays the count and updating its text
        const productsMeta = document.querySelector('.products-meta');
        if (productsMeta) {
            productsMeta.textContent = `Showing ${visibleCount} of ${productCards.length} products`;
        }
    }

    // Setting up the sort dropdown that lets users organize products in different ways
    // This gives users control over the order they see products in
    const sortDropdown = document.querySelector('.sort-dropdown');

    if (sortDropdown) {
        sortDropdown.addEventListener('change', function() {
            const sortValue = this.value;
            const productsGrid = document.getElementById('productsGrid');
            
            // Converting the NodeList of product cards into a regular array so we can sort it
            const productArray = Array.from(productCards);
            
            // Sorting the array based on the user's selection
            productArray.sort((a, b) => {
                // Extracting product names for alphabetical sorting
                const aName = a.querySelector('.product-name')?.textContent || '';
                const bName = b.querySelector('.product-name')?.textContent || '';
                
                // Extracting and parsing prices for price-based sorting
                const aPriceText = a.querySelector('.product-price')?.textContent.replace('R', '').replace(/,/g, '').trim() || '0';
                const bPriceText = b.querySelector('.product-price')?.textContent.replace('R', '').replace(/,/g, '').trim() || '0'; 
                const aPrice = parseFloat(aPriceText);
                const bPrice = parseFloat(bPriceText);
                
                // Applying the appropriate sorting logic based on user selection
                switch(sortValue) {
                    case 'Price: Low to High':
                        return aPrice - bPrice;
                    case 'Price: High to Low':
                        return bPrice - aPrice;
                    case 'Name: A to Z':
                        return aName.localeCompare(bName);
                    default:
                        // For "Default" or any unrecognized option, maintain current order
                        return 0;
                }
            });
            
            // Reordering the DOM elements to match our sorted array
            // This physically moves the product cards around on the page
            if (productsGrid) {
                productArray.forEach(card => productsGrid.appendChild(card));
            }
        });
    }

    // Creating a global function that resets all filters back to their default state
    // This is attached to the "Clear Filters" button and helps users start over easily
    window.clearAllFilters = function() {
        // Clearing the search input
        if (searchInput) {
            searchInput.value = '';
        }
        
        // Resetting the price slider to its maximum value
        if (priceSlider && maxPriceDisplay) {
            priceSlider.value = 6000;
            maxPriceDisplay.textContent = 'R6000';
        }
        
        // Resetting all category checkboxes - checking "All Products" and unchecking everything else
        categoryCheckboxes.forEach(checkbox => {
            if (checkbox.id === 'all-products') {
                checkbox.checked = true;
            } else {
                checkbox.checked = false;
            }
        });
        
        // Making sure all product cards are visible again
        productCards.forEach(card => {
            card.style.display = 'flex';
        });
        
        // Resetting the sort dropdown to its default option
        if (sortDropdown) {
            sortDropdown.value = 'Sort by: Default';
        }
        
        // Updating the product count to reflect that all products are now showing
        updateProductCount();
    };

    // Running the product count update when the page first loads
    // This ensures the count is accurate even before any filtering happens
    updateProductCount();
});

// Function that handles adding products to the shopping cart using AJAX
// This runs when someone clicks the cart button on any product card
function addToCart(button, productId, productName, productPrice, productImage) {
    // Building the data we need to send to the server
    // Using FormData makes it easy to send this info via POST request
    const formData = new FormData();
    formData.append('add_to_cart', '1');
    formData.append('product_id', productId);
    formData.append('product_name', productName);
    formData.append('product_price', productPrice);
    formData.append('product_image', productImage);
    formData.append('product_quantity', '1'); // Always adding just 1 item at a time
    
    // Sending the data to cart.php without refreshing the page
    // This creates a smooth user experience where adding to cart feels instant
    fetch('cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        // If the request was successful, show a nice confirmation message
        showCardNotification(button, 'Added to cart!');
    })
    .catch(error => {
        // If something went wrong, log the error and show a basic alert
        console.error('Error:', error);
        alert('Error adding to cart');
    });
}

// Creating and displaying a temporary notification message when items are added to cart
// This gives users immediate feedback that their action was successful
function showCardNotification(button, message) {
    // Creating a new div element to hold our notification message
    const notification = document.createElement('div');
    notification.textContent = message;
    
    // Styling the notification to look nice and be positioned correctly
    // Using inline styles here since this is a temporary element
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
    
    // Finding the product actions container and adding our notification to it
    const productActions = button.closest('.product-actions');
    productActions.style.position = 'relative'; // Needed for absolute positioning to work
    productActions.appendChild(notification);
    
    // Setting up a timer to automatically remove the notification after 2 seconds
    // This keeps the interface clean and prevents notifications from piling up
    setTimeout(() => {
        if (notification.parentElement) {
            notification.parentElement.removeChild(notification);
        }
    }, 2000);
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>