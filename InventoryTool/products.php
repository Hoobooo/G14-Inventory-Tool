<?php
require_once 'utils/Session.php';
require_once 'utils/AuthMiddleware.php';
AuthMiddleware::requireLogin();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Product Management</title>
    <style>
        body { margin: 0; font-family: Arial; background: #f0f2f5; }
        .navbar { background: #1a73e8; padding: 1rem; color: white; }
        .container { padding: 20px; max-width: 1200px; margin: 0 auto; }
        .btn { 
            background: #1a73e8; color: white; padding: 10px 20px; 
            border: none; border-radius: 4px; cursor: pointer; 
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th { background: #f8f9fa; }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }
        .modal-content {
            background: white;
            margin: 10% auto;
            padding: 20px;
            width: 80%;
            max-width: 500px;
            border-radius: 8px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <h2>Product Management</h2>
            <a href="dashboard.php" style="color: white; text-decoration: none;">Dashboard</a>
        </div>
    </nav>

    <div class="container">
        <button class="btn" onclick="showAddModal()">Add New Product</button>
        
        <table id="productsTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- Add/Edit Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <h3>Add Product</h3>
            <form id="productForm">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id" required></select>
                </div>
                <div class="form-group">
                    <label>Brand</label>
                    <select name="brand_id" required></select>
                </div>
                <div class="form-group">
                    <label>Purchase Price</label>
                    <input type="number" name="purchase_price" step="0.01" required>
                </div>
                <button type="submit" class="btn">Save</button>
                <button type="button" class="btn" onclick="closeModal()">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        function loadProducts() {
            fetch('api/products/read.php')
                .then(response => response.json())
                .then(products => {
                    const tbody = document.querySelector('#productsTable tbody');
                    tbody.innerHTML = '';
                    products.forEach(product => {
                        tbody.innerHTML += `
                            <tr>
                                <td>${product.name}</td>
                                <td>${product.category_name || '-'}</td>
                                <td>${product.brand_name || '-'}</td>
                                <td>$${product.purchase_price}</td>
                                <td>${product.quantity || 0}</td>
                                <td>
                                    <button class="btn" onclick="editProduct(${product.id})">Edit</button>
                                    <button class="btn" onclick="deleteProduct(${product.id})">Delete</button>
                                </td>
                            </tr>
                        `;
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load products');
                });
        }

        function loadCategories() {
            fetch('api/categories/read.php')
                .then(response => response.json())
                .then(categories => {
                    const select = document.querySelector('[name="category_id"]');
                    select.innerHTML = '<option value="">Select Category</option>';
                    categories.forEach(category => {
                        select.innerHTML += `<option value="${category.id}">${category.name}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load categories');
                });
        }

        function loadBrands() {
            fetch('api/brands/read.php')
                .then(response => response.json())
                .then(brands => {
                    const select = document.querySelector('[name="brand_id"]');
                    select.innerHTML = '<option value="">Select Brand</option>';
                    brands.forEach(brand => {
                        select.innerHTML += `<option value="${brand.id}">${brand.name}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load brands');
                });
        }

        function showAddModal() {
            document.getElementById('productModal').style.display = 'block';
            document.getElementById('productForm').reset();
            delete document.getElementById('productForm').dataset.id;
        }

        function closeModal() {
            document.getElementById('productModal').style.display = 'none';
        }

        function editProduct(id) {
            fetch(`api/products/read_single.php?id=${id}`)
                .then(response => response.json())
                .then(product => {
                    const form = document.getElementById('productForm');
                    form.querySelector('[name="name"]').value = product.name;
                    form.querySelector('[name="category_id"]').value = product.category_id;
                    form.querySelector('[name="brand_id"]').value = product.brand_id;
                    form.querySelector('[name="purchase_price"]').value = product.purchase_price;
                    form.dataset.id = id;
                    showAddModal();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load product details');
                });
        }

        function deleteProduct(id) {
            if (confirm('Are you sure you want to delete this product?')) {
                fetch(`api/products/delete.php?id=${id}`, { method: 'DELETE' })
                    .then(response => response.json())
                    .then(() => loadProducts())
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to delete product');
                    });
            }
        }

        document.getElementById('productForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            const id = this.dataset.id;
            
            const url = id ? 
                `api/products/update.php?id=${id}` : 
                'api/products/create.php';
            
            const method = id ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(() => {
                closeModal();
                loadProducts();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to save product');
            });
        });

        // Initial load
        loadProducts();
        loadCategories();
        loadBrands();
    </script>
</body>
</html>