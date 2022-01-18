<?php
    session_start();
    include("group_detail.php");
    ini_set('display_errors', 1);
    ini_set('log_errors',1);
    error_reporting(E_ALL);
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    // checks that product and quantity session variables are set
    if(isset($_SESSION['product']) && isset($_SESSION['quantity'])){
        $product = $_SESSION['product'];
    } else{
        header('Location: homepage.php');
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta content="en-ie" http-equiv="Content-Language" />
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <link rel = "stylesheet" type = "text/css" href = "style2.css"/>
    <link rel="shortcut icon" href="favicon.ico" />
    <title>Breakage Confirmation</title>
</head>
<body>

<div class="topnav">
        <a>Dublin Party Hire</a>
        <a href="homepage.php">Homepage</a>
        <?php
            // sends anyone who does not work for dph back to homepage
            if(isset($_SESSION['user_type'])){
                if ($_SESSION['user_type'] == 'Manager'){
                    echo '<a href="employee_registration.php">Employee Registration</a>';
                    echo '<a href="edit_items.php">Edit Products</a>';
                    echo '<a href="edit_employees.php">Edit Employee Info</a>';
                    echo '<a href="edit_hours.php">Edit Shifts</a>';
                    echo '<a href="reports.php">Reports</a>';
                    echo '<a class="active" href="breakage_form.php">Report Breakage</a>';
                    echo '<a href="login.php">Log Out</a>';
                } else if($_SESSION['user_type'] == 'Employee'){
                    echo '<a href="work_log.php">Clock In/Out</a>';
                    echo '<a class="active" href="breakage_form.php">Report Breakage</a>';
                    echo '<a href="login.php">Log Out</a>';
                }else if($_SESSION['user_type'] == 'customer'){
                    header('Location: homepage.php');
                } else{
                    header('Location: homepage.php');
                }
            }
            else{
                    header('Location: homepage.php');
            }
        ?>
    </div>

    <br><br>

    <h3 style="text-align: center;"><?php echo $_SESSION['quantity']?> of the following items have been removed from the system and
        added to breakages: </h3>

        <br>

        <h3 style="text-align: center;">
            <?php
                // pulls up info from product table based on $product
                $query = "SELECT * from product WHERE product_ID = '$product'";
                $result = $db->query($query); // runs the query
                $row = mysqli_fetch_assoc($result);
                $name = $row['product_name'];
                echo $name;
                unset($_SESSION['quantity']);
                unset($_SESSION['product']);
            ?> 
        </h3>

        <br> 

        <p style="text-align: center;"> <a style = "width: 50%;", href="homepage.php"><button>Ok</button> </a> </p>
    
</body>
</html>