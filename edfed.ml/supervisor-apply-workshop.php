<?php
session_start();
date_default_timezone_set("Asia/Kolkata");
$getid = ''; //doing this since there are 2 ways a member's id can be worked on (admin & supervisor using get['id'] vs Member using session['id']. I needn't worry about which method is used once i set $getid to the id value through either method. Rest of the program uses getid ather than get['id'] or session['id']

//if admin has accessed the page, see if the id has been sent by $_GET, then show the user data by $_GET['id']
//if supervisor has logged in, show his own data using $_SESSION['id']
if ($_SESSION['permission'] == 'admin') {
    if (array_key_exists("id", $_GET) and $_GET['id']!='') {
        include('connect-db.php');
        $query = "SELECT * FROM `cutabove_council` WHERE council_id = '".mysqli_real_escape_string($link, $_GET['id'])."' LIMIT 1";
        $result = mysqli_query($link, $query) or die(mysql_error());
        if (mysqli_num_rows($result)==0) {
            echo "The user does not exist. Use the <a href='all-people.php'>Admin mode</a> to click on a valid user for details.";
            die();
        } else {
            $row = mysqli_fetch_array($result);
            $getid = $_GET['id'];
        }
    } else {
        echo "You didn't specify which id to fetch. You are admin. Use the <a href='all-people.php'>Admin mode</a> to click on a user for details.";
        die();
    }
} elseif ($_SESSION['permission'] == 'supervisor' and array_key_exists("id", $_SESSION)) {
    if (array_key_exists("id", $_GET) and $_GET['id']!=$_SESSION['id']) {
        //this is done so that it's clear the person's own page is being accessed, not someone else's
        //header("Location: supervisor-attendance.php");
        echo '<html><head><meta http-equiv="refresh" content="5;url=supervisor-apply-workshop.php?id.php"></head><body>Redirecting to your own workshop page</body></html>';
        die();
    }
    include('connect-db.php');
    $query = "SELECT * FROM `cutabove_council` WHERE council_id = '".mysqli_real_escape_string($link, $_SESSION['id'])."' LIMIT 1";
    $result = mysqli_query($link, $query) or die(mysql_error());
    $row = mysqli_fetch_array($result);
    $getid = $_SESSION['id'];
} else {
    header("Location: index.php?redirect=".$_SERVER['REQUEST_URI']);
}

function defineWorkshopRow($stagename, $stg_w_, $row, $getid, $link)
{
    /* Eg of arguments passed:
    $stagename: Just the workshop name like Workshop 1, Workshop 2...
    $stg_w_: stg1w1, stg1w2...
    $row: $row is for the initially extracted data at start of this page. Basically every detail of the supervisor is imported into this function. (Working with outside variables inside a function- https://stackoverflow.com/a/2531234/2365231)
    $link: database link
    */
    echo '<tr><th scope="row">'.$stagename.'</th>';
    
    //find workshops that have not been completed and are of the desired level
    $query1 = "SELECT * FROM `cutabove_workshop` WHERE completed = 0 AND level_name = '".$stg_w_."'";
    $result1 = mysqli_query($link, $query1);
    if (mysqli_num_rows($result1)!= 0) {
        echo '<td><select class="form-control" name="'.$stg_w_.'">';
        echo '<option value= "0" hidden>Choose a date</option>';
        $noVacancy = 0;
        while ($row1 = mysqli_fetch_array($result1)) {
            //enter a workshop
            $seats_avaiable = 0;
            $query_find_pre_applied_status = "SELECT * FROM `cutabove_workshop_supervisors_applied_fo` WHERE 
			workshop_level = '".mysqli_real_escape_string($link, $row1['level_name'])."' AND
			workshop_id= '".mysqli_real_escape_string($link, $row1['workshop_id'])."' AND
			supervisor_id = '".mysqli_real_escape_string($link, $getid)."' LIMIT 1";

            $result_find_pre_applied_status = mysqli_query($link, $query_find_pre_applied_status);
            if (mysqli_num_rows($result_find_pre_applied_status)!= 0) {
                //check that haven't already applied for it, if already applied, display a disabled option
                echo '<option disabled value="'.$row1['workshop_id'].'">'.date('d-M-Y h:i A', strtotime($row1['date'])).' (#'.$row1['workshop_id'].'), Already Selected</option>';
            } else {
                //if not applied for the workshop
                for ($i=1; $i < 21 ; $i++) {
                    //look at all the seats
                    if ($row1['supervisor_id_'.$i.'']== 0) {
                        //find the first empty seat
                        for ($i=1; $i < 21 ; $i++) {
                            if ($row1['supervisor_id_'.$i.'']== 0) {
                                //count all empty seats now that you found at least one empty seat.
                                $seats_avaiable++;
                            }
                        }

                        echo '<option value="'.$row1['workshop_id'].'">'.date('d-M-Y h:i A', strtotime($row1['date'])).' (#'.$row1['workshop_id'].'), ('.$seats_avaiable.' Seats available)</option>';
                        //increment total number of workshops available
                        $noVacancy++;
                        //don't look anymore after finding at least one empty seat
                        break;
                    }
                }
            }
        }

        if ($noVacancy ==0) {
            //didn't find even one empty seat, so display this info as an option
            echo '<option selected hidden>No Vacant Workshops available.</option>';
        }
        echo '</select>';
        if ($noVacancy != 0) {
            echo '<small>'.$noVacancy.' upcoming workshops to apply to</small>';
        }
        echo '</td>';
    } else {
        echo '<td>No '.$stagename.' scheduled</td>';
    }
    echo '</tr>';
}

function insertIntoWorkshop_User($stagename, $stg_w_, $getid, $link, &$successfullyInserted)
{
    /* Eg:
    $stagename: stg1w1
    $stg_w_: passes the id of that workshop
    $getid: the user's id
    $link: the database connection
    &$successfullyInserted: passes value by reference. Read about the & symbol: https://stackoverflow.com/a/598226/2365231
    */
    $query1 = "SELECT * FROM `cutabove_workshop` WHERE `workshop_id` ='".$stg_w_."' LIMIT 1";
    $result1 = mysqli_query($link, $query1);
    $row1 = mysqli_fetch_array($result1);
    for ($i=1; $i < 21 ; $i++) {
        if ($row1['supervisor_id_'.$i.'']== 0) {
            //find the first supervisor seat that's empty
            $query = "UPDATE `cutabove_workshop`
			SET `supervisor_id_".$i."` = '".mysqli_real_escape_string($link, $getid)."',
			`supervisor_id_".$i."a` = 0
			WHERE `cutabove_workshop`.`workshop_id` = '".$stg_w_."' LIMIT 1";
            if (mysqli_query($link, $query)) {
                //if workshop seat successfully updated with new id,
                //create new row in the cutabove_workshop_supervisors_applied_fo table and populate values
                $query = "INSERT INTO `cutabove_workshop_supervisors_applied_fo` (`workshop_level`, `workshop_id`, `supervisor_id`, `supervisor_attendance`, `attendance_updated_by`)
				VALUES (
				'".mysqli_real_escape_string($link, $stagename)."',
				'".mysqli_real_escape_string($link, $stg_w_)."',
				'".mysqli_real_escape_string($link, $getid)."',
				0,
				0)";

                if (mysqli_query($link, $query)) {
                    $successfullyInserted = 1;
                    break;
                }
            }
        }
    }
}

if (array_key_exists("submit", $_POST) and $_POST['submit']== 'propose_to_attend') {
    $successfullyInserted = 0;

    for ($i=1; $i < 6; $i++) {
        //stage 1 has 4 workshops, rest all 5
        if ($i == 1) {
            $max = 5;
        } else {
            $max = 6;
        }

        for ($j=1; $j < $max; $j++) {
            if (array_key_exists('stg'.$i.'w'.$j, $_POST) and $_POST['stg'.$i.'w'.$j]!=0) {
                insertIntoWorkshop_User('stg'.$i.'w'.$j, mysqli_real_escape_string($link, $_POST['stg'.$i.'w'.$j]), $getid, $link, $successfullyInserted);
            }
        }
    }
    
    if ($successfullyInserted == 1) {
        //success
        header("Location: supervisor-apply-workshop.php?id=".$getid."&successEdit=1");
    } else {
        echo '<div id="tablediv">';
        //print_r($_POST);
        echo "failed to edit";
        echo '</div>';
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="refresh" content="30">

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <?php include 'header.php'; //css-theme detector?>

    <title>Supervisor Workshop- <?php echo $row['name']; ?></title>

    <!--Hardcoded CSS for this page-->
    <style type="text/css">
    #alert {
        text-align: center;
    }

    @media only screen and (max-width: 600px) {

        /* this makes the table headings occupy full width on mobile view */
        .table {
            display: contents;
        }
    }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div id="tablediv" class="container-fluid">
        <div class="alert alert-<?php echo $_SESSION['colour'];?>" role="alert" id="alert">This page refreshes every 30
            seconds. Better act quick or the best dates may fill up/your selections will be reset.</div>
        <?php if ($_SESSION['permission'] == 'admin') { ?>
        <div class="card mb-2 w-100 border-primary" style="width: 18rem;">
            <div class="card-body">
                <?php
                    echo '<h5 class="card-title">' . $row['name'] . '</h5>';
                    echo '<h6 class="card-subtitle mb-2 text-muted">Permission: ';
                    if ($_SESSION['permission'] == 'admin') {
                        echo ' <span class="badge badge-danger">Admin</span>';
                    } elseif ($_SESSION['permission'] == 'supervisor') {
                        echo ' <span class="badge badge-primary">Supervisor</span>';
                    }
                    echo '</h6>';
                    echo '<p class="card-text">';
                    if ($row['phone']!= 0) {
                        echo '<b class="text-muted">Phone (Call):</b> <a href="tel:' . $row['phone'] . '">+91-' . $row['phone'] . '</a>';
                    }
                    if ($row['phone']!= 0 and $row['phone_whatsapp']!= 0) {
                        echo '<br />';
                    }
                    if ($row['phone_whatsapp']!= 0) {
                        echo '<b class="text-muted">Phone (WA):</b> <a href="https://wa.me/+91' . $row['phone_whatsapp'] . '">+91-' . $row['phone_whatsapp'] .'</a><br />';
                    }
                    echo '<b class="text-muted">Email:</b> <a href="mailto:' . $row['email'] . '?subject=A Cut Above&body=Hi '.strtok($row['name'], " ").',%0A%0A%0A%0A Yours Sincerely,%0A'.$_SESSION['name'].' ('.$_SESSION['permission'].')%0ACut Above%0AKMC, Mangalore" target="_top">' . $row['email'] . '</a>';
                    echo '</p>'; ?>
            </div>
        </div>
        <?php
        }
        ?>
    </div>
    <form method="post">
        <?php
        for ($i=1; $i < 6 ; $i++) {
            //stage 1 has 4 workshops, rest all 5
            if ($i == 1) {
                $max = 5;
            } else {
                $max = 6;
            }

            echo '<table class="table table-responsive-sm"><thead class="thead-dark"> <th>Stage '.$i.'</th> <th>Options</th> </thead> <tbody>';
            for ($j=1; $j < $max; $j++) {
                defineWorkshopRow('Workshop '. $j, 'stg'.$i.'w'.$j, $row, $getid, $link);
            }
            echo '</tbody></table>';
        }
        ?>
        <button type="submit" name="submit" class="btn btn-<?php echo $_SESSION['colour'];?> mb-2 ml-2"
            value="propose_to_attend">Place Reservations</button>
    </form>

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