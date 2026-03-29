<?php
session_start();
include 'db.php';

// 1. Security Check: Only admins can assign plans
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$msg = "";
$msg_type = "";

// 2. Process Plan Assignment
if (isset($_POST['assign'])) {
    $user_id    = mysqli_real_escape_string($conn, $_POST['user_id']);
    $plan_name  = mysqli_real_escape_string($conn, $_POST['plan_name']);
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end_date   = mysqli_real_escape_string($conn, $_POST['end_date']);

    $insert = "INSERT INTO user_plans (user_id, plan_name, start_date, end_date)
               VALUES ('$user_id', '$plan_name', '$start_date', '$end_date')";

    if (mysqli_query($conn, $insert)) {
        $msg = "Plan assigned successfully!";
        $msg_type = "success";
    } else {
        $msg = "Error assigning plan: " . mysqli_error($conn);
        $msg_type = "error";
    }
}

// 3. Fetch Users for the dropdown
$users = mysqli_query($conn, "SELECT id, username FROM users WHERE role='user'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Plan | Fitzone Gym</title>
    
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

        input, select { 
            background: rgba(255,255,255,0.03) !important; 
            border: 1px solid rgba(255,255,255,0.1) !important;
            color: white !important;
        }

        input:focus, select:focus { 
            border-color: var(--accent-orange) !important; 
            outline: none;
            box-shadow: 0 0 0 2px rgba(255, 125, 42, 0.2);
        }

        select option { 
            background: #1c1f2e; 
            color: white; 
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-2xl">
        <a href="admin_dashboard.php" class="inline-flex items-center gap-2 text-gray-400 hover:text-white mb-6 transition-colors">
            <i class="fas fa-arrow-left text-sm"></i> Back to Dashboard
        </a>

        <div class="form-card p-8 md:p-12 shadow-2xl">
            <div class="mb-10 text-center md:text-left">
                <div class="w-14 h-14 bg-teal-500/10 text-teal-400 rounded-2xl flex items-center justify-center text-2xl mb-4 mx-auto md:mx-0">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <h2 class="text-3xl font-black tracking-tight">Assign Membership Plan</h2>
                <p class="text-gray-400 mt-2">Connect a user with a specific gym membership tier.</p>
                
                <?php if($msg): ?>
                    <div class="mt-4 p-4 rounded-xl text-sm font-medium <?php echo $msg_type == 'success' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20'; ?>">
                        <i class="<?php echo $msg_type == 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'; ?> mr-2"></i>
                        <?php echo $msg; ?>
                    </div>
                <?php endif; ?>
            </div>

            <form method="POST" id="assignForm" class="space-y-6">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Select Member</label>
                    <select name="user_id" required class="w-full px-4 py-3 rounded-xl cursor-pointer transition-all">
                        <option value="">-- Choose a user --</option>
                        <?php 
                        mysqli_data_seek($users, 0);
                        while($u = mysqli_fetch_assoc($users)): 
                        ?>
                            <option value="<?php echo $u['id']; ?>">
                                <?php echo htmlspecialchars($u['username']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Plan Name</label>
                    <select name="plan_name" id="plan_name" required class="w-full px-4 py-3 rounded-xl cursor-pointer transition-all">
                        <option value="">-- Select a Plan --</option>
                        <option value="1 Month">1 Month Plan</option>
                        <option value="3 Months">3 Months Plan</option>
                        <option value="6 Months">6 Months Plan</option>
                        <option value="12 Months">12 Months Plan</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Start Date</label>
                        <input type="date" name="start_date" id="start_date" required 
                               value="<?php echo date('Y-m-d'); ?>"
                               class="w-full px-4 py-3 rounded-xl transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">End Date</label>
                        <input type="date" name="end_date" id="end_date" required 
                               class="w-full px-4 py-3 rounded-xl transition-all bg-white/5">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" name="assign" 
                            class="w-full bg-[#ff7d2a] hover:bg-[#e66a1f] text-white font-bold py-4 rounded-xl transition-all transform hover:scale-[1.01] active:scale-[0.99] shadow-lg shadow-orange-500/20">
                        <i class="fas fa-plus-circle mr-2"></i> Confirm Assignment
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const planSelect = document.getElementById('plan_name');
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        /**
         * Automatically calculates the end date based on selected months
         */
        function updateEndDate() {
            const startDateValue = startDateInput.value;
            const planValue = planSelect.value;

            if (startDateValue && planValue) {
                const startDate = new Date(startDateValue);
                const monthsToAdd = parseInt(planValue);
                
                if (!isNaN(monthsToAdd)) {
                    const endDate = new Date(startDate);
                    endDate.setMonth(startDate.getMonth() + monthsToAdd);
                    
                    const yyyy = endDate.getFullYear();
                    const mm = String(endDate.getMonth() + 1).padStart(2, '0');
                    const dd = String(endDate.getDate()).padStart(2, '0');
                    
                    endDateInput.value = `${yyyy}-${mm}-${dd}`;
                }
            }
        }

        planSelect.addEventListener('change', updateEndDate);
        startDateInput.addEventListener('change', updateEndDate);
    </script>

</body>
</html>