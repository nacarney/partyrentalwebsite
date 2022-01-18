<?php

session_start();
include("group_detail.php");

    ini_set('display_errors', 1);
    ini_set('log_errors',1);
    error_reporting(E_ALL);
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    // chooses entries from employee hours where employees forgot to clock in or out
    $result = mysqli_query($db,"SELECT employee_hours.employee_ID, name, date, clock_in_time, clock_out_time
    FROM employee_hours, employee WHERE clock_out_time IS NULL AND date < DATE_FORMAT(CURDATE(), '%Y/%m/%d')
    AND employee_hours.employee_ID = employee.employee_ID");

?>

<!DOCTYPE html>
<html>
    <head>
        <title> Edit Products</title>
        <link rel="stylesheet" href="style2.css">
    </head>
    <body>
        <div class="topnav">
            <a>Dublin Party Hire</a>
            <a href="homepage.php"> Hours</a>
            <?php
                // relocates anyone who isn't a manager to homepage
                if (isset($_SESSION['user_type'])){
                    if ($_SESSION['user_type'] == 'Manager'){
                        echo '<a href="employee_registration.php">Employee Registration</a>';
                        echo '<a href="edit_items.php">Edit Products</a>';
                        echo '<a href="edit_employees.php">Edit Employee Info</a>';
                        echo '<a class="active" href="edit_hours.php">Edit Shifts</a>';
                        echo '<a href="reports.php">Reports</a>';
                        echo '<a href="breakage_form.php">Report Breakage</a>';
                        echo '<a href="login.php">Log Out</a>';
                    } else {
                        header('Location: login.php');
                    }
                }else{
                    header('Location: login.php');
                }
            ?>
        </div>
        <br><br><br>
        <?php
            // if there are entries this table is shown
            if (mysqli_num_rows($result) > 0) {
        ?>
        <table class="styled-table">
            <caption class="styled-caption"><p>Edit items</p></caption>
            <tr>
                <!--This button will allow the manager to add a new shift-->
                <td></td><td colspan="2"><strong><a href="add_shift.php"><br>Click Here to Add a New Shift<br><br></a></strong></td>
            </tr>
            <tr>
                <td>Employee ID</td>
                <td>Name</td>
                <td>Date</td>
                <td>Clock-In Time</td>
                <td>Clock-Out Time</td>
            </tr>
                <?php
                    $i=0;
                    while($row = mysqli_fetch_array($result)) {
                ?>
            <tr>
                <td><?php echo $row["employee_ID"]; ?></td>
                <td><?php echo $row["name"]; ?></td>
                <td><?php echo $row["date"]; ?></td>
                <td><?php echo $row["clock_in_time"]; ?></td>
                <td><?php echo $row["clock_out_time"]; ?></td>
                <td><a href="update_hours.php?id=<?php echo $row["employee_ID"]; ?>&date=<?php echo $row['date']; ?>">Edit</a></td>
            </tr>
                <?php
                    $i++;
                    }
                ?>
        </table>
        <?php
            }
            else
            {
                // display when the table is empty
                echo '<table class="styled-table">';
                    echo '<caption class="styled-caption"><p>Edit items</p></caption>';
                    echo '<tr>';
                        echo '<td colspan="2"><strong><a href="add_shift.php"><br>Click Here to Add a New Shift for an Employee<br><br></a></strong></td>';
                    echo '</tr>';
                    echo '<tr>';
                        echo '<td>No Employees have clocked in without clocking out</td>';
                    echo '</tr>';
                echo '</table>';
            }
        ?>
    </body>
</html>
