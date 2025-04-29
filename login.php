<?php
include 'db.php';
session_start();

define('MAX_ATTEMPTS', 5);         // How many wrong tries allowed
define('BLOCK_TIME', 5 * 60);      // Block time in seconds (5 minutes)

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];

    // Check login attempts first
    $stmt = $conn->prepare("SELECT attempts, UNIX_TIMESTAMP(last_attempt) FROM login_attempts WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($attempts, $last_attempt_time);
    $stmt->fetch();

    $current_time = time();
    if ($stmt->num_rows > 0) {
        if ($attempts >= MAX_ATTEMPTS && ($current_time - $last_attempt_time) < BLOCK_TIME) {
            die("Too many failed attempts. Please try again after a few minutes.");
        }
    }
    $stmt->close();

    // Now check the user credentials
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['username'] = $username;
            
            // Successful login ➔ Reset login attempts
            $stmt_reset = $conn->prepare("DELETE FROM login_attempts WHERE username = ?");
            $stmt_reset->bind_param("s", $username);
            $stmt_reset->execute();

            header('Location: dashboard.php');
            exit();
        } else {
            // Wrong password ➔ Increase attempts
            recordFailedAttempt($conn, $username);
            echo "Invalid credentials.";
        }
    } else {
        // Username not found ➔ Increase attempts
        recordFailedAttempt($conn, $username);
        echo "Invalid credentials.";
    }
}

function recordFailedAttempt($conn, $username) {
    $stmt = $conn->prepare("INSERT INTO login_attempts (username, attempts) 
                            VALUES (?, 1) 
                            ON DUPLICATE KEY UPDATE 
                                attempts = attempts + 1, 
                                last_attempt = CURRENT_TIMESTAMP");
    $stmt->bind_param("s", $username);
    $stmt->execute();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form method="post">
            Username: <input type="text" name="username" required><br>
            Password: <input type="password" name="password" required><br>
            <input type="submit" value="Login">
        </form>
        <p>Don't have an account? <a href="register.php">Register now</a></p>
    </div>
</body>
</html>
