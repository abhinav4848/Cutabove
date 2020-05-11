<?php
session_start();
date_default_timezone_set("Asia/Kolkata");

include 'assets/acknowledgements.php';


//if admin has accessed the page, see if the id has been sent by $_GET, then show the user data by $_GET['id']
//if user has logged in, show his own data using $_SESSION['id']
if (array_key_exists("id", $_SESSION) and $_SESSION['permission'] == 'admin') {
    if (array_key_exists("id", $_GET) and $_GET['id']!='') {
        /* useless code since now, a redirect is done instead. Earlier, the page was displayed
        include('connect-db.php');
        $query = "SELECT * FROM `cutabove` WHERE id = '".mysqli_real_escape_string($link, $_GET['id'])."' LIMIT 1";
        $result = mysqli_query($link, $query) or die(mysql_error());
        $row = mysqli_fetch_array($result); //since only one row is fetched, no need to run a fancy while loop like we did in all-people.php
        */
        header("Location: edit.php?id=".$_GET['id']);
    } else {
        echo "You didn't specify which id to fetch. You are admin. Use the <a href='all-people.php'>Admin mode</a> to click on a user for details.";
        die();
    }
} elseif (array_key_exists("id", $_SESSION) and $_SESSION['permission']== 'member') {
    include('connect-db.php');
    $query = "SELECT * FROM `cutabove` WHERE id = '".mysqli_real_escape_string($link, $_SESSION['id'])."' LIMIT 1";
    $result = mysqli_query($link, $query) or die(mysql_error());
    $row = mysqli_fetch_array($result);
} else {
    header("Location: index.php");
}


if (array_key_exists("submit", $_POST)) {
    //echo '<div id="tablediv">';
    //print_r($_POST);
    //echo '</div>';
    $_SESSION['theme'] = mysqli_real_escape_string($link, $_POST['theme']);

    $query = "UPDATE `cutabove` 
	SET `semester` = '".mysqli_real_escape_string($link, $_POST['semester'])."', 
	`phone` = '".mysqli_real_escape_string($link, $_POST['phone'])."', 
	`phone_whatsapp` = '".mysqli_real_escape_string($link, $_POST['phone_whatsapp'])."', 
	`email` = '".mysqli_real_escape_string($link, $_POST['email'])."',  
	`theme` = '".mysqli_real_escape_string($link, $_POST['theme'])."', 
	`edited_by` = '".$_SESSION['name']."', 
	`last_edited_at` = '". date('d-m-Y h:i:sa') ."'
	WHERE `cutabove`.`id` = '".$_SESSION['id']."' LIMIT 1";

    if (mysqli_query($link, $query)) {
        //success
        header("Location: member-self-edit.php?id=".$_SESSION['id']."&successEdit=1");
    } else {
        echo '<div id="tablediv">';
        echo "failed to edit.";
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

    <title>Edit Personal Details</title>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div id="tablediv" class="container-fluid">
        <form method="post">
            <?php
          //display general data
            echo '<div class="row">';
            echo '<table class="table table-responsive-sm">';
            echo '<thead class="thead-dark"><tr> <th>Property</th> <th>Value</th> </tr></thead>';
            echo '<tbody>';
            //echo '<tr><th scope="row">ID</th><td>'. $row['id'] .'</td></tr>';
            echo '<tr><th scope="row">College Registration</th><td>' . $row['clg_reg'] . '</td></tr>';
            echo '<tr><th scope="row">Name</th><td>' . $row['name'] . '</td></tr>';
            echo '<tr><th scope="row">DOB (DD-MM-YYYY)</th><td>' . date('d-M-Y', strtotime($row['dob'])) . '</td></tr>';
            echo '<tr><th scope="row">Semester</th><td><input type="text" class="form-control" name="semester" maxlength="1" value="' . $row['semester'] . '" placeholder="Enter 1-9"></td></tr>';
            echo '<tr><th scope="row">Phone (Call)</th><td><input type="text" class="form-control" name="phone" value="' . $row['phone'] . '"></td></tr>';
            echo '<tr><th scope="row">Phone (Whatsapp)</th><td><input type="text" class="form-control" name="phone_whatsapp" value="' . $row['phone_whatsapp'] . '"></td></tr>';
            echo '<tr><th scope="row">E-mail</th><td><input type="text" class="form-control" name="email" value="' . $row['email'] . '"></td></tr>';
            //theme select
            echo '<tr><th scope="row">Theme</th><td><select class="form-control" name="theme">
            <option '; if ($row['theme'] == 'cyborg') {
                echo 'selected';
            } echo' value="cyborg">Cyborg</option>
            <option '; if ($row['theme'] == 'darkly') {
                echo 'selected';
            } echo' value="darkly">Darkly</option>
            <option '; if ($row['theme'] == 'litera') {
                echo 'selected';
            } echo' value="litera">Litera</option>
            <option '; if ($row['theme'] == 'normal') {
                echo 'selected';
            } echo' value="normal">Normal</option>
            <option '; if ($row['theme'] == 'no-one') {
                echo 'selected';
            } echo' value="no-one">No-one</option>
			</select></td></tr>';
            echo '</tbody></table>';
            echo "</div>";

            ?>
            <button type="submit" name="submit" class="btn btn-<?php echo $_SESSION['colour'];?>">Submit Page</button>
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
</body>

</html>