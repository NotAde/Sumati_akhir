<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$userId = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Handle update domain
if (
  $_SERVER["REQUEST_METHOD"] === "POST" &&
  isset($_POST['account_id'], $_POST['domain_name'])
) {
  $accountId = $_POST['account_id'];
  $domainName = $_POST['domain_name'];

  $filePath = null;
  if (!empty($_FILES['project_file']['name'])) {
    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) mkdir($uploadDir);
    $fileName = basename($_FILES['project_file']['name']);
    $filePath = $uploadDir . $fileName;
    move_uploaded_file($_FILES['project_file']['tmp_name'], $filePath);
  }

  if ($filePath) {
    $stmt = $conn->prepare("UPDATE user_hosting_accounts SET domain_name=?, file_path=? WHERE account_id=?");
    $stmt->bind_param("ssi", $domainName, $filePath, $accountId);
  } else {
    $stmt = $conn->prepare("UPDATE user_hosting_accounts SET domain_name=? WHERE account_id=?");
    $stmt->bind_param("si", $domainName, $accountId);
  }

  $stmt->execute();
  echo "<script>location.href='my_domains.php';</script>";
  exit;
}
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_domain_id"])) {
    $domainId = $_POST["delete_domain_id"];
    
    $deleteStmt = $conn->prepare("DELETE FROM user_hosting_accounts WHERE account_id = ? AND user_id = ?");
    $deleteStmt->bind_param("ii", $domainId, $userId);
    $deleteStmt->execute();
  
    // Redirect to avoid resubmission
    header("Location: my_domains.php");
    exit;
  }
  
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>My Domains</title>
  <link rel="stylesheet" href="server.css" />
  <style>
    .modal {
      display: none;
      position: fixed;
      z-index: 999;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.5);
    }
    .modal-content {
      background-color: #1e1e2f;
      margin: 10% auto;
      padding: 20px;
      border-radius: 10px;
      width: 400px;
      color: white;
    }
    .close-btn {
      float: right;
      font-size: 24px;
      cursor: pointer;
    }
  

  </style>
</head>
<body>

<h2 style="text-align:center;color:white;">Hello, <?= htmlspecialchars($username) ?> — Manage Your Domains</h2>
<div class="manage-container">
<a href="server.php" class="back-btn">← Kembali ke Server</a>
<?php
$query = "
  SELECT 
    uha.account_id,
    uha.domain_name,
    uha.start_date,
    uha.expiry_date,
    s.hostname,
    s.ip_address,
    s.status
  FROM user_hosting_accounts uha
  JOIN servers s ON uha.server_id = s.server_id
  WHERE uha.user_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $statusColor = match(strtolower($row['status'])) {
      'active' => '#00e676',
      'maintenance' => '#ffc107',
      'offline' => '#f44336',
      default => '#9e9e9e',
    };

    echo '
<div class="server-container">
  <div class="status-indicator" style="background-color:' . $statusColor . ';"></div>
  <div class="server-info">
    <h3>Domain: ' . htmlspecialchars($row['domain_name']) . '</h3>
    <p>Server: ' . htmlspecialchars($row['hostname']) . '</p>
    <p>IP: ' . htmlspecialchars($row['ip_address']) . '</p>
    <p>Status: <span class="status-text" style="color:' . $statusColor . ';">' . ucfirst($row['status']) . '</span></p>
    <p>Start Date: ' . date("d M Y", strtotime($row['start_date'])) . '</p>
    <p>Expiry Date: ' . date("d M Y", strtotime($row['expiry_date'])) . '</p>
    <button class="manage-btn edit-btn" 
      data-id="' . $row['account_id'] . '" 
      data-domain="' . htmlspecialchars($row['domain_name']) . '">Edit</button>
<form method="POST" onsubmit="return confirm(\'Are you sure you want to delete this domain?\');" style="display:inline;">
  <input type="hidden" name="delete_domain_id" value="' . $row['account_id'] . '">
  <button type="submit" class="manage-btn danger-btn">Delete</button>
</form>
  </div>
</div>';
  }
} else {
  echo "<p style='text-align:center;color:gray;'>You have no hosted domains.</p>";
}
?>
</div>

<!-- Modal HTML -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <span class="close-btn" onclick="document.getElementById('editModal').style.display='none';">&times;</span>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="account_id" id="modalAccountId">
      <label>Domain Name:</label>
      <input type="text" name="domain_name" id="modalDomainName" required>
      <label>Replace Project File (optional):</label>
      <input type="file" name="project_file">
      <input type="submit" value="Update">
    </form>
  </div>
</div>

<script>
document.querySelectorAll('.edit-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.getElementById('modalAccountId').value = btn.dataset.id;
    document.getElementById('modalDomainName').value = btn.dataset.domain;
    document.getElementById('editModal').style.display = 'block';
  });
});
</script>

</body>
</html>
