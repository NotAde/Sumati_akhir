<?php
session_start();
include 'koneksi.php';

if (!isset($_GET['domain_id'])) {
    header("Location: server.php");
    exit;
}

$domainId = $_GET['domain_id'];

// Ambil data domain
$stmt = $conn->prepare("SELECT * FROM user_hosting_accounts WHERE account_id = ?");
$stmt->bind_param("i", $domainId);
$stmt->execute();
$result = $stmt->get_result();
$domain = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newDomain = $_POST['domain_name'];
    
    // Jika ada file baru diupload
    if (!empty($_FILES['project_file']['name'])) {
        $fileName = $_FILES['project_file']['name'];
        $tmpPath = $_FILES['project_file']['tmp_name'];
        $uploadDir = "uploads/";
        $targetFile = $uploadDir . basename($fileName);

        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        move_uploaded_file($tmpPath, $targetFile);

        // update nama + file
        $update = $conn->prepare("UPDATE user_hosting_accounts SET domain_name=?, file_path=? WHERE id=?");
        $update->bind_param("ssi", $newDomain, $targetFile, $domainId);
    } else {
        // update hanya nama
        $update = $conn->prepare("UPDATE user_hosting_accounts SET domain_name=? WHERE id=?");
        $update->bind_param("si", $newDomain, $domainId);
    }

    $update->execute();
    header("Location: manage_server.php?server_id=" . $domain['server_id']);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Domain</title>
  
</head>
<body>
  <h2>Edit Domain: <?= htmlspecialchars($domain['domain_name']) ?></h2>
  <form method="POST" enctype="multipart/form-data">
    <label>Domain Name:</label><br>
    <input type="text" name="domain_name" value="<?= htmlspecialchars($domain['domain_name']) ?>" required><br><br>

    <label>Replace Project File (optional):</label><br>
    <input type="file" name="project_file"><br><br>

    <button type="submit">Update</button>
  </form>

  <br>
  <a href="manage_server.php?server_id=<?= $domain['server_id'] ?>">← Back to Manage Server</a>
</body>
</html>
