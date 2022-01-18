<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta content="en-ie" http-equiv="Content-Language" />
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <link rel = "stylesheet" type = "text/css" href = "style2.css"/>
        <title>Reports</title>

    </head>

    <body>
        <?php
            // displays errors
            ini_set('display_errors', 1);
            ini_set('log_errors',1);
            error_reporting(E_ALL);
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            session_start();
            include ("group_detail.php");
            // initialise  variables
            $reportErr = "";
            // runs different queries based on what report is selected
            if($_SESSION['report'] == "pick_up_schedule"){
                // show all the pick up schedule
                $result = mysqli_query($db,
                    "SELECT c.customer_ID, c.name, DATE_FORMAT(o.start_date,'%d %M %Y') as start_date,
                    DATE_FORMAT(o.end_date,'%d %M %Y') as end_date,
                    o.order_ID FROM `customer` AS c LEFT JOIN `order_table` AS o ON ( c.customer_ID = o.customer_ID )
                    WHERE `delivery_method` = 'Pick-Up' AND start_date = '".$_SESSION['chosen_date']."'");
            }else if($_SESSION['report'] == "delivery_schedule"){
            // shows delivery Schedule
                $result = mysqli_query($db,
                        "SELECT DATE_FORMAT(o.start_date,'%d %M %Y') as start_date,
                        DATE_FORMAT(o.end_date,'%d %M %Y') as end_date, o.order_ID, o.customer_ID, c.name
                        as customer_name, e.name as driver, o.van_ID FROM order_table AS o LEFT JOIN
                        customer as c ON (o.customer_ID=c.customer_ID) LEFT JOIN employee AS e ON
                        (e.employee_ID=o.employee_ID) WHERE start_date = '".$_SESSION['chosen_date']."' AND
                        delivery_method = 'Delivery'");
            }else if($_SESSION['report'] == "employee_hours"){
            // shows employee hours
                $result = mysqli_query($db,
                    "SELECT TIMESTAMPDIFF(HOUR,eh.clock_in_time,eh.clock_out_time) as hours_worked,
                    e.employee_ID, DATE_FORMAT(eh.date,'%d %M %Y') as date, e.name FROM employee_hours as eh
                    LEFT JOIN employee as e ON (e.employee_ID=eh.employee_ID) WHERE date = '".$_SESSION['chosen_date']."'
                    GROUP by employee_ID");
            }
            if(isset($_POST['choose_report'])) {
                // returns user to the page where they can choose reports
                $_SESSION['report']="";
                $_SESSION['chosen_date']="";
                header('Location: reports.php');
            }
            if(isset($_POST["export"])){
                // exports employee data to csv
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename=data.csv');
                $output = fopen("php://output", "w");
                fputcsv($output, array('Employee ID', 'Name', 'Hours Worked', 'Date'));
                $query = "SELECT e.employee_ID, e.name, TIMESTAMPDIFF(HOUR,eh.clock_in_time,eh.clock_out_time) ";
                $query .= "as hours_worked, DATE_FORMAT(eh.date,'%d %M %Y') as date FROM employee_hours as eh";
                $query .= "LEFT JOIN employee as e ON (e.employee_ID=eh.employee_ID)GROUP by employee_ID, date";
                $result = mysqli_query($db, $query);
                while($row = mysqli_fetch_assoc($result))
                {
                   fputcsv($output, $row);
                }
                fclose($output);
            }
        ?>
        <div class="topnav">
            <a>Dublin Party Hire</a>
            <a href="homepage.php">Homepage</a>
            <?php
                // relocates anyone who isn't a manager to homepage
                if (isset($_SESSION['user_type'])){
                    if ($_SESSION['user_type']== 'Manager'){
                        echo '<a href="employee_registration.php">Employee Registration</a>';
                        echo '<a href="edit_items.php">Edit Products</a>';
                        echo '<a href="edit_employees.php">Edit Employee Info</a>';
                        echo '<a href="edit_hours.php">Edit Shifts</a>';
                        echo '<a class="active" href="reports.php">Reports</a>';
                        echo '<a href="breakage_form.php">Report Breakage</a>';
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
                <!--Shows Pick Up Schedule-->
                <?php if($_SESSION['report']== "pick_up_schedule"){?>
                    <caption class="styled-caption"><p>Pick Up Schedule</p></caption>
                    <tr>
                        <td>
                            Order ID
                        </td>
                        <td style="padding:10px">
                            Pick Up Date
                        </td>
                        <td style="padding:10px">
                            Return Date
                        </td>
                        <td style="padding:10px">
                           Customer
                        </td>
                    </tr>
                <?php
                        $i=0;
                        while($row = mysqli_fetch_array($result)) {
                    ?>
                        <tr>
                            <td style="padding:10px"><?php echo $row["order_ID"]; ?>  </td>
                            <td style="padding:10px"><?php echo $row["start_date"]; ?>  </td>
                            <td style="padding:10px"><?php echo $row["end_date"]; ?>  </td>
                            <td style="padding:10px"><?php echo $row["name"]; ?></td>
                            <td style="padding:10px"><a href="">Ask Nathan to do this</td>
                        </tr>
                <?php $i++; } }else if($_SESSION['report']== "delivery_schedule"){?>
                    <caption class="styled-caption"><p>Delivery Schedule</p></caption>
                    <!--Shows Delivery Schedule-->
                    <tr>
                        <td>
                            Order ID
                        </td>
                        <td style="padding:10px">
                            Delivery Date
                        </td>
                        <td style="padding:10px">
                            Collection Date
                        </td>
                        <td style="padding:10px">
                           Driver
                        </td>
                        <td style="padding:10px">
                           Customer
                        </td>
                    </tr>
                <?php
                        $i=0;
                        while($row = mysqli_fetch_array($result)) {
                    ?>
                        <tr>
                            <td style="padding:10px"><?php echo $row["order_ID"]; ?>  </td>
                            <td style="padding:10px"><?php echo $row["start_date"]; ?>  </td>
                            <td style="padding:10px"><?php echo $row["end_date"]; ?></td>
                            <td style="padding:10px"><?php echo $row['driver']; ?></td>
                            <td style="padding:10px"><?php echo $row["customer_name"]; ?></td>
                        </tr>
                <?php $i++; } } else if($_SESSION['report']== "employee_hours"){?>
                <caption class="styled-caption"><p>Employee Hours Worked</p></caption>
                <!--Shows Employee hours worked-->
                <!-- Allows the user to export data in csv form-->
                <form method="post" action="export.php">
                <tr><td></td><td><input type="submit" name="export" value="CSV Export" class="btn btn-success" /> </td></tr>
                </form>
                <tr>
                    <td>
                        Employee ID
                    </td>
                    <td style="padding:10px">
                        Name
                    </td>
                    <td style="padding:10px">
                       Hours Worked
                    </td>
                    <td style="padding:10px">
                       Date
                    </td>
                </tr>
                <?php
                    $i=0;
                    while($row = mysqli_fetch_array($result)) {
                ?>
                <tr>
                    <td style="padding:10px"><?php echo $row["employee_ID"]; ?>  </td>
                    <td style="padding:10px"><?php echo $row["name"]; ?>  </td>
                    <td style="padding:10px"><?php echo $row["hours_worked"]; ?></td>
                    <td style="padding:10px"><?php echo $row["date"]; ?></td>
                </tr>
                <?php $i++; } }?>
                <tr>
                    <td style="width: 195px">&nbsp;</td>
                    <td><input name="choose_report" type="submit" value="Return to Menu" class="button"></td>
                </tr>
            </table>
        </form>

    </body>

</html>