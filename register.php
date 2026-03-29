<?php
include 'db.php';

/* --- Handle Registration Logic --- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        header("Location: login.php");
        exit;
    } else {
        $error = "Registration failed. Try a different username.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Fitzone Gym</title>
    
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

        /* --- Form Components --- */
        .form-card { 
            background-color: var(--card-navy); 
            border: 1px solid rgba(255, 255, 255, 0.05); 
            border-radius: 1.5rem; 
        }

        input { 
            background: #12141d !important; 
            border: 1px solid rgba(255, 255, 255, 0.1) !important; 
            color: white !important; 
        }
        
        input:focus {
            border-color: var(--accent-orange) !important;
            outline: none;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4 py-12">

    <div class="form-card w-full max-w-md p-8 shadow-2xl">
        <div class="text-center mb-8">
            <div class="w-12 h-12 bg-[#ff7d2a] rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-orange-500/20">
                <i class="fas fa-user-plus text-white text-xl"></i>
            </div>
            <h2 class="text-3xl font-bold tracking-tight">Join Us</h2>
            
            <?php if (isset($error)): ?>
                <p class="text-red-500 mt-2 text-sm"><?php echo $error; ?></p>
            <?php endif; ?>
        </div>

        <form method="POST" class="space-y-5">
            <input type="text" name="username" placeholder="Username" required 
                   class="w-full px-4 py-3 rounded-xl focus:ring-2 focus:ring-[#ff7d2a] transition-all">
            
            <input type="email" name="email" placeholder="Email" required 
                   class="w-full px-4 py-3 rounded-xl focus:ring-2 focus:ring-[#ff7d2a] transition-all">
            
            <input type="password" name="password" placeholder="Password" required 
                   class="w-full px-4 py-3 rounded-xl focus:ring-2 focus:ring-[#ff7d2a] transition-all">
            
            <button type="submit" class="w-full bg-[#ff7d2a] hover:bg-[#e66a1f] text-white font-bold py-3 rounded-xl transition-all shadow-lg active:scale-95">
                Register
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-gray-400">
            Already have an account? 
            <a href="login.php" class="text-[#ff7d2a] font-bold hover:underline">Login</a>
        </div>
    </div>

</body>
</html>