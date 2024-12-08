<?php
require_once 'utils/Session.php';
require_once 'utils/AuthMiddleware.php';
AuthMiddleware::requireLogin();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Category Management</title>
    <style>
        body { margin: 0; font-family: Arial; background: #f0f2f5; }
        .navbar { background: #1a73e8; padding: 1rem; color: white; }
        .container { padding: 20px; max-width: 1200px; margin: 0 auto; }
        .btn { 
            background: #1a73e8; color: white; padding: 10px 20px; 
            border: none; border-radius: 4px; cursor: pointer; 
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
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        
        .form-group input, .form-group textarea {
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
            <h2>Category Management</h2>
            <a href="dashboard.php">Dashboard</a>
        </div>
    </nav>

    <div class="container">
        <button class="btn" onclick="showAddModal()">Add New Category</button>
        
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="categoriesTable"></tbody>
        </table>
    </div>

    <div id="modal" class="modal">
        <div class="modal-content">
            <h3>Category</h3>
            <form id="categoryForm" onsubmit="saveCategory(event)">
                <input type="hidden" id="categoryId">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" id="categoryName" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea id="categoryDescription" rows="3"></textarea>
                </div>
                <button type="submit" class="btn">Save</button>
                <button type="button" class="btn" onclick="closeModal()">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        function loadCategories() {
            fetch('api/categories/read.php')
                .then(response => response.json())
                .then(categories => {
                    const tbody = document.getElementById('categoriesTable');
                    tbody.innerHTML = '';
                    categories.forEach(category => {
                        tbody.innerHTML += `
                            <tr>
                                <td>${category.name}</td>
                                <td>${category.description || '-'}</td>
                                <td>
                                    <button class="btn" onclick="editCategory(${category.id})">Edit</button>
                                    <button class="btn" onclick="deleteCategory(${category.id})">Delete</button>
                                </td>
                            </tr>
                        `;
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load categories');
                });
        }

        function showAddModal() {
            document.getElementById('modal').style.display = 'block';
            document.getElementById('categoryForm').reset();
            document.getElementById('categoryId').value = '';
        }

        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }

        function saveCategory(event) {
            event.preventDefault();
            const id = document.getElementById('categoryId').value;
            const data = {
                name: document.getElementById('categoryName').value,
                description: document.getElementById('categoryDescription').value
            };

            const url = id ? 
                `api/categories/update.php?id=${id}` : 
                'api/categories/create.php';

            fetch(url, {
                method: id ? 'PUT' : 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(() => {
                closeModal();
                loadCategories();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to save category');
            });
        }

        function editCategory(id) {
            fetch(`api/categories/read.php?id=${id}`)
                .then(response => response.json())
                .then(category => {
                    document.getElementById('categoryId').value = id;
                    document.getElementById('categoryName').value = category.name;
                    document.getElementById('categoryDescription').value = category.description || '';
                    showAddModal();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load category details');
                });
        }

        function deleteCategory(id) {
            if (confirm('Delete this category?')) {
                fetch(`api/categories/delete.php?id=${id}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(() => loadCategories())
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to delete category');
                });
            }
        }

        // Initial load
        loadCategories();
    </script>
</body>
</html>