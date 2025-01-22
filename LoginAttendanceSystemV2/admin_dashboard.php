<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all employees
$sql = "SELECT * FROM employees";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <script>
        function autocomplete(inp, arr) {
            var currentFocus;
            inp.addEventListener("input", function(e) {
                var a, b, i, val = this.value;
                closeAllLists();
                if (!val) { return false;}
                currentFocus = -1;
                a = document.createElement("DIV");
                a.setAttribute("id", this.id + "autocomplete-list");
                a.setAttribute("class", "autocomplete-items");
                this.parentNode.appendChild(a);
                for (i = 0; i < arr.length; i++) {
                    if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                        b = document.createElement("DIV");
                        b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                        b.innerHTML += arr[i].substr(val.length);
                        b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                        b.addEventListener("click", function(e) {
                            inp.value = this.getElementsByTagName("input")[0].value;
                            closeAllLists();
                        });
                        a.appendChild(b);
                    }
                }
            });
            inp.addEventListener("keydown", function(e) {
                var x = document.getElementById(this.id + "autocomplete-list");
                if (x) x = x.getElementsByTagName("div");
                if (e.keyCode == 40) {
                    currentFocus++;
                    addActive(x);
                } else if (e.keyCode == 38) {
                    currentFocus--;
                    addActive(x);
                } else if (e.keyCode == 13) {
                    e.preventDefault();
                    if (currentFocus > -1) {
                        if (x) x[currentFocus].click();
                    }
                }
            });
            function addActive(x) {
                if (!x) return false;
                removeActive(x);
                if (currentFocus >= x.length) currentFocus = 0;
                if (currentFocus < 0) currentFocus = (x.length - 1);
                x[currentFocus].classList.add("autocomplete-active");
            }
            function removeActive(x) {
                for (var i = 0; i < x.length; i++) {
                    x[i].classList.remove("autocomplete-active");
                }
            }
            function closeAllLists(elmnt) {
                var x = document.getElementsByClassName("autocomplete-items");
                for (var i = 0; i < x.length; i++) {
                    if (elmnt != x[i] && elmnt != inp) {
                        x[i].parentNode.removeChild(x[i]);
                    }
                }
            }
            document.addEventListener("click", function (e) {
                closeAllLists(e.target);
            });
        }

        window.onload = function() {
            var employees = <?php
                $names = array();
                while($row = $result->fetch_assoc()) {
                    $names[] = $row['name'];
                }
                echo json_encode($names);
            ?>;
            autocomplete(document.getElementById("employee_name"), employees);
        };
    </script>
    <style>
        .autocomplete {
            position: relative;
            display: inline-block;
        }
        .autocomplete-items {
            position: absolute;
            border: 1px solid #d4d4d4;
            border-bottom: none;
            border-top: none;
            z-index: 99;
            top: 100%;
            left: 0;
            right: 0;
        }
        .autocomplete-items div {
            padding: 10px;
            cursor: pointer;
            background-color: #fff;
            border-bottom: 1px solid #d4d4d4;
        }
        .autocomplete-items div:hover,
        .autocomplete-active {
            background-color: #e9e9e9;
        }
        .autocomplete-active {
            background-color: DodgerBlue !important;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <h2>Admin Dashboard</h2>
    <h3>Employee Management</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Hourly Rate</th>
            <th>Role</th>
        </tr>
        <?php
        $result->data_seek(0); // Reset the result pointer
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                if ($row['role'] != 'admin') {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['name'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>" . $row['hourly_rate'] . "</td>";
                    echo "<td>" . $row['role'] . "</td>";
                    echo "</tr>";
                }
            }
        } else {
            echo "<tr><td colspan='5'>No employees found.</td></tr>";
        }
        ?>
    </table>

    <h3>View Attendance</h3>
    <form method="get" action="attendance.php">
        <div class="autocomplete">
            <label for="employee_name">Employee Name:</label>
            <input type="text" id="employee_name" name="employee_name" required>
        </div><br><br>
        <input type="submit" value="View Attendance">
    </form>

    <h3>Generate Payroll</h3>
    <form method="post" action="payroll.php">
        <div class="autocomplete">
            <label for="employee_name_payroll">Employee Name:</label>
            <input type="text" id="employee_name_payroll" name="employee_name" required>
        </div><br><br>
        <label for="pay_period_start">Pay Period Start:</label>
        <input type="date" id="pay_period_start" name="pay_period_start" required><br><br>
        <label for="pay_period_end">Pay Period End:</label>
        <input type="date" id="pay_period_end" name="pay_period_end" required><br><br>
        <input type="submit" value="Generate Payroll">
    </form>

    <script>
        window.onload = function() {
            var employees = <?php
                $names = array();
                $result->data_seek(0); // Reset the result pointer
                while($row = $result->fetch_assoc()) {
                    $names[] = $row['name'];
                }
                echo json_encode($names);
            ?>;
            autocomplete(document.getElementById("employee_name"), employees);
            autocomplete(document.getElementById("employee_name_payroll"), employees);
        };
    </script>
</body>
</html>
