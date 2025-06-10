<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$username = $_SESSION['username'];

$query = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Settings</title>
  <link rel="stylesheet" href="server.css" />
  <style>
    .setting-container {
      max-width: 500px;
      margin: 50px auto;
      padding: 30px;
      background-color: #0e1142;
      border-radius: 20px;
      color: white;
      box-shadow: 0 0 10px rgba(0, 255, 200, 0.1);
    }

    .setting-container h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    .setting-container p {
      margin: 10px 0;
    }

    .setting-container form {
      margin-top: 30px;
    }

    .setting-container input[type="password"],
    .setting-container input[type="submit"] {
      width: 100%;
      padding: 10px;
      border-radius: 10px;
      border: none;
      margin-bottom: 15px;
    }

    .setting-container input[type="submit"] {
      background-color: #2196f3;
      color: white;
      cursor: pointer;
    }

    .logout-btn {
      display: inline-block;
      margin-top: 10px;
      color: #ff6666;
      text-decoration: none;
    }

    .logout-btn:hover {
      text-decoration: usnderline;
    }
  </style>
</head>
<body>
<a href="server.php" class="back-btn">← Kembali ke Server</a>
  <div class="setting-container">
    <h2>Account Settings</h2>
    <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>

    <form method="POST" action="update_password.php">
      <h3>Change Password</h3>
      <input type="password" name="new_password" placeholder="New Password" required />
      <input type="submit" value="Update Password" />
    </form>

    <a href="logout.php" class="logout-btn">Logout</a>
  </div>
</body>
</html>
