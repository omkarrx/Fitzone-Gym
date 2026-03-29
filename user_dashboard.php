 <?php
session_start();
include 'db.php';

//1. SECURITY & DATA FETCHING 
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// 2. ATTENDANCE LOGIC (Fixed to prevent errors) 
$isMarked = false;
$checkToday = mysqli_query($conn, "SELECT * FROM attendance WHERE user_id='$user_id' AND date=CURDATE()");
if(mysqli_num_rows($checkToday) > 0) {
    $isMarked = true;
}

if(isset($_POST['mark_attendance']) && !$isMarked){
    $insert_query = "INSERT INTO attendance (user_id, date, time) VALUES ('$user_id', CURDATE(), CURTIME())";
    if(mysqli_query($conn, $insert_query)) {
        header("Location: user_dashboard.php"); // Refresh to update UI
        exit;
    }
}

//3. WORKOUT LOGIC 
if(isset($_POST['save_workout'])){
    $ex = mysqli_real_escape_string($conn, $_POST['exercise']);
    $s  = (int)$_POST['sets'];
    $r  = (int)$_POST['reps'];
    $w  = (float)$_POST['weight'];
    $dt = date('Y-m-d'); 
    
    mysqli_query($conn, "INSERT INTO workouts (user_id, exercise, sets, reps, weight, workout_date) VALUES ('$user_id', '$ex', '$s', '$r', '$w', '$dt')");
    header("Location: user_dashboard.php");
    exit;
}

// 4. SMART GREETING & QUOTES 
$hour = date("H");
if ($hour < 12) {
    $greeting = "Good Morning";
} elseif ($hour < 17) {
    $greeting = "Good Afternoon";
} elseif ($hour < 21) {
    $greeting = "Good Evening";
} else {
    $greeting = "Good Night";
}

$quotes = [
    "Push yourself, because no one else is going to do it for you.",
    "Success starts with self-discipline.",
    "Your body can stand almost anything. It’s your mind you have to convince.",
    "Consistency is what transforms average into excellence.",
    "Small progress is still progress."
];
$daily_quote = $quotes[array_rand($quotes)];
$today_date = date("l, d M Y");

// 5. MEMBERSHIP DATA 
$plan_query = mysqli_query($conn, "SELECT * FROM user_plans WHERE user_id='$user_id' ORDER BY id DESC LIMIT 1");
$plan = mysqli_fetch_assoc($plan_query);

// Attendance History for Current Plan 
$attendance_history = null;

if($plan){
    $plan_start = $plan['start_date'];
    $plan_end   = $plan['end_date'];

    $attendance_history = mysqli_query(
        $conn,
        "SELECT date, time FROM attendance 
         WHERE user_id='$user_id'
         AND date BETWEEN '$plan_start' AND '$plan_end'
         ORDER BY date DESC, time DESC"
    );
}

//Attendance Streak Logic (Fixed)
$streak = 0;

if($plan){

    $streak_query = mysqli_query(
        $conn,
        "SELECT DISTINCT date FROM attendance
         WHERE user_id='$user_id'
         AND date BETWEEN '$plan_start' AND '$plan_end'
         ORDER BY date DESC"
    );

    $dates = [];
    while($row = mysqli_fetch_assoc($streak_query)){
        $dates[] = $row['date'];
    }

    if(count($dates) > 0){

        $expected = $dates[0]; // start from latest attendance

        foreach($dates as $d){
            if($d == $expected){
                $streak++;
                $expected = date('Y-m-d', strtotime($expected . ' -1 day'));
            } else {
                break;
            }
        }
    }
}


$percent = 0;
$days_left = 0;
$start_date = "--";
if($plan){
    $start_date = $plan['start_date'];
    $start = strtotime($plan['start_date']);
    $end = strtotime($plan['end_date']);
    $now = time();
    $total_duration = $end - $start;
    $elapsed = $now - $start;
    $percent = ($total_duration > 0) ? round(($elapsed / $total_duration) * 100) : 0;
    $percent = max(0, min(100, $percent)); 
    $days_left = round(($end - $now) / (60 * 60 * 24));
    if($days_left < 0) $days_left = 0;
}

$total_visits = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM attendance WHERE user_id='$user_id'"));
$total_workouts = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM workouts WHERE user_id='$user_id'"));
$recent_workouts = mysqli_query($conn, "SELECT * FROM workouts WHERE user_id='$user_id' ORDER BY id DESC LIMIT 5");

?><?php
date_default_timezone_set('Asia/Kolkata');

$currentHour = date('H');

if ($currentHour >= 5 && $currentHour < 12) {
    $greeting = "Good Morning";
} elseif ($currentHour >= 12 && $currentHour < 17) {
    $greeting = "Good Afternoon";
} elseif ($currentHour >= 17 && $currentHour < 21) {
    $greeting = "Good Evening";
} else {
    $greeting = "Good Night";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitZone | Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #0f111a; color: #e2e8f0; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
    </style>
</head>
<body class="p-4 md:p-8">

    <nav class="flex justify-between items-center mb-10 max-w-7xl mx-auto">
        <div class="flex items-center gap-2">
            <div class="bg-orange-500 p-2 rounded-lg">
                <i data-lucide="dumbbell" class="text-white w-6 h-6"></i>
            </div>
            <h1 class="text-xl font-bold tracking-tight">Fitzone Gym</h1>
        </div>
        <div class="flex items-center gap-6 text-sm font-medium">
            <a href="index.php" class="hover:text-orange-500 transition-colors">Home</a>
            <a href="logout.php" class="flex items-center gap-2 hover:text-red-400 transition-colors">
                <i data-lucide="log-out" class="w-4 h-4"></i> Logout
            </a>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto space-y-8">
        
        <div class="bg-[#1a1d2b] border border-slate-800 rounded-3xl p-8 relative overflow-hidden">
            <div class="relative z-10 flex flex-col items-center text-center">
                <span class="text-orange-500 text-xs font-bold uppercase tracking-[0.2em] mb-4">Active Session</span>
                <div class="flex items-center gap-2 mb-2">
                    <i data-lucide="flame" class="text-orange-500 w-5 h-5 fill-orange-500"></i>
                    <h2 class="text-2xl font-bold"><?php echo $greeting . ", " . htmlspecialchars($username); ?></h2>
                </div>
                <p class="text-slate-400 text-sm mb-4"><?php echo $today_date; ?></p>
                <p class="text-slate-500 italic text-sm max-w-lg mb-6">"<?php echo $daily_quote; ?>"</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full max-w-3xl">
                    <div class="bg-[#13151f] p-6 rounded-2xl border border-slate-800">
                        <span class="text-slate-500 text-xs uppercase tracking-wider block mb-1">Days Remaining</span>
                        <span class="text-3xl font-bold text-white"><?php echo $days_left; ?></span>
                    </div>
                    <div class="bg-[#13151f] p-6 rounded-2xl border border-slate-800">
                        <span class="text-slate-500 text-xs uppercase tracking-wider block mb-1">Total Visits</span>
                        <span class="text-3xl font-bold text-white"><?php echo $total_visits; ?></span>
                    </div>
                    <div class="bg-[#13151f] p-6 rounded-2xl border border-slate-800">
                        <span class="text-slate-500 text-xs uppercase tracking-wider block mb-1">Workouts</span>
                        <span class="text-3xl font-bold text-white"><?php echo $total_workouts; ?></span>
                    </div>
                </div>
            </div>
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-64 h-64 bg-orange-500/10 blur-[100px] rounded-full pointer-events-none"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <div class="bg-[#1a1d2b] border border-slate-800 rounded-3xl p-6 flex flex-col items-center">
                <div class="flex items-center gap-2 self-start mb-6">
                    <i data-lucide="credit-card" class="text-orange-500 w-5 h-5"></i>
                    <h3 class="font-semibold">Membership</h3>
                </div>
                
                <div class="relative w-40 h-40 mb-8">
                    <svg class="w-full h-full transform -rotate-90">
                        <circle cx="80" cy="80" r="70" stroke="currentColor" stroke-width="8" fill="transparent" class="text-slate-800" />
                        <circle cx="80" cy="80" r="70" stroke="currentColor" stroke-width="8" 
                                stroke-dasharray="440" 
                                stroke-dashoffset="<?php echo 440 * (1 - ($percent/100)); ?>" 
                                stroke-linecap="round" fill="transparent" class="text-orange-500 transition-all duration-1000" />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-2xl font-bold"><?php echo $percent; ?>%</span>
                        <span class="text-[10px] text-slate-500 uppercase tracking-tighter">Plan Used</span>
                    </div>
                </div>

                <div class="w-full space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Plan Name</span>
                        <span class="font-medium"><?php echo $plan ? $plan['plan_name'] : 'N/A'; ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Start Date</span>
                        <span class="font-medium"><?php echo $start_date; ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Expiry Date</span>
                        <span class="font-medium text-orange-400"><?php echo $plan ? $plan['end_date'] : '--'; ?></span>
                    </div>
                </div>
            </div>

            <div class="bg-[#1a1d2b] border border-slate-800 rounded-3xl p-6 flex flex-col">
                <div class="flex items-center gap-2 mb-6">
                    <i data-lucide="calendar" class="text-orange-500 w-5 h-5"></i>
                    <h3 class="font-semibold">Attendance</h3>
                </div>
                 <div class="flex-1 flex flex-col items-center space-y-6">

    <!-- Attendance Status Icon -->
    <div class="p-4 rounded-full <?php echo $isMarked ? 'bg-green-500/20 text-green-400' : 'bg-orange-500/10 text-orange-500 animate-pulse'; ?>">
        <i data-lucide="<?php echo $isMarked ? 'check-circle-2' : 'user'; ?>" class="w-12 h-12"></i>
    </div>

    <!-- Attendance Button -->
    <form method="POST" class="w-full">
        <button type="submit" name="mark_attendance"
            <?php if($isMarked) echo 'disabled'; ?>
            class="px-6 py-3 rounded-xl font-bold transition-all w-full
            <?php echo $isMarked ? 'bg-slate-800 text-slate-500 cursor-not-allowed' : 'bg-gradient-to-r from-orange-600 to-orange-400 text-white shadow-lg shadow-orange-500/20'; ?>">
            <?php echo $isMarked ? "Attendance Marked" : "Mark Today's Attendance"; ?>
        </button>
    </form>

<?php if($streak > 0): ?>
<div class="w-full bg-orange-500/10 border border-orange-500/30 rounded-xl p-3 text-center">
    <p class="text-orange-400 font-semibold">
        🔥 <?php echo $streak; ?> Day Streak
    </p>
</div>
<?php endif; ?>


    <!-- Scrollable Attendance History -->
    <?php if($attendance_history && mysqli_num_rows($attendance_history) > 0): ?>
    <div class="w-full bg-[#13151f] border border-slate-800 rounded-2xl p-3 max-h-40 overflow-y-auto custom-scrollbar">
        <p class="text-xs text-slate-500 mb-2 uppercase tracking-wider">
            Attendance History
        </p>

        <?php while($a = mysqli_fetch_assoc($attendance_history)): ?>
            <div class="flex justify-between text-sm py-1 border-b border-slate-800 last:border-0">
                <span class="text-slate-300"><?php echo $a['date']; ?></span>
                <span class="text-orange-400">
                    <?php echo date("h:i A", strtotime($a['time'])); ?>
                </span>
            </div>
        <?php endwhile; ?>

    </div>
    <?php endif; ?>

</div>

            </div>

            <div class="bg-[#1a1d2b] border border-slate-800 rounded-3xl p-6 flex flex-col">
                <div class="flex items-center gap-2 mb-6">
                    <i data-lucide="history" class="text-orange-500 w-5 h-5"></i>
                    <h3 class="font-semibold">Recent Activity</h3>
                </div>
                <div class="space-y-4 flex-1 overflow-y-auto max-h-[250px] pr-2 custom-scrollbar">
                    <?php while($w = mysqli_fetch_assoc($recent_workouts)){ ?>
                    <div class="bg-[#13151f] p-4 rounded-2xl border border-slate-800 group hover:border-orange-500/30 transition-colors">
                        <div class="flex justify-between items-start mb-1">
                            <span class="font-bold text-white"><?php echo $w['exercise']; ?></span>
                            <span class="text-[10px] text-slate-500 bg-slate-900 px-2 py-0.5 rounded-full"><?php echo $w['workout_date']; ?></span>
                        </div>
                        <p class="text-xs text-slate-400">
                            <?php echo $w['sets']; ?> sets × <?php echo $w['reps']; ?> reps | <span class="text-orange-400"><?php echo $w['weight']; ?> kg</span>
                        </p>
                    </div>
                    <?php } ?>
                </div>
            </div>

            <div class="bg-[#1a1d2b] border border-orange-500/20 rounded-3xl p-6 flex flex-col shadow-xl shadow-orange-500/5">
                <div class="flex items-center gap-2 mb-6">
                    <i data-lucide="plus" class="text-orange-500 w-5 h-5"></i>
                    <h3 class="font-semibold">Log Workout</h3>
                </div>
                <form method="POST" class="space-y-4">
                    <input type="text" name="exercise" placeholder="e.g. Deadlift" required class="w-full bg-[#13151f] border border-slate-800 rounded-xl px-4 py-3 text-sm focus:border-orange-500 outline-none transition-colors">
                    <div class="grid grid-cols-2 gap-4">
                        <input type="number" name="sets" placeholder="Sets" required class="w-full bg-[#13151f] border border-slate-800 rounded-xl px-4 py-3 text-sm focus:border-orange-500 outline-none">
                        <input type="number" name="reps" placeholder="Reps" required class="w-full bg-[#13151f] border border-slate-800 rounded-xl px-4 py-3 text-sm focus:border-orange-500 outline-none">
                    </div>
                    <input type="number" step="0.1" name="weight" placeholder="Weight (kg)" required class="w-full bg-[#13151f] border border-slate-800 rounded-xl px-4 py-3 text-sm focus:border-orange-500 outline-none">
                    <button type="submit" name="save_workout" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-4 rounded-2xl shadow-lg shadow-orange-500/20 transition-all mt-4 transform active:scale-95">
                        Save Workout
                    </button>
                </form>
            </div>

        </div>
    </main>

    <script>lucide.createIcons();</script>
</body>
</html>