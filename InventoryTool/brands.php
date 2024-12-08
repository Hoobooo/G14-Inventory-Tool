<?php
require_once 'utils/Session.php';
require_once 'utils/AuthMiddleware.php';
AuthMiddleware::requireLogin();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Brand Management</title>
    <style>
        body { margin: 0; font-family: Arial; background: #f0f2f5; }
        .navbar { background: #1a73e8; padding: 1rem; color: white; }
        .container { padding: 20px; max-width: 1200px; margin: 0 auto; }
        
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
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #5f6368;
        }
        
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        a { color: white; text-decoration: none; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <h2>Brand Management</h2>
            <a href="dashboard.php">Dashboard</a>
        </div>
    </nav>

    <div class="container">
        <button class="btn" onclick="showAddModal()">Add New Brand</button>
        
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Manufacturer</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="brandsTable"></tbody>
        </table>
    </div>

    <div id="modal" class="modal">
        <div class="modal-content">
            <h3>Brand</h3>
            <form id="brandForm" onsubmit="saveBrand(event)">
                <input type="hidden" id="brandId">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" id="brandName" required>
                </div>
                <div class="form-group">
                    <label>Manufacturer</label>
                    <input type="text" id="brandManufacturer">
                </div>
                <button type="submit" class="btn">Save</button>
                <button type="button" class="btn" onclick="closeModal()">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        function loadBrands() {
            fetch('api/brands/read.php')
                .then(response => response.json())
                .then(brands => {
                    const tbody = document.getElementById('brandsTable');
                    tbody.innerHTML = '';
                    brands.forEach(brand => {
                        tbody.innerHTML += `
                            <tr>
                                <td>${brand.name}</td>
                                <td>${brand.manufacturer || '-'}</td>
                                <td>
                                    <button class="btn" onclick="editBrand(${brand.id})">Edit</button>
                                    <button class="btn" onclick="deleteBrand(${brand.id})">Delete</button>
                                </td>
                            </tr>
                        `;
                    });
                })
                .catch(error => console.error('Error:', error));
        }

        function showAddModal() {
            document.getElementById('modal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }

        function saveBrand(event) {
            event.preventDefault();
            const id = document.getElementById('brandId').value;
            const data = {
                name: document.getElementById('brandName').value,
                manufacturer: document.getElementById('brandManufacturer').value
            };

            const url = id ? 
                `api/brands/update.php?id=${id}` : 
                'api/brands/create.php';

            fetch(url, {
                method: id ? 'PUT' : 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(() => {
                closeModal();
                loadBrands();
            });
        }

        function editBrand(id) {
            fetch(`api/brands/read.php?id=${id}`)
                .then(response => response.json())
                .then(brand => {
                    document.getElementById('brandId').value = id;
                    document.getElementById('brandName').value = brand.name;
                    document.getElementById('brandManufacturer').value = brand.manufacturer || '';
                    showAddModal();
                });
        }

        function deleteBrand(id) {
            if (confirm('Delete this brand?')) {
                fetch(`api/brands/delete.php?id=${id}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(() => loadBrands());
            }
        }

        // Initial load
        loadBrands();
    </script>
</body>
</html>