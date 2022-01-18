<?php
    // code to display errors
    session_start();
    include("group_detail.php");
    ini_set('display_errors', 1);
    ini_set('log_errors',1);
    error_reporting(E_ALL);
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    // function to trim entries
    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
      }
    // initialises variables
    $id = $_SESSION['id'];
    $productErr = $quantityErr = $reportErr = "";
    // form action
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // checks that a product id is entered
        if (empty($_POST["product"])) {

            $productErr = "Product ID is required";

          } else {
            // selects product details from mysql table
            $product_test = test_input($_POST["product"]);
            $sql = "SELECT * from product WHERE product_ID = '$product_test'";
            
            // Checks if product ID exists
            $result = $db->query($sql); // runs the query
            $num_rows= mysqli_num_rows($result); // counts how many rows the query applies to
                
            if($num_rows == 0){
                    // Provides an error if the given product ID doesn't exist
                    $productErr = "Product does not exist, please enter the correct Product ID "; 
                }
                else{
                    // if results are obtained, then ID is correct and in the correct format
                    $product = test_input($_POST["product"]);                        
                }
          }
        // checks if a quantity is entered for breakage
        if(empty($_POST['quantity']) or $_POST['quantity'] == 0 or !is_numeric($_POST['quantity'])){
            $quantityErr = "A quantity is required for item breakages";
        }

        else{
            if(!empty($product)){
                // ensures that broken quantity does not exceed quantity of item in stock
                $quantity_test = test_input($_POST["quantity"]);
                $sql = "SELECT qty from product WHERE product_ID = '$product'";
            
                // Checks if product ID exists
                $result = $db->query($sql); // runs the query
                $value = mysqli_fetch_assoc($result);
    
                // Number of broken products can't exceed total number of products prior to breakage
                if($quantity > $value){
                    $quantityErr = "Quantity can not be greater than the number of products before breakage";
                }
                else{
                    $quantity = test_input($_POST['quantity']);
                }

            }
            else{
                $quantityErr = "Please ensure correct Product ID has been entered";
            }
        }
        // checks if a report is entered
        if(empty($_POST['report'])){
                $reportErr = "A report is required for item breakages";
        }else{
                $report = test_input($_POST['report']);
        }
        // procedure if all fields are filled in properly
        if ($productErr == "" && $quantityErr == "" && $reportErr == ""){
            // This code enters the data from the form into the breakages table
        $q  = "INSERT INTO breakages(";
        $q .= "product_ID, employee_ID, quantity, report, date";
        $q .= ") VALUES (";
        $q .= "'$product', '$id', '$quantity', '$report', DATE_FORMAT(CURDATE(), '%Y/%m/%d'))";

        $result = $db->query($q);
        // updates the product table
        $sql = "UPDATE product SET qty = qty - ".$quantity." WHERE product_ID = '$product';";

        $result1 = $db->query($sql);
        // sets session variable values
        $_SESSION['product'] = $product;
        $_SESSION['quantity'] = $quantity;
       
        header('Location: confirm_breakage.php');
        }
      }

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta content="en-ie" http-equiv="Content-Language" />
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <link rel = "stylesheet" type = "text/css" href = "style2.css"/>
        <link rel="shortcut icon" href="favicon.ico" />
    <title>Report Breakage</title>
</head>
<body>

<div class="topnav">
        <a>Dublin Party Hire</a>
        <a href="homepage.php">Homepage</a>
        <?php
            // allows any dph worker to log breakages
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
            }else{
                    header('Location: homepage.php');
            }
        ?>
    </div>

    <br><br>
        <form action="<?php echo $_SERVER["PHP_SELF"];?>" method="post" class="styled-form">
            <table class="styled-table">
                <caption class="styled-caption"><br><br>Breakage Report</caption>
                <!--The required fields are marked with * and display error messages when filled in incorrectly-->
                <tr>
                    <td>Required fields are marked with a *</td>
                </tr>
                <tr>
                    <td style="width: 195px">
                    
                    <strong>Employee ID</strong>
                    
                    </td>

                    <td>

                    <?php echo $_SESSION['id']; ?>

                    </td>

                </tr>
                <tr>
                    <td style="width: 195px">
                    
                    <strong>Enter Product ID</strong>
                    
                    </td>

                    <td>
                    
                    <input name="product" type = "integer" style="width: 169px" title = "Please enter a valid Product ID" 
                    required></input>*
                    <span class="error">* <?php echo $productErr;?></span></td>
                    
                    </td>
                </tr>
                <tr>

                    <td style="height: 23px; width: 195px">
                    
                    <strong>Enter Quantity</strong>
                    
                    </td>

                    <td style="height: 23px">

                        <input name="quantity" type="integer"
                        title="Please enter a valid email address." required style="width: 169px"/>
                        <span class="error">* <?php echo $quantityErr;?></span>

                    </td>

                </tr>
                <tr>
                    <td>
                    
                    <strong>Enter Report:</strong>
                    
                    </td>

                    <td>

                        <textarea name="report" rows="4" ></textarea>
                        <span class="error">* <?php echo $reportErr;?></span>
                    
                    </td>
                </tr>

                <tr>
                    <td style="width: 195px">&nbsp;</td>
                    <td><input name="submit" type="submit" value="Submit" /><input class="auto-style1" name="reset" type="reset" value="Reset" /></td>
                </tr>
            </table>
        </form>

</body>
</html>