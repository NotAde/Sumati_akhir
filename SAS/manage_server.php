<?php
session_start();
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["domain_name"]) && isset($_FILES['project_file'])) {
  $domainName = $_POST["domain_name"];
  $userId = $_SESSION['user_id'];
  $serverId = $_GET['server_id'];

  $fileName = $_FILES['project_file']['name'];
  $tmpPath  = $_FILES['project_file']['tmp_name'];
  $uploadDir = "uploads/";

  // Buat folder uploads jika belum ada
  if (!is_dir($uploadDir)) {
      mkdir($uploadDir, 0755, true);
  }

  // Tentukan path final
  $targetFile = $uploadDir . basename($fileName);

  // Simpan file ke server
  if (move_uploaded_file($tmpPath, $targetFile)) {
      // Simpan ke database
      $startDate = date("Y-m-d H:i:s");
      $expiryDate = date("Y-m-d H:i:s", strtotime("+1 year"));
      
      $stmt = $conn->prepare("INSERT INTO user_hosting_accounts (user_id, server_id, domain_name, file_path, start_date, expiry_date) VALUES (?, ?, ?, ?, ?, ?)");
      $stmt->bind_param("iissss", $userId, $serverId, $domainName, $targetFile, $startDate, $expiryDate);
      
      $stmt->execute();
      // Ubah role user jadi admin setelah tambah project (jika belum)
      $updateRole = $conn->prepare("UPDATE users SET role = 'admin' WHERE user_id = ? AND role != 'admin'");
      $updateRole->bind_param("i", $userId);
      $updateRole->execute();

  } else {
      echo "<p style='color:red;'>Upload file gagal.</p>";
  }
}


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Server</title>
  <link rel="stylesheet" href="server.css" />
</head>
<body>

<h2 style="text-align: center; color: white;">Hello, <?php echo htmlspecialchars($username); ?> — Your Hosted Domains</h2>

<div class="manage-container">
<a href="server.php" class="back-btn">← Kembali ke Server</a>

<button id="toggleModalBtn" class="add-project-btn">+ Add Project</button>

<div id="projectModal" class="modal">
  <div class="modal-content">
    <span id="closeModalBtn" class="close-btn">&times;</span>
    <form method="POST" enctype="multipart/form-data">
      <input type="text" name="domain_name" required>
      <input type="file" name="project_file" required>
      <input type="submit" value="Submit">
    </form>
  </div>
</div>

<?php
  $check = $conn->prepare("SELECT COUNT(*) as total FROM user_hosting_accounts WHERE server_id = ?");
  $check->bind_param("i", $serverId);
  $check->execute();
  $total = $check->get_result()->fetch_assoc()['total'];

if ($total == 1) { // artinya ini project pertama untuk server itu
    // pastikan status-nya masih 'offline'
    $checkStatus = $conn->prepare("SELECT status FROM servers WHERE server_id = ?");
    $checkStatus->bind_param("i", $serverId);
    $checkStatus->execute();
    $statusResult = $checkStatus->get_result()->fetch_assoc();

    if ($statusResult && $statusResult['status'] == 'ac') {
        $conn->query("UPDATE servers SET status = 'active' WHERE server_id = $serverId");
    }
}

?>
<!--------------------------------------------------------------------------------------------------------------------------------------------->

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
      $status = 'active'; 
$statusColor = '#00e676'; // hijau


        echo '
        <div class="server-container">
          <div class="status-indicator" style="background-color: ' . $statusColor . ';"></div>
          <div class="server-info">
              <h3>Domain: ' . htmlspecialchars($row['domain_name']) . '</h3>
              <p>Server: ' . htmlspecialchars($row['hostname']) . '</p>
              <p>IP: ' . htmlspecialchars($row['ip_address']) . '</p>
              <p>Status: <span class="status-text" style="color:' . $statusColor . ';">' . ucfirst($row['status']) . '</span></p>
              
              <p>Start Date: ' . date("d M Y", strtotime($row['start_date'])) . '</p>
              <p>Expiry Date: ' . date("d M Y", strtotime($row['expiry_date'])) . '</p>

          </div>
        </div>';
    }
} else {
    echo "<p style='text-align:center; color: gray;'>You have no hosted domains.</p>";
}
?>
</div>

<script>
  const toggleBtn = document.getElementById('toggleModalBtn');
  const modal = document.getElementById('projectModal');
  const closeBtn = document.getElementById('closeModalBtn');

  toggleBtn.onclick = () => modal.style.display = "block";
  closeBtn.onclick = () => modal.style.display = "none";
  window.onclick = (e) => {
    if (e.target === modal) modal.style.display = "none";
  }
  document.getElementById('toggleFormBtn').addEventListener('click', function() {
    const form = document.getElementById('projectForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
  });
</script>
</body>
</html>
