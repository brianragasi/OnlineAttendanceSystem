<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'employee') {
    header("Location: login.php");
    exit();
}

$employee_id = $_SESSION['user_id'];

// Fetch attendance records
$sql = "SELECT * FROM attendance WHERE employee_id=$employee_id ORDER BY check_in DESC";
$result = $conn->query($sql);

// Calculate statistics
$total_hours = 0;
$attendance_streak = 0;
$last_date = null;

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        if ($row['check_out']) {
            $total_hours += (strtotime($row['check_out']) - strtotime($row['check_in'])) / 3600;
        }

        $current_date = date('Y-m-d', strtotime($row['check_in']));
        if ($last_date === null || $current_date === date('Y-m-d', strtotime($last_date . ' -1 day'))) {
            $attendance_streak++;
        } else {
            break;
        }
        $last_date = $current_date;
    }
}

$result->data_seek(0); // Reset result pointer

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];

    if ($action == 'check_in') {
        $check_in_time = date('Y-m-d h:i:s A', strtotime('+8 hours'));
        $sql = "INSERT INTO attendance (employee_id, check_in) VALUES ($employee_id, '$check_in_time')";
        if ($conn->query($sql) === TRUE) {
            echo "Check-in successful at " . $check_in_time;
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } elseif ($action == 'check_out') {
        $sql = "SELECT id FROM attendance WHERE employee_id=$employee_id AND check_out IS NULL ORDER BY check_in DESC LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $attendance_id = $row['id'];
            $check_out_time = date('Y-m-d h:i:s A', strtotime('+8 hours'));
            $sql = "UPDATE attendance SET check_out='$check_out_time' WHERE id=$attendance_id";
            if ($conn->query($sql) === TRUE) {
                echo "Check-out successful at " . $check_out_time;
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "No active check-in found.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { transition: transform 0.3s; }
        .card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">TimeTrack Pro</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="text-center mb-4">Welcome, <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User'; ?>!</h2>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Total Hours</h5>
                        <p class="card-text display-4"><?php echo number_format($total_hours, 1); ?></p>
                        <i class="fas fa-clock fa-3x text-primary"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Attendance Streak</h5>
                        <p class="card-text display-4"><?php echo $attendance_streak; ?> days</p>
                        <i class="fas fa-fire fa-3x text-danger"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Check In/Out</h5>
                        <div class="d-flex justify-content-around">
                            <form method="post" action="">
                                <input type="hidden" name="action" value="check_in">
                                <button type="submit" class="btn btn-success"><i class="fas fa-sign-in-alt"></i> Check In</button>
                            </form>
                            <form method="post" action="">
                                <input type="hidden" name="action" value="check_out">
                                <button type="submit" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Check Out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Attendance Records</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Duration</th>
                            </tr>
                        </thead>
                        <tbody>
<?php
// Fetch attendance records again to ensure the latest data is displayed
$sql = "SELECT * FROM attendance WHERE employee_id=$employee_id ORDER BY check_in DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . date('Y-m-d', strtotime($row['check_in'])) . "</td>";
        echo "<td>" . date('h:i:s A', strtotime($row['check_in'])) . "</td>";
        echo "<td>" . ($row['check_out'] ? date('h:i:s A', strtotime($row['check_out'])) : 'Not Checked Out') . "</td>";
        if ($row['check_out']) {
            $duration = (strtotime($row['check_out']) - strtotime($row['check_in'])) / 3600;
            echo "<td>" . number_format($duration, 2) . " hours</td>";
        } else {
            echo "<td>-</td>";
        }
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4' class='text-center'>No attendance records found.</td></tr>";
}
?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
