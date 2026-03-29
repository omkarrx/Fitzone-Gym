<?php
session_start();
include 'db.php';

// 1. Security Check: Only logged-in users can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";

// 2. Process Workout Submission
if (isset($_POST['save'])) {
    // Sanitize input data
    $exercise = mysqli_real_escape_string($conn, $_POST['exercise']);
    $sets     = (int)$_POST['sets'];
    $reps     = (int)$_POST['reps'];
    $weight   = mysqli_real_escape_string($conn, $_POST['weight']);
    $date     = mysqli_real_escape_string($conn, $_POST['workout_date']);

    $insert_query = "INSERT INTO workouts (user_id, exercise, sets, reps, weight, workout_date)
                     VALUES ('$user_id', '$exercise', '$sets', '$reps', '$weight', '$date')";

    if (mysqli_query($conn, $insert_query)) {
        $msg = "Workout added successfully!";
    } else {
        $msg = "Error saving workout: " . mysqli_error($conn);
    }
}

// 3. Fetch Recent Workouts for the logged-in user
$workout_list = mysqli_query($conn, "SELECT * FROM workouts 
                                     WHERE user_id = '$user_id' 
                                     ORDER BY id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Workout | Fitzone Gym</title>
</head>
<body>

    <h2>Add Workout</h2>

    <?php if ($msg): ?>
        <p><strong><?php echo $msg; ?></strong></p>
    <?php endif; ?>

    <form method="POST">
        <label>Exercise:</label><br>
        <input type="text" name="exercise" required><br><br>

        <label>Sets:</label><br>
        <input type="number" name="sets" required><br><br>

        <label>Reps:</label><br>
        <input type="number" name="reps" required><br><br>

        <label>Weight (kg):</label><br>
        <input type="number" step="0.1" name="weight" required><br><br>

        <label>Date:</label><br>
        <input type="date" name="workout_date" required><br><br>

        <button type="submit" name="save">Save Workout</button>
    </form>

    <hr>

    <h3>Recent Workouts</h3>

    <?php if (mysqli_num_rows($workout_list) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($workout_list)): ?>
            <p>
                <?php 
                    echo "{$row['exercise']} | {$row['sets']} sets | ";
                    echo "{$row['reps']} reps | {$row['weight']} kg | ";
                    echo "{$row['workout_date']}"; 
                ?>
            </p>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No workouts logged yet.</p>
    <?php endif; ?>

    <br>
    <a href="user_dashboard.php">← Back to Dashboard</a>

</body>
</html>