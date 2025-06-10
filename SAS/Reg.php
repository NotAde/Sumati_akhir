<?php
include 'koneksi.php'; // your existing DB connection

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data safely
    $username = $_POST['username'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    $confirm_password = $_POST['confirm_password'] ?? null;

    if (!$username || !$email || !$password || !$confirm_password) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Insert into database
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sss", $username, $email, $password);
            if ($stmt->execute()) {
                $success = "Registration successful!";
            } else {
                $error = "Registration failed: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Database error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="newReg.css">
    <title>Skenaver Register Page</title>
</head>
<body>
    <div class="background"></div>
    <div class="logo">
        <img src="..\resource\skenlogo.png" alt="">
        </div>
        <div class="regist-box">
                <h2 id="regf">REGISTER</h2>
                    <form method="POST" id="inpt">
                        <div>
                            <div class="input-container">
                            <label for="username">Username</label>
                            <input type="text" name="username" placeholder="Enter Username" required>
                            </div>

                            <div class="input-container"><label for="email">Email</label>
                            <input type="email" name="email" placeholder="Enter Email" required>
                            </div>

                            <div class="input-container"><label for="password">Password</label>
                            <input type="password" name="password" placeholder="Enter Password" required>
                            </div>

                            <div class="input-container"><label for="confirm_password">Repeat Password</label>
                            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                            </div>
                        </div>
                        <input type="submit" value="Register">
                        </div>    
                    </form>
                <p class="bottom-text">Already have an account? <a href="Login.php">Log in</a></p>
        </div>
</body>
</html>