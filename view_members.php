 <?php
session_start();

//Admin protection
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: user_dashboard.php");
    exit;
}

include 'db.php';

$result = mysqli_query($conn,"SELECT * FROM members");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Members List</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>

<h2>Members List</h2>

<table border="1" cellpadding="10">
<tr>
<th>ID</th>
<th>Name</th>
<th>Age</th>
<th>Phone</th>
<th>Plan</th>
<th>Join Date</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>

<tr>
<td><?php echo $row['id']; ?></td>
<td><?php echo $row['name']; ?></td>
<td><?php echo $row['age']; ?></td>
<td><?php echo $row['phone']; ?></td>
<td><?php echo $row['plan']; ?></td>
<td><?php echo $row['join_date']; ?></td>
</tr>

<?php } ?>

</table>

<br>
<a href="admin_dashboard.php">Back to Dashboard</a>

</body>
</html>
