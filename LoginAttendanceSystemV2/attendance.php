<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['employee_name'])) {
    $employee_name = $_GET['employee_name'];
    $sql = "SELECT id FROM employees WHERE name='$employee_name'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $employee_id = $row['id'];
    } else {
        echo "Employee not found.";
        exit();
    }
$sql = "SELECT * FROM attendance WHERE employee_id=$employee_id ORDER BY check_in DESC";
    $result = $conn->query($sql);
} else {
    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Attendance</title>
</head>
<body>
    <h2>Attendance Records</h2>
    <table border="1">
        <tr>
            <th>Check In</th>
            <th>Check Out</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . date('Y-m-d h:i:s A', strtotime($row['check_in'])) . "</td>";
                echo "<td>" . ($row['check_out'] ? date('Y-m-d h:i:s A', strtotime($row['check_out'])) : 'Not Checked Out') . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='2'>No attendance records found.</td></tr>";
        }
        ?>
    </table>
</body>
</html>
