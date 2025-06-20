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
$product = $stmt->get_result();
$stmt->close();

// ========================================
// GET CATEGORY COUNTS FOR FILTER SIDEBAR
// ========================================

$category_count = [];

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

<?php include('layouts/header.php'); ?>

<div class="container-fluid py-4">
    <div class="row">
        <!-- Filters Sidebar -->
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
                            <span class="product-count"><?php echo htmlspecialchars($total_count); ?></span>
                        </label>
                    </div>
                    <div class="filter-option">
                        <input type="checkbox" id="football-boots">
                        <label for="football-boots">
                            <span>Football Boots</span>
                            <span class="product-count"><?php echo htmlspecialchars($category_count['Football Boots'] ?? 0); ?></span>
                        </label>
                    </div>
                    <div class="filter-option">
                        <input type="checkbox" id="club-shirts">
                        <label for="club-shirts">
                            <span>Club Shirts</span>
                            <span class="product-count"><?php echo htmlspecialchars($category_count['Club Shirts'] ?? 0); ?></span>
                        </label>
                    </div>
                    <div class="filter-option">
                        <input type="checkbox" id="national-team">
                        <label for="national-team">
                            <span>National Kits</span>
                            <span class="product-count"><?php echo htmlspecialchars($category_count['National Team Shirts'] ?? 0); ?></span>
                        </label>
                    </div>
                    <div class="filter-option">
                        <input type="checkbox" id="footballs">
                        <label for="footballs">
                            <span>Footballs</span>
                            <span class="product-count"><?php echo htmlspecialchars($category_count['Footballs'] ?? 0); ?></span>
                        </label>
                    </div>
                    <div class="filter-option">
                        <input type="checkbox" id="training-gear">
                        <label for="training-gear">
                            <span>Training Gear</span>
                            <span class="product-count"><?php echo htmlspecialchars($category_count['Gear'] ?? 0); ?></span>
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

        <!-- Products Section -->
        <div class="col-lg-10 col-md-9">
            <div class="products-section">
                <div class="section-header">
                    <div>
                        <h2 class="section-title">Product Locker</h2>
                        <p class="products-meta">Showing 16 of 24 products</p>
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
                        <!-- Product Card -->
                        <div class="product-card" onclick="window.location.href='singleProduct.php?product_id=<?php echo urlencode($row['product_id']); ?>';">
                            <div class="product-image-container">
                                <img class="product-image" 
                                     src="assets/<?php echo htmlspecialchars($row['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($row['product_name']); ?>" 
                                     onerror="this.onerror=null; this.src='assets/images/Placeholder.png';"/>
                                <div class="product-badge">New</div>
                                <button class="wishlist-btn" onclick="event.stopPropagation();">
                                    <i class="far fa-heart"></i>
                                </button>
                            </div>
                            <div class="product-info">
                                <div class="product-category"><?php echo htmlspecialchars($row['category_name']); ?></div>
                                <h5 class="product-name"><?php echo htmlspecialchars($row['product_name']); ?></h5>
                                <span class="description mb-1"><?php echo htmlspecialchars($row['description']); ?></span>
                                <div class="product-price">$<?php echo htmlspecialchars(number_format($row['price'], 2)); ?></div>
                                <div class="product-actions">
                                    <button class="btn btn-primary-custom" 
                                            onclick="event.stopPropagation(); window.location.href='singleProduct.php?product_id=<?php echo urlencode($row['product_id']); ?>';">
                                        Buy Now
                                    </button>
                                    <button class="add-to-cart btn btn-outline-custom" 
                                            onclick="event.stopPropagation(); addToCart(this, '<?php echo htmlspecialchars($row['product_id']); ?>', '<?php echo htmlspecialchars($row['product_name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['price']); ?>', '<?php echo htmlspecialchars($row['image_url']); ?>');">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
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

<!-- Custom JavaScript for functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Price range slider functionality
    const priceSlider = document.getElementById('priceRange');
    const maxPriceDisplay = document.getElementById('maxPrice');

    if (priceSlider && maxPriceDisplay) {
        priceSlider.addEventListener('input', function() {
            maxPriceDisplay.textContent = '$' + this.value;
        });
    }

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const productCards = document.querySelectorAll('.product-card');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            filterProducts();
        });
    }

    // Category filter functionality
    const categoryCheckboxes = document.querySelectorAll('input[type="checkbox"]');
    const allProductsCheckbox = document.getElementById('all-products');

    categoryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.id === 'all-products') {
                if (this.checked) {
                    categoryCheckboxes.forEach(cb => {
                        if (cb.id !== 'all-products') {
                            cb.checked = false;
                        }
                    });
                }
            } else {
                if (this.checked && allProductsCheckbox) {
                    allProductsCheckbox.checked = false;
                }
            }
            
            filterProducts();
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
        const maxPrice = parseInt(priceSlider ? priceSlider.value : 500);
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        
        productCards.forEach(card => {
            const productName = card.querySelector('.product-name');
            const productCategory = card.querySelector('.product-category');
            const productPriceElement = card.querySelector('.product-price');
            
            if (!productName || !productCategory || !productPriceElement) return;
            
            const nameText = productName.textContent.toLowerCase();
            const categoryText = productCategory.textContent.toLowerCase();
            const priceText = productPriceElement.textContent.replace('$', '').trim();
            const price = parseFloat(priceText);

            if (isNaN(price)) return;

            // Check search term
            const matchesSearch = searchTerm === '' || nameText.includes(searchTerm) || categoryText.includes(searchTerm);
            
            // Check category filter
            let matchesCategory = true;
            if (!allProductsCheckbox?.checked && selectedCategories.length > 0) {
                matchesCategory = selectedCategories.some(cat => {
                    const categoryMap = {
                        'football boots': ['football boots', 'boots'],
                        'club shirts': ['club shirts', 'shirts', 'club'],
                        'national kits': ['national kits', 'national team', 'kits'],
                        'footballs': ['footballs', 'ball'],
                        'training gear': ['gear', 'training gear', 'training']
                    };
                    
                    const searchTerms = categoryMap[cat.toLowerCase()] || [cat.toLowerCase()];
                    return searchTerms.some(term => categoryText.includes(term));
                });
            }
            
            // Check brand filter
            let matchesBrand = true;
            if (selectedBrands.length > 0) {
                matchesBrand = selectedBrands.some(brand => nameText.includes(brand.toLowerCase()));
            }
            
            // Check price filter
            const matchesPrice = isNaN(price) || price <= maxPrice;
            
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
        const categoryMappings = {
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

    function updateProductCount() {
        const visibleCount = Array.from(productCards).filter(card => 
            card.style.display !== 'none'
        ).length;
        
        const productsMeta = document.querySelector('.products-meta');
        if (productsMeta) {
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
                const aPriceText = a.querySelector('.product-price')?.textContent.replace('$', '').trim() || '0';
                const bPriceText = b.querySelector('.product-price')?.textContent.replace('$', '').trim() || '0';
                const aPrice = parseFloat(aPriceText);
                const bPrice = parseFloat(bPriceText);
                
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
            
            if (productsGrid) {
                productArray.forEach(card => productsGrid.appendChild(card));
            }
        });
    }

    window.clearAllFilters = function() {
        if (searchInput) {
            searchInput.value = '';
        }
        
        if (priceSlider && maxPriceDisplay) {
            priceSlider.value = 500;
            maxPriceDisplay.textContent = '$500';
        }
        
        categoryCheckboxes.forEach(checkbox => {
            if (checkbox.id === 'all-products') {
                checkbox.checked = true;
            } else {
                checkbox.checked = false;
            }
        });
        
        productCards.forEach(card => {
            card.style.display = 'flex';
        });
        
        if (sortDropdown) {
            sortDropdown.value = 'Sort by: Default';
        }
        
        updateProductCount();
    };

    updateProductCount();
});

function addToCart(button, productId, productName, productPrice, productImage) {
    const formData = new FormData();
    formData.append('add_to_cart', '1');
    formData.append('product_id', productId);
    formData.append('product_name', productName);
    formData.append('product_price', productPrice);
    formData.append('product_image', productImage);
    formData.append('product_quantity', '1');
    
    fetch('cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        showCardNotification(button, 'Added to cart!');
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding to cart');
    });
}

function showCardNotification(button, message) {
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
    
    const productActions = button.closest('.product-actions');
    productActions.style.position = 'relative';
    productActions.appendChild(notification);
    
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