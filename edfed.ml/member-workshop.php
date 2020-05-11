<?php
session_start();
include 'assets/acknowledgements.php';


$getid = ''; //doing this since there are 2 ways a member's id can be worked on (admin & supervisor using get['id'] vs Member using session['id']. I needn't worry about which method is used once I set $getid to the id value through either method. Rest of the program uses getid ather than get['id'] or session['id']
$stg1c=0; //for enabling stage 2, this value is checked instead of database
$stg2c=$stg3c=$stg4c=0;

//if admin has accessed the page, see if the id has been sent by $_GET, then show the user data by $_GET['id']
//if user has logged in, show his own data using $_SESSION['id']
if (($_SESSION['permission'] == 'admin') or ($_SESSION['permission'] == 'supervisor')) {
    if (array_key_exists("id", $_GET) and $_GET['id']!='') {
        include('connect-db.php');
        $_GET['id'] = mysqli_real_escape_string($link, $_GET['id']);
        
        $query = "SELECT * FROM `cutabove` WHERE id = '".mysqli_real_escape_string($link, $_GET['id'])."' LIMIT 1";
        $result = mysqli_query($link, $query) or die(mysql_error());
        $row = mysqli_fetch_array($result); //since only one row is fetched, no need to run a fancy while loop like we did in all-people.php
        $getid = $_GET['id'];
    } else {
        echo "You didn't specify which id to fetch. You are admin. Use the <a href='all-people.php'>Admin mode</a> to click on a user for details.";
        die();
    }
} elseif (array_key_exists("id", $_SESSION)) {
    include('connect-db.php');
    $query = "SELECT * FROM `cutabove` WHERE id = '".mysqli_real_escape_string($link, $_SESSION['id'])."' LIMIT 1";
    $result = mysqli_query($link, $query) or die(mysql_error());
    $row = mysqli_fetch_array($result);
    $getid = $_SESSION['id'];
} else {
    header("Location: index.php?redirect=".$_SERVER['REQUEST_URI']);
}

function defineWorkshopRow($stagename, $stg_w_, $stg_fee, $row, $link)
{
    /* Eg of arguments passed:
    $stagename: Just the workshop name like Workshop 1, Workshop 2...
    $stg_w_: stg1w1, stg1w2...
    $stg_fee: asks to check which stage fee should be looked at for the member
    $row: $row is for the initially extracted data at start of this page. Basically every detail of the member is imported into this function. (Working with outside variables inside a function- https://stackoverflow.com/a/2531234/2365231)
    $link: database link
    */
    echo '<tr><th scope="row">'.$stagename.'</th>';
    if ($row[$stg_w_]== 0) {
        if ($row[$stg_w_.'_applied_for']== 0) {
            $query1 = "SELECT * FROM `cutabove_workshop` WHERE completed = 0 AND level_name = '".$stg_w_."'";
            $result1 = mysqli_query($link, $query1);
            if (mysqli_num_rows($result1)!= 0) {
                echo '<td><select class="form-control" name="'.$stg_w_.'">';
                echo '<option value= "0" hidden>Choose a date</option>';
                $noVacancy = 0;
                while ($row1 = mysqli_fetch_array($result1)) {
                    //enter a workshop
                    $seats_avaiable = 0;
                    for ($i=1; $i < 31 ; $i++) {
                        //look at all the seats
                        if ($row1['member_id_'.$i.'']== 0) {
                            //find the first empty seat
                            for ($i=1; $i < 31 ; $i++) {
                                if ($row1['member_id_'.$i.'']== 0) {
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
                if ($noVacancy ==0) {
                    //didn't find even one empty seat, so display this info as an option
                    echo '<option selected hidden>No Vacant Workshops available.</option>';
                }
                echo '</select>';
                if ($noVacancy != 0) {
                    echo '<small>'.$noVacancy.' workshops available</small>';
                }
                echo '</td>';
            } else {
                echo '<td>No '.$stagename.' scheduled</td>';
            }
        } else {
            $query1 = "SELECT * FROM `cutabove_workshop` WHERE workshop_id = '".$row[$stg_w_.'_applied_for']."'";
            $result1 = mysqli_query($link, $query1);
            $row1 = mysqli_fetch_array($result1);
            echo '<td>Applied for <a href="supervisor_workshop.php?workshop_id='.$row[$stg_w_.'_applied_for'].'" title="Only admins can view the workshop.">Workshop#'.$row[$stg_w_.'_applied_for'].'</a>. <small>';
            if (mysqli_num_rows($result1)==0) {
                echo '<code>(Deleted workshop. You should never have to see this. Please let an admin know.)</code>';
            } else {
                echo '(Date: '.date('d-M-Y h:i A', strtotime($row1['date'])).')';
                if ($row1['completed']==1) {
                    echo '<code>(Missed workshop)</code>';
                }
            }
            echo '</small></td>';
        }
    } else {
        $query1 = "SELECT * FROM `cutabove_workshop` WHERE workshop_id = '".$row[$stg_w_]."'";
        $result1 = mysqli_query($link, $query1);
        $row1 = mysqli_fetch_array($result1);
        echo '<td>Completed at <a href="supervisor_workshop.php?workshop_id='.$row[$stg_w_].'" title="Only admins can view the workshop.">Workshop#'.$row[$stg_w_].'</a>. <small>';
        if (mysqli_num_rows($result1)==0) {
            echo '<code>(Deleted workshop)</code>';
        } else {
            echo '(Date: '.date('d-M-Y h:i A', strtotime($row1['date'])).')';
        }
        echo '</small></td>';
    }
    echo '</tr>';
}

function insertIntoWorkshop_User($stagename, $stg_w_, $getid, $link, &$successfullyInserted)
{
    /* Eg:
    $stagename: stg1w1
    $stg_w_: passes the value chosen for that level workhop
    $getid: the user's id
    $link: the database connection
    &$successfullyInserted: passes value by reference. Read about the & symbol: https://stackoverflow.com/a/598226/2365231
    */
    $query1 = "SELECT * FROM `cutabove_workshop` WHERE `workshop_id` ='".$stg_w_."' LIMIT 1";
    $result1 = mysqli_query($link, $query1);
    $row1 = mysqli_fetch_array($result1);
    for ($i=1; $i < 31 ; $i++) {
        if ($row1['member_id_'.$i.'']== 0) {
            $query_get_clg_reg = "SELECT `clg_reg` FROM `cutabove` WHERE id = ".$getid." LIMIT 1";
            $result_get_clg_reg = mysqli_query($link, $query_get_clg_reg);
            $row_get_clg_reg = mysqli_fetch_array($result_get_clg_reg);

            $query = "UPDATE `cutabove_workshop`
			SET `member_id_".$i."` = '".$row_get_clg_reg['clg_reg']."'
			WHERE `cutabove_workshop`.`workshop_id` = '".$stg_w_."' LIMIT 1";
            if (mysqli_query($link, $query)) {
                $query = "UPDATE `cutabove`
				SET `".$stagename."_applied_for` = '".$stg_w_."'
				WHERE `id` = '".$getid."' LIMIT 1";
                if (mysqli_query($link, $query)) {
                    $successfullyInserted = 1;
                }
            }
            break;
        }
    }
}

if (array_key_exists("submit", $_POST) and $_POST['submit']== 'propose_to_attend') {
    $successfullyInserted = 0;

    for ($i=1; $i <6 ; $i++) {
        if ($i==1) {
            $max=4;
        } else {
            $max=5;
        }
        for ($j=1; $j <=$max; $j++) {
            if (array_key_exists('stg'.$i.'w'.$j, $_POST) and $_POST['stg'.$i.'w'.$j]!=0) {
                insertIntoWorkshop_User('stg'.$i.'w'.$j, mysqli_real_escape_string($link, $_POST['stg'.$i.'w'.$j]), $getid, $link, $successfullyInserted);
            }
        }
    }
    
    if ($successfullyInserted == 1) {
        //success
        header("Location: member-workshop.php?id=".$getid."&successEdit=1");
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

    <title>Choose Workshop</title>

    <!--Hardcoded CSS for this page-->
    <style type="text/css">
    #alert {
        text-align: center;
    }

    @media only screen and (max-width: 600px) {

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
        <?php
        if ($_SESSION['permission'] != 'member') {
            echo '<div class="card mb-2 w-100 border-primary">
			<div class="card-body">';

            echo '<h5 class="card-title">' . $row['name'] . '</h5>';
            echo '<h6 class="card-subtitle mb-2 text-muted">Permission: <span class="badge badge-success">Member</span></h6>';
            echo '<p class="card-text">';
            echo '<b class="text-muted">College Registration:</b> ' . $row['clg_reg'] . '<br /><b class="text-muted">DOB:</b> ' . date('d-M-Y', strtotime($row['dob'])) . '<br /><b class="text-muted">Semester:</b> ' . $row['semester'].'<br />';
            if ($row['phone']!= 0) {
                echo '<b class="text-muted">Phone (Call):</b> <a href="tel:' . $row['phone'] . '">+91-' . $row['phone'] . '</a>';
            }
            if ($row['phone']!= 0 and $row['phone_whatsapp']!= 0) {
                echo '<br />';
            }
            if ($row['phone_whatsapp']!= 0) {
                echo '<b class="text-muted">Phone (WA):</b> <a href="https://wa.me/+91' . $row['phone_whatsapp'] . '">+91-' . $row['phone_whatsapp'] . '</a>';
            }
            echo '<br /><b class="text-muted">Email:</b> <a href="mailto:' . $row['email'] . '?subject=A Cut Above&body=Hi '.strtok($row['name'], " ").',%0A%0A%0A%0A Yours Sincerely,%0A'.$_SESSION['name'].' ('.$_SESSION['permission'].')%0ACut Above%0AKMC, Mangalore" target="_top">' . $row['email'] . '</a>';
            echo '</p>';
            echo '</div></div>';
        }
        ?>
        <form method="post">
            <?php
            //stage 1
            if ($row['stg1w1'] != '0' and $row['stg1w2'] != '0' and $row['stg1w3'] != '0' and $row['stg1w4'] != '0') {
                $stg1c='1';
            }

            if ($stg1c!=1) {
                if ($row['stg1fee']!= 0) {
                    echo '<table class="table table-responsive-sm"><thead class="thead-dark"> <th>Stage 1</th> <th>Options</th> </thead> <tbody>';
                    for ($i=1; $i <= 4; $i++) {
                        //for creating all 4 workshops in stage 1
                        defineWorkshopRow('Workshop '. $i, 'stg1w'.$i, 'stg1fee', $row, $link);
                    }
                    echo '</tbody></table>';
                } else {
                    echo '<table class="table table-responsive-sm"><thead class="thead-dark"> <th>Stage 1</th> <th>Fee Not Paid</th> </thead> <tbody></tbody></table>';
                }
            } else {
                echo '<table class="table table-responsive-sm"><thead class="thead-dark"> <th>Stage 1</th> <th>Completed</th> </thead> <tbody></tbody></table>';
            }

            //stage 2
            if ($row['stg2w1'] != '0' and $row['stg2w2'] != '0' and $row['stg2w3'] != '0' and $row['stg2w4'] != '0' and $row['stg2w5'] != '0') {
                $stg2c='1';
            }

            if ($stg1c!=0) {
                if ($stg2c!=1) {
                    if ($row['stg2fee']!= 0) {
                        echo '<table class="table table-responsive-sm"><thead class="thead-dark"> <th>Stage 2</th> <th>Options</th> </thead> <tbody>';

                        for ($i=1; $i <= 5; $i++) {
                            //for creating all 5 workshops in stage 2
                            defineWorkshopRow('Workshop '. $i, 'stg2w'.$i, 'stg2fee', $row, $link);
                        }

                        echo '</tbody></table>';
                    } else {
                        echo '<table class="table table-responsive-sm"><thead class="thead-dark"> <th>Stage 2</th> <th>Fee Not Paid</th> </thead> <tbody></tbody></table>';
                    }
                } else {
                    echo '<table class="table table-responsive-sm"><thead class="thead-dark"> <th>Stage 2</th> <th>Completed</th> </thead> <tbody></tbody></table>';
                }
            } else {
                echo '<table class="table table-responsive-sm"><thead class="thead-dark"> <th>Stage 2</th> <th>Complete Stage 1 First</th> </thead> <tbody></tbody></table>';
            }

            //stage 3
            if ($row['stg3w1'] != '0' and $row['stg3w2'] != '0' and $row['stg3w3'] != '0' and $row['stg3w4'] != '0' and $row['stg3w5'] != '0') {
                $stg3c='1';
            }

            if ($stg2c!=0) {
                if ($stg3c!=1) {
                    if ($row['stg3fee']!= 0) {
                        echo '<table class="table table-responsive-sm"><thead class="thead-dark"> <th>Stage 3</th> <th>Options</th> </thead> <tbody>';

                        for ($i=1; $i <= 5; $i++) {
                            //for creating all 5 workshops in stage 3
                            defineWorkshopRow('Workshop '. $i, 'stg3w'.$i, 'stg3fee', $row, $link);
                        }

                        echo '</tbody></table>';
                    } else {
                        echo '<table class="table table-responsive-sm"><thead class="thead-dark"> <th>Stage 3</th> <th>Fee Not Paid</th> </thead> <tbody></tbody></table>';
                    }
                } else {
                    echo '<table class="table table-responsive-sm"><thead class="thead-dark"> <th>Stage 3</th> <th>Completed</th> </thead> <tbody></tbody></table>';
                }
            } else {
                echo '<table class="table table-responsive-sm"><thead class="thead-dark"> <th>Stage 3</th> <th>Complete Stage 2 First</th> </thead> <tbody></tbody></table>';
            }

            //stage 4
            if ($row['stg4w1'] != '0' and $row['stg4w2'] != '0' and $row['stg4w3'] != '0' and $row['stg4w4'] != '0' and $row['stg4w5'] != '0') {
                $stg4c='1';
            }

            if ($stg3c!=0) {
                if ($stg4c!=1) {
                    if ($row['stg4fee']!= 0) {
                        echo '<table class="table table-responsive-sm"><thead class="thead-dark"> <th>Stage 4</th> <th>Options</th> </thead> <tbody>';

                        for ($i=1; $i <= 5; $i++) {
                            //for creating all 5 workshops in stage 4
                            defineWorkshopRow('Workshop '. $i, 'stg4w'.$i, 'stg4fee', $row, $link);
                        }

                        echo '</tbody></table>';
                    } else {
                        echo '<table class="table table-responsive-sm"><thead class="thead-dark"> <th>Stage 4</th> <th>Fee Not Paid</th> </thead> <tbody></tbody></table>';
                    }
                } else {
                    echo '<table class="table table-responsive-sm"><thead class="thead-dark"> <th>Stage 4</th> <th>Completed</th> </thead> <tbody></tbody></table>';
                }
            } else {
                echo '<table class="table table-responsive-sm"><thead class="thead-dark"> <th>Stage 4</th> <th>Complete Stage 3 First</th> </thead> <tbody></tbody></table>';
            }

            //stage 5
            if ($row['stg5w1'] != '0' and $row['stg5w2'] != '0' and $row['stg5w3'] != '0' and $row['stg5w5'] != '0' and $row['stg5w5'] != '0') {
                $stg5c='1';
            }

            if ($stg4c!=0) {
                if ($stg5c!=1) {
                    echo '<table class="table table-responsive-sm"><thead class="thead-dark"> <th>Stage 5</th> <th>Options</th> </thead> <tbody>';

                    for ($i=1; $i < 6; $i++) {
                        if ($row['stg5w'.$i.'fee']!= 0) {
                            defineWorkshopRow('Workshop '. $i, 'stg5w'.$i, 'stg5w'.$i.'fee', $row, $link);
                        } else {
                            echo '<tr><th>Workshop '.$i.'</th> <th>Fee Not Paid</th></tr>';
                        }
                    }
                    
                    echo '</tbody></table>';
                } else {
                    echo '<table class="table table-responsive-sm"><thead class="thead-dark"> <th>Stage 5</th> <th>Completed</th> </thead> <tbody></tbody></table>';
                }
            } else {
                echo '<table class="table table-responsive-sm"><thead class="thead-dark"> <th>Stage 5</th> <th>Complete Stage 4 First</th> </thead> <tbody></tbody></table>';
            }
            
            ?>
            <button type="submit" name="submit" class="btn btn-<?php echo $_SESSION['colour'];?>"
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