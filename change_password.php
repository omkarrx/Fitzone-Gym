<?php
session_start();

// 1. Access Control: Ensure the user is actually logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'db.php'; 

$msg = "";
$msg_type = "";

// 2. Process Password Change
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if ($new_password !== $confirm_password) {
        $msg = "Passwords do not match.";
        $msg_type = "error";
    } else {
        // Securely hash the new password
        $hash = password_hash($new_password, PASSWORD_DEFAULT);
        $username = $_SESSION['username'];

        // Update the password and RESET the must_change_password flag to 0
        $sql = "UPDATE users SET password = ?, must_change_password = 0 WHERE username = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $hash, $username);

        if ($stmt->execute()) {
            $msg = "Password updated successfully! Redirecting...";
            $msg_type = "success";
            
            // Redirect to dashboard after 2 seconds
            header("refresh:2; url=" . ($_SESSION['role'] === 'admin' ? "admin_dashboard.php" : "user_dashboard.php"));
        } else {
            $msg = "Error updating password. Please try again.";
            $msg_type = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password | Fitzone Gym</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root { 
            --bg-dark: #12141d; 
            --card-navy: #1c1f2e; 
            --accent-orange: #ff7d2a; 
        }
        
        body { 
            background-color: var(--bg-dark); 
            color: #ffffff; 
            font-family: 'Inter', sans-serif; 
        }

        .form-card { 
            background-color: var(--card-navy); 
            border: 1px solid rgba(255,255,255,0.05); 
            border-radius: 1.5rem; 
        }

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
        <div class="form-card p-8 md:p-12 shadow-2xl">
            <div class="text-center mb-10">
                <div class="w-16 h-16 bg-orange-500/10 text-orange-400 rounded-2xl flex items-center justify-center text-3xl mb-4 mx-auto">
                    <i class="fas fa-key"></i>
                </div>
                <h2 class="text-3xl font-black tracking-tight">Change Password</h2>
                <p class="text-gray-400 text-sm mt-2">You must update your temporary password to continue.</p>
                
                <?php if($msg != ""): ?>
                    <div class="mt-6 p-4 rounded-xl text-sm font-medium <?php echo $msg_type == 'success' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20'; ?>">
                        <i class="<?php echo $msg_type == 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'; ?> mr-2"></i>
                        <?php echo $msg; ?>
                    </div>
                <?php endif; ?>
            </div>

            <form method="POST" class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">New Password</label>
                    <input type="password" name="password" placeholder="••••••••" required
                           class="w-full px-4 py-3 rounded-xl transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Confirm New Password</label>
                    <input type="password" name="confirm_password" placeholder="••••••••" required
                           class="w-col px-4 py-3 rounded-xl transition-all w-full">
                </div>

                <div class="pt-4">
                    <button type="submit"
                            class="w-full bg-[#ff7d2a] hover:bg-[#e66a1f] text-white font-bold py-4 rounded-xl transition-all transform hover:scale-[1.02] active:scale-[0.98] shadow-lg shadow-orange-500/20">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>