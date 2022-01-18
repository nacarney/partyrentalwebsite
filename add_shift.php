<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta content="en-ie" http-equiv="Content-Language" />
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <link rel = "stylesheet" type = "text/css" href = "style2.css"/>
        <title>Record Shift</title>

    </head>

    <body>
        <?php
            // code to display errors
            ini_set('display_errors', 1);
            ini_set('log_errors',1);
            error_reporting(E_ALL);
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            session_start();
            include ("group_detail.php");
            // initilise variables
            $dateErr = $inErr = $outErr = "";
            $id = $date = $clock_in = $clock_out = "";
            $today = new DateTime();
            // form validation
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // assigns id of the employee with chosen name
                if (!empty($_POST["name"])) {
                    $result1 = mysqli_query($db,"SELECT * FROM employee WHERE name = '". $_POST['name'] ."'");
                    $row1 = mysqli_fetch_array($result1);
                    $id = $row1['employee_ID'];
                }
                // checks that a time is entered for clock in
                if(empty($_POST['clock_in'])){
                    $inErr = "You must enter a clock in time";
                }else{
                    $clock_in = $_POST['clock_in'];
                }
                // checks that a time is entered for clock out
                if(empty($_POST['clock_out'])){
                    $outErr = "You must enter a clock in time";
                }else{
                    // checks that clock out is after clock in time
                    if($clock_in > $_POST['clock_out']){
                        $outErr = 'The clock out time must be after the clock in time';
                    }else{
                        $clock_out = $_POST['clock_out'];
                    }
                }
                // checks if date is entered
                if(empty($_POST['date'])){
                    $dateErr = "You must enter a date for the shift";
                }else{
                    $date = $_POST['date'];
                }

                if ($dateErr == "" && $inErr == "" && $outErr == "")
                {
                    // inserts form values into mysql database
                    $q  = "INSERT INTO employee_hours(";
                    $q .= "employee_ID, clock_in_time, clock_out_time, date";
                    $q .= ") VALUES (";
                    $q .= "'$id', '$clock_in', '$clock_out', '$date')";

                    $result = $db->query($q);
                    $message = 'Shift logged succesfully';
                }
            }
            // pulls existing details for the intial values in form
            $result = mysqli_query($db,"SELECT * FROM employee WHERE title = 'Employee'");
        ?>
        <div class="topnav">
        <a>Dublin Party Hire</a>
        <a href="homepage.php">Homepage</a>
        <?php
            // displays options to manager an dsends all other user types back to homepage
            if (isset($_SESSION['user_type'])){
                if ($_SESSION['user_type']== 'Manager'){
                    echo '<a href="employee_registration.php">Employee Registration</a>';
                    echo '<a href="edit_items.php">Edit Products</a>';
                    echo '<a href="edit_employees.php">Edit Employee Info</a>';
                    echo '<a class="active" href="edit_hours.php">Edit Shifts</a>';
                    echo '<a href="reports.php">Reports</a>';
                    echo '<a href="login.php">Log Out</a>';
                }else{
                    header('Location: homepage.php');
                }
            }else{
                header('Location: homepage.php');
            }
        ?>
    </div>
        <br><br>
        <p>
            <?php if(isset($message)) { echo $message; } ?>
        </p>
        <form action="<?php echo $_SERVER["PHP_SELF"];?>" method="post" class="styled-form">
            <table class="styled-table">
                <caption class="styled-caption"><br>Record a Shift<br><br></caption>
                <!--The required fields are marked with * and display error messages when filled in incorrectly-->
                <tr>
                    <td colspan="2">Required fields are marked with a *</td>
                </tr>
                <tr>
                    <td><strong>Employee:</strong</td>
                    <td>
                        <select name="name" style="width: 200px" required>
                        <?php
                            $i=0;
                            while($row = mysqli_fetch_array($result)) {
                        ?>

                            <?php echo '<option value="'. $row['name'] .'" >'. $row['name'] .'</option>'; ?>

                        <?php
                            $i++;
                            }
                        ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><strong>Date:</strong></td>
                    <td>
                        <input type="date" name="date" max= <?php echo date('Y-m-d');?>
                         ><span class="error">* <?php echo $dateErr;?></span>
                    </td>
                </tr>
                <tr>
                    <td>Clock In Time:</td>
                    <td>
                        <input type="time" name="clock_in">
                        <span class="error">* <?php echo $inErr;?></span>
                    </td>
                </tr>
                <tr>
                <td>Clock Out Time:</td>
                    <td>
                        <input type="time" name="clock_out">
                        <span class="error">* <?php echo $outErr;?></span></td></td>
                    </td>
                </tr>
                <tr>
                    <td style="width: 195px">&nbsp;</td>
                    <td><input name="submit" type="submit" value="Submit" /></td>
                </tr>
            </table>
        </form>

    </body>

</html>