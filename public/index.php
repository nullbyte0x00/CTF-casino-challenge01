<?php 

require 'config.php';
require 'db.php';


$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'register') {
        // Registration logic
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        if (!$username || !$password || !$password_confirm) {
            $error = "Please fill all required fields.";
        } elseif ($password !== $password_confirm) {
            $error = "Passwords do not match.";
        } else {
            // Check if username already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = "Username is already taken.";
            } else {

                $model = new PDOModel('users');

                $data = $_POST;

                $inserted = $model->insert($data);

                // Auto login after register
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['username'] = $username;

                // Redirect to dashboard (you can create dashboard.php)
                header("Location: dashboard.php?user_id=" . $_SESSION['user_id']);
                exit;
            }
        }
    } elseif ($action === 'login') {
        // Login logic
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$username || !$password) {
            $error = "Please enter both username and password.";
        } else {
            $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && $password == $user['password']) {

                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                // Redirect to dashboard
                header("Location: dashboard.php?user_id=" . $user['id']);
                exit;
            } else {
                $error = "Invalid username or password.";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Casino Challenge 01</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@500&family=Roboto&display=swap');

  body {
    background: radial-gradient(circle at center, #0b0c0f, #1a1a1a);
    font-family: 'Roboto', sans-serif;
    color: #eee;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
  }

  .casino-form {
    background: linear-gradient(145deg, #4a148c, #880e4f);
    padding: 40px 50px;
    border-radius: 15px;
    box-shadow: 0 0 25px #ff4081, 0 0 60px #7c4dff;
    width: 320px;
    text-align: center;
  }

  .casino-form h2 {
    font-family: 'Orbitron', sans-serif;
    font-weight: 500;
    font-size: 2rem;
    margin-bottom: 25px;
    letter-spacing: 2px;
    color: #ff4081;
    text-shadow: 0 0 10px #ff4081;
  }

  .casino-form input {
    width: 100%;
    padding: 12px 15px;
    margin: 12px 0;
    border-radius: 8px;
    border: none;
    font-size: 1rem;
    outline: none;
    transition: box-shadow 0.3s ease;
    background-color: #2e0249;
    color: #eee;
  }

  .casino-form input:focus {
    box-shadow: 0 0 10px #ff4081;
    background-color: #3f007d;
  }

  .casino-form button {
    width: 48%;
    padding: 12px 0;
    margin: 15px 1%;
    border-radius: 25px;
    border: none;
    font-weight: 600;
    font-size: 1.1rem;
    cursor: pointer;
    color: #fff;
    background: #ff4081;
    box-shadow: 0 0 15px #ff4081;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
  }

  .casino-form button:hover {
    background: #e040fb;
    box-shadow: 0 0 20px #e040fb;
  }

  .btn-login {
    background: #00e676;
    box-shadow: 0 0 15px #00e676;
  }

  .btn-login:hover {
    background: #00c853;
    box-shadow: 0 0 20px #00c853;
  }

  .toggle-link {
    margin-top: 10px;
    font-size: 0.9rem;
    color: #ff80ab;
    cursor: pointer;
    display: inline-block;
  }
  .toggle-link:hover {
    text-decoration: underline;
  }
</style>
</head>
<body>

<div class="casino-form" id="form-container">
  <!-- Login Form -->
  <form id="login-form" method="POST" style="display: block;">
    <h2>🎰 Casino</h2>
      <!-- Error message -->
   <?php if ($error && ($_POST['action'] ?? '') === 'login'): ?>
    <div style="color: #ff5252; margin-bottom: 10px;">
      ❌ <?= htmlspecialchars($error) ?>
    </div>
   <?php endif; ?>

    <input name="username" placeholder="Username" required autocomplete="off" />
    <input name="password" type="password" placeholder="Password" required autocomplete="off" />
    <div>
      <button type="submit" name="action" value="login" class="btn-login">Login</button>
      <button type="button" id="show-register">Register</button>
    </div>
  </form>

  <!-- Registration Form -->
  <form id="register-form" method="POST" style="display: none;">
    <h2>📝 Register Account</h2>
    <input name="username" placeholder="Choose username" required autocomplete="off" />
    <input name="email" type="email" placeholder="Email (optional)" autocomplete="off" />
    <input name="password" type="password" placeholder="Password" required autocomplete="off" />
    <input name="password_confirm" type="password" placeholder="Confirm password" required autocomplete="off" />
    <div>
      <button type="submit" name="action" value="register">Register</button>
      <button type="button" id="show-login" class="btn-login">Back to Login</button>
    </div>
  </form>
</div>

<script>
  const loginForm = document.getElementById('login-form');
  const registerForm = document.getElementById('register-form');
  const showRegisterBtn = document.getElementById('show-register');
  const showLoginBtn = document.getElementById('show-login');

  showRegisterBtn.addEventListener('click', () => {
    loginForm.style.display = 'none';
    registerForm.style.display = 'block';
  });

  showLoginBtn.addEventListener('click', () => {
    registerForm.style.display = 'none';
    loginForm.style.display = 'block';
  });
</script>

</body>
</html>


