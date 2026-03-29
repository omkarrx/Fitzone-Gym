<?php
session_start();

/* Only admin allowed */
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

include 'db.php'; /* */

$msg = "";
$msg_type = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    /* automatic password hashing */
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, email, password, role, must_change_password)
            VALUES (?, ?, ?, 'admin', 1)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $hash);

    if ($stmt->execute()) {
        $msg = "Admin account created successfully!";
        $msg_type = "success";
    } else {
        $msg = "Error: This username or email might already be taken.";
        $msg_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin | Fitzone Gym</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --bg-dark: #12141d; --card-navy: #1c1f2e; --accent-orange: #ff7d2a; }
        body { background-color: var(--bg-dark); color: #ffffff; font-family: 'Inter', sans-serif; }
        .form-card { background-color: var(--card-navy); border: 1px solid rgba(255,255,255,0.05); border-radius: 1.5rem; }
        input { 
            background: rgba(255,255,255,0.03) !important; 
            border: 1px solid rgba(255,255,255,0.1) !important;
            color: white !important;
        }
        input:focus { 
            border-color: var(--accent-orange) !important; 
            outline: none;
            box-shadow: 0 0 0 2px rgba(255, 125, 42, 0.2);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-lg">
        <a href="admin_dashboard.php" class="inline-flex items-center gap-2 text-gray-400 hover:text-white mb-6 transition-colors">
            <i class="fas fa-arrow-left text-sm"></i> Back to Dashboard
        </a>

        <div class="form-card p-8 md:p-10 shadow-2xl">
            <div class="mb-8 text-center">
                <div class="w-16 h-16 bg-blue-500/10 text-blue-400 rounded-2xl flex items-center justify-center text-3xl mb-4 mx-auto border border-blue-500/20">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h2 class="text-2xl font-black tracking-tight">Create New Admin</h2>
                <p class="text-gray-400 text-sm mt-2">Assign administrative privileges to a new user.</p>
                
                <?php if($msg != ""): ?>
                    <div class="mt-6 p-4 rounded-xl text-sm font-medium <?php echo $msg_type == 'success' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20'; ?>">
                        <i class="<?php echo $msg_type == 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'; ?> mr-2"></i>
                                                <?php echo $msg; ?>
                    </div>
                <?php endif; ?>
            </div>

            <form method="POST" class="space-y-4">
                <input type="text" name="username" placeholder="Username" required
                       class="w-full px-4 py-3 rounded-xl">

                <input type="email" name="email" placeholder="Email" required
                       class="w-full px-4 py-3 rounded-xl">

                <input type="password" name="password" placeholder="Password" required
                       class="w-full px-4 py-3 rounded-xl">

                <button type="submit"
                        class="w-full bg-orange-500 hover:bg-orange-600 transition rounded-xl py-3 font-semibold">
                    Create Admin
                </button>
            </form>

        </div>
    </div>

</body>
</html>
