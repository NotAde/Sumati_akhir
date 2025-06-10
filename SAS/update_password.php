<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$userId = $_SESSION['user_id'];
$newPassword = $_POST['new_password'];

// Simpan password secara langsung (tidak aman). Rekomendasi: pakai password_hash()
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
$stmt->bind_param("si", $newPassword, $userId);

if ($stmt->execute()) {
    echo "<script>alert('Password updated successfully'); window.location.href='setting.php';</script>";
} else {
    echo "Error updating password: " . $conn->error;
}
?>
