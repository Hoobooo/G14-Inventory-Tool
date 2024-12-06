<?php
require_once 'utils/Session.php';
require_once 'utils/AuthMiddleware.php';
AuthMiddleware::requireLogin();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory Tracking</title>
    <style>
        /* Reuse previous styles */
        body { margin: 0; font-family: Arial; background: #f0f2f5; }
        .navbar { background: #1a73e8; padding: 1rem; color: white; }
        .container { padding: 20px; max-width: 1200px; margin: 0 auto; }
        .btn { 
            background: #1a73e8; color: white; padding: 10px 20px; 
            border: none; border-radius: 4px; cursor: pointer; 
        }
        
        .inventory-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .inventory-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .stock-low { color: #d93025; }
        .stock-ok { color: #1e8e3e; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <h2>Inventory Tracking</h2>
            <a href="dashboard.php">Dashboard</a>
        </div>
    </nav>

    <div class="container">
        <div class="filters">
            <select id="categoryFilter">
                <option value="">All Categories</option>
            </select>
            <select id="stockFilter">
                <option value="">All Stock Levels</option>
                <option value="low">Low Stock</option>
                <option value="ok">OK Stock</option>
            </select>
        </div>

        <div class="inventory-grid" id="inventoryGrid"></div>
    </div>

    <script>
        function loadInventory() {
            fetch('api/products/read.php')
                .then(response => response.json())
                .then(products => {
                    const grid = document.getElementById('inventoryGrid');
                    grid.innerHTML = '';
                    
                    products.forEach(product => {
                        const stockStatus = (product.quantity <= product.min_quantity) 
                            ? 'stock-low' : 'stock-ok';
                        
                        grid.innerHTML += `
                            <div class="inventory-card">
                                <h3>${product.name}</h3>
                                <p>SKU: ${product.sku}</p>
                                <p>Category: ${product.category_name}</p>
                                <p class="${stockStatus}">
                                    Stock: ${product.quantity || 0} 
                                    ${(product.quantity <= product.min_quantity) ? '(Low Stock!)' : ''}
                                </p>
                                <button class="btn" onclick="updateStock(${product.id})">
                                    Update Stock
                                </button>
                            </div>
                        `;
                    });
                });
        }

        function updateStock(productId) {
            const quantity = prompt("Enter new stock quantity:");
            if (quantity === null) return;

            fetch(`api/products/update.php?id=${productId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ quantity: parseInt(quantity) })
            })
            .then(response => response.json())
            .then(() => loadInventory());
        }

        // Initial load
        loadInventory();
    </script>
</body>
</html>