<?php
session_start();
include 'assets/acknowledgements.php';


$error="";
if ($_SESSION['permission'] == 'admin') {
    header("Location: add-new.php");
} else {
    include('connect-db.php');
    
    $query = "SELECT `value` FROM `cutabove_misc` WHERE property = 'registrations' LIMIT 1";
    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_array($result);
    if ($row['value']=='0') {
        echo '<html><head><meta http-equiv="refresh" content="5;url=index.php"></head><body><h1>Registrations disabled.</h1>Contact Admins for registration or go back to <a href="index.php">Login Page</a><br /><small>Redirecting there shortly.</small></body></html>';
        die();
    }
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
			'0');";

            if (mysqli_query($link, $query)) {
                header("Location: registrations.php?id=".mysqli_insert_id($link)."&successEdit=1");
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
?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Cutabove- Registration</title>

    <style>
    .back {
        float: right;
        font-size: 1.5rem;
        font-weight: 700;
        text-shadow: 0 1px 0 #fff;
        opacity: .9;
    }
    </style>
</head>

<body>
    <div class="container" id="tablediv">
        <div class="row">
            <div class="col-sm">
                <a href="../" class="back">Go Back</a>
                <h2 id="addMembers">New Member Registration</h2>
                <?php
                if ($error!="") {
                    echo '<div class="alert alert-danger mt-1" role="alert">'.$error.'</div>';
                }

                if (array_key_exists("id", $_GET) and array_key_exists("successEdit", $_GET)) {
                    echo '<div class="alert alert-success mt-1" role="alert">Registration successful.<br />ID# '.$_GET['id'].'</div>';
                }
                ?>
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
                                <td><input type="text" class="form-control" name="semester" maxlength="3"
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
                                <th scope="row">Comments (if any)</th>
                                <td><textarea class="form-control"
                                        name="comments"><?php pre_fill_inputBoxes_member('comments') ?></textarea></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><button type="submit" name="submit" class="btn btn-danger"
                                        value="addNewMember">Register</button></td>
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
</body>

</html>