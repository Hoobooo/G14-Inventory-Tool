<!DOCTYPE html>
<html>
<head>
    <title>Inventory Management - Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
        }
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #1a73e8;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #5f6368;
        }
        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #1a73e8;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #1557b0;
        }
        .switch-form {
            text-align: center;
            margin-top: 20px;
            color: #5f6368;
        }
        .switch-form a {
            color: #1a73e8;
            text-decoration: none;
        }
        .error {
            color: #d93025;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <form id="loginForm" action="api/users/login.php" method="POST">
                <h2>Login</h2>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">Login</button>
            </form>
            <div class="switch-form">
                Don't have an account? <a href="#" onclick="toggleForm()">Register</a>
            </div>
        </div>
    </div>

    <script>
        function toggleForm() {
            const form = document.getElementById('loginForm');
            const title = document.querySelector('h2');
            const switchText = document.querySelector('.switch-form');
            
            if (form.action.includes('login.php')) {
                form.action = 'api/users/register.php';
                title.textContent = 'Register';
                switchText.innerHTML = 'Already have an account? <a href="#" onclick="toggleForm()">Login</a>';
                
                // Add email field for registration
                if (!document.getElementById('email')) {
                    const emailGroup = document.createElement('div');
                    emailGroup.className = 'form-group';
                    emailGroup.innerHTML = `
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    `;
                    document.querySelector('.form-group').parentNode.insertBefore(emailGroup, document.querySelector('.form-group'));
                }
            } else {
                form.action = 'api/users/login.php';
                title.textContent = 'Login';
                switchText.innerHTML = 'Don\'t have an account? <a href="#" onclick="toggleForm()">Register</a>';
                
                // Remove email field for login
                const emailGroup = document.getElementById('email').parentNode;
                if (emailGroup) {
                    emailGroup.remove();
                }
            }
        }

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = {};
            formData.forEach((value, key) => data[key] = value);

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.message === "Login successful.") {
                    window.location.href = 'dashboard.php';
                } else {
                    const error = document.querySelector('.error') || document.createElement('div');
                    error.className = 'error';
                    error.textContent = data.message;
                    if (!document.querySelector('.error')) {
                        document.querySelector('h2').after(error);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>