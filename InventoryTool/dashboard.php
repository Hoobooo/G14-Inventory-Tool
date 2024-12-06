<?php
require_once 'utils/Session.php';
require_once 'utils/AuthMiddleware.php';
AuthMiddleware::requireLogin();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory Dashboard</title>
    <!-- Previous styles remain the same -->
    <style>
        body { 
            margin: 0;
            font-family: Arial;
            background: #f0f2f5;
        }
        
        .navbar {
            background: #1a73e8;
            padding: 1rem;
            color: white;
        }

        .container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .card h3 {
            margin-top: 0;
            color: #1a73e8;
        }

        .actions {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .btn {
            background: #1a73e8;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .low-stock {
            color: #d93025;
        }

        .recent-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .recent-list li {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .recent-list li:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <h2>Inventory Management System</h2>
            <span>Welcome, <?php echo Session::get('username'); ?></span>
            <button onclick="handleLogout()" class="btn">Logout</button>
        </div>
    </nav>

    <div class="container">
        <!-- Rest of the dashboard content remains the same -->
        <div class="cards">
            <div class="card">
                <h3>Total Products</h3>
                <p id="totalProducts">Loading...</p>
            </div>
            <div class="card">
                <h3>Low Stock Alerts</h3>
                <p id="lowStock">Loading...</p>
                <div id="lowStockList"></div>
            </div>
            <div class="card">
                <h3>Recent Transactions</h3>
                <ul id="recentTransactions" class="recent-list">Loading...</ul>
            </div>
        </div>

        <div class="actions">
            <h3>Quick Actions</h3>
            <a href="products.php" class="btn">Manage Products</a>
            <a href="inventory.php" class="btn">Check Inventory</a>
            <a href="brands.php" class="btn">Manage Brands</a>
            <a href="categories.php" class="btn">Manage Categories</a>
        </div>
    </div>

    <script>
        // Handle logout with proper redirection
        async function handleLogout() {
            try {
                const response = await fetch('api/users/logout.php', {
                    method: 'POST',
                    credentials: 'include'
                });
                const data = await response.json();
                if (data.message === "Logged out successfully") {
                    window.location.href = 'index.php';
                } else {
                    console.error('Logout failed:', data.message);
                }
            } catch (error) {
                console.error('Error during logout:', error);
            }
        }

        // Load total products
        fetch('api/products/read.php')
            .then(response => response.json())
            .then(data => {
                document.getElementById('totalProducts').textContent = data.length + ' products in database';
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('totalProducts').textContent = 'Error loading data';
            });

        // Load low stock alerts
        fetch('api/products/low-stock.php')
            .then(response => response.json())
            .then(data => {
                const lowStockCount = data.length;
                document.getElementById('lowStock').textContent = lowStockCount + ' items low in stock';
                
                if (lowStockCount > 0) {
                    const list = document.getElementById('lowStockList');
                    list.innerHTML = '<ul class="recent-list">' + 
                        data.slice(0, 5).map(item => 
                            `<li class="low-stock">${item.name}: ${item.quantity} remaining</li>`
                        ).join('') + '</ul>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('lowStock').textContent = 'Error loading data';
            });

        // Load recent transactions
        fetch('api/transactions/read.php')
            .then(response => response.json())
            .then(data => {
                const transactionsList = document.getElementById('recentTransactions');
                if (data.length > 0) {
                    transactionsList.innerHTML = data.slice(0, 5).map(transaction => 
                        `<li>${transaction.type === 'in' ? '📥' : '📤'} ${transaction.product_name} - 
                         Qty: ${transaction.quantity} (${new Date(transaction.date).toLocaleDateString()})</li>`
                    ).join('');
                } else {
                    transactionsList.innerHTML = '<li>No recent transactions</li>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('recentTransactions').innerHTML = '<li>Error loading transactions</li>';
            });
    </script>
</body>
</html>