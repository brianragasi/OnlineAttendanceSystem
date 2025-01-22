<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = $_POST['employee_id'];
    $pay_period_start = $_POST['pay_period_start'];
    $pay_period_end = $_POST['pay_period_end'];

    // Calculate total hours worked
    $sql = "SELECT SUM(TIMESTAMPDIFF(HOUR, check_in, check_out)) AS total_hours
            FROM attendance
            WHERE employee_id=$employee_id
            AND check_in >= '$pay_period_start'
            AND check_out <= '$pay_period_end'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $total_hours = $row['total_hours'];

    // Get hourly rate
    $sql = "SELECT hourly_rate FROM employees WHERE id=$employee_id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $hourly_rate = $row['hourly_rate'];

    // Calculate gross pay
    $gross_pay = $total_hours * $hourly_rate;

    // Apply tax (20% deduction)
    $net_pay = $gross_pay * 0.8;

    // Insert payroll record
    $sql = "INSERT INTO payroll (employee_id, pay_period_start, pay_period_end, hours_worked, gross_pay, net_pay)
            VALUES ($employee_id, '$pay_period_start', '$pay_period_end', $total_hours, $gross_pay, $net_pay)";
    if ($conn->query($sql) === TRUE) {
        echo "Payroll generated successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payroll Calculation</title>
</head>
<body>
    <h2>Payroll Calculation</h2>
    <form method="post" action="">
        <label for="employee_id">Employee ID:</label>
        <input type="number" id="employee_id" name="employee_id" required><br><br>
        <label for="pay_period_start">Pay Period Start:</label>
        <input type="date" id="pay_period_start" name="pay_period_start" required><br><br>
        <label for="pay_period_end">Pay Period End:</label>
        <input type="date" id="pay_period_end" name="pay_period_end" required><br><br>
        <input type="submit" value="Generate Payroll">
    </form>
</body>
</html>
