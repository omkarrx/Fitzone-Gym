<?php
session_start();

/* --- Auth Check --- */
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

include 'db.php';

/* --- Action Logic: Delete Member --- */
if (isset($_GET['delete_id'])) {
    $id_to_delete = $_GET['delete_id'];
    
    $stmt = $conn->prepare("DELETE FROM members WHERE id = ?");
    $stmt->bind_param("i", $id_to_delete);
    
    if ($stmt->execute()) {
        header("Location: members.php?msg=Member Removed Successfully");
        exit;
    }
}

/* --- Fetch Data --- */
$result = mysqli_query($conn, "SELECT * FROM members");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members Management | Fitzone Gym</title>
    
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

        /* --- Table Components --- */
        .table-container { 
            background-color: var(--card-navy); 
            border: 1px solid rgba(255,255,255,0.05); 
            border-radius: 1.25rem; 
            overflow: hidden; 
        }

        th { 
            background-color: rgba(255,255,255,0.02); 
            color: #9ca3af; 
            text-transform: uppercase; 
            font-size: 0.75rem; 
            font-weight: 700; 
            letter-spacing: 0.05em; 
        }

        td { border-bottom: 1px solid rgba(255,255,255,0.05); }
        tr:hover td { background-color: rgba(255,255,255,0.01); }

        /* --- Actions --- */
        .btn-delete { 
            color: #ff4d4d; 
            transition: all 0.2s; 
            padding: 8px; 
            border-radius: 8px; 
            display: inline-block; 
        }
        .btn-delete:hover { 
            background: rgba(255, 77, 77, 0.1); 
            transform: scale(1.1); 
        }
    </style>
</head>

<body class="p-4 md:p-8">

    <div class="max-w-7xl mx-auto">
        
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-extrabold tracking-tight">Members Management</h2>
                <?php if (isset($_GET['msg'])): ?>
                    <p class='text-green-400 text-xs mt-1 font-bold'>✓ <?php echo htmlspecialchars($_GET['msg']); ?></p>
                <?php endif; ?>
            </div>
            <a href="admin_dashboard.php" class="bg-gray-800 hover:bg-gray-700 text-white px-5 py-2.5 rounded-xl text-sm transition-all flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <div class="table-container shadow-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr>
                            <th class="px-6 py-5">Name</th>
                            <th class="px-6 py-5">Phone</th>
                            <th class="px-6 py-5">Plan</th>
                            <th class="px-6 py-5 text-blue-400">Join Date</th>
                            <th class="px-6 py-5 text-orange-400">Expiry Date</th>
                            <th class="px-6 py-5 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td class="px-6 py-4 font-semibold"><?php echo htmlspecialchars($row['name']); ?></td>
                                <td class="px-6 py-4 text-gray-400"><?php echo htmlspecialchars($row['phone']); ?></td>
                                <td class="px-6 py-4">
                                    <span class="bg-orange-500/10 text-[#ff7d2a] px-3 py-1 rounded-full text-xs font-bold border border-orange-500/20">
                                        <?php echo htmlspecialchars($row['plan']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-400 text-sm">
                                    <?php echo htmlspecialchars($row['join_date']); ?>
                                </td>
                                <td class="px-6 py-4 text-gray-300 font-medium">
                                    <?php
                                        $months = (int) filter_var($row['plan'], FILTER_SANITIZE_NUMBER_INT);
                                        if ($months <= 0) $months = 1;
                                        echo date('Y-m-d', strtotime($row['join_date'] . " + $months months"));
                                    ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="members.php?delete_id=<?php echo $row['id']; ?>" 
                                       class="btn-delete" 
                                       onclick="return confirm('Permanently delete <?php echo addslashes($row['name']); ?>?');">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>