<?php
// display errors
session_start();
include('group_detail.php');
ini_set('display_errors', 1);
ini_set('log_errors',1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    // Validating that user has the necessary credentials to access this page, i.e that they are a signed in employee
    if(isset($_SESSION['id']) && isset($_SESSION['name']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'Employee')
    {
        // if they have a clockStatus, then this is unset and their actual clockStatus is assigned
        if(isset($_SESSION['clockStatus']))
        {
            unset($_SESSION['clockStatus']);
        }
        
        $id = $_SESSION['id'];
        //Getting employee's clock ins/outs for today's date in the correct SQL format (Year/Month/Day)
        $rows_query = "SELECT * FROM employee_hours WHERE date = DATE_FORMAT(CURDATE(), '%Y/%m/%d') AND employee_ID = '$id'";
        $result = $db->query($rows_query);
        $num_rows = mysqli_num_rows($result);

        // if the number of rows returned is 0 then no clock ins have been completed for the day, if it is 1 then 
        // the employee has either clocked in once or has completed one shift today

        if($num_rows == 0 or $num_rows == 1)
        {
            $row = mysqli_fetch_assoc($result);

            // one shift completed or no shifts started 

            if(empty($row))
            {
                $clockStatus = 1;
            }
            else if(!empty($row['clock_in_time']) && !empty($row['clock_out_time']))
            {
                $clockStatus = 1;
            }
            else if(!empty($row['clock_in_time']) && empty($row['clock_out_time']))
            {
                // user has completed their clock in for this shift and has yet to clock out
                $clockStatus = 2;
            }

        }

        // if there are two returned rows then the employee has completed one shift and completed a clock in for a second shift
        // or has completed two shifts
        else if($num_rows == 2)
        {
            // below returns the latest clock in information from the data table
            $query = "SELECT * FROM employee_hours WHERE date = DATE_FORMAT(CURDATE(), '%Y/%m/%d') AND employee_ID = '$id' ";
            $query .= "ORDER BY clock_in_time DESC LIMIT 1 ;";  // This retrieves the latest clock in time of this employee on this date
            $value = $db->query($query);
            $row = mysqli_fetch_assoc($value);

            // if the employee has a second clock out time for the day then they have completed all of their shifts
            if(!empty($row['clock_out_time'])){

                $clockStatus = 3;
            }
            else
            {
                $clockStatus = 2;
            }

        }

        // Once clock-in status has been retrieved, the corresponding session variable is created
        $_SESSION['clockStatus'] = $clockStatus ;
    }

    // If user does not have all necessary credentials, they are automatically brought back to the login page
    else {
    header('Location: login.php');
    }
    ?>

  

<!DOCTYPE html>
<html lang="en">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <link rel = "stylesheet" type = "text/css" href = "style2.css"/>
        <title>DPH Clock In/Out</title>
</head>

<body>
    <div class="topnav">
        <a>Dublin Party Hire</a>
        <a href="homepage.php">Homepage</a>
        <?php
            if(isset($_SESSION['user_type'])){
                if($_SESSION['user_type'] == 'Employee'){
                    echo '<a class="active" href="work_log.php">Clock In/Out</a>';
                    echo '<a href="breakage_form.php">Report Breakage</a>';
                    echo '<a href="login.php">Log Out</a>';
                }else{
                    header('Location: login.php');
                }
            } else{
                header('Location: login.php');
            }
        ?>
    </div>

    <h2>DPH Employee Clock In</h2>
    <br><br>
    <!-- employee_ID is displayed in the top right corner of the clock in page-->
    <p style="text-align: center;">Employee ID: <?php echo $_SESSION['id']?> </p>  

        <h3 style="text-align: center;"> Hi <?php echo $_SESSION['name']?>! Press the 
        button below to clock in or clock out </h3>

       <p  style="text-align: center; font-size: 80px"> The current time is: <?php echo date("H:i")?>

       <?php

       // Button will change depending on clock in status

       // clockStatus of 1 indicates a new clock in is needed, clockStatus of 2 indicates that a clock out is needed and a value of 
       // 3 indicates that the employee has completed two shifts today
       if($clockStatus == 1 or $clockStatus == 2){

            echo '<p style="text-align: center;"> <a style = "width: 50%;", href="submit_time.php">';

            if($clockStatus == 1){

                echo '<button style = "height = 60px; width = 140px;">Clock In</button> </a> </p>';

            }
            else if($clockStatus == 2){

                echo '<button>Clock Out</button> </a> </p>';

            }
        }
        // clockStatus value of 3 indicates a user has already clocked in and out twice in one day,
        // and a message is displayed informing them that they have 
        // already completed their shifts and the clockStatus Session Variable is unset
 
        else if($clockStatus == 3){
            
            unset($_SESSION['clockStatus']);

                echo '<p style="text-align: center;">You have already completed your shifts for the day.  </p>';
                echo '<p style="text-align: center;"> <a style = "width: 50%;", href="homepage.php">';
                echo '<button> Return to Homepage</button> </a> </p>';

        }

        // this option is executed when the employee has not been assigned a clockStatus session variable, where their status is invalid
        // and there is an error
        else{
                header('Location: homepage.php');
            } 
        

       ?>


</body>
</html>