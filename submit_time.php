<?php 

// This page is executed to send the current time to the database as either a clock in time or clock out time for the current
// employee on today's date

session_start();

include('group_detail.php');

ini_set('display_errors', 1);
ini_set('log_errors',1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if(isset($_SESSION['id']) && isset($_SESSION['name']) && isset($_SESSION['user_type']) && ($_SESSION['user_type'] == 'Employee') 
&& isset($_SESSION['clockStatus']))
{

}

else{
    header('Location: login.php');
}
        $id = $_SESSION['id'];
        $date = date("Y/m/d");
        $current_time = date("H:i");

        if($_SESSION['clockStatus'] == 1){

            // relevant values are entered into the database
            $query  = "INSERT INTO employee_hours(";
            $query .= "employee_ID, date, clock_in_time";
            $query .= ") VALUES (";
            $query .= "'$id', '$date', '$current_time')";

            $result = $db->query($query);

        }

        else{

             // relevant values are entered into the database, clock out time is entered where the clock out time field is null
            $clock_out_query  = "UPDATE employee_hours ";
            $clock_out_query .= "SET clock_out_time = '$current_time' ";
            $clock_out_query .= "WHERE employee_ID = '$id' AND date = '$date' ";
            $clock_out_query .= "AND clock_out_time IS NULL";

            $result = $db->query($clock_out_query);

        }

        header('Location: clock_summary.php');

?>