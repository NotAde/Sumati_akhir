<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$isAdmin = ($_SESSION['role'] === 'admin');
$userId = $_SESSION['user_id'];

// Cek apakah user punya project
$hasProject = false;
$checkProject = $conn->prepare("SELECT COUNT(*) AS total FROM user_hosting_accounts WHERE user_id = ?");
$checkProject->bind_param("i", $userId);
$checkProject->execute();
$checkResult = $checkProject->get_result();
if ($checkResult) {
    $row = $checkResult->fetch_assoc();
    $hasProject = $row['total'] > 0;
}

$userId = $_SESSION['user_id'];
$username = $_SESSION['username']; // atau $_SESSION['name'] jika itu yang kamu simpan
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="server.css">
    <title>Server</title>
</head>
<body>
<header class="main-header">
  <div class="left-section">
    <span class="user-id"><?php echo htmlspecialchars($username) . '/' . htmlspecialchars($userId); ?></span>
  </div>
  <div class="center-section">
    <img src="asset/Skenaaver.png" alt="Skenaver Logo" class="logo" />
  </div>

  <div class="right-section">
    <div class="profile-icon"></div>
  </div>
</header>

<nav class="nav-bar">
  <div class="nav-toggle" id="navToggle">&#9776;</div>
  <ul class="nav-links" id="navLinks">
    <li><a href="#">SERVER</a></li>
    <li><a href="my_domains.php">MANAGE</a></li>
    <li><a href="setting.php">SETTING</a></li>
  </ul>
</nav>

<?php if ($isAdmin || $hasProject): ?>
  <!-- Tampilkan daftar server seperti biasa -->
  <?php
    $query = "SELECT * FROM servers";
    $result = mysqli_query($conn, $query);
    while ($server = mysqli_fetch_assoc($result)) {
        // ... tampilkan server-container (seperti biasa)
    }
  ?>
<?php else: ?>
  <!-- User biasa & belum punya project -->
  <div class="server-container" style="text-align: center;">
    <p style="color:white;">You currently have no projects hosted.</p>
    <a href="manage_server.php?server_id=1" class="add-project-btn">+ Add Your First Project</a>
  </div>
<?php endif; ?>
<!----------------------------------------------------------------------------------------------------------------------------------------->
<?php
$query = "SELECT * FROM servers";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query error: " . mysqli_error($conn));
}

echo '<div class="server-list">';

while ($server = mysqli_fetch_assoc($result)) {
  $status = strtolower($server['status']);
  $statusColor = match($status) {
      'active' => '#00e676',
      'maintenance' => '#ffc107',
      'offline' => '#f44336',
      default => '#9e9e9e',
  };

  echo '
  <div class="server-container">
      <div class="status-indicator" style="background-color:' . $statusColor . ';"></div>
      <div class="server-info">
          <h3>' . htmlspecialchars($server['hostname']) . '</h3>
          <p>IP: ' . htmlspecialchars($server['ip_address']) . '</p>
          <p>Location: ' . htmlspecialchars($server['location']) . '</p>
          <p>Status: <span class="status-text" style="color:' . $statusColor . ';">' . ucfirst($status) . '</span></p>
      </div>';

  if ($status === 'active') {
      echo '<a href="manage_server.php?server_id=' . $server['server_id'] . '" class="manage-btn">Manage</a>';
  } else {
      echo '<button class="manage-btn disabled-btn" disabled>Manage</button>';
  }

  echo '</div>';
}

echo '</div>'; // penutup .server-list
?>

<script>
  const toggle = document.getElementById('navToggle');
  const navLinks = document.getElementById('navLinks');

  toggle.addEventListener('click', () => {
    navLinks.classList.toggle('show');
  });
</script>

</body>
</html>
