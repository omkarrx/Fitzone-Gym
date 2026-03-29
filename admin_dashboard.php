 <?php
session_start();

// 1. Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// 2. Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: user_dashboard.php"); 
    exit;
}

include 'db.php';

// --- Fetch Statistics ---

// Total members
$q1 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM members");
$r1 = mysqli_fetch_assoc($q1);
$total = $r1['total'];

// Active members (joined within last 30 days)
$q2 = mysqli_query($conn, "
    SELECT COUNT(*) AS active 
    FROM members 
    WHERE join_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
");
$r2 = mysqli_fetch_assoc($q2);
$active = $r2['active'];

// Expired members (joined more than 30 days ago)
$q3 = mysqli_query($conn, "
    SELECT COUNT(*) AS expired 
    FROM members 
    WHERE join_date < DATE_SUB(CURDATE(), INTERVAL 30 DAY)
");
$r3 = mysqli_fetch_assoc($q3);
$expired = $r3['expired'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Fitzone Gym</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --bg-dark: #12141d;
            --card-navy: #1c1f2e;
            --accent-orange: #ff7d2a;
            --text-light: #d1d5db;
        }

        body {
            background-color: var(--bg-dark);
            color: #ffffff;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        .dashboard-card {
            background-color: var(--card-navy);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 1.25rem;
            transition: all 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            border-color: rgba(255, 125, 42, 0.3);
            box-shadow: 0 10px 20px -10px rgba(255, 125, 42, 0.3);
        }

        /* Dropdown Styles */
        .profile-menu { position: relative; padding: 10px 0; }
        .dropdown { 
            display: none; position: absolute; top: 45px; right: 0; 
            background: #1c1f2e; min-width: 180px; border-radius: 12px; 
            border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 10px 25px rgba(0,0,0,0.5); 
            padding: 12px; z-index: 100;
        }
        .dropdown a { 
            color: #d1d5db !important; display: block; padding: 10px 12px; 
            text-decoration: none; border-radius: 6px; transition: 0.2s; 
        }
        .dropdown a:hover { 
            color: #ffffff !important; background: rgba(255, 125, 42, 0.15) !important; 
        }
        .profile-menu:hover .dropdown { display: block; }
    </style>
</head>

<body class="min-h-screen bg-[--bg-dark]">

    <nav class="flex items-center justify-between px-8 py-5 w-full bg-[#12141d]/90 backdrop-blur-md border-b border-white/5 sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-tr from-[#ff7d2a] to-[#e66a1f] rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/20">
                <i class="fas fa-dumbbell text-white text-lg"></i>
            </div>
            <span class="text-2xl font-black tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400">Fitzone Admin</span>
        </div>

        <ul class="flex items-center gap-8 text-sm font-medium">
            <li><a href="index.php" class="text-gray-400 hover:text-white transition-colors flex items-center gap-2"><i class="fas fa-home"></i> Home View</a></li>
            <li class="profile-menu">
                <div class="flex items-center gap-3 cursor-pointer group">
                    <div class="text-right hidden md:block">
                        <p class="text-sm font-bold text-white group-hover:text-[--accent-orange] transition-colors"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                        <p class="text-xs text-gray-400">Administrator</p>
                    </div>
                    <div class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center border border-white/10 group-hover:border-[--accent-orange] transition-all">
                         <i class="fas fa-user-tie text-gray-300 group-hover:text-[--accent-orange]"></i>
                    </div>
                </div>
                <div class="dropdown">
                    <p class="px-3 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Account</p>
                    <a href="logout.php" class="text-red-400! hover:bg-red-500/10!"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
                </div>
            </li>
        </ul>
    </nav>

    <main class="max-w-7xl mx-auto px-6 py-12">
        
        <div class="mb-12">
            <h1 class="text-4xl font-extrabold text-white mb-2">Dashboard Overview</h1>
            <p class="text-lg text-gray-400">Welcome back, <span class="text-[--accent-orange] font-bold"><?php echo htmlspecialchars($_SESSION['username']); ?></span>. Here's your gym's performance.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
            <div class="dashboard-card p-6 flex items-center gap-5">
                <div class="w-16 h-16 bg-blue-500/10 text-blue-400 rounded-2xl flex items-center justify-center text-3xl">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <p class="text-gray-400 text-sm font-bold uppercase tracking-wider">Total Members</p>
                    <p class="text-4xl font-black text-white mt-1"><?php echo $total; ?></p>
                </div>
            </div>

            <div class="dashboard-card p-6 flex items-center gap-5">
                <div class="w-16 h-16 bg-green-500/10 text-green-400 rounded-2xl flex items-center justify-center text-3xl">
                    <i class="fas fa-user-check"></i>
                </div>
                <div>
                    <p class="text-gray-400 text-sm font-bold uppercase tracking-wider">Active (New)</p>
                    <p class="text-4xl font-black text-white mt-1"><?php echo $active; ?></p>
                </div>
            </div>

            <div class="dashboard-card p-6 flex items-center gap-5">
                <div class="w-16 h-16 bg-red-500/10 text-red-400 rounded-2xl flex items-center justify-center text-3xl">
                    <i class="fas fa-user-times"></i>
                </div>
                <div>
                    <p class="text-gray-400 text-sm font-bold uppercase tracking-wider">Expired (>30 Days)</p>
                    <p class="text-4xl font-black text-white mt-1"><?php echo $expired; ?></p>
                </div>
            </div>
        </div>

        <div class="mb-6">
             <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                <i class="fas fa-rocket text-[--accent-orange]"></i> Quick Actions
            </h2>
        </div>
       
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <a href="add_member.php" class="dashboard-card group p-8 flex flex-col items-center justify-center text-center hover:bg-[#1c1f2e]/80">
                <div class="w-20 h-20 bg-[--accent-orange]/10 text-[--accent-orange] rounded-full flex items-center justify-center text-3xl mb-6 group-hover:scale-110 group-hover:bg-[--accent-orange] group-hover:text-white transition-all duration-300">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Add New Member</h3>
                <p class="text-gray-400 text-sm">Register a new client to the gym.</p>
            </a>

            <a href="members.php" class="dashboard-card group p-8 flex flex-col items-center justify-center text-center hover:bg-[#1c1f2e]/80">
                <div class="w-20 h-20 bg-purple-500/10 text-purple-400 rounded-full flex items-center justify-center text-3xl mb-6 group-hover:scale-110 group-hover:bg-purple-500 group-hover:text-white transition-all duration-300">
                    <i class="fas fa-list-ul"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">View All Members</h3>
                <p class="text-gray-400 text-sm">Manage existing members & plans.</p>
            </a>

             <a href="assign_plan.php" class="dashboard-card group p-8 flex flex-col items-center justify-center text-center hover:bg-[#1c1f2e]/80">
                <div class="w-20 h-20 bg-teal-500/10 text-teal-400 rounded-full flex items-center justify-center text-3xl mb-6 group-hover:scale-110 group-hover:bg-teal-500 group-hover:text-white transition-all duration-300">
                   <i class="fas fa-clipboard-list"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Assign Plan</h3>
                <p class="text-gray-400 text-sm">Update or assign memberships.</p>
            </a>

            <a href="create_admin.php" class="dashboard-card group p-8 flex flex-col items-center justify-center text-center hover:bg-[#1c1f2e]/80">
                <div class="w-20 h-20 bg-blue-500/10 text-blue-400 rounded-full flex items-center justify-center text-3xl mb-6 group-hover:scale-110 group-hover:bg-blue-500 group-hover:text-white transition-all duration-300">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Create Admin</h3>
                <p class="text-gray-400 text-sm">Grant administrative access.</p>
            </a>

        </div>
    </main>

</body>
</html>