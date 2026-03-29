<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitzone Gym | Train Hard, Stay Strong</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css"> 
    <style>
        :root {
            --bg-dark: #12141d;
            --card-navy: #1c1f2e;
            --accent-orange: #ff7d2a;
            --accent-orange-hover: #e66a1f;
            --card-border: rgba(255, 255, 255, 0.05);
        }
        body {
            background-color: var(--bg-dark);
            color: #ffffff;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }
        .card-dashboard {
            background-color: var(--card-navy);
            border: 1px solid var(--card-border);
            border-radius: 1.25rem;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }
        .card-dashboard:hover { border-color: rgba(255, 125, 42, 0.3); transform: translateY(-5px); }
        .btn-orange { background-color: var(--accent-orange); color: white; transition: all 0.2s ease; }
        .btn-orange:hover { background-color: var(--accent-orange-hover); transform: scale(1.05); }
        
        .hero-section {
            background: linear-gradient(rgba(18, 20, 29, 0.8), rgba(18, 20, 29, 0.95)), 
                        url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=2070');
            background-size: cover;
            background-position: center;
        }

        /* Modern Profile Styling */
        .profile-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            cursor: pointer;
        }

        .profile-icon-container {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: 0.3s;
        }

        .profile-menu:hover .profile-icon-container {
            border-color: var(--accent-orange);
            color: var(--accent-orange);
        }

        .username-display {
            font-size: 12px;
            color: #9ca3af;
            font-weight: 500;
        }

        .profile-menu { 
            position: relative; 
            list-style: none; 
            padding-bottom: 10px;
            margin-bottom: -10px; 
        }

        .dropdown { 
            display: none;
            position: absolute;
            top: 55px; 
            right: 0; 
            background: #1c1f2e;
            min-width: 180px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            padding: 12px;
            z-index: 100;
        }

        .dropdown a {
            color: #d1d5db !important;
            display: block;
            padding: 10px 12px;
            text-decoration: none;
            font-size: 14px;
            border-radius: 6px;
            transition: 0.2s;
        }

        .dropdown a:hover {
            color: #ffffff !important;
            background: rgba(255, 125, 42, 0.15) !important;
        }

        .profile-menu:hover .dropdown {
            display: block;
        }
    </style>
</head>
<body class="min-h-screen">

    <nav class="flex items-center justify-between px-8 py-5 w-full sticky top-0 bg-[#12141d]/90 backdrop-blur-md z-50">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-[#ff7d2a] rounded-lg flex items-center justify-center">
                <i class="fas fa-dumbbell text-white text-xs"></i>
            </div>
            <span class="text-xl font-bold tracking-tight">Fitzone Gym</span>
        </div>

        <ul class="flex items-center gap-8 text-sm font-medium">
            <?php if (!isset($_SESSION['username'])) { ?>
                <li><a href="plans.html" class="text-gray-300 hover:text-[#ff7d2a]">Plans</a></li>
                <li><a href="login.php" class="text-gray-300 hover:text-[#ff7d2a]">Login</a></li>
                <li><a href="register.php" class="btn-orange px-4 py-2 rounded-lg">Register</a></li>
            <?php } else { ?>
                <li class="profile-menu">
                    <div class="profile-wrapper">
                        <div class="profile-icon-container">
                            <i class="fas fa-user text-lg"></i>
                        </div>
                        <span class="username-display"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </div>
                    
                    <div class="dropdown">
                        <a href="plans.html">Plans</a>
                        <?php if ($_SESSION['role'] == 'admin') { ?>
                            <a href="admin_dashboard.php">Dashboard</a>
                            <a href="members.php">Members</a>
                        <?php } else { ?>
                            <a href="user_dashboard.php">Dashboard</a>
                        <?php } ?>
                        <a href="logout.php" style="border-top: 1px solid rgba(255,255,255,0.1); margin-top: 5px; color: #ff6b6b !important;">Logout</a>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </nav>

    <header class="hero-section h-[500px] flex items-center justify-center text-center px-4">
        <div class="max-w-3xl">
            <h1 class="text-3xl md:text-5xl font-extrabold mb-4">Train Hard, Stay Strong 💪</h1>
            <p class="text-gray-400 text-lg mb-8">Manage your gym members and plans easily</p>

            <?php if (!isset($_SESSION['username'])) { ?>
                <a href="login.php" class="btn-orange px-10 py-3 rounded-full font-bold inline-block">Login</a>
            <?php } else { ?>
                <?php $target = ($_SESSION['role'] == 'admin') ? 'admin_dashboard.php' : 'user_dashboard.php'; ?>
                <a href="<?php echo $target; ?>" class="btn-orange px-10 py-3 rounded-full font-bold inline-block">Go to Dashboard</a>
            <?php } ?>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-16 space-y-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="card-dashboard p-8 text-center">
                <div class="text-3xl mb-4">👦</div>
                <h3 class="text-lg font-bold mb-2">Members</h3>
                <p class="text-sm text-gray-400">Manage gym members & details</p>
            </div>
            <div class="card-dashboard p-8 text-center flex flex-col justify-center items-center">
                <div class="text-xl font-bold">🧘‍♀️ Yoga & Cardio</div>
                <p class="text-sm text-gray-400 mt-2">Yoga: Flexibility & mindfulness<br>Cardio: Heart & endurance</p>
            </div>
            <div class="card-dashboard p-8 text-center">
                <div class="text-3xl mb-4">💪</div>
                <h3 class="text-lg font-bold mb-2">Personal Training</h3>
                <p class="text-sm text-gray-400">Customized fitness guidance</p>
            </div>
            <div class="card-dashboard p-8 text-center flex flex-col justify-center items-center">
                <div class="text-xl font-bold">🤼 Unisex</div>
                <p class="text-sm text-gray-400 mt-2">Fitness for everyone</p>
            </div>
            <div class="card-dashboard p-8 text-center md:col-span-2 flex flex-col justify-center items-center">
                <div class="text-xl font-bold">🏋️ Modern Equipments</div>
                <p class="text-sm text-gray-400 mt-2">Smart Strength Machines</p>
            </div>
            <div class="card-dashboard p-8 text-center md:col-span-2 flex flex-col justify-center items-center">
                <div class="text-xl font-bold">🚿 Clean Changing & Shower Rooms</div>
                <p class="text-sm text-gray-400 mt-2">Safe, Hygienic & Respectful</p>
            </div>
        </div>

        <div class="dashboard-panel p-12 text-center" style="background-color: #1c1f2e; border-radius: 1.5rem; border: 1px solid rgba(255,255,255,0.05);">
            <h2 class="text-3xl font-bold mb-6">About Us</h2>
            <p class="text-gray-300 max-w-4xl mx-auto leading-relaxed">
                Welcome to FitZone Gym, a place where fitness meets discipline and dedication. Stay fit. Stay strong. 💪
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="card-dashboard p-8 text-center">
                <div class="text-2xl text-[#ff7d2a] mb-2">📍 Location</div>
                <p class="text-gray-300">Near ITM College, Nanded</p>
            </div>
            <div class="card-dashboard p-8 text-center">
                <div class="text-2xl text-[#ff7d2a] mb-2">📞 Phone</div>
                <p class="text-gray-300">7666245541</p>
            </div>
        </div>
    </main>

    <footer class="bg-[#0b0d12] py-10 border-t border-white/5 text-center mt-20">
        <p class="text-gray-500 text-sm mb-4">
            © 2026 FitZone Gym | <i class="far fa-envelope text-[#ff7d2a]"></i> fitlife@gym.com
        </p>
        <div class="flex justify-center gap-8 text-xs font-semibold text-gray-400">
            <a href="index.php" class="uppercase tracking-widest hover:text-white transition-colors">Home</a>
            <?php if (!isset($_SESSION['username'])) { ?>
                <a href="login.php" class="uppercase tracking-widest hover:text-white transition-colors">Login</a>
            <?php } else { ?>
                <a href="logout.php" class="uppercase tracking-widest hover:text-white transition-colors">Logout</a>
            <?php } ?>
        </div>
    </footer>

</body>
</html>