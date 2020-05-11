<?php
session_start();
include 'assets/acknowledgements.php';

$error="";
if ($_SESSION['permission'] == 'admin') {
    include('connect-db.php');
//to be deleted if found that the page still works flawless
    /*$query = "SELECT * FROM `cutabove_council` WHERE council_id = ".$_SESSION['id']." LIMIT 1";
    $result = mysqli_query($link, $query) or die(mysql_error());
    $row = mysqli_fetch_array($result);*/
} else {
    header("Location: index.php");
}

if (array_key_exists("submit", $_POST)) {
    if ($_POST['submit']== 'addNewMember') {
        if ($_POST['clg_reg']== '' or !is_numeric($_POST['clg_reg']) or strlen((string)$_POST['clg_reg'])<=6) {
            $error .= "Enter a valid Registration Number<br />";
        } else {
            $query = "SELECT `id`, `name` FROM `cutabove` WHERE clg_reg = '".mysqli_real_escape_string($link, $_POST['clg_reg'])."'";
            $result = mysqli_query($link, $query);
            if (mysqli_num_rows($result)>0) {
                $row = mysqli_fetch_array($result);
                $error.= "That Registration Number <strong>".$_POST['clg_reg']."</strong> has already been taken by <strong><a href='one-person.php?id=".$row['id']."'>".$row['name']."</a></strong>.<br />";
            }
        }
        if ($_POST['name']== '') {
            $error .= "Enter a Name<br />";
        }
        if ($_POST['dob']== '') {
            $error .= "Enter a Date of Birth<br />";
        }
        if ($error !="") {
            $error = "<p><strong>There were errors in you input:</strong></p>".$error;
        } else {
            $query = "INSERT INTO `cutabove` (`clg_reg`, `name`, `dob`, `semester`, `phone`, `phone_whatsapp`, `email`, `theme`, `comments`, `kit`) 
			VALUES (
			'".mysqli_real_escape_string($link, $_POST['clg_reg'])."',
			'".mysqli_real_escape_string($link, $_POST['name'])."',
			'".mysqli_real_escape_string($link, $_POST['dob'])."',
			'".mysqli_real_escape_string($link, $_POST['semester'])."',
			'".mysqli_real_escape_string($link, $_POST['phone'])."',
			'".mysqli_real_escape_string($link, $_POST['phone_whatsapp'])."',
			'".mysqli_real_escape_string($link, $_POST['email'])."',
			'normal',
			'<small>[". date('d-m-Y h:i:sa') ."] ".mysqli_real_escape_string($link, $_SESSION['name'])." (ID# ".mysqli_real_escape_string($link, $_SESSION['id'])."):</small>Added from Admin Interface. <b>Comment</b>: ".mysqli_real_escape_string($link, $_POST['comments'])."',
			'".mysqli_real_escape_string($link, $_POST['kit'])."');";

            if (mysqli_query($link, $query)) {
                header("Location: edit.php?id=".mysqli_insert_id($link)."&successEdit=1");
            } else {
                echo '<div id="tablediv">';
                echo "failed to edit.";
                echo '</div>';
            }
        }
    } elseif ($_POST['submit']== 'addNewCouncil') {
        //echo '<div id="tablediv">';
        //print_r($_POST);
        //echo '</div>';
        if ($_POST['username']== '') {
            $error .= "Enter a Username<br />";
        } else {
            $query = "SELECT `council_id`, `name` FROM `cutabove_council` WHERE username = '".mysqli_real_escape_string($link, $_POST['username'])."' LIMIT 1";
            $result = mysqli_query($link, $query);
            if (mysqli_num_rows($result)>0) {
                $row = mysqli_fetch_array($result);
                $error.= "That username <strong>".$_POST['username']."</strong> has already been taken by <strong>".$row['name']."</strong><br />";
            }
        }
        if ($_POST['password']== '') {
            $error .= "Enter a Password<br />";
        }
        if ($_POST['name']== '') {
            $error .= "Enter a Name<br />";
        }
        if ($error !="") {
            $error = "<p><strong>There were errors in you input:</strong></p>".$error;
        } else {
            $query = "INSERT INTO `cutabove_council` (`username`, `password`, `name`, `permission`, `theme`, `phone`, `phone_whatsapp`, `email`, `comments`) 
			VALUES (
			'".mysqli_real_escape_string($link, $_POST['username'])."', 
			'".mysqli_real_escape_string($link, $_POST['password'])."',
			'".mysqli_real_escape_string($link, $_POST['name'])."', 
			'".mysqli_real_escape_string($link, $_POST['permission'])."', 
			'normal',
			'".mysqli_real_escape_string($link, $_POST['phone'])."',
			'".mysqli_real_escape_string($link, $_POST['phone_whatsapp'])."',
			'".mysqli_real_escape_string($link, $_POST['email'])."',
			'<small>[". date('d-m-Y h:i:sa') ."] ".mysqli_real_escape_string($link, $_SESSION['name'])." (ID# ".mysqli_real_escape_string($link, $_SESSION['id'])."):</small>Added from Admin Interface. <b>Comment</b>: ".mysqli_real_escape_string($link, $_POST['comments'])."');";

            if (mysqli_query($link, $query)) {
                header("Location: add-new.php?successEdit=1");
            } else {
                echo '<div id="tablediv">';
                echo "failed to edit.";
                echo '</div>';
            }
        }
    }
}

function pre_fill_inputBoxes_member($inputName)
{
    if (array_key_exists('submit', $_POST) and $_POST['submit']== 'addNewMember') {
        if ($inputName == 'comments') {
            echo $_POST['comments'];
        } elseif ($_POST[$inputName]!='') {
            echo ' value="'.$_POST[$inputName].'" ';
        }
    }
}

function pre_fill_inputBoxes_council($inputName)
{
    if (array_key_exists('submit', $_POST) and $_POST['submit']== 'addNewCouncil') {
        if ($inputName == 'comments') {
            echo $_POST['comments'];
        } elseif ($_POST[$inputName]!='') {
            echo ' value="'.$_POST[$inputName].'" ';
        }
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <?php include 'header.php'; //css-theme detector?>

    <title>Add People Page</title>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <!--<div class="jumbotron jumbotron-fluid" id="tablediv">
		<div class="container">
			<h1 class="display-4">Add New Stuff</h1>
			<p class="lead">Add members, council members, workshop</p>
		</div>
	</div>-->
    <div class="container-fluid" id="tablediv">
        <?php
        if ($_SESSION['god'] != 1) {
            echo '<h1>Go back to <a href="council-dashboard.php">Dashboard</a> and enable <b>God Mode</b></h1>';
            die();
        }
        ?>
        <div id="error"><?php if ($error!="") {
            echo '<div class="alert alert-danger" role="alert">'.$error.'</div>';
        } ?></div>
        <div class="row">
            <div class="col-sm">
                <h2 id="addMembers">Add Member</h2>
                <form method="post">
                    <table class="table table-responsive-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th>Property</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">College Registration</th>
                                <td><input type="number" class="form-control" name="clg_reg"
                                        <?php pre_fill_inputBoxes_member('clg_reg') ?>></td>
                            </tr>
                            <tr>
                                <th scope="row">Name</th>
                                <td><input type="text" class="form-control" name="name"
                                        <?php pre_fill_inputBoxes_member('name') ?>></td>
                            </tr>
                            <tr>
                                <th scope="row">DOB (DD-MM-YYYY)</th>
                                <td><input type="date" class="form-control" name="dob"
                                        <?php pre_fill_inputBoxes_member('dob') ?>></td>
                            </tr>
                            <tr>
                                <th scope="row">Semester</th>
                                <td><input type="text" class="form-control" name="semester" maxlength="1"
                                        placeholder="Enter 1-9/Int/PG1/PG2/PG3"
                                        <?php pre_fill_inputBoxes_member('semester') ?>></td>
                            </tr>
                            <tr>
                                <th scope="row">Phone (Call)</th>
                                <td><input type="number" class="form-control" name="phone"
                                        <?php pre_fill_inputBoxes_member('phone') ?>></td>
                            </tr>
                            <tr>
                                <th scope="row">Phone (Whatsapp)</th>
                                <td><input type="number" class="form-control" name="phone_whatsapp"
                                        <?php pre_fill_inputBoxes_member('phone_whatsapp') ?>></td>
                            </tr>
                            <tr>
                                <th scope="row">E-mail</th>
                                <td><input type="email" class="form-control" name="email"
                                        <?php pre_fill_inputBoxes_member('email') ?>></td>
                            </tr>
                            <tr>
                                <th scope="row">Status</th>
                                <td>
                                    <select class="form-control" name="kit">
                                        <?php echo '
										<option value="0"'; if (isset($_POST) and $_POST['kit'] == '0') {
            echo 'selected';
        } echo '>Approval Pending</option>
										<option value="1"'; if (isset($_POST) and $_POST['kit'] == '1') {
            echo 'selected';
        } echo '>Approved</option>
										<option value="2"'; if (isset($_POST) and $_POST['kit'] == '2') {
            echo 'selected';
        } echo '>Rejected</option>
										<option value="3"'; if (isset($_POST) and $_POST['kit'] == '3') {
            echo 'selected';
        } echo '>Debarred</option>
										<option value="4"'; if (isset($_POST) and $_POST['kit'] == '4') {
            echo 'selected';
        } echo '>Completed</option>
										<option value="5"'; if (isset($_POST) and $_POST['kit'] == '5') {
            echo 'selected';
        } echo '>Discontinued</option>
										<option value="6"'; if (isset($_POST) and $_POST['kit'] == '6') {
            echo 'selected';
        } echo '>Status Unknown</option>'; ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Comments (Only admins can view)</th>
                                <td><textarea class="form-control"
                                        name="comments"><?php pre_fill_inputBoxes_member('comments') ?></textarea></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><button type="submit" name="submit" class="btn btn-danger" value="addNewMember">Add
                                        Member</button></td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="col-sm">
                <h2 id="addCouncil">Add Core Member</h2>
                <form method="post">
                    <table class="table table-responsive-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th>Property</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">Username</th>
                                <td><input type="text" class="form-control" name="username"
                                        <?php pre_fill_inputBoxes_council('username') ?>></td>
                            </tr>
                            <tr>
                                <th scope="row">Password</th>
                                <td><input type="text" class="form-control" name="password"
                                        <?php pre_fill_inputBoxes_council('password') ?>></td>
                            </tr>
                            <tr>
                                <th scope="row">Name</th>
                                <td><input type="text" class="form-control" name="name"
                                        <?php pre_fill_inputBoxes_council('name') ?>></td>
                            </tr>
                            <tr>
                                <th scope="row">Permission</th>
                                <td>
                                    <select class="form-control" name="permission" id="permission">
                                        <?php echo '<option value="admin" '; if (isset($_POST) and $_POST['permission'] == 'admin') {
            echo 'selected';
        } echo '>Admin</option>
										<option selected value="supervisor" '; if (isset($_POST) and $_POST['permission'] == 'supervisor') {
            echo 'selected';
        } echo '>Supervisor</option>'; ?>
                                    </select>
                                </td>
                            </tr>
                            <!--<tr id="group_allotted"><th scope="row">Group</th><td><select class="form-control" name="group_allotted"><option value="a">A</option><option value="b">B</option><option value="c">C</option><option value="d">D</option><option value="0" selected hidden>Allot a group</option></select></td></tr>-->
                            <tr>
                                <th scope="row">Phone (Call)</th>
                                <td><input type="number" class="form-control" name="phone"
                                        <?php pre_fill_inputBoxes_council('phone') ?>></td>
                            </tr>
                            <tr>
                                <th scope="row">Phone (Whatsapp)</th>
                                <td><input type="number" class="form-control" name="phone_whatsapp"
                                        <?php pre_fill_inputBoxes_council('phone_whatsapp') ?>></td>
                            </tr>
                            <tr>
                                <th scope="row">E-mail</th>
                                <td><input type="text" class="form-control" name="email"
                                        <?php pre_fill_inputBoxes_council('email') ?>></td>
                            </tr>
                            <tr>
                                <th scope="row">Comments (Only admins can view)</th>
                                <td><textarea class="form-control"
                                        name="comments"><?php pre_fill_inputBoxes_council('comments') ?></textarea></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><button type="submit" name="submit" class="btn btn-danger" value="addNewCouncil">Add
                                        Council Member</button></td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>

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
    // Makes sure the code contained doesn't run until all the DOM elements have loaded
    $(function() {
        $('#permission').change(function() {
            var val = $(this).val();
            if (val === 'supervisor') {
                $('#group_allotted').show();
            } else {
                $('#group_allotted').hide();
            }
        });
    });
    </script>
</body>

</html>