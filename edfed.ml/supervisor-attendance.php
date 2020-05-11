<?php
session_start();

include 'assets/acknowledgements.php';


//if admin has accessed the page, see if the id has been sent by $_GET, then show the user data by $_GET['id']
//if supervisor has logged in, show his own data using $_SESSION['id']
if ($_SESSION['permission'] == 'admin') {
    if (array_key_exists("id", $_GET) and $_GET['id']!='') {
        include('connect-db.php');
        $query = "SELECT * FROM `cutabove_council` WHERE council_id = '".mysqli_real_escape_string($link, $_GET['id'])."' LIMIT 1";
        $result = mysqli_query($link, $query) or die();
        $row = mysqli_fetch_array($result);
        $getid = $_GET['id'];
    } else {
        echo "You didn't specify which id to fetch. You are admin. Use the <a href='all-people.php'>Admin mode</a> to click on a user for details.";
        die();
    }
} elseif ($_SESSION['permission'] == 'supervisor') {
    if (array_key_exists("id", $_GET) and $_GET['id']!=$_SESSION['id']) {
        //done so that it's clear the person's own page is being accessed, not someone else's
        //header("Location: supervisor-attendance.php");
        echo '<html><head><meta http-equiv="refresh" content="5;url=supervisor-attendance.php"></head><body>Redirecting to your own attendance page</body></html>';
        die();
    }
    include('connect-db.php');
    $query = "SELECT * FROM `cutabove_council` WHERE council_id = '".mysqli_real_escape_string($link, $_SESSION['id'])."' LIMIT 1";
    $result = mysqli_query($link, $query) or die();
    $row = mysqli_fetch_array($result);
    $getid = $_SESSION['id'];
} else {
    header("Location: index.php?redirect=".$_SERVER['REQUEST_URI']);
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

    <!-- Icons -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css"
        integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">

    <title>Supervisor Attendance- <?php echo $row['name']; ?></title>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container-fluid" id="tablediv">
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
                    
                    echo '<b class="text-muted">Email:</b> <a href="mailto:' . $row['email'] . '?subject=A Cut Above&body=Hi '.strtok($row['name'], " ").',%0A%0A%0A%0AYours Sincerely,%0A'.$_SESSION['name'].' ('.$_SESSION['permission'].')%0ACut Above%0AKMC, Mangalore" target="_top">' . $row['email'] . '</a>';
                    echo '</p>';
                    ?>
            </div>
        </div>
        <?php }

        echo '<div class="row">';
        echo '<table class="table table-striped col-sm">';
        echo '<thead><tr class="thead-dark"> <th>Applied for Upcoming</th> </tr></thead>';
        echo '<tbody>';

        $query_applied = "SELECT * FROM `cutabove_workshop_supervisors_applied_fo` WHERE `supervisor_id` = ".$getid." AND `supervisor_attendance` = 0 ORDER BY workshop_level";
        $result_applied = mysqli_query($link, $query_applied) or die();
        while ($row_applied = mysqli_fetch_array($result_applied)) {
            $query_workshopdetail = "SELECT * FROM `cutabove_workshop` WHERE `workshop_id` = ".$row_applied['workshop_id']." LIMIT 1";
            $result_workshopdetail = mysqli_query($link, $query_workshopdetail);
            $row_workshopdetail = mysqli_fetch_array($result_workshopdetail);
            if ($row_workshopdetail['completed']== 0) {
                echo '<tr><td><a href="supervisor_workshop.php?workshop_id='.$row_applied['workshop_id'].'">#'.$row_applied['workshop_id'].'</a>: <b>'.$row_workshopdetail['level_name'].'</b>- '.$row_workshopdetail['memorisable_name'].', (Date: '.date("d-M-Y h:i A", strtotime($row_workshopdetail['date'])).')</td></tr>';
            }
        }
        echo '</tbody></table>';

        echo '<table class="table table-striped col-sm">';
        echo '<thead><tr class="thead-dark"> <th>Attended Workshops</th> </tr></thead>';
        echo '<tbody>';
        $query_completed = "SELECT * FROM `cutabove_workshop_supervisors_applied_fo` WHERE `supervisor_id` = ".$getid." AND `supervisor_attendance` = 1";
        $result_completed = mysqli_query($link, $query_completed) or die();
        while ($row_completed = mysqli_fetch_array($result_completed)) {
            $query_workshopdetail = "SELECT * FROM `cutabove_workshop` WHERE `workshop_id` = ".$row_completed['workshop_id']." LIMIT 1";
            $result_workshopdetail = mysqli_query($link, $query_workshopdetail);
            $row_workshopdetail = mysqli_fetch_array($result_workshopdetail);
            echo '<tr><td>';
            echo '<a href="supervisor_workshop.php?workshop_id='.$row_completed['workshop_id'].'">#'.$row_completed['workshop_id'].'</a>: ';
            
            if (mysqli_num_rows($result_workshopdetail)!=0) {
                echo '<b>'.$row_workshopdetail['level_name'].'</b>- '.$row_workshopdetail['memorisable_name'].', (Date: '.date("d-M-Y h:i A", strtotime($row_workshopdetail['date'])).')<br />';
            } else {
                echo '<code>Workshop Deleted</code>';
            }
            echo '</td></tr>';
        }
        echo '</tbody></table>';


        echo '<table class="table table-striped col-sm">';
        echo '<thead><tr class="thead-dark"> <th><span class="text-danger">Missed Workshops</span></th> </tr></thead>';
        echo '<tbody>';
        mysqli_data_seek($result_applied, 0);
        while ($row_applied = mysqli_fetch_array($result_applied)) {
            $query_workshopdetail = "SELECT * FROM `cutabove_workshop` WHERE `workshop_id` = ".$row_applied['workshop_id']." LIMIT 1";
            $result_workshopdetail = mysqli_query($link, $query_workshopdetail);
            $row_workshopdetail = mysqli_fetch_array($result_workshopdetail);
            echo '';
            if ($row_workshopdetail['completed']== 1) {
                echo '<tr><td><a href="supervisor_workshop.php?workshop_id='.$row_applied['workshop_id'].'">#'.$row_applied['workshop_id'].'</a>: <b>'.$row_workshopdetail['level_name'].'</b>- '.$row_workshopdetail['memorisable_name'].', (Date: '.date("d-M-Y h:i A", strtotime($row_workshopdetail['date'])).')</td></tr>';
            }
        }
        echo '</tbody></table>';

        echo '</div>';
        ?>
    </div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <!--<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>-->
    <!-- https://stackoverflow.com/questions/44212202/my-javascript-is-returning-this-error-ajax-is-not-a-function -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
</body>

</html>