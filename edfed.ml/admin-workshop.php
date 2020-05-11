<?php
session_start();
include 'assets/acknowledgements.php';


if ($_SESSION['permission'] == 'admin') {
    include('connect-db.php');
} else {
    header("Location: index.php");
}
$error= '';
if (array_key_exists("submit", $_POST)) {
    //echo '<div id="tablediv">';
    //print_r($_POST);
    //echo '</div>';
    if (!$_POST['date']) {
        $error .= "Date Invalid<br />";
    }
    if ($_POST['level_name'] == 'Choose Level') {
        $error .= "Choose Level<br />";
    }
    if ($error !="") {
        $error = "<p><strong>There were errors in you input:</strong></p>".$error;
    } else {
        //$date = DateTime::createFromFormat('Y-m-d\TH:i', $_POST['date'])->format('Y-m-d H:i:s');
        // Both work equally well
        $date = date('Y-m-d H:i:s', strtotime($_POST['date']));
        
        //get the default feedback link to be inserted while creating the workshop
        $query_misc_feedback_link = "SELECT `value` FROM `cutabove_misc` WHERE `property` = '".mysqli_real_escape_string($link, $_POST['level_name'])."_feedback_link' LIMIT 1";
        $result_misc_feedback_link = mysqli_query($link, $query_misc_feedback_link);
        $row_misc_feedback_link = mysqli_fetch_array($result_misc_feedback_link);

        //get the default bonus file link to be inserted while creating the workshop
        $query_misc_bonus_files = "SELECT `value` FROM `cutabove_misc` WHERE `property` = '".mysqli_real_escape_string($link, $_POST['level_name'])."_bonus_files' LIMIT 1";
        $result_misc_bonus_files = mysqli_query($link, $query_misc_bonus_files);
        $row_misc_bonus_files = mysqli_fetch_array($result_misc_bonus_files);

        $query = "INSERT INTO `cutabove_workshop` (`date`, `level_name`, `feedback_link`, `bonus_files`, `memorisable_name`) 
		VALUES (
		'".mysqli_real_escape_string($link, $date)."', 
		'".mysqli_real_escape_string($link, $_POST['level_name'])."',
		'".mysqli_real_escape_string($link, $row_misc_feedback_link['value'])."',
		'".mysqli_real_escape_string($link, $row_misc_bonus_files['value'])."', 
		'".mysqli_real_escape_string($link, $_POST['memorisable_name'])."')";

        if (mysqli_query($link, $query)) {
            header("Location: admin-workshop.php?successEdit=1");
        } else {
            echo '<div id="tablediv">';
            echo "failed to edit.";
            echo '</div>';
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

    <?php include 'header.php'; //css-theme detector
    mediaQueryforTable(); //function in header.php?>

    <title>Admin's workshop</title>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div id="tablediv" class="container-fluid">
        <h2>Create New Workshop</h2>
        <div id="error"><?php if ($error!="") {
        echo '<div class="alert alert-danger" role="alert">'.$error.'</div>';
    } ?></div>
        <form method="post">
            <div class="form-group">
                <input type="datetime-local" name="date" class="form-control">
            </div>
            <div class="form-group">
                <select class="form-control" name="level_name" required>
                    <option selected hidden>Choose Level</option>
                    <optgroup label="Stage 1">
                        <option value="stg1w1">Workshop 1</option>
                        <option value="stg1w2">Workshop 2</option>
                        <option value="stg1w3">Workshop 3</option>
                        <option value="stg1w4">Workshop 4</option>
                    </optgroup>
                    <optgroup label="Stage 2">
                        <option value="stg2w1">Workshop 1</option>
                        <option value="stg2w2">Workshop 2</option>
                        <option value="stg2w3">Workshop 3</option>
                        <option value="stg2w4">Workshop 4</option>
                        <option value="stg2w5">Workshop 5</option>
                    </optgroup>
                    <optgroup label="Stage 3">
                        <option value="stg3w1">Workshop 1</option>
                        <option value="stg3w2">Workshop 2</option>
                        <option value="stg3w3">Workshop 3</option>
                        <option value="stg3w4">Workshop 4</option>
                        <option value="stg3w5">Workshop 5</option>
                    </optgroup>
                    <optgroup label="Stage 4">
                        <option value="stg4w1">Workshop 1</option>
                        <option value="stg4w2">Workshop 2</option>
                        <option value="stg4w3">Workshop 3</option>
                        <option value="stg4w4">Workshop 4</option>
                        <option value="stg4w5">Workshop 5</option>
                    </optgroup>
                    <optgroup label="Stage 5">
                        <option value="stg5w1">Workshop 1</option>
                        <option value="stg5w2">Workshop 2</option>
                        <option value="stg5w3">Workshop 3</option>
                        <option value="stg5w4">Workshop 4</option>
                        <option value="stg5w5">Workshop 5</option>
                    </optgroup>
                </select>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="memorisable_name"
                    placeholder="Enter a name/number for this workshop">
            </div>
            <button type="submit" name="submit" class="btn btn-danger">Create Workshop</button>
        </form>
    </div>
    <br>
    <h2>Upcoming Workshops</h2>
    <table class="table table-striped table-responsive-sm" id="detailsTable">
        <thead class="thead-dark">
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Date</th>
                <th scope="col"><abbr title="Stage Name + User memorisable name">Name</abbr></th>
                <th scope="col">Core Enrolled</th>
                <th scope="col">Members Enrolled</th>
                <th scope="col">Actions</th>
                <th scope="col">Last Edited By</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT * FROM `cutabove_workshop` ORDER BY `date` ASC LIMIT 20";
            $result = mysqli_query($link, $query) or die(mysql_error());
            while ($row = mysqli_fetch_array($result)) {
                //echo out the contents of each row into a table
                if ($row['completed'] == '0') {
                    echo '<tr>';
                    echo '<td scope="row" data-title="ID">' . $row['workshop_id'] . '</td>';
                    echo '<td data-title="Date">' . date("d-M-Y h:i A", strtotime($row['date'])) . '</td>';
                    echo '<td data-title="Name">' . $row['level_name'] . '-';
                    if ($row['memorisable_name'] == '') {
                        echo '<span class = "text-danger" >Not Assigned</span>';
                    } else {
                        echo '<span class = "text-success" style="">'.$row['memorisable_name'].'</span>';
                    }
                    echo '</td>';

                    $supervisorcount = $membercount = 0;
                    for ($i=1; $i < 31 ; $i++) {
                        if ($row['member_id_'.$i.'']!= 0) {
                            $membercount++;
                        }
                    }

                    for ($i=1; $i < 21 ; $i++) {
                        if ($row['supervisor_id_'.$i.'']!= 0) {
                            $supervisorcount++;
                        }
                    }
                    echo '<td data-title="Core">'.$supervisorcount.'/20</td>';
                    echo '<td data-title="Members">'.$membercount.'/30</td>';
                    echo '<td><a href="supervisor_workshop.php?workshop_id='.$row['workshop_id'].'">View</a> | <a href="'.$row['feedback_link'].'" target="_blank">Feedback</a> | <a href="'.$row['bonus_files'].'" target="_blank">Files</a>';
                    if ($_SESSION['god']== 1) {
                        echo ' | <a href="deletion.php?wk_id='.$row['workshop_id'].'">Delete</a>';
                    }
                    echo '</td><td data-title="Last Edit By">';
                    $query1 = "SELECT * FROM `cutabove_council` WHERE council_id = '".$row['supervisor_id']."' LIMIT 1";
                    $result1 = mysqli_query($link, $query1);
                    $row1 = mysqli_fetch_array($result1);
                    if ($row1['permission'] == 'admin') {
                        echo '<a href="edit-council.php?id='.$row1['council_id'].'" class="badge badge-danger">'.$row1['name'].'</a>';
                    } else {
                        echo '<a href="edit-council.php?id='.$row1['council_id'].'" class="badge badge-primary">'.$row1['name'].'</a>';
                    }
                    echo "</td></tr>";
                }
            }
            ?>
        </tbody>
    </table>

    <h2>Completed Workshops</h2>
    <table class="table table-striped table-responsive-sm" id="detailsTable">
        <thead class="thead-dark">
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Date</th>
                <th scope="col"><abbr title="Stage Name + User memorisable name">Name</abbr></th>
                <th scope="col">Core Attended</th>
                <th scope="col">Members Attended</th>
                <th scope="col">View</th>
                <th scope="col">Last Edited By</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT * FROM `cutabove_workshop` ORDER BY `date` DESC LIMIT 15";
            $result = mysqli_query($link, $query) or die(mysql_error());
            while ($row = mysqli_fetch_array($result)) {
                //echo out the contents of each row into a table
                if ($row['completed'] == '1') {
                    echo '<tr>';
                    echo '<td scope="row" data-title="ID">' . $row['workshop_id'] . '</td>';
                    echo '<td data-title="Date">' . date("d-M-Y h:i A", strtotime($row['date'])) . '</td>';
                    echo '<td data-title="Name">' . $row['level_name'] . '-';
                    if ($row['memorisable_name'] == '') {
                        echo '<span class = "text-danger" >Not Assigned</span>';
                    } else {
                        echo '<span class = "text-success" style="">'.$row['memorisable_name'].'</span>';
                    }
                    echo '</td>';

                    $supervisorcount = $membercount = 0;
                    for ($i=1; $i < 21 ; $i++) {
                        if ($row['supervisor_id_'.$i.'']!= 0) {
                            $supervisorcount++;
                        }
                        if ($row['member_id_'.$i.'']!= 0) {
                            $membercount++;
                        }
                    }
                    $supervisorcount_a = $membercount_a = 0;
                    for ($i=1; $i < 21 ; $i++) {
                        if ($row['supervisor_id_'.$i.'a']!= 0) {
                            $supervisorcount_a++;
                        }
                        if ($row['member_id_'.$i.'a']!= 0) {
                            $membercount_a++;
                        }
                    }
                    echo '<td data-title="Core">'.$supervisorcount_a.'/'.$supervisorcount.'</td>';
                    echo '<td data-title="Members">'.$membercount_a.'/'.$membercount.'</td>';
                    echo '<td data-title="View"><a href="supervisor_workshop.php?workshop_id='.$row['workshop_id'].'">View</a></td>';
                    $query1 = "SELECT * FROM `cutabove_council` WHERE council_id = '".$row['supervisor_id']."' LIMIT 1";
                    $result1 = mysqli_query($link, $query1);
                    $row1 = mysqli_fetch_array($result1);
                    echo '<td data-title="Last Edit By">';
                    if ($row1['permission'] == 'admin') {
                        echo '<a href="edit-council.php?id='.$row1['council_id'].'" class="badge badge-danger">'.$row1['name'].'</a>';
                    } else {
                        echo '<a href="edit-council.php?id='.$row1['council_id'].'" class="badge badge-primary">'.$row1['name'].'</a>';
                    }
                    echo "</td></tr>";
                }
            }
            ?>
        </tbody>
    </table>

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