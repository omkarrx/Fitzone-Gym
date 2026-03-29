<?php
session_start();
include 'db.php';

// 1. Access Control: Only admins can add members
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$message = "";

// 2. Process Form Submission
if (isset($_POST['submit'])) {
    // Sanitize and collect input data
    $name        = mysqli_real_escape_string($conn, $_POST['name']);
    $age         = (int)$_POST['age'];
    $phone       = mysqli_real_escape_string($conn, $_POST['phone']);
    $plan        = mysqli_real_escape_string($conn, $_POST['plan']);
    $join_date   = $_POST['join_date'];
    $expiry_date = $_POST['expiry_date'];

    $sql = "INSERT INTO members (name, age, phone, plan, join_date, expiry_date) 
            VALUES ('$name', '$age', '$phone', '$plan', '$join_date', '$expiry_date')";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: members.php?msg=Member added successfully");
        exit;
    } else {
        $message = "Database Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Member | Fitzone Gym</title>
    
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
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-2xl">
        <a href="admin_dashboard.php" class="inline-flex items-center gap-2 text-gray-400 hover:text-white mb-6 transition-colors">
            <i class="fas fa-arrow-left text-sm"></i> Back to Dashboard
        </a>

        <div class="form-card p-8 md:p-12 shadow-2xl">
            <header class="mb-10">
                <h2 class="text-3xl font-black tracking-tight">Add New Member</h2>
                <p class="text-gray-400 mt-2">Enter client details to create a new membership.</p>
                
                <?php if($message): ?>
                    <p class="text-red-400 mt-4 text-sm font-semibold"><?php echo $message; ?></p>
                <?php endif; ?>
            </header>

            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Full Name</label>
                    <input type="text" name="name" placeholder="e.g. Rahul Sharma" required 
                           class="w-full px-4 py-3 rounded-xl transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Age</label>
                    <input type="number" name="age" placeholder="24"
                           class="w-full px-4 py-3 rounded-xl transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Phone Number</label>
                    <input type="text" name="phone" placeholder="9876543210"
                           class="w-full px-4 py-3 rounded-xl transition-all">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Membership Plan</label>
                    <select name="plan" id="plan_select" required onchange="calculateExpiry()"
                            class="w-full px-4 py-3 rounded-xl transition-all cursor-pointer">
                        <option value="1 Month">Basic - 1 Month</option>
                        <option value="3 Months">Standard - 3 Months</option>
                        <option value="6 Months">Premium - 6 Months</option>
                        <option value="12 Months">Elite - 1 Year</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Join Date</label>
                    <input type="date" name="join_date" id="join_date" required onchange="calculateExpiry()"
                           value="<?php echo date('Y-m-d'); ?>"
                           class="w-full px-4 py-3 rounded-xl transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-orange-400 uppercase tracking-wider mb-2">Expiry Date</label>
                    <input type="date" name="expiry_date" id="expiry_date" required readonly
                           class="w-full px-4 py-3 rounded-xl transition-all border-orange-500/30 bg-orange-500/5! font-bold">
                    <p class="text-[10px] text-gray-500 mt-1 italic">* Calculated automatically</p>
                </div>

                <div class="md:col-span-2 mt-4">
                    <button type="submit" name="submit" 
                            class="w-full bg-[#ff7d2a] hover:bg-[#e66a1f] text-white font-bold py-4 rounded-xl transition-all transform hover:scale-[1.02] active:scale-[0.98] shadow-lg shadow-orange-500/20">
                        <i class="fas fa-user-plus mr-2"></i> Register Member
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        /**
         * Automatically calculates the expiry date based on the 
         * selected plan and the start date.
         */
        function calculateExpiry() {
            const joinDateInput = document.getElementById('join_date');
            const planSelect = document.getElementById('plan_select');
            const expiryInput = document.getElementById('expiry_date');

            if (!joinDateInput.value) return;

            // Extract number from plan string (e.g. "6 Months" -> 6)
            const monthsToAdd = parseInt(planSelect.value) || 1;
            
            let date = new Date(joinDateInput.value);
            date.setMonth(date.getMonth() + monthsToAdd);
            
            // Format to YYYY-MM-DD
            const yyyy = date.getFullYear();
            const mm = String(date.getMonth() + 1).padStart(2, '0');
            const dd = String(date.getDate()).padStart(2, '0');
            
            expiryInput.value = `${yyyy}-${mm}-${dd}`;
        }

        // Initialize expiry date on page load
        window.onload = calculateExpiry;
    </script>
</body>
</html>