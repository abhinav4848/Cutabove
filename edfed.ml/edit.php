<?php
session_start();
$stg1c=0; //for enabling stage 2, this value is checked instead of database
$stg2c=$stg3c=$stg4c=0;
$error = '';
if ($_SESSION['permission'] == 'admin') {
    if (array_key_exists("id", $_GET) and is_numeric($_GET['id'])) {
        include('connect-db.php');
        $_GET['id'] = mysqli_real_escape_string($link, $_GET['id']);

        $query = "SELECT * FROM `cutabove` WHERE id = '".$_GET['id']."' LIMIT 1";
        $result = mysqli_query($link, $query) or die(mysql_error());
        $row = mysqli_fetch_array($result);
    } else {
        echo "You didn't specify which id to fetch (it should be a number). You are admin. Use the <a href='all-people.php'>Admin mode</a> to click on a user for details.";
        die();
    }
} else {
    header("Location: index.php?redirect=".$_SERVER['REQUEST_URI']);
}
if (array_key_exists("submit", $_POST)) {
    //echo '<div id="tablediv">';
    //print_r($_POST);
    //echo '</div>';
    
    //ERROR checking
    if (array_key_exists('clg_reg', $_POST) and $row['clg_reg']!=$_POST['clg_reg']) {
        // check if clg_reg was changed. If so, make sure the new number isn't already taken
        $query_error = "SELECT `id`, `name` FROM `cutabove` WHERE clg_reg = '".mysqli_real_escape_string($link, $_POST['clg_reg'])."' LIMIT 1";
        $result_error = mysqli_query($link, $query_error);
        if (mysqli_num_rows($result_error)>0) {
            $row_error = mysqli_fetch_array($result_error);
            $error.= "That Registration Number <strong>".$_POST['clg_reg']."</strong> has already been taken by <strong><a href='one-person.php?id=".$row_error['id']."'>".$row_error['name']."</a></strong>.";
        }
    }
    
    //UPDATING database
    if ($error=='') {
        if (array_key_exists('kit', $_POST) and $_POST['kit']!=$row['kit']) {
            //update kit separately cuz if changed from another value to 1 (approved), it would cause a loss of previously attended workshop data. (cuz the form wouldn't exist if kit!=1, so nil values would be submitted when changing kit)
            $query = "UPDATE `cutabove` 
            SET `kit` = '".mysqli_real_escape_string($link, $_POST['kit'])."'
            WHERE `cutabove`.`id` = '".$_GET['id']."' LIMIT 1";
        } else {
            //$dob = DateTime::createFromFormat('Y-m-d', mysqli_real_escape_string($link, $_POST['dob']))->format('d-m-Y');
            $query = "UPDATE `cutabove` 
            SET `clg_reg` = '".mysqli_real_escape_string($link, $_POST['clg_reg'])."',
            `name` = '".mysqli_real_escape_string($link, $_POST['name'])."', 
            `dob` = '".mysqli_real_escape_string($link, $_POST['dob'])."', 
            `semester` = '".mysqli_real_escape_string($link, $_POST['semester'])."',
            `phone` = '".mysqli_real_escape_string($link, $_POST['phone'])."', 
            `phone_whatsapp` = '".mysqli_real_escape_string($link, $_POST['phone_whatsapp'])."', 
            `email` = '".mysqli_real_escape_string($link, $_POST['email'])."', 
            `comments` = '".mysqli_real_escape_string($link, $row['comments'])." <br /><small>[". date('d-m-Y h:i:sa') ."] ".mysqli_real_escape_string($link, $_SESSION['name'])." (ID# ".mysqli_real_escape_string($link, $_SESSION['id'])."):</small> ". mysqli_real_escape_string($link, $_POST['comments']) ."', 
            `stg1w1` = '".mysqli_real_escape_string($link, $_POST['stg1w1'])."', 
            `stg1w2` = '".mysqli_real_escape_string($link, $_POST['stg1w2'])."', 
            `stg1w3` = '".mysqli_real_escape_string($link, $_POST['stg1w3'])."', 
            `stg1w4` = '".mysqli_real_escape_string($link, $_POST['stg1w4'])."',  
            `stg2w1` = '".mysqli_real_escape_string($link, $_POST['stg2w1'])."', 
            `stg2w2` = '".mysqli_real_escape_string($link, $_POST['stg2w2'])."', 
            `stg2w3` = '".mysqli_real_escape_string($link, $_POST['stg2w3'])."', 
            `stg2w4` = '".mysqli_real_escape_string($link, $_POST['stg2w4'])."', 
            `stg2w5` = '".mysqli_real_escape_string($link, $_POST['stg2w5'])."', 
            `stg3w1` = '".mysqli_real_escape_string($link, $_POST['stg3w1'])."', 
            `stg3w2` = '".mysqli_real_escape_string($link, $_POST['stg3w2'])."', 
            `stg3w3` = '".mysqli_real_escape_string($link, $_POST['stg3w3'])."', 
            `stg3w4` = '".mysqli_real_escape_string($link, $_POST['stg3w4'])."', 
            `stg3w5` = '".mysqli_real_escape_string($link, $_POST['stg3w5'])."',           
            `stg4w1` = '".mysqli_real_escape_string($link, $_POST['stg4w1'])."', 
            `stg4w2` = '".mysqli_real_escape_string($link, $_POST['stg4w2'])."', 
            `stg4w3` = '".mysqli_real_escape_string($link, $_POST['stg4w3'])."', 
            `stg4w4` = '".mysqli_real_escape_string($link, $_POST['stg4w4'])."', 
            `stg4w5` = '".mysqli_real_escape_string($link, $_POST['stg4w5'])."', 
            `stg5w1` = '".mysqli_real_escape_string($link, $_POST['stg5w1'])."', 
            `stg5w2` = '".mysqli_real_escape_string($link, $_POST['stg5w2'])."', 
            `stg5w3` = '".mysqli_real_escape_string($link, $_POST['stg5w3'])."', 
            `stg5w4` = '".mysqli_real_escape_string($link, $_POST['stg5w4'])."', 
            `stg5w5` = '".mysqli_real_escape_string($link, $_POST['stg5w5'])."', 
            `edited_by` = '".$_SESSION['name']."', 
            `last_edited_at` = '". date('d-m-Y h:i:sa') ."'
            WHERE `cutabove`.`id` = '".$_GET['id']."' LIMIT 1";
        }
    
        if (mysqli_query($link, $query)) {
            //success
            header("Location: edit.php?id=".$_GET['id']."&successEdit=1");
        } else {
            echo '<div id="tablediv">';
            echo "failed to edit.";
            echo '</div>';
        }
    }
}

function generateStage($stg)
{
    global $row;
    if ($row['stg'.$stg.'fee'] != '0') {
        if ($stg==1) {
            $max=5;
        } else {
            $max=6;
        }
        
        for ($i=1; $i < $max; $i++) {
            echo '<tr><th scope="row">Workshop '.$i.'</th><td><select class="form-control" name="stg'.$stg.'w'.$i.'">';
            if ($row['stg'.$stg.'w'.$i] != '0') {
                echo '<option selected value="'.$row['stg'.$stg.'w'.$i].'">Yes #'.$row['stg'.$stg.'w'.$i].'</option><option value="0">No</option>';
            } else {
                echo '<option value="1">Yes</option><option selected value="0">No</option>';
            }
            echo '</select></td></tr>';
        }
    } else {
        echo '<tr><th scope="row">Fee Not Paid</th><td>x</td></tr>';
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

    <title>Edit User</title>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div id="tablediv" class="container-fluid">
        <div id="error"><?php if ($error!="") {
    echo '<div class="alert alert-danger" role="alert">'.$error.'</div>';
} ?></div>
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
                            <td><input type="text" readonly class="form-control-plaintext" name="clg_reg"
                                    value="<?=$row['clg_reg']?>"></td>
                        </tr>
                        <tr>
                            <th scope="row">Name</th>
                            <td><input type="text" class="form-control" name="name" value="<?=$row['name']?>"></td>
                        </tr>
                        <tr>
                            <th scope="row">DOB (DD-MM-YYYY)</th>
                            <td><input type="date" class="form-control" name="dob" value="<?=$row['dob']?>"></td>
                        </tr>
                        <tr>
                            <th scope="row">Semester</th>
                            <td><input type="text" class="form-control" name="semester" maxlength="3"
                                    value="<?=$row['semester']?>" placeholder="Enter 1-9/Int/PG1/PG2/PG3"></td>
                        </tr>
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
                                <small class="text-muted"><a href="help.php#kit" target="_blank">Click here</a> for
                                    help. <br />No other value will be changed on submitting, if this value was
                                    changed.</small>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Phone (Call)</th>
                            <td><input type="number" class="form-control" name="phone" value="<?=$row['phone']?>"></td>
                        </tr>
                        <tr>
                            <th scope="row">Phone (Whatsapp)</th>
                            <td><input type="number" class="form-control" name="phone_whatsapp"
                                    value="<?=$row['phone_whatsapp']?>">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">E-mail</th>
                            <td><input type="email" class="form-control" name="email" value="<?=$row['email']?>"></td>
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
                                    onKeyUp="limitText(this.form.comments,this.form.countdown,280);">
                                    </textarea>
                                <font size="1">You have <input readonly type="text" name="countdown" size="3"
                                        value="280">
                                    characters left. Same as Twitter</font>
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
                            <th>Stage 1</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        //do initial verification that user is approved
                        if ($row['kit'] == '1') {
                            generateStage(1);
                            
                            if ($row['stg1fee'] and $row['stg1w1'] != '0' and $row['stg1w2'] != '0' and $row['stg1w3'] != '0' and $row['stg1w4'] != '0') {
                                //mark stage 1 completed
                                $stg1c='1';
                            }
                        } else {
                            echo "<tr><td>User isn't approved.</td><td></td></tr>";
                        }
                        
                        ?>
                    </tbody>
                </table>

                <!--display stage 2 data-->
                <table class="table col-sm">
                    <thead>
                        <tr class="thead-dark">
                            <th>Stage 2</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($stg1c != '0') {
                            //generate stage 2 only if stage 1 is completed
                            generateStage(2);
                            if ($row['stg2w1'] != '0' and $row['stg2w2'] != '0' and $row['stg2w3'] != '0' and $row['stg2w4'] != '0' and $row['stg2w5'] != '0') {
                                //mark stage 2 completed
                                $stg2c='1';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div> <!-- /.row -->

            <div class="row">
                <!--display stage 3 data-->
                <table class="table col-sm">
                    <thead>
                        <tr class="thead-dark">
                            <th>Stage 3</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($stg2c != '0') {
                            generateStage(3);
                            if ($row['stg3w1'] != '0' and $row['stg3w2'] != '0' and $row['stg3w3'] != '0' and $row['stg3w4'] != '0' and $row['stg3w5'] != '0') {
                                $stg3c='1';
                            }
                        }
                        ?>
                    </tbody>
                </table>

                <!--display stage 4 data-->
                <table class="table col-sm">
                    <thead>
                        <tr class="thead-dark">
                            <th>Stage 4</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($stg3c != '0') {
                            generateStage(4);
                            if ($row['stg4w1'] != '0' and $row['stg4w2'] != '0' and $row['stg4w3'] != '0' and $row['stg4w4'] != '0') {
                                $stg4c='1';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div> <!-- /.row -->

            <div class="row">
                <!--display stage 5 data-->
                <table class="table col-sm">
                    <thead>
                        <tr class="thead-dark">
                            <th>Stage 5</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($stg4c != '0') {
                            for ($i=1; $i < 6; $i++) {
                                if ($row['stg5w'.$i.'fee'] != '0') {
                                    echo '<tr><th scope="row">Workshop '.$i.'</th><td><select class="form-control" name="stg5w'.$i.'">';
                                    if ($row['stg5w'.$i] != '0') {
                                        echo '<option selected value="'.$row['stg5w'.$i].'">Yes #'.$row['stg5w'.$i].'</option><option value="0">No</option>';
                                    } else {
                                        echo '<option value="1">Yes</option><option selected value="0">No</option>';
                                    }
                                    echo '</select></td></tr>';
                                } else {
                                    echo '<tr><th scope="row">Workshop '.$i.'</th><td>Fee Not Paid</td></tr>';
                                }
                            }
                        }
                        
                        ?>
                    </tbody>
                </table>
            </div>

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