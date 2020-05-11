<?php
session_start();
include 'assets/acknowledgements.php';


$stg1c_onthefly=''; //for enabling stage 2, this value is checked instead of database
$stg2c_onthefly='';
if ($_SESSION['permission'] == 'admin') {
    if (array_key_exists("id", $_GET)) {
        include('connect-db.php');
        $query = "SELECT * FROM `cutabove` WHERE id = '".mysqli_real_escape_string($link, $_GET['id'])."' LIMIT 1";
        $result = mysqli_query($link, $query) or die(mysql_error());
        $row = mysqli_fetch_array($result);
    } else {
        echo "You didn't specify which id to fetch. You are admin. Use the <a href='all-people.php'>Admin mode</a> to click on a user for details.";
        die();
    }
} else {
    header("Location: index.php?redirect=".$_SERVER['REQUEST_URI']);
}
if (array_key_exists("submit", $_POST)) {
    //echo '<div id="tablediv">';
    //print_r($_POST);
    //echo '</div>';
    $feeTracker = '';
    if ($_POST['stg1fee']!=$row['stg1fee']) {
        $feeTracker.=" Stage 1 Fee: ". mysqli_real_escape_string($link, $row['stg1fee']) ." -> ".mysqli_real_escape_string($link, $_POST['stg1fee']);
    }
    if ($_POST['stg2fee']!=$row['stg2fee']) {
        $feeTracker.=" Stage 2 Fee: ". mysqli_real_escape_string($link, $row['stg2fee']) ." -> ".mysqli_real_escape_string($link, $_POST['stg2fee']);
    }
    if ($_POST['stg3fee']!=$row['stg3fee']) {
        $feeTracker.=" Stage 3 Fee: ". mysqli_real_escape_string($link, $row['stg3fee']) ." -> ".mysqli_real_escape_string($link, $_POST['stg3fee']);
    }
    if ($_POST['stg4fee']!=$row['stg4fee']) {
        $feeTracker.=" Stage 4 Fee: ". mysqli_real_escape_string($link, $row['stg4fee']) ." -> ".mysqli_real_escape_string($link, $_POST['stg4fee']);
    }
    if (array_key_exists('stg5w1fee', $_POST) and $_POST['stg5w1fee']!=$row['stg5w1fee']) {
        $feeTracker.=" Stage 5w1 Fee: ". mysqli_real_escape_string($link, $row['stg5w1fee']) ." -> ".mysqli_real_escape_string($link, $_POST['stg5w1fee']);
    }
    if (array_key_exists('stg5w2fee', $_POST) and $_POST['stg5w2fee']!=$row['stg5w2fee']) {
        $feeTracker.=" Stage 5w2 Fee: ". mysqli_real_escape_string($link, $row['stg5w2fee']) ." -> ".mysqli_real_escape_string($link, $_POST['stg5w2fee']);
    }
    if (array_key_exists('stg5w3fee', $_POST) and $_POST['stg5w3fee']!=$row['stg5w3fee']) {
        $feeTracker.=" Stage 5w3 Fee: ". mysqli_real_escape_string($link, $row['stg5w3fee']) ." -> ".mysqli_real_escape_string($link, $_POST['stg5w3fee']);
    }
    if (array_key_exists('stg5w4fee', $_POST) and $_POST['stg5w4fee']!=$row['stg5w4fee']) {
        $feeTracker.=" Stage 5w4 Fee: ". mysqli_real_escape_string($link, $row['stg5w4fee']) ." -> ".mysqli_real_escape_string($link, $_POST['stg5w4fee']);
    }
    if (array_key_exists('stg5w5fee', $_POST) and $_POST['stg5w5fee']!=$row['stg5w5fee']) {
        $feeTracker.=" Stage 5w5 Fee: ". mysqli_real_escape_string($link, $row['stg5w5fee']) ." -> ".mysqli_real_escape_string($link, $_POST['stg5w5fee']);
    }

    $comment='';
    if (array_key_exists('comment', $_POST) and $_POST['comment']!='') {
        $comment = ", <b>Comment</b>: ".mysqli_real_escape_string($link, $_POST['comments']);
    } else {
        $comment = " No comments";
    }

    $query = "UPDATE `cutabove` 
	SET  `kit` = '".mysqli_real_escape_string($link, $_POST['kit'])."', 
	`stg1fee` = '".mysqli_real_escape_string($link, $_POST['stg1fee'])."', 
	`stg2fee` = '".mysqli_real_escape_string($link, $_POST['stg2fee'])."', 
    `stg3fee` = '".mysqli_real_escape_string($link, $_POST['stg3fee'])."', 
    `stg4fee` = '".mysqli_real_escape_string($link, $_POST['stg4fee'])."', 
    `stg5w1fee` = '".mysqli_real_escape_string($link, $_POST['stg5w1fee'])."', 
    `stg5w2fee` = '".mysqli_real_escape_string($link, $_POST['stg5w2fee'])."', 
    `stg5w3fee` = '".mysqli_real_escape_string($link, $_POST['stg5w3fee'])."', 
    `stg5w4fee` = '".mysqli_real_escape_string($link, $_POST['stg5w4fee'])."', 
    `stg5w5fee` = '".mysqli_real_escape_string($link, $_POST['stg5w5fee'])."', 
	`comments` = '".mysqli_real_escape_string($link, $row['comments'])." <br /><small>[". date('d-m-Y h:i:sa') ."] ".mysqli_real_escape_string($link, $_SESSION['name'])." (ID# ".mysqli_real_escape_string($link, $_SESSION['id'])."):</small>".$feeTracker.$comment."',
	`edited_by` = '".$_SESSION['name']."', 
	`last_edited_at` = '". date('d-m-Y h:i:sa') ."'
	WHERE `cutabove`.`id` = '".$row['id']."' LIMIT 1";

    $success = '';
    // date('Y-m-d H:i:s') because https://stackoverflow.com/a/2215359/2365231 as this is the format that mysql "datetime" accepts
    if (mysqli_query($link, $query)) {
        for ($i=1; $i < 5; $i++) {
            if ($_POST['stg'.$i.'fee']!= $row['stg'.$i.'fee']) {
                $query = "INSERT INTO `cutabove_fee` (`user_id`, `stage`, `old_value`, `new_value`, `edited_by`, `last_edited_at`, `comments`) 
				VALUES (
				'".mysqli_real_escape_string($link, $row['id'])."',
				'stg".$i."fee',
				'".mysqli_real_escape_string($link, $row['stg'.$i.'fee'])."',
				'".mysqli_real_escape_string($link, $_POST['stg'.$i.'fee'])."',
				'".$_SESSION['id']."',
				'". date('Y-m-d H:i:s') ."',
				'<small>[". date('d-m-Y h:i:sa') ."] ".mysqli_real_escape_string($link, $_SESSION['name'])." (ID# ".mysqli_real_escape_string($link, $_SESSION['id'])."):</small> Stage 1 Fee: ". mysqli_real_escape_string($link, $row['stg1fee']) ." -> ".mysqli_real_escape_string($link, $_POST['stg1fee']).", Stage 2 Fee: ". mysqli_real_escape_string($link, $row['stg2fee']) ." -> ".mysqli_real_escape_string($link, $_POST['stg2fee']).", <b>Comment</b>: ".mysqli_real_escape_string($link, $_POST['comments'])."');";

                if (mysqli_query($link, $query)) {
                    $success = 1;
                }
            }
        }
        $success = 2;
    }
    if ($success == 1) {
        //success in changing fee
        header("Location: fee.php?id=".$row['id']."&successEdit=1");
    } else {
        echo '<div id="tablediv">';
        echo "No fee changes were made.";
        echo '</div>';
    }
    
    if ($success == 2) {
        //success changing kit status
        header("Location: fee.php?id=".$row['id']."&successEdit=1");
    } else {
        echo '<div id="tablediv">';
        echo "Kit was not changed";
        echo '</div>';
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <?php include 'header.php'; //css-theme detector?>

    <title>Manage Fees</title>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div id="tablediv" class="container-fluid">
        <?php
        if ($_SESSION['god'] != 1) {
            echo '<h1>Go back to <a href="council-dashboard.php">Dashboard</a> and enable <b>God Mode</b></h1>';
            die();
        }
        ?>
        <form method="post">
            <!--display general data-->
            <div class="row">
                <table class="table table-responsive-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th>Property</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">ID</th>
                            <td><?=$row['id']?></td>
                        </tr>
                        <tr>
                            <th scope="row">College Registration</th>
                            <td><?=$row['clg_reg']?></td>
                        </tr>
                        <tr>
                            <th scope="row">Name</th>
                            <td><?=$row['name']?></td>
                        </tr>
                        <tr>
                            <th scope="row">DOB (DD-MM-YYYY)</th>
                            <td><?=date('d-M-Y', strtotime($row['dob']))?></td>
                        </tr>
                        <tr>
                            <th scope="row">Semester</th>
                            <td><?=$row['semester']?></td>
                        </tr>
                        <tr>
                            <th scope="row">Phone</th>
                            <td>
                                <?php
                                //https://stackoverflow.com/a/2220529/2365231
                                if ($row['phone']!= 0) {
                                    echo '<a href="tel:' . $row['phone'] . '">+91-' . $row['phone'] . ' (Call)</a>';
                                }
                                if ($row['phone']!= 0 and $row['phone_whatsapp']!= 0) {
                                    echo ',<br />';
                                }
                                if ($row['phone_whatsapp']!= 0) {
                                    echo '<a href="https://wa.me/+91' . $row['phone_whatsapp'] . '">+91-' . $row['phone_whatsapp'] . ' (WA)</a>';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Email</th>
                            <td><a href="mailto:<?=$row['email']?>?subject=A Cut Above&body=Hi<?=strtok($row['name'], " ")?>,%0A%0A%0A%0A Yours Sincerely,%0A<?=$_SESSION['name']?> (<?=$_SESSION['permission']?>)%0ACut Above%0AKMC, Mangalore"
                                    target="_top"><?=$row['email']?></a></td>
                        </tr>
                        <tr>
                            <th scope="row">Old Comments (Only admins can view)</th>
                            <td><button type="button" id="show-hide" class="btn btn-outline-danger">Toggle
                                    Comments</button>
                                <div id="comments"><?=$row['comments']?></div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Comments (Only admins can view)</th>
                            <td><textarea class="form-control" name="comments"
                                    placeholder="The new text you add will be appended to the previous pieces of text"
                                    onKeyDown="limitText(this.form.comments,this.form.countdown,280);"
                                    onKeyUp="limitText(this.form.comments,this.form.countdown,280);"></textarea>
                                <font size="1">You have <input readonly type="text" name="countdown" size="3"
                                        value="280"> characters left. Same as Twitter</font>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!--pretty-fy with putting stuff inside div row.-->
            <div class="row">
                <!--display stage 1 data-->
                <table class="table col-sm">
                    <thead>
                        <tr class="thead-dark">
                            <th>Stage</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">Status</th>
                            <td>
                                <select class="form-control" name="kit">
                                    <?php
                                    echo '<option value="0"'; if ($row['kit'] == '0') {
                                        echo 'selected';
                                    } echo '>Approval Pending</option>
                                    <option value="1"'; if ($row['kit'] == '1') {
                                        echo 'selected';
                                    } echo '>Approved</option>
                                    <option value="2"'; if ($row['kit'] == '2') {
                                        echo 'selected';
                                    } echo '>Rejected</option>
                                    <option value="3"'; if ($row['kit'] == '3') {
                                        echo 'selected';
                                    } echo '>Debarred</option>
                                    <option value="4"'; if ($row['kit'] == '4') {
                                        echo 'selected';
                                    } echo '>Completed</option>
                                    <option value="5"'; if ($row['kit'] == '5') {
                                        echo 'selected';
                                    } echo '>Discontinued</option>
                                    <option value="6"'; if ($row['kit'] == '6') {
                                        echo 'selected';
                                    } echo '>Status Unknown</option>';
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <?php
                        /** Generate Fee Boxes. 3 values. 0, pre-existing value, and current required value */
                        for ($i=1; $i<5; $i++) {
                            $query_default_fees_stg = "SELECT * FROM `cutabove_misc` WHERE property='stg".$i."fee' LIMIT 1";
                            $result_default_fees_stg = mysqli_query($link, $query_default_fees_stg) or die(mysql_error());
                            $row_default_fees_stg = mysqli_fetch_array($result_default_fees_stg);

                            echo '<th scope="row">Stage '.$i.' Fee</th><td><select class="form-control" name="stg'.$i.'fee">
                            <option value="'.$row['stg'.$i.'fee'].'" selected>'.$row['stg'.$i.'fee'].' (Pre Existing Value)</option>
                            <option value="0">0</option>
                            <option value="'.$row_default_fees_stg['value'].'">'.$row_default_fees_stg['value'].' (Current Requirement)</option>';
                            echo '</select></td></tr>';
                        }

                        if ($row['stg4fee']!=0) {
                            echo '<thead>
                            <tr class="thead-dark">
                            <th>Stage 5</th>
                            <th>Status</th>
                            </tr>
                            </thead>';
                            //** Generate fee box for stage 5 only if stage 4 fee paid*/
                            for ($i=1; $i < 6; $i++) {
                                $query_default_fees_stg = "SELECT * FROM `cutabove_misc` WHERE property='stg5w".$i."fee' LIMIT 1";
                                $result_default_fees_stg = mysqli_query($link, $query_default_fees_stg) or die(mysql_error());
                                $row_default_fees_stg = mysqli_fetch_array($result_default_fees_stg);

                                echo '<th scope="row">Stage 5w'.$i.' Fee</th><td><select class="form-control" name="stg5w'.$i.'fee">
                                <option value="'.$row['stg5w'.$i.'fee'].'" selected>'.$row['stg5w'.$i.'fee'].' (Pre Existing Value)</option>
                                <option value="0">0</option>
                                <option value="'.$row_default_fees_stg['value'].'">'.$row_default_fees_stg['value'].' (Current Requirement)</option>';
                                echo '</select></td></tr>';
                            }
                        } else {
                            echo '<tr><td>Stage 5</td><td>Unlocked once Stage 4 fee paid.</td></tr>';
                        }
                        
                        ?>
                    </tbody>
                </table>
            </div> <!-- //row -->

            <button type="submit" name="submit" class="btn btn-danger">Submit Page</button>
        </form>
    </div>
    <br>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
    <script type="text/javascript">
    function limitText(limitField, limitCount, limitNum) {
        if (limitField.value.length > limitNum) {
            limitField.value = limitField.value.substring(0, limitNum);
        } else {
            limitCount.value = limitNum - limitField.value.length;
        }
    }
    $("#show-hide").click(function() {
        $("#comments").toggle();
        $("#show-hide").button('toggle');
    });
    </script>
</body>

</html>