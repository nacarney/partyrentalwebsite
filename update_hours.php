<?php
    // display errors
    session_start();
    include("group_detail.php");
    ini_set('display_errors', 1);
    ini_set('log_errors',1);
    error_reporting(E_ALL);
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    // updates employee hours
    if(count($_POST)>0) {
        mysqli_query($db,"UPDATE employee_hours SET clock_in_time = '" . $_POST['clock_in_time'] . "',
        clock_out_time = '" . $_POST['clock_out_time'] . "' WHERE employee_ID = '" . $_POST['employee_ID'] . "' AND date =  '" . $_POST['date'] . "' ");
        $message = "Hours Modified Successfully";
    }
    // selects data to prefill form
    $result = mysqli_query($db,"SELECT * FROM employee_hours WHERE employee_ID = '" . $_GET['id'] . "' AND date = '" . $_GET['date'] . "' ");
    $row= mysqli_fetch_array($result);
    $name_query = "SELECT name from employee WHERE employee_ID =  '" . $_GET['id'] . "' ";
    $name_result = $db->query($name_query);
    $name = mysqli_fetch_assoc($name_result);

    // Time Format Notation - https://www.mysqltutorial.org/mysql-time/
    $query = "SELECT TIME_FORMAT(clock_in_time, '%H:%i') clock_in_time, TIME_FORMAT(clock_out_time, '%H:%i') clock_out_time ";
    $query .= "FROM employee_hours WHERE employee_ID = '" . $row['employee_ID'] . "'";
    $query .= " AND date = '" . $row['date'] . "' ";
    $clock_result = $db->query($query); // runs the query
    $value = mysqli_fetch_assoc($clock_result);

   // deletes an entry if the manager clicks the remove button
    if(isset($_POST['remove'])) {
        mysqli_query($db,"DELETE FROM employee_hours WHERE employee_ID ='" . $_POST['employee_ID'] . "' AND date = '" .$_POST['date'] . "'
         AND clock_in_time = '" .$row['clock_in_time'] . "'");
        $message = "Hours Deleted Successfully";
    }

?>
<html>
    <head>
        <title>Update Employee Hours</title>
        <link rel="stylesheet" href="style2.css">
    </head>
    <body>
        <div class="topnav">
            <a>Dublin Party Hire</a>
            <a href="homepage.php">Homepage</a>
            <?php
                // relocates a user if they aren't a manager
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
                        header('Location: homepage.php');
                    }
                }else{
                    header('Location: homepage.php');
                }
            ?>
        </div>
        <br><br><br>
        <form name="frmUser" method="post" action="" class="styled-form">
            <table class="styled-table">
                <div>
                    <?php if(isset($message)) { echo $message; } ?>
                </div>
                <tr>
                    <?php
                        echo '<td>Employee ID: '. $row['employee_ID'] . '</td>';
                    ?>
                    <td><input type="hidden" name="employee_ID" class="txtField" value="<?php echo $row['employee_ID']; ?>"></td>
                </tr>
                <tr>
                <?php
                        echo '<td>Employee Name: '. $name['name'] . '</td>';
                    ?>
                    <td>
                        <input type="hidden" name="employee_name" class="txtField" value="<?php echo $name['name']; ?>">
                    </td>
                </tr>
                <tr>
                <?php
                        echo '<td>Date: '. $row['date'] . '</td>';
                    ?>
                    <td>

                    <input type="hidden" name="date" class="txtField" value="<?php echo $_GET['date']; ?>">

                    </td>
                </tr>
                <tr>
                    <td>Clock In Time:</td>
                    <td>
                        <input type="time" name="clock_in_time" value="<?php echo $value['clock_in_time']; ?>">
                    </td>
                </tr>
                <tr>
                <td>Clock Out Time:</td>
                    <td>
                        <input type="time" name="clock_out_time" value="<?php echo $value['clock_out_time']; ?>">
                    </td>
                </tr>
                <tr>
                    <td><input type="submit" name="submit" value="Submit" class="button"></td>
                </tr>
                <tr>
                    <td><input type="submit" name="remove" value="Remove" class="button"></td>
                </tr>
            </table>
        </form>
    </body>
</html>
