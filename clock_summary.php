<?php

    session_start();
    include('group_detail.php');
    ini_set('display_errors', 1);
    ini_set('log_errors',1);
    error_reporting(E_ALL);
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    if(isset($_SESSION['id']) && isset($_SESSION['name']) && isset($_SESSION['user_type']) && ($_SESSION['user_type'] == 'Employee')
    && isset($_SESSION['clockStatus']))
    {

    } else{
        header('Location: login.php');
    }

    // below code retrieves the necessary information to be displayed by the page
        $id = $_SESSION['id'];
        $date = date("Y/m/d");

        // returns all the necessary information about this employee's current shift, returns 1 row which has the latest clock in time
        $query = "SELECT * from employee_hours WHERE employee_ID = '$id' AND date = '$date' ";
        $query .= "ORDER BY clock_in_time DESC LIMIT 1";

        $result = $db->query($query);

        // Returns the row with the above credentials in the employee Table
        $row = mysqli_fetch_assoc($result);



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clock In</title>
    <link rel = "stylesheet" type = "text/css" href = "style2.css"/>

</head>

<body>
    <div class="topnav">
        <a>Dublin Party Hire</a>
        <a class="active" href="homepage.php">Homepage</a>
        <?php
            // sends anyone who isn't an employee to homepage
            if($_SESSION['user_type'] == 'Employee'){
                echo '<a href="customer_registration_form.php">Customer Sign Up</a>';
                echo '<a href="login.php">Log Out </a>';
                echo '<a href="work_log.php">Clock In/Out</a>';
            } else{
                header('Location: homepage.php');
            }
        ?>
    </div>

    <h2>DPH Employee Clock In</h2>

    <p style="text-align: right;">Employee ID: <?php echo $_SESSION['id']?> </p>
    
    <?php 
    
    // Employee has just clocked in so their clock in time is displayed
    if($_SESSION['clockStatus'] == 1){

        // converting SQL time field to UNIX timestamp
        $timestamp = strtotime($row['clock_in_time']);
        $clock_in_time = date("H:i", $timestamp  );
        echo '<p style = "text-align: center; font-size: 50px;"> You clocked in at '.$clock_in_time.'.';
        }

    // Employee has just clocked out so their clock out time is displayed
    else{

        // converting SQL time field to UNIX timestamp
        $timestamp = strtotime($row['clock_out_time']);
        $clock_out_time = date("H:i", $timestamp  );
        echo '<p style = "text-align: center; font-size: 50px;"> You clocked out at '.$clock_out_time.'.';
    }
        
        ?> 

<p style="text-align: center;"> <a style = "width: 50%;", href="homepage.php"><button>Ok</button> </a> </p>

</body>
</html>








