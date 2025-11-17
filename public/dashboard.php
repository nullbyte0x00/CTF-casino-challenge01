<?php
require 'db.php';

// Check if logged in
if (!isset($_SESSION['user_id']) || !isset($_GET['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_GET['user_id'];
$username = $_SESSION['username'];

// Fetch balance
$stmt = $pdo->prepare("SELECT balance,username FROM users WHERE id = ?");
$stmt->execute([$user_id]);
// Fetch as associative array
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if ($userData) {
    $balance = $userData['balance'];
    $username = $userData['username'];
} else {
    // Handle user not found
    die("error");
}

$balance = $userData['balance'];;
$username = $userData['username'];

//print_r($stmt);
//die();
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Casino Dashboard</title>
<style>
  body {
    background: radial-gradient(circle at center, #0b0c0f, #1a1a1a);
    font-family: 'Roboto', sans-serif;
    color: #eee;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100vh;
    margin: 0;
    text-align: center;
  }
  h1 {
    font-family: 'Orbitron', sans-serif;
    font-weight: 500;
    font-size: 3rem;
    color: #ff4081;
    text-shadow: 0 0 15px #ff4081;
  }
  p {
    font-size: 1.5rem;
    margin: 15px 0;
  }
  .balance {
    font-size: 2rem;
    font-weight: bold;
    margin: 20px 0;
    color: #00e676;
    text-shadow: 0 0 10px #00e676;
  }
  a.logout {
    display: inline-block;
    margin-top: 40px;
    padding: 10px 25px;
    background: #ff4081;
    color: white;
    font-weight: 600;
    border-radius: 25px;
    text-decoration: none;
    box-shadow: 0 0 15px #ff4081;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
  }
  a.logout:hover {
    background: #e040fb;
    box-shadow: 0 0 20px #e040fb;
  }

  .top-up-btn {
  display: inline-block;
  padding: 10px 25px;
  background: #00e676;
  color: white;
  font-weight: 600;
  border-radius: 25px;
  text-decoration: none;
  box-shadow: 0 0 15px #00e676;
  border: none;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.3s ease, box-shadow 0.3s ease;
  margin-right: 10px;
  }

  .top-up-btn:hover {
  background: #00c853;
  box-shadow: 0 0 20px #00c853;
  }


</style>
</head>
<body>

<h1>Welcome, <?= htmlspecialchars($username) ?>!</h1>
<p>Your current balance is:</p>
<div class="balance">$<?= number_format($balance, 2) ?></div>

<form method="POST" style="margin-top: 20px;">
  <button type="submit" name="topup" class="top-up-btn">💰 Top Up</button>
  <a href="logout.php" class="logout">Logout</a>
</form>

</body>
</html>
