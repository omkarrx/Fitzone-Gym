<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            // Set session data
            $_SESSION['username'] = $row['username'];
            $_SESSION['role']     = $row['role'];
            $_SESSION['user_id']  = $row['id'];

            // CRITICAL: Check this first before dashboard redirection
            if (isset($row['must_change_password']) && $row['must_change_password'] == 1) {
                header("Location: change_password.php");
                exit;
            }

            // If no forced change, go to dashboard
            if ($row['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Fitzone</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #12141d; color: white; }
        .card { background: #1c1f2e; border: 1px solid rgba(255,255,255,0.05); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full card p-10 rounded-3xl shadow-2xl">
        <h2 class="text-3xl font-bold text-center mb-6">Login</h2>
        <?php if(isset($error)) echo "<p class='text-red-500 text-center mb-4'>$error</p>"; ?>
        <form method="POST" class="space-y-4">
            <input type="text" name="username" placeholder="Username" required class="w-full p-3 rounded-xl bg-white/5 border border-white/10 text-white">
            <input type="password" name="password" placeholder="Password" required class="w-full p-3 rounded-xl bg-white/5 border border-white/10 text-white">
            <button type="submit" class="w-full bg-[#ff7d2a] py-3 rounded-xl font-bold">Sign In</button>
        </form>
    </div>
</body>
</html>