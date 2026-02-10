<?php
// Enable error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session for cart
session_start();

// Set a session ID for cart if not exists
if (!isset($_COOKIE['session_id'])) {
    $session_id = uniqid('session_', true);
    setcookie('session_id', $session_id, time() + (86400 * 30), "/"); // 30 days
    $_COOKIE['session_id'] = $session_id;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini E-commerce API Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --success-color: #4cc9f0;
            --danger-color: #f72585;
            --warning-color: #f8961e;
            --dark-color: #212529;
            --light-color: #f8f9fa;
            --gray-color: #6c757d;
            --border-color: #dee2e6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            color: var(--dark-color);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header Styles */
        header {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 30px 0;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(67, 97, 238, 0.3);
            position: relative;
            overflow: hidden;
        }

        header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .header-content {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 0 20px;
        }

        h1 {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .tagline {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 25px;
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: 5px solid var(--primary-color);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            color: white;
            font-size: 1.5rem;
        }

        .stat-info h3 {
            font-size: 1.1rem;
            color: var(--gray-color);
            margin-bottom: 5px;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark-color);
        }

        /* Main Content */
        .main-content {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
        }

        @media (max-width: 992px) {
            .main-content {
                grid-template-columns: 1fr;
            }
        }

        /* API Tester Section */
        .tester-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 25px;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--border-color);
        }

        .section-title i {
            color: var(--primary-color);
        }

        /* Tabs */
        .tabs {
            display: flex;
            margin-bottom: 25px;
            border-bottom: 1px solid var(--border-color);
            flex-wrap: wrap;
        }

        .tab {
            padding: 12px 25px;
            background: none;
            border: none;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            color: var(--gray-color);
            transition: all 0.3s ease;
            position: relative;
        }

        .tab:hover {
            color: var(--primary-color);
        }

        .tab.active {
            color: var(--primary-color);
        }

        .tab.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: var(--primary-color);
            border-radius: 3px 3px 0 0;
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Form Styles */
        .endpoint-form {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark-color);
        }

        input, select, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        /* Method Badge */
        .method-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-right: 10px;
        }

        .method-get { background: #61affe; color: white; }
        .method-post { background: #49cc90; color: white; }
        .method-put { background: #fca130; color: white; }
        .method-delete { background: #f93e3e; color: white; }

        /* Buttons */
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-success {
            background: var(--success-color);
            color: white;
        }

        .btn-danger {
            background: var(--danger-color);
            color: white;
        }

        .btn-block {
            width: 100%;
        }

        /* Response Panel */
        .response-panel {
            background: #1e1e1e;
            color: #f8f8f2;
            padding: 20px;
            border-radius: 10px;
            margin-top: 25px;
            max-height: 400px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .response-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #4cc9f0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Live Cart Section */
        .cart-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .cart-items {
            max-height: 400px;
            overflow-y: auto;
            margin-bottom: 20px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-item-img {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--primary-color);
            font-size: 1.2rem;
        }

        .cart-item-info {
            flex: 1;
        }

        .cart-item-name {
            font-weight: 500;
            margin-bottom: 5px;
        }

        .cart-item-price {
            color: var(--primary-color);
            font-weight: 600;
        }

        .cart-item-quantity {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quantity-btn {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            border: none;
            background: var(--border-color);
            color: var(--dark-color);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .quantity-btn:hover {
            background: var(--primary-color);
            color: white;
        }

        .cart-total {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            font-weight: 600;
            font-size: 1.2rem;
            color: var(--primary-color);
        }

        .empty-cart {
            text-align: center;
            padding: 40px 20px;
            color: var(--gray-color);
        }

        .empty-cart i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: var(--border-color);
        }

        /* Documentation Section */
        .docs-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-top: 30px;
        }

        .endpoint-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .endpoint-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid var(--primary-color);
        }

        .endpoint-method {
            font-weight: 600;
            margin-right: 10px;
        }

        .endpoint-path {
            font-family: 'Courier New', monospace;
            color: var(--secondary-color);
        }

        .endpoint-desc {
            margin-top: 10px;
            color: var(--gray-color);
            font-size: 0.9rem;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 30px 0;
            margin-top: 40px;
            color: var(--gray-color);
            border-top: 1px solid var(--border-color);
        }

        /* Loading Spinner */
        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Toast Notifications */
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 15px 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 10px;
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s ease;
            z-index: 1000;
            max-width: 350px;
        }

        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        .toast-success {
            border-left: 4px solid var(--success-color);
        }

        .toast-error {
            border-left: 4px solid var(--danger-color);
        }

        .toast-info {
            border-left: 4px solid var(--primary-color);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header>
            <div class="header-content">
                <h1><i class="fas fa-shopping-cart"></i> Mini E-commerce API</h1>
                <p class="tagline">A complete backend solution for your e-commerce applications</p>
                <div class="session-info">
                    <small>Session ID: <code><?php echo htmlspecialchars($_COOKIE['session_id'] ?? 'Not set'); ?></code></small>
                </div>
            </div>
        </header>

        <!-- Stats Cards -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-info">
                    <h3>Products Available</h3>
                    <div class="stat-number" id="products-count">0</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-info">
                    <h3>Cart Items</h3>
                    <div class="stat-number" id="cart-items-count">0</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="stat-info">
                    <h3>Orders Processed</h3>
                    <div class="stat-number" id="orders-count">0</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-code"></i>
                </div>
                <div class="stat-info">
                    <h3>API Endpoints</h3>
                    <div class="stat-number">12</div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Left Column: API Tester -->
            <div class="tester-section">
                <h2 class="section-title"><i class="fas fa-flask"></i> API Testing Console</h2>
                
                <!-- Tabs -->
                <div class="tabs">
                    <button class="tab active" data-tab="products">Products API</button>
                    <button class="tab" data-tab="cart">Cart API</button>
                    <button class="tab" data-tab="orders">Orders API</button>
                    <button class="tab" data-tab="checkout">Checkout</button>
                </div>

                <!-- Products Tab -->
                <div class="tab-content active" id="products-tab">
                    <div class="tabs">
                        <button class="tab small active" data-subtab="get-products">GET Products</button>
                        <button class="tab small" data-subtab="get-product">GET Product</button>
                        <button class="tab small" data-subtab="create-product">CREATE Product</button>
                        <button class="tab small" data-subtab="update-product">UPDATE Product</button>
                        <button class="tab small" data-subtab="delete-product">DELETE Product</button>
                    </div>

                    <!-- Get Products Form -->
                    <div class="subtab-content active" id="get-products">
                        <div class="endpoint-form">
                            <div class="form-group">
                                <label><span class="method-badge method-get">GET</span> /api.php/products</label>
                                <p style="color: var(--gray-color); margin-bottom: 15px; font-size: 0.9rem;">Retrieve all products with pagination and filtering options.</p>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="products-page">Page</label>
                                    <input type="number" id="products-page" value="1" min="1">
                                </div>
                                <div class="form-group">
                                    <label for="products-limit">Limit</label>
                                    <input type="number" id="products-limit" value="10" min="1" max="100">
                                </div>
                                <div class="form-group">
                                    <label for="products-category">Category (optional)</label>
                                    <input type="text" id="products-category" placeholder="e.g., Electronics">
                                </div>
                            </div>
                            <button class="btn btn-primary btn-block" onclick="callApi('get-products')">
                                <i class="fas fa-play"></i> Execute Request
                            </button>
                        </div>
                    </div>

                    <!-- Get Single Product Form -->
                    <div class="subtab-content" id="get-product">
                        <div class="endpoint-form">
                            <div class="form-group">
                                <label><span class="method-badge method-get">GET</span> /api.php/products/{id}</label>
                                <p style="color: var(--gray-color); margin-bottom: 15px; font-size: 0.9rem;">Retrieve a single product by ID.</p>
                            </div>
                            <div class="form-group">
                                <label for="product-id">Product ID</label>
                                <input type="number" id="product-id" value="1" min="1" placeholder="Enter product ID">
                            </div>
                            <button class="btn btn-primary btn-block" onclick="callApi('get-product')">
                                <i class="fas fa-play"></i> Execute Request
                            </button>
                        </div>
                    </div>

                    <!-- Create Product Form -->
                    <div class="subtab-content" id="create-product">
                        <div class="endpoint-form">
                            <div class="form-group">
                                <label><span class="method-badge method-post">POST</span> /api.php/products</label>
                                <p style="color: var(--gray-color); margin-bottom: 15px; font-size: 0.9rem;">Create a new product. All fields except description, image_url and sku are required.</p>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="create-name">Product Name *</label>
                                    <input type="text" id="create-name" value="New Wireless Earbuds" required>
                                </div>
                                <div class="form-group">
                                    <label for="create-price">Price *</label>
                                    <input type="number" id="create-price" value="129.99" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="create-sku">SKU</label>
                                    <input type="text" id="create-sku" value="WEB-006" placeholder="Unique SKU">
                                </div>
                                <div class="form-group">
                                    <label for="create-stock">Stock Quantity</label>
                                    <input type="number" id="create-stock" value="100" min="0">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="create-category">Category</label>
                                    <input type="text" id="create-category" value="Electronics" placeholder="Product category">
                                </div>
                                <div class="form-group">
                                    <label for="create-active">Active</label>
                                    <select id="create-active">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="create-description">Description</label>
                                <textarea id="create-description" rows="3">Premium wireless earbuds with noise cancellation</textarea>
                            </div>
                            <div class="form-group">
                                <label for="create-image">Image URL</label>
                                <input type="text" id="create-image" placeholder="https://example.com/image.jpg">
                            </div>
                            <button class="btn btn-success btn-block" onclick="callApi('create-product')">
                                <i class="fas fa-plus"></i> Create Product
                            </button>
                        </div>
                    </div>

                    <!-- Update Product Form -->
                    <div class="subtab-content" id="update-product">
                        <div class="endpoint-form">
                            <div class="form-group">
                                <label><span class="method-badge method-put">PUT</span> /api.php/products/{id}</label>
                                <p style="color: var(--gray-color); margin-bottom: 15px; font-size: 0.9rem;">Update an existing product.</p>
                            </div>
                            <div class="form-group">
                                <label for="update-id">Product ID *</label>
                                <input type="number" id="update-id" value="1" min="1" required>
                            </div>
                            <div class="form-group">
                                <label for="update-name">Product Name</label>
                                <input type="text" id="update-name" placeholder="Leave empty to keep current">
                            </div>
                            <div class="form-group">
                                <label for="update-price">Price</label>
                                <input type="number" id="update-price" step="0.01" min="0" placeholder="Leave empty to keep current">
                            </div>
                            <div class="form-group">
                                <label for="update-stock">Stock Quantity</label>
                                <input type="number" id="update-stock" min="0" placeholder="Leave empty to keep current">
                            </div>
                            <button class="btn btn-warning btn-block" onclick="callApi('update-product')">
                                <i class="fas fa-edit"></i> Update Product
                            </button>
                        </div>
                    </div>

                    <!-- Delete Product Form -->
                    <div class="subtab-content" id="delete-product">
                        <div class="endpoint-form">
                            <div class="form-group">
                                <label><span class="method-badge method-delete">DELETE</span> /api.php/products/{id}</label>
                                <p style="color: var(--gray-color); margin-bottom: 15px; font-size: 0.9rem;">Delete a product by ID.</p>
                            </div>
                            <div class="form-group">
                                <label for="delete-id">Product ID *</label>
                                <input type="number" id="delete-id" value="1" min="1" required>
                            </div>
                            <button class="btn btn-danger btn-block" onclick="callApi('delete-product')">
                                <i class="fas fa-trash"></i> Delete Product
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Cart Tab -->
                <div class="tab-content" id="cart-tab">
                    <div class="tabs">
                        <button class="tab small active" data-subtab="get-cart">GET Cart</button>
                        <button class="tab small" data-subtab="add-cart">ADD to Cart</button>
                        <button class="tab small" data-subtab="update-cart">UPDATE Cart</button>
                        <button class="tab small" data-subtab="clear-cart">CLEAR Cart</button>
                    </div>

                    <!-- Get Cart Form -->
                    <div class="subtab-content active" id="get-cart">
                        <div class="endpoint-form">
                            <div class="form-group">
                                <label><span class="method-badge method-get">GET</span> /api.php/cart</label>
                                <p style="color: var(--gray-color); margin-bottom: 15px; font-size: 0.9rem;">Retrieve current cart contents.</p>
                            </div>
                            <button class="btn btn-primary btn-block" onclick="callApi('get-cart')">
                                <i class="fas fa-shopping-cart"></i> Get Cart Contents
                            </button>
                        </div>
                    </div>

                    <!-- Add to Cart Form -->
                    <div class="subtab-content" id="add-cart">
                        <div class="endpoint-form">
                            <div class="form-group">
                                <label><span class="method-badge method-post">POST</span> /api.php/cart/items</label>
                                <p style="color: var(--gray-color); margin-bottom: 15px; font-size: 0.9rem;">Add a product to the cart.</p>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="add-product-id">Product ID *</label>
                                    <input type="number" id="add-product-id" value="1" min="1" required>
                                </div>
                                <div class="form-group">
                                    <label for="add-quantity">Quantity *</label>
                                    <input type="number" id="add-quantity" value="1" min="1" required>
                                </div>
                            </div>
                            <button class="btn btn-success btn-block" onclick="callApi('add-cart')">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </div>
                    </div>

                    <!-- Update Cart Form -->
                    <div class="subtab-content" id="update-cart">
                        <div class="endpoint-form">
                            <div class="form-group">
                                <label><span class="method-badge method-put">PUT</span> /api.php/cart/items</label>
                                <p style="color: var(--gray-color); margin-bottom: 15px; font-size: 0.9rem;">Update product quantity in cart.</p>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="update-cart-product-id">Product ID *</label>
                                    <input type="number" id="update-cart-product-id" value="1" min="1" required>
                                </div>
                                <div class="form-group">
                                    <label for="update-cart-quantity">New Quantity *</label>
                                    <input type="number" id="update-cart-quantity" value="2" min="1" required>
                                </div>
                            </div>
                            <button class="btn btn-warning btn-block" onclick="callApi('update-cart')">
                                <i class="fas fa-edit"></i> Update Cart Item
                            </button>
                        </div>
                    </div>

                    <!-- Clear Cart Form -->
                    <div class="subtab-content" id="clear-cart">
                        <div class="endpoint-form">
                            <div class="form-group">
                                <label><span class="method-badge method-delete">DELETE</span> /api.php/cart</label>
                                <p style="color: var(--gray-color); margin-bottom: 15px; font-size: 0.9rem;">Remove all items from the cart.</p>
                            </div>
                            <button class="btn btn-danger btn-block" onclick="callApi('clear-cart')">
                                <i class="fas fa-trash"></i> Clear Entire Cart
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Orders Tab -->
                <div class="tab-content" id="orders-tab">
                    <div class="tabs">
                        <button class="tab small active" data-subtab="get-orders">GET Orders</button>
                        <button class="tab small" data-subtab="get-order">GET Order</button>
                        <button class="tab small" data-subtab="update-order">UPDATE Status</button>
                    </div>

                    <!-- Get Orders Form -->
                    <div class="subtab-content active" id="get-orders">
                        <div class="endpoint-form">
                            <div class="form-group">
                                <label><span class="method-badge method-get">GET</span> /api.php/orders</label>
                                <p style="color: var(--gray-color); margin-bottom: 15px; font-size: 0.9rem;">Retrieve user's orders.</p>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="orders-user-id">User ID *</label>
                                    <input type="number" id="orders-user-id" value="1" min="1" required>
                                </div>
                                <div class="form-group">
                                    <label for="orders-page">Page</label>
                                    <input type="number" id="orders-page" value="1" min="1">
                                </div>
                                <div class="form-group">
                                    <label for="orders-limit">Limit</label>
                                    <input type="number" id="orders-limit" value="10" min="1" max="100">
                                </div>
                            </div>
                            <button class="btn btn-primary btn-block" onclick="callApi('get-orders')">
                                <i class="fas fa-list"></i> Get Orders
                            </button>
                        </div>
                    </div>

                    <!-- Get Order Details Form -->
                    <div class="subtab-content" id="get-order">
                        <div class="endpoint-form">
                            <div class="form-group">
                                <label><span class="method-badge method-get">GET</span> /api.php/orders/{id}</label>
                                <p style="color: var(--gray-color); margin-bottom: 15px; font-size: 0.9rem;">Retrieve order details.</p>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="order-id">Order ID *</label>
                                    <input type="number" id="order-id" value="1" min="1" required>
                                </div>
                                <div class="form-group">
                                    <label for="order-user-id">User ID (optional)</label>
                                    <input type="number" id="order-user-id" min="1" placeholder="For user-specific lookup">
                                </div>
                            </div>
                            <button class="btn btn-primary btn-block" onclick="callApi('get-order')">
                                <i class="fas fa-search"></i> Get Order Details
                            </button>
                        </div>
                    </div>

                    <!-- Update Order Status Form -->
                    <div class="subtab-content" id="update-order">
                        <div class="endpoint-form">
                            <div class="form-group">
                                <label><span class="method-badge method-put">PUT</span> /api.php/orders/{id}/status</label>
                                <p style="color: var(--gray-color); margin-bottom: 15px; font-size: 0.9rem;">Update order status.</p>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="status-order-id">Order ID *</label>
                                    <input type="number" id="status-order-id" value="1" min="1" required>
                                </div>
                                <div class="form-group">
                                    <label for="status">Status *</label>
                                    <select id="status">
                                        <option value="pending">Pending</option>
                                        <option value="processing">Processing</option>
                                        <option value="shipped">Shipped</option>
                                        <option value="delivered">Delivered</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="payment-status">Payment Status</label>
                                <select id="payment-status">
                                    <option value="">Keep current</option>
                                    <option value="pending">Pending</option>
                                    <option value="paid">Paid</option>
                                    <option value="failed">Failed</option>
                                    <option value="refunded">Refunded</option>
                                </select>
                            </div>
                            <button class="btn btn-warning btn-block" onclick="callApi('update-order')">
                                <i class="fas fa-sync"></i> Update Order Status
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Checkout Tab -->
                <div class="tab-content" id="checkout-tab">
                    <div class="endpoint-form">
                        <div class="form-group">
                            <label><span class="method-badge method-post">POST</span> /api.php/orders/checkout</label>
                            <p style="color: var(--gray-color); margin-bottom: 15px; font-size: 0.9rem;">Checkout the current cart and create an order.</p>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="checkout-user-id">User ID *</label>
                                <input type="number" id="checkout-user-id" value="1" min="1" required>
                            </div>
                            <div class="form-group">
                                <label for="checkout-payment">Payment Method</label>
                                <select id="checkout-payment">
                                    <option value="card">Credit Card</option>
                                    <option value="paypal">PayPal</option>
                                    <option value="bank">Bank Transfer</option>
                                    <option value="cod">Cash on Delivery</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="shipping-address">Shipping Address *</label>
                            <textarea id="shipping-address" rows="3" required>123 Main Street, Apt 4B, New York, NY 10001, USA</textarea>
                        </div>
                        <div class="form-group">
                            <label for="billing-address">Billing Address (leave empty to use shipping address)</label>
                            <textarea id="billing-address" rows="2" placeholder="Same as shipping address"></textarea>
                        </div>
                        <button class="btn btn-success btn-block" onclick="callApi('checkout')">
                            <i class="fas fa-check-circle"></i> Process Checkout
                        </button>
                    </div>
                </div>

                <!-- Response Display -->
                <div class="response-section">
                    <div class="response-title">
                        <i class="fas fa-code"></i> API Response
                    </div>
                    <div class="response-panel" id="api-response">
                        // Response will appear here after API call
                    </div>
                </div>
            </div>

            <!-- Right Column: Live Cart -->
            <div class="cart-section">
                <div class="cart-header">
                    <h2 class="section-title"><i class="fas fa-shopping-cart"></i> Live Cart</h2>
                    <button class="btn btn-secondary" onclick="refreshCart()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>

                <div class="cart-items" id="cart-items-container">
                    <!-- Cart items will be loaded here -->
                    <div class="empty-cart">
                        <i class="fas fa-shopping-cart"></i>
                        <p>Your cart is empty</p>
                        <small>Add products using the API tester</small>
                    </div>
                </div>

                <div class="cart-total">
                    <span>Total:</span>
                    <span id="cart-total">$0.00</span>
                </div>

                <button class="btn btn-primary btn-block" style="margin-top: 20px;" onclick="scrollToCheckout()">
                    <i class="fas fa-arrow-right"></i> Proceed to Checkout
                </button>

                <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid var(--border-color);">
                    <h3 style="font-size: 1rem; margin-bottom: 15px; color: var(--gray-color);">
                        <i class="fas fa-info-circle"></i> Cart Session Info
                    </h3>
                    <div style="font-size: 0.85rem; background: #f8f9fa; padding: 15px; border-radius: 8px;">
                        <p><strong>Session ID:</strong> <code style="word-break: break-all;"><?php echo htmlspecialchars($_COOKIE['session_id'] ?? 'Not set'); ?></code></p>
                        <p><strong>User ID:</strong> <span id="current-user-id">Not specified</span></p>
                        <p><strong>Cart Status:</strong> <span id="cart-status">Active</span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documentation Section -->
        <div class="docs-section">
            <h2 class="section-title"><i class="fas fa-book"></i> API Documentation</h2>
            
            <div class="endpoint-list">
                <div class="endpoint-item">
                    <div>
                        <span class="endpoint-method method-get">GET</span>
                        <span class="endpoint-path">/api.php/products</span>
                    </div>
                    <p class="endpoint-desc">Retrieve all products with pagination. Query parameters: page, limit, category.</p>
                </div>
                
                <div class="endpoint-item">
                    <div>
                        <span class="endpoint-method method-get">GET</span>
                        <span class="endpoint-path">/api.php/products/{id}</span>
                    </div>
                    <p class="endpoint-desc">Retrieve a single product by ID.</p>
                </div>
                
                <div class="endpoint-item">
                    <div>
                        <span class="endpoint-method method-post">POST</span>
                        <span class="endpoint-path">/api.php/products</span>
                    </div>
                    <p class="endpoint-desc">Create a new product. Requires name and price.</p>
                </div>
                
                <div class="endpoint-item">
                    <div>
                        <span class="endpoint-method method-put">PUT</span>
                        <span class="endpoint-path">/api.php/products/{id}</span>
                    </div>
                    <p class="endpoint-desc">Update an existing product.</p>
                </div>
                
                <div class="endpoint-item">
                    <div>
                        <span class="endpoint-method method-delete">DELETE</span>
                        <span class="endpoint-path">/api.php/products/{id}</span>
                    </div>
                    <p class="endpoint-desc">Delete a product by ID.</p>
                </div>
                
                <div class="endpoint-item">
                    <div>
                        <span class="endpoint-method method-get">GET</span>
                        <span class="endpoint-path">/api.php/cart</span>
                    </div>
                    <p class="endpoint-desc">Retrieve current cart contents.</p>
                </div>
                
                <div class="endpoint-item">
                    <div>
                        <span class="endpoint-method method-post">POST</span>
                        <span class="endpoint-path">/api.php/cart/items</span>
                    </div>
                    <p class="endpoint-desc">Add item to cart. Requires product_id and quantity.</p>
                </div>
                
                <div class="endpoint-item">
                    <div>
                        <span class="endpoint-method method-put">PUT</span>
                        <span class="endpoint-path">/api.php/cart/items</span>
                    </div>
                    <p class="endpoint-desc">Update item quantity in cart. Requires product_id and quantity.</p>
                </div>
                
                <div class="endpoint-item">
                    <div>
                        <span class="endpoint-method method-delete">DELETE</span>
                        <span class="endpoint-path">/api.php/cart/items/{product_id}</span>
                    </div>
                    <p class="endpoint-desc">Remove item from cart.</p>
                </div>
                
                <div class="endpoint-item">
                    <div>
                        <span class="endpoint-method method-delete">DELETE</span>
                        <span class="endpoint-path">/api.php/cart</span>
                    </div>
                    <p class="endpoint-desc">Clear all items from cart.</p>
                </div>
                
                <div class="endpoint-item">
                    <div>
                        <span class="endpoint-method method-post">POST</span>
                        <span class="endpoint-path">/api.php/orders/checkout</span>
                    </div>
                    <p class="endpoint-desc">Checkout cart and create order. Requires user_id and shipping_address.</p>
                </div>
                
                <div class="endpoint-item">
                    <div>
                        <span class="endpoint-method method-get">GET</span>
                        <span class="endpoint-path">/api.php/orders</span>
                    </div>
                    <p class="endpoint-desc">Get user's orders. Query parameters: user_id, page, limit.</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer>
            <p>Mini E-commerce API &copy; <?php echo date('Y'); ?> | Built with PHP & MySQL</p>
            <p style="margin-top: 10px; font-size: 0.9rem;">
                <i class="fas fa-code"></i> Backend API: <code>/api.php</code> |
                <i class="fas fa-database"></i> Database: <code>mini_ecommerce</code>
            </p>
        </footer>
    </div>

    <!-- Toast Notification Container -->
    <div id="toast-container"></div>

    <script>
        // Global variables
        let currentCart = null;
        let products = [];

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // Load initial data
            loadProductsCount();
            refreshCart();
            loadOrdersCount();
            
            // Setup tab switching
            setupTabs();
            
            // Setup subtabs
            setupSubTabs();
        });

        // Tab switching
        function setupTabs() {
            const tabs = document.querySelectorAll('.tab[data-tab]');
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    
                    // Update active tab
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Show corresponding content
                    document.querySelectorAll('.tab-content').forEach(content => {
                        content.classList.remove('active');
                    });
                    document.getElementById(`${tabId}-tab`).classList.add('active');
                    
                    // Reset subtabs to first one
                    const subtabs = document.querySelectorAll('.tab[data-subtab]');
                    if (subtabs.length > 0) {
                        subtabs.forEach(t => t.classList.remove('active'));
                        subtabs[0].classList.add('active');
                        
                        const subtabContents = document.querySelectorAll('.subtab-content');
                        subtabContents.forEach(content => content.classList.remove('active'));
                        subtabContents[0].classList.add('active');
                    }
                });
            });
        }

        // Subtab switching
        function setupSubTabs() {
            const subtabs = document.querySelectorAll('.tab[data-subtab]');
            subtabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const subtabId = this.getAttribute('data-subtab');
                    
                    // Update active subtab
                    subtabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Show corresponding content
                    document.querySelectorAll('.subtab-content').forEach(content => {
                        content.classList.remove('active');
                    });
                    document.getElementById(subtabId).classList.add('active');
                });
            });
        }

        // Toast notification system
        function showToast(message, type = 'info') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            
            let icon = 'info-circle';
            if (type === 'success') icon = 'check-circle';
            if (type === 'error') icon = 'exclamation-circle';
            
            toast.innerHTML = `
                <i class="fas fa-${icon}"></i>
                <span>${message}</span>
            `;
            
            container.appendChild(toast);
            
            // Show toast
            setTimeout(() => {
                toast.classList.add('show');
            }, 10);
            
            // Hide and remove after 5 seconds
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }, 5000);
        }

        // API Call Handler
        async function callApi(endpoint) {
            const responsePanel = document.getElementById('api-response');
            responsePanel.innerHTML = '<span style="color: #4cc9f0;">Calling API... <i class="fas fa-spinner fa-spin"></i></span>';
            
            let url = 'api.php';
            let method = 'GET';
            let body = null;
            let queryParams = {};
            
            switch(endpoint) {
                case 'get-products':
                    method = 'GET';
                    queryParams.page = document.getElementById('products-page').value;
                    queryParams.limit = document.getElementById('products-limit').value;
                    const category = document.getElementById('products-category').value;
                    if (category) queryParams.category = category;
                    url += '/products';
                    break;
                    
                case 'get-product':
                    method = 'GET';
                    const productId = document.getElementById('product-id').value;
                    url += `/products/${productId}`;
                    break;
                    
                case 'create-product':
                    method = 'POST';
                    url += '/products';
                    body = {
                        name: document.getElementById('create-name').value,
                        description: document.getElementById('create-description').value,
                        price: parseFloat(document.getElementById('create-price').value),
                        sku: document.getElementById('create-sku').value || null,
                        stock_quantity: parseInt(document.getElementById('create-stock').value) || 0,
                        category: document.getElementById('create-category').value || null,
                        image_url: document.getElementById('create-image').value || null,
                        is_active: parseInt(document.getElementById('create-active').value)
                    };
                    break;
                    
                case 'update-product':
                    method = 'PUT';
                    const updateId = document.getElementById('update-id').value;
                    url += `/products/${updateId}`;
                    body = {};
                    const updateName = document.getElementById('update-name').value;
                    const updatePrice = document.getElementById('update-price').value;
                    const updateStock = document.getElementById('update-stock').value;
                    
                    if (updateName) body.name = updateName;
                    if (updatePrice) body.price = parseFloat(updatePrice);
                    if (updateStock) body.stock_quantity = parseInt(updateStock);
                    break;
                    
                case 'delete-product':
                    method = 'DELETE';
                    const deleteId = document.getElementById('delete-id').value;
                    url += `/products/${deleteId}`;
                    break;
                    
                case 'get-cart':
                    method = 'GET';
                    url += '/cart';
                    break;
                    
                case 'add-cart':
                    method = 'POST';
                    url += '/cart/items';
                    body = {
                        product_id: parseInt(document.getElementById('add-product-id').value),
                        quantity: parseInt(document.getElementById('add-quantity').value)
                    };
                    break;
                    
                case 'update-cart':
                    method = 'PUT';
                    url += '/cart/items';
                    body = {
                        product_id: parseInt(document.getElementById('update-cart-product-id').value),
                        quantity: parseInt(document.getElementById('update-cart-quantity').value)
                    };
                    break;
                    
                case 'clear-cart':
                    method = 'DELETE';
                    url += '/cart';
                    break;
                    
                case 'get-orders':
                    method = 'GET';
                    url += '/orders';
                    queryParams.user_id = document.getElementById('orders-user-id').value;
                    queryParams.page = document.getElementById('orders-page').value;
                    queryParams.limit = document.getElementById('orders-limit').value;
                    break;
                    
                case 'get-order':
                    method = 'GET';
                    const orderId = document.getElementById('order-id').value;
                    url += `/orders/${orderId}`;
                    const orderUserId = document.getElementById('order-user-id').value;
                    if (orderUserId) queryParams.user_id = orderUserId;
                    break;
                    
                case 'update-order':
                    method = 'PUT';
                    const statusOrderId = document.getElementById('status-order-id').value;
                    url += `/orders/${statusOrderId}/status`;
                    body = {
                        status: document.getElementById('status').value
                    };
                    const paymentStatus = document.getElementById('payment-status').value;
                    if (paymentStatus) body.payment_status = paymentStatus;
                    break;
                    
                case 'checkout':
                    method = 'POST';
                    url += '/orders/checkout';
                    body = {
                        user_id: parseInt(document.getElementById('checkout-user-id').value),
                        shipping_address: document.getElementById('shipping-address').value,
                        payment_method: document.getElementById('checkout-payment').value
                    };
                    const billingAddress = document.getElementById('billing-address').value;
                    if (billingAddress.trim()) {
                        body.billing_address = billingAddress;
                    }
                    break;
            }
            
            // Build query string
            const queryString = Object.keys(queryParams)
                .map(key => `${encodeURIComponent(key)}=${encodeURIComponent(queryParams[key])}`)
                .join('&');
            
            if (queryString) {
                url += `?${queryString}`;
            }
            
            try {
                const options = {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json'
                    }
                };
                
                if (body) {
                    options.body = JSON.stringify(body);
                }
                
                const response = await fetch(url, options);
                const data = await response.json();
                
                // Format and display response
                responsePanel.innerHTML = syntaxHighlight(JSON.stringify(data, null, 2));
                
                // Show toast notification
                if (data.success) {
                    showToast(data.message || 'API call successful', 'success');
                    
                    // Refresh relevant data
                    if (endpoint.includes('cart') || endpoint === 'checkout') {
                        refreshCart();
                    }
                    
                    if (endpoint.includes('product') && !endpoint.includes('get')) {
                        loadProductsCount();
                    }
                    
                    if (endpoint.includes('order') && !endpoint.includes('get')) {
                        loadOrdersCount();
                    }
                    
                    if (endpoint === 'checkout') {
                        // Switch to orders tab after checkout
                        document.querySelector('.tab[data-tab="orders"]').click();
                    }
                } else {
                    showToast(data.message || 'API call failed', 'error');
                }
                
            } catch (error) {
                responsePanel.innerHTML = `<span style="color: #f72585;">Error: ${error.message}</span>`;
                showToast('Network error: ' + error.message, 'error');
            }
        }

        // Syntax highlighting for JSON
        function syntaxHighlight(json) {
            if (typeof json != 'string') {
                json = JSON.stringify(json, null, 2);
            }
            
            json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            
            return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function(match) {
                let cls = 'number';
                if (/^"/.test(match)) {
                    if (/:$/.test(match)) {
                        cls = 'key';
                    } else {
                        cls = 'string';
                    }
                } else if (/true|false/.test(match)) {
                    cls = 'boolean';
                } else if (/null/.test(match)) {
                    cls = 'null';
                }
                return '<span class="' + cls + '">' + match + '</span>';
            });
        }

        // Load products count
        async function loadProductsCount() {
            try {
                const response = await fetch('api.php/products?limit=1');
                const data = await response.json();
                
                if (data.success && data.data) {
                    // Get total count from a separate endpoint or estimate
                    // For simplicity, we'll just count the available data
                    document.getElementById('products-count').textContent = data.data.length > 0 ? '10+' : '0';
                }
            } catch (error) {
                console.error('Error loading products count:', error);
            }
        }

        // Load orders count
        async function loadOrdersCount() {
            try {
                // Try to get orders for user 1
                const response = await fetch('api.php/orders?user_id=1&limit=1');
                const data = await response.json();
                
                if (data.success && data.data) {
                    document.getElementById('orders-count').textContent = data.data.length > 0 ? '1+' : '0';
                }
            } catch (error) {
                console.error('Error loading orders count:', error);
            }
        }

        // Refresh cart display
        async function refreshCart() {
            const cartContainer = document.getElementById('cart-items-container');
            const cartTotal = document.getElementById('cart-total');
            
            cartContainer.innerHTML = '<div class="empty-cart"><i class="fas fa-spinner fa-spin"></i><p>Loading cart...</p></div>';
            
            try {
                const response = await fetch('api.php/cart');
                const data = await response.json();
                
                if (data.success && data.data && data.data.items) {
                    currentCart = data.data;
                    
                    // Update cart items count
                    document.getElementById('cart-items-count').textContent = data.data.items.length;
                    
                    // Update cart info
                    if (data.data.user_id) {
                        document.getElementById('current-user-id').textContent = data.data.user_id;
                    }
                    document.getElementById('cart-status').textContent = data.data.status || 'Active';
                    
                    if (data.data.items.length === 0) {
                        cartContainer.innerHTML = '<div class="empty-cart"><i class="fas fa-shopping-cart"></i><p>Your cart is empty</p><small>Add products using the API tester</small></div>';
                        cartTotal.textContent = '$0.00';
                        return;
                    }
                    
                    // Build cart items HTML
                    let html = '';
                    let total = 0;
                    
                    data.data.items.forEach(item => {
                        const itemTotal = item.price * item.quantity;
                        total += itemTotal;
                        
                        html += `
                            <div class="cart-item">
                                <div class="cart-item-img">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div class="cart-item-info">
                                    <div class="cart-item-name">${item.name || 'Product #' + item.product_id}</div>
                                    <div class="cart-item-price">$${item.price.toFixed(2)} each</div>
                                </div>
                                <div class="cart-item-quantity">
                                    <button class="quantity-btn" onclick="updateCartItem(${item.product_id}, ${item.quantity - 1})">-</button>
                                    <span>${item.quantity}</span>
                                    <button class="quantity-btn" onclick="updateCartItem(${item.product_id}, ${item.quantity + 1})">+</button>
                                </div>
                            </div>
                        `;
                    });
                    
                    cartContainer.innerHTML = html;
                    cartTotal.textContent = `$${total.toFixed(2)}`;
                    
                    // Add remove buttons
                    data.data.items.forEach(item => {
                        const itemElement = cartContainer.querySelector(`.cart-item:nth-child(${data.data.items.indexOf(item) + 1})`);
                        const removeBtn = document.createElement('button');
                        removeBtn.className = 'quantity-btn';
                        removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                        removeBtn.style.marginLeft = '10px';
                        removeBtn.style.background = '#f72585';
                        removeBtn.style.color = 'white';
                        removeBtn.onclick = () => removeCartItem(item.product_id);
                        
                        itemElement.querySelector('.cart-item-quantity').appendChild(removeBtn);
                    });
                    
                } else {
                    cartContainer.innerHTML = '<div class="empty-cart"><i class="fas fa-shopping-cart"></i><p>Your cart is empty</p><small>Add products using the API tester</small></div>';
                    cartTotal.textContent = '$0.00';
                    document.getElementById('cart-items-count').textContent = '0';
                }
            } catch (error) {
                cartContainer.innerHTML = '<div class="empty-cart"><i class="fas fa-exclamation-circle"></i><p>Error loading cart</p><small>Check console for details</small></div>';
                console.error('Error loading cart:', error);
            }
        }

        // Update cart item quantity
        async function updateCartItem(productId, newQuantity) {
            if (newQuantity < 1) {
                removeCartItem(productId);
                return;
            }
            
            try {
                const response = await fetch('api.php/cart/items', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: newQuantity
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showToast('Cart updated', 'success');
                    refreshCart();
                } else {
                    showToast(data.message || 'Failed to update cart', 'error');
                }
            } catch (error) {
                showToast('Error updating cart: ' + error.message, 'error');
            }
        }

        // Remove cart item
        async function removeCartItem(productId) {
            try {
                const response = await fetch(`api.php/cart/items/${productId}`, {
                    method: 'DELETE'
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showToast('Item removed from cart', 'success');
                    refreshCart();
                } else {
                    showToast(data.message || 'Failed to remove item', 'error');
                }
            } catch (error) {
                showToast('Error removing item: ' + error.message, 'error');
            }
        }

        // Scroll to checkout tab
        function scrollToCheckout() {
            document.querySelector('.tab[data-tab="checkout"]').click();
            document.getElementById('checkout-tab').scrollIntoView({ behavior: 'smooth' });
        }

        // Add some sample products to the database
        async function seedSampleProducts() {
            const sampleProducts = [
                {
                    name: "Wireless Headphones",
                    description: "High-quality wireless headphones with noise cancellation",
                    price: 199.99,
                    sku: "WH-001",
                    stock_quantity: 50,
                    category: "Electronics"
                },
                {
                    name: "Smart Watch",
                    description: "Water-resistant smartwatch with fitness tracking",
                    price: 299.99,
                    sku: "SW-002",
                    stock_quantity: 30,
                    category: "Electronics"
                },
                {
                    name: "USB-C Cable",
                    description: "Durable USB-C charging cable, 6ft",
                    price: 19.99,
                    sku: "UC-003",
                    stock_quantity: 200,
                    category: "Accessories"
                },
                {
                    name: "Laptop Backpack",
                    description: "Water-resistant backpack for 15-inch laptops",
                    price: 49.99,
                    sku: "LB-004",
                    stock_quantity: 75,
                    category: "Bags"
                },
                {
                    name: "Coffee Mug",
                    description: "Ceramic coffee mug with heat retention",
                    price: 14.99,
                    sku: "CM-005",
                    stock_quantity: 150,
                    category: "Home"
                }
            ];

            for (const product of sampleProducts) {
                try {
                    await fetch('api.php/products', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(product)
                    });
                } catch (error) {
                    console.error('Error seeding product:', error);
                }
            }
            
            showToast('Sample products added to database', 'success');
            loadProductsCount();
        }

        // Expose functions to global scope for button onclick handlers
        window.callApi = callApi;
        window.refreshCart = refreshCart;
        window.updateCartItem = updateCartItem;
        window.removeCartItem = removeCartItem;
        window.scrollToCheckout = scrollToCheckout;
        window.seedSampleProducts = seedSampleProducts;
    </script>
</body>
</html>