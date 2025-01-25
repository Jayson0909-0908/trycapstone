<?php
include('header.php');
include('footer.php');
require_once 'connection.php';
$db = new database();

// Fetch categories
$categories = $db->readAllActiveCategory();

// Fetch products by category if available
if (isset($_GET['category'])) {
    $categoryID = $_GET['category'];
    $products = $db->getproductActive($categoryID);
    $cid = $products['categname'];
    $product = $db->getproductsbycategory($cid);
  
    if (!empty($product) > 0) {
      echo '<div class="product-grid">'; // Start of the product grid container
        foreach ($product as $row) {
            ?>
            <style>
                                  .productResultContainer:hover{
                                      cursor: pointer;
                                  }
                              </style>
            <div class="product-items" id="product-items">
                <div class="card shadow">
                    <div class="card-body2 productContainer" id="modalbtn" data-pid="<?=$row['productID']?>">
                        <center>
                            <img src="uploads/image/<?=$row['image']?>" alt="product img" class="productImage">
                            <h4 class="productName" id="productName"><?= strlen($row['brandID']) > 10 ? substr($row['brandID'], 0, 15) . '...' : $row['brandID'] ?></h4>
                            <p class="productPrice">₱<?=$row['unitPrice']?></p>
                            <p class="productVatStatus" style="display: none;">
                        <?= $row['isVatable'] == 1 ? "Vatable" : "Non-Vatable" ?>
                    </p>
                        </center>
                    </div>
                </div>
            </div>
            <?php
        }
        echo '</div>'; // End of the product grid container
    } else {
       
        echo "<p>No products available for this category.</p>";
    }
    
    exit; // Stop further execution after product output
  }
  
// Check if we're showing all products
if (isset($_GET['showAll']) && $_GET['showAll'] == 'true') {
    $allproducts = $db->readAllActiveproduct(); // Fetch all products

    if (!empty($allproducts)) {
        echo '<div class="product-grid">';
        foreach ($allproducts as $row) {
            ?>
            <div class="product-items" id="product-items">
                <div class="card shadow">
                    <div class="card-body2 productContainer" id="modalbtn" data-pid="<?= htmlspecialchars($row['productID']) ?>">
                        <img src="uploads/image/<?= htmlspecialchars($row['image']) ?>" alt="product img" class="productImage">
                        <h4 class="productName">
                            <?= strlen($row['brandID']) > 10 ? substr(htmlspecialchars($row['brandID']), 0, 15) . '...' : htmlspecialchars($row['brandID']) ?>
                        </h4>
                        <p class="productPrice">₱<?= number_format($row['unitPrice'], 2) ?></p>
                        <p class="productVatStatus" style="display: none;">
                        <?= $row['isVatable'] == 1 ? "Vatable" : "Non-Vatable" ?>
                    </p>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        echo "<p>No products available.</p>";
    }
    exit; // Stop further execution after product output
} else {
    $allproducts = $db->readAllActiveproduct();
    // Fetch all active products if no category is selected
   
}

?>
    <style>
        /* Fix for product grid alignment */
        #products-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px; /* Adds spacing between grid items */
            margin-top: 20px;
        }

        .product-items {
            margin: 0 auto;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container" style="margin-left: 50px;margin-top: 50px;">
        <div class="row">
            <!-- Sidebar or other sections can go here -->
            <div class="col-10">
                <!-- Categories Section -->
                <div>
                    <div class="row">
                        <div class="col-11" style="margin-top: 20px;">
                            <div class="title-search-container d-flex justify-content-between align-items-center">
                                <div class="h4">Category</div>
                                <!-- Search Bar -->
                                <div class="search-container">
                                    <input type="text" placeholder="Search" id="search-bar" class="form-control" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Scrollable Categories -->
                <div id="category-section">
                    <div class="category-scroll-container">
                        <?php foreach ($categories as $category) { ?>
                            <div class="category-item" data-category="<?= htmlspecialchars($category['categID']) ?>">
                                <div class="card shadow text-center">
                                    <div class="card-body1">
                                        <img src="uploads/categoryimage/<?= htmlspecialchars($category['image']) ?>" alt="category img">
                                        <h6><?= htmlspecialchars($category['categname']) ?></h6>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                  </div>
                </div>
                <div class="h4">Products</div>              
                <div class="row">
                    <div class="col-11" style="margin-top: 20px;">
                <!-- Products Section -->   
                <div class="backButton" id="backButton" style="display: none;">Show All Products</div>
                <div id="products-section" class="products-section" style="margin-top: 2px;">
                    <?php if (!empty($allproducts)) { ?>
                        <?php foreach ($allproducts as $row) { ?>
                            <div class="product-items" id="product-items">
                                <div class="card shadow">
                                    <div class="card-body2 productContainer" id="modalbtn" data-pid="<?= htmlspecialchars($row['productID']) ?>">
                                        <img src="uploads/image/<?= htmlspecialchars($row['image']) ?>" alt="product img" class="productImage">
                                        <h4 class="productName">
                                            <?= strlen($row['brandID']) > 10 ? substr(htmlspecialchars($row['brandID']), 0, 15) . '...' : htmlspecialchars($row['brandID']) ?>
                                        </h4>
                                        <p class="productPrice">₱<?= number_format($row['unitPrice'], 2) ?></p>
                                        <p class="productVatStatus" style="display: none;">
                        <?= $row['isVatable'] == 1 ? "Vatable" : "Non-Vatable" ?>
                    </p>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <p>No products available.</p>
                    <?php } ?>
                </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<style>
    /* Overall container for horizontal scroll */
    .category-scroll-container {
        display: flex;
        overflow-x: auto;
        padding: 15px 0;
        border-bottom: 1px solid #ddd;
        -webkit-overflow-scrolling: touch; /* Smooth scrolling for mobile */
        scrollbar-width: thin; /* Firefox */
        scrollbar-color: transparent transparent; /* Make scrollbar track invisible in Firefox */
    }

    /* Hide the scrollbar on non-hover */
    .category-scroll-container::-webkit-scrollbar {
        height: 4px; /* Very thin scrollbar */
        background: transparent; /* Hide the scrollbar background */
    }

    .category-scroll-container::-webkit-scrollbar-thumb {
        background-color: transparent; /* Invisible thumb */
        border-radius: 10px;
    }

    .category-scroll-container::-webkit-scrollbar-track {
        background: transparent; /* Make track invisible */
    }

    /* Category item styling */
    .category-item {
        flex: 0 0 auto;
        text-align: center;
        margin: 0 12px; /* Space between items */
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .category-item img {
        border-radius: 8px;
        width: 100%;
        max-width: 120px; /* Control image size */
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .category-item:hover {
        cursor: pointer;
        transform: translateY(-5px); /* Lift the item up on hover */
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); /* Subtle shadow for the whole item */
    }

    /* Title styling */
    .category-item h6 {
        font-weight: 500;
        margin-top: 8px;
        font-size: 14px; /* Slightly smaller font for better proportion */
        color: #555;
        transition: color 0.3s ease;
    }

    /* Hover color change for title */
    .category-item:hover h6 {
        color: #007bff; /* Highlight color on hover */
    }
</style>
<div class="sidenavfortransaction">
  <ul class="sidebar-nav" id="sidebar-nav">
  <?php 
  $status = $_SESSION['position']=='Admin'?"1":"0";
  if($status == 1){

  echo "<li class='nav-item' style='margin-top: 50px;'>
    <a class='nav-link collapsed' id='dashboard' name='dashboard' title='Dashboard' href='index.php'>
      <i class='bi bi-grid'></i>
    </a>
  </li><!-- End Dashboard Nav -->
  <li class='nav-item'>
    <a class='nav-link collapsed ' title='sales' href='sales.php'>
      <i class='bi bi-receipt'></i>
    </a>
  </li><!-- End Dashboard Nav -->";
  }else{
    echo "<li class='nav-item' style='margin-top: 50px;'>
    <a class='nav-link collapsed' id='dashboard' name='dashboard' title='Dashboard' href='index.php'>
      <i class='bi bi-grid'></i>
    </a>
  </li><!-- End Dashboard Nav -->
  <li class='nav-item'>
    <a class='nav-link collapsed ' title='sales' href='sales.php'>
      <i class='bi bi-receipt'></i>
    </a>
  </li><!-- End Dashboard Nav -->";
  }
  ?>
  </ul>
</div>
<div style="margin-left:110px;margin-right:20px">
    <div class="row">
      <div class="col-8" style="margin-top: 20px;">
      <div class="pagetitle">
      <!-- <h1><b>Products</b></h1> -->
          </div><!-- End Page Title -->
          <div>
          <h5><b>
          <!-- <a href="transaction.php"><i class="fas fa-arrow-left main-title"></i></a>
       </b></h5>
      </div> -->
    <hr>
      <div class="row">
                <?php 
                $product = $db->readproduct();
                if($db->product() > 0){
                    foreach($product as $row){
                        ?>
                        
                        <?php
                    }
                }
                else {
                    echo "No data Available";
                }

                ?>
       
            </div>
            </div>
      </div>
    </div>
</div>
</div>
</div>
<div class="posOrderContainer">
    <div style="background: linear-gradient(0deg, rgba(254, 52, 148, 1) 0%, rgba(126, 65, 150, 1) 100%);">
        <br>
        <p style="margin: 0px;color: white;font-size: 20px;font-weight: bold;text-align:center;">K3O</p>
        <p id="current-date" style="font-weight: bold;text-align:center;font-size:10px;color:white;"></p>
        <br>
    </div>
    <div class="pos_items_container">
        <div class="pos_items">
            <p style="text-align: center;font-size:20px;padding:55px;text-transform:uppercase">No data</p>
        </div>
    </div>
<div class="total-container" style="display: flex; flex-direction: column; gap: 10px;">
    <!-- Subtotal -->
    <div class="subtotal" style="display: flex; justify-content: space-between;">
        <span style="font-size: 15px;">Subtotal:</span>
        <span class="item_total--value" style="font-size: 15px; font-weight: bold;">₱0.00</span>
    </div>




    <div class="discount" style="display: flex; justify-content: space-between; align-items: center;">
    <span style="font-size: 15px;">Discount:</span>
    <span style="font-size: 15px; display: flex; align-items: center; gap: 10px;">
        <!-- Radio buttons for PWD and Senior -->
        <label style="display: flex; align-items: center; gap: 5px;">
            <input type="radio" name="discountType" value="PWD" class="toggleable-radio" style="width: 16px; height: 16px;" /> PWD
        </label>
        <label style="display: flex; align-items: center; gap: 5px;">
            <input type="radio" name="discountType" value="Senior" class="toggleable-radio" style="width: 16px; height: 16px;" /> Senior
        </label>
        
        <!-- Percentage input and discount display -->
        <input class="item_total--percentdiscount" type="number" min="0" max="100" value="0" style="width: 40px; text-align: right;" disabled /> %
        <span class="item_total--discount" style="font-weight: bold;">(₱0.00)</span>
    </span>
</div>

    <!-- Tax -->
    <div class="tax" style="display: flex; justify-content: space-between;">
        <span style="font-size: 15px;">Vat:</span>
        <span class="item_total--vat" style="font-size: 15px; font-weight: bold;">₱0.00</span>
    </div>

    <!-- Total -->
    <div class="total" style="display: flex; justify-content: space-between;">
        <span style="font-size: 15px;">Total:</span>
        <span class="item_total--total" style="font-size: 20px; font-weight: bold;">₱0.00</span>
    </div>
</div>

<style>
.voidBtn {
    font-size: 40px;
    text-align: center;
    display: block;
    border: 2px solid rgb(255, 0, 43);
    border-radius: 5px;
    padding: 10px;
    color: red;
    text-decoration: none;
    transition: background-color 0.3s, color 0.3s; /* Ensure both color and background-color transition smoothly */
}

.voidBtn:hover {
    color: white; /* Change text color to white */
    background-color: red; /* Change background to red */
}

.checkOutBtn {
    font-size: 40px;
    text-align: center;
    display: block;
    border: 2px solid green;
    border-radius: 5px;
    padding: 10px;
    color: green; /* Text color is green by default */
    text-decoration: none;
    transition: background-color 0.3s, color 0.3s; /* Smooth transition */
}

.checkOutBtn:hover {
    background-color: green; /* Background turns green on hover */
    color: white; /* Optional: Change text to white for better contrast */
}

</style>

    <div class="checkout-container">
        <div class="row">
            <div class="col-4">
            <a href="javascript:void(0);" class="voidBtn">VOID</a>
            </div>
            <div class="col-8">
        <a href="javascript:void(0);" class="checkOutBtn">CHECKOUT</a>
        </div>
        </div>
    </div>
</div>

</main>
</body>

<script>
    let productJson = <?= json_encode($product)?>;
    var product = {};
    console.log(product); 
    productJson.forEach(($row) => {
      product[$row.productID] = {
        name: $row.brandID,
        price: $row.unitPrice,
        vat: $row.isVatable
      }
    });
</script>
<script src="assets/js/script.js?v=<?= time() ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    applySearchFunctionality();

    // Event listeners for category selection
    document.querySelectorAll('.category-item').forEach(function (categoryItem) {
        categoryItem.addEventListener('click', function () {
            const categoryID = this.getAttribute('data-category');
            fetchProductsByCategory(categoryID);
        });
    });

    // Event listener for "Back" button
    document.getElementById('backButton').addEventListener('click', function () {
        showAllProducts();
    });
});

// Apply search functionality
function applySearchFunctionality() {
    const searchBar = document.getElementById('search-bar');
    searchBar.addEventListener('input', function () {
        const searchTerm = searchBar.value.toLowerCase();
        filterItems(document.querySelectorAll('.product-items'), '.productName', searchTerm);
    });
}

// Helper function to filter items based on the search term
function filterItems(items, selector, searchTerm) {
    items.forEach(function (item) {
        const textContent = item.querySelector(selector).textContent.toLowerCase();
        item.style.display = textContent.includes(searchTerm) ? 'block' : 'none';
    });
}

// Fetch and display products by category dynamically
function fetchProductsByCategory(categoryID) {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `?category=${encodeURIComponent(categoryID)}`, true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            const productsSection = document.getElementById('products-section');
            const categoriesContainer = document.getElementById('category-section');
            
            // Clear existing products before adding new ones
            productsSection.innerHTML = ''; 
            categoriesContainer.offsetHeight;
            productsSection.offsetHeight;
            // Update with new products
            productsSection.innerHTML = xhr.responseText;
            document.getElementById('backButton').style.display = 'inline-block'; // Show back button

        } else {
            console.error('Failed to fetch products:', xhr.statusText);
        }
    };
    xhr.onerror = function () {
        console.error('Request error');
    };
    xhr.send();
}

// Show all products when the back button is clicked
function showAllProducts() {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', '?showAll=true', true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            const productsSection = document.getElementById('products-section');
            productsSection.innerHTML = xhr.responseText; // Update with all products
            document.getElementById('backButton').style.display = 'none'; // Hide back button
            document.getElementById('products-section').style.display = 'inline-block';
        } else {
            console.error('Failed to fetch all products:', xhr.statusText);
        }
    };
    xhr.onerror = function () {
        console.error('Request error');
    };
    xhr.send();
}

// Attach event listeners to category items
document.querySelectorAll('.category-item').forEach((item) => {
    item.addEventListener('click', () => {
        const categoryID = item.getAttribute('data-category');
        fetchProductsByCategory(categoryID);
    });
});

document.getElementById('backButton').addEventListener('click', function () {
    // Show the category list and hide the product list
    document.getElementById('backButton').style.display = 'none';
    document.getElementById('products-section').style.display = 'inline-block';

    // Clear the search input when going back to categories
    document.getElementById('search-bar').value = '';
    applySearchFunctionality();
});

function getSubtotal() {
    const subtotalElement = document.querySelector(".item_total--value");
    if (!subtotalElement) {
        console.error("Subtotal element not found.");
        return 0; // Fallback to 0
    }
    const subtotalText = subtotalElement.textContent.replace(/[₱,]/g, ""); // Remove ₱ and commas
    return parseFloat(subtotalText) || 0; // Parse to a number, fallback to 0 if invalid
}

// Function to update total dynamically
function updateTotal(discountPercent) {
    const subtotal = getSubtotal(); // Get the current subtotal
    const discountElement = document.querySelector('.item_total--discount');
    const totalElement = document.querySelector('.item_total--total'); // Select the total span
    const vatElement = document.querySelector('.item_total--vat'); // Select the VAT element

    // Get the current VAT amount from the DOM (it should be in the .item_total--vat element)
    const currentVat = parseFloat(vatElement.textContent.replace('₱', '').trim()) || 0;

    // Calculate discount value
    const discountValue = (subtotal * discountPercent) / 100;

    // Check if discount exceeds subtotal
    if (discountValue > subtotal) {
        alert("Discount cannot be greater than the subtotal!");
        discountElement.textContent = `(₱0.00)`; // Reset discount display
        totalElement.textContent = loadScript.addCommas(`₱${subtotal.toFixed(2)}`); // Reset total to subtotal
        vatElement.textContent = `₱0.00`; // Reset VAT display
        return; // Exit early
    }

    // Calculate the discounted subtotal
    const discountedSubtotal = subtotal - discountValue;

    // Add VAT to the discounted subtotal
    const total = discountedSubtotal + currentVat; // Add VAT from the VAT element

    // Update displayed values
    discountElement.textContent = loadScript.addCommas(`(₱${discountValue.toFixed(2)})`);
    totalElement.textContent = loadScript.addCommas(`₱${total.toFixed(2)}`); // Update the total span
}

// Event listener for discount input
document.addEventListener('input', function (e) {
    if (e.target.matches('.item_total--percentdiscount')) { // Match the discount input
        const discountPercent = parseFloat(e.target.value) || 0;
        updateTotal(discountPercent);
    }
});

// Handle enabling/disabling discount input
document.querySelectorAll('.toggleable-radio').forEach((radio) => {
    radio.addEventListener('click', function () {
        const discountInput = document.querySelector('.item_total--percentdiscount');
        const discountElement = document.querySelector('.item_total--discount');

        // Toggle the radio button state
        if (this.checked) {
            this.dataset.wasChecked = this.dataset.wasChecked === "true" ? "false" : "true";
            if (this.dataset.wasChecked === "true") {
                this.checked = false;
            }
        } else {
            this.dataset.wasChecked = "false";
        }

        // Check if any radio button is selected
        const isSelected = Array.from(document.querySelectorAll('.toggleable-radio')).some(r => r.checked);

        if (isSelected) {
            discountInput.disabled = false; // Enable the discount input
        } else {
            discountInput.disabled = true; // Disable the discount input
            discountInput.value = 0; // Reset discount input value to 0
            discountElement.textContent = `(₱0.00)`; // Reset displayed discount value
            updateTotal(0); // Update total with 0% discount
        }
    });
});

// Initialize total on page load
updateTotal(0);

            </script>