<?php
session_start();
$error="";

if ($_SESSION['permission'] == 'admin' or $_SESSION['permission'] == 'supervisor') {
    include('connect-db.php');
    $query = "SELECT * FROM `cutabove_council` WHERE council_id = ".$_SESSION['id']." LIMIT 1";
    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_array($result);
} else {
    header("Location: index.php");
}


include('awardSystem.php');
# search feature
if (array_key_exists("id", $_POST)) {
    if ($_POST['id']!= '') {
        # members
        $query = "SELECT * FROM `cutabove` WHERE ";
        if (is_numeric($_POST['id'])) {
            $query.= "clg_reg LIKE '%".mysqli_real_escape_string($link, $_POST['id'])."%'";
        } else {
            $query.= "name LIKE '%".mysqli_real_escape_string($link, $_POST['id'])."%'";
        }
        $query.= " LIMIT 5";
        $result = mysqli_query($link, $query);
        if (mysqli_num_rows($result) > 0) {
            echo 'Members:';
            echo '<ul>';
            while ($row = mysqli_fetch_array($result)) {
                if ($_SESSION['permission']== 'admin') {
                    echo '<li><a href="one-person.php?id='.$row['id'].'">'.$row['name'].'</a> ('.$row['clg_reg'].') | '.awardSystem($row).' | <a href="edit.php?id='.$row['id'].'">Edit</a> | <a href="member-workshop.php?id='.$row['id'].'">Workshop</a> | <a href="fee.php?id='.$row['id'].'">Fee</a> | <a href="deletion.php?clg_reg='.$row['clg_reg'].'">Delete</a></li>';
                } else {
                    echo '<li><a href="one-person.php?id='.$row['id'].'">'.$row['name'].'</a> ('.$row['clg_reg'].') | <a href="member-workshop.php?id='.$row['id'].'">Workshop</a></li>';
                }
            }
            echo '</ul>';
        }

        # council
        $query = "SELECT * FROM `cutabove_council` WHERE username LIKE '%".mysqli_real_escape_string($link, $_POST['id'])."%' OR name LIKE '%".mysqli_real_escape_string($link, $_POST['id'])."%' LIMIT 5";
        $result = mysqli_query($link, $query);
        if (mysqli_num_rows($result) > 0) {
            echo 'Council:';
            echo '<ul>';
            while ($row = mysqli_fetch_array($result)) {
                if ($_SESSION['permission']== 'admin') {
                    echo '<li><a href="edit-council.php?id='.$row['council_id'].'">'.$row['name'].'</a> ('.$row['permission'].')  | <a href="deletion.php?council_username='.$row['username'].'">Delete</a></li>';
                } else {
                    echo '<li><a href="all-council.php?id='.$row['council_id'].'">'.$row['name'].'</a> ('.$row['permission'].') </li>';
                }
            }
            echo '</ul>';
        }

        # workshops
        $query = "SELECT * FROM `cutabove_workshop` WHERE ";
        if (is_numeric($_POST['id'])) {
            $query.= "workshop_id LIKE '%".mysqli_real_escape_string($link, $_POST['id'])."%'";
        } else {
            $query.= "memorisable_name LIKE '%".mysqli_real_escape_string($link, $_POST['id'])."%'";
        }
        $query.= " LIMIT 5";
        $result = mysqli_query($link, $query);
        if (mysqli_num_rows($result) > 0) {
            echo 'Workshops:';
            echo '<ul>';
            while ($row = mysqli_fetch_array($result)) {
                echo '<li><a href="supervisor_workshop.php?workshop_id='.$row['workshop_id'].'">'.$row['memorisable_name'].'</a> (# '.$row['workshop_id'].') | '.$row['level_name'].' | ('.$row['date'].')   | <a href="deletion.php?wk_id='.$row['workshop_id'].'">Delete</a></li>';
            }
            echo '</ul>';
        }
    }
    die();
}
if (array_key_exists("password", $_POST) and $_SESSION['permission'] == 'admin') {
    if ($_POST['password'] == '1234') {
        $_SESSION['god'] = 1;
        echo "God Mode: On";
    } else {
        $_SESSION['god'] = 0;
        echo "God Mode: Off";
    }
    die();
}
?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <?php include 'header.php'; //css-theme detector?>

    <title>Dashboard</title>

    <style type="text/css">
    #results {
        display: none;
    }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="jumbotron jumbotron-fluid" id="tablediv">
        <div class="container">
            <h1 class="display-4">Welcome to Dashboard!</h1>
            <p class="lead">Relax. You can do this.</p>
        </div>
    </div>
    <div class="container-fluid">
        <?php if ($_SESSION['permission']== 'admin') { ?>
        <div class="row">
            <div class="col-sm">
                <table class="table table-responsive-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><a href="all-people.php"><i class="fas fa-users"></i> View All Members (and relevant
                                    actions)</a>
                            </td>
                        </tr>
                        <tr>
                            <td><a href="admin-workshop.php">Manage Workshops</a> | <a
                                    href="analyse-workshops.php">Analyse Workshops</a></td>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" class="form-control" name="inputID" id="inputID"
                                    placeholder="Search Anything" autocomplete="off">
                            </td>
                        </tr>
                        <tr>
                            <td id="results"></td>
                        </tr>
                        <tr>
                            <td><a href="transaction-history.php">Transaction History</a></td>
                        </tr>
                        <tr>
                            <td><a href="all-people.php?kit=0">Review New Registrations</a></td>
                        </tr>
                        <tr>
                            <td><a href="all-council.php">Manage Core</a></td>
                        </tr>
                        <!--<tr><td><a href="add-new.php">Add Council or Members</a></td></tr>-->
                        <tr>
                            <td>
                                <a href="supervisor-apply-workshop.php?id=<?php echo $row['council_id']; ?>">
                                    <i class="fas fa-user-tag"></i> Apply for Workshops</a> |
                                <a href="supervisor-attendance.php?id=<?php echo $row['council_id']; ?>">
                                    <i class="fas fa-user-check"></i> Your Attendance</a> |
                                <a href="edit-council.php?id=<?php echo $row['council_id']; ?>">
                                    <i class="fas fa-user-edit"></i> Update Profile</a>
                            </td>
                        </tr>
                        <tr>
                            <td><a href="help.php">Help Page</a></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="btn-group-toggle" data-toggle="buttons">

                                    <?php
                                echo '<label id="god_label" class="btn btn-outline-danger';
                                if ($_SESSION['god'] == 1) {
                                    echo ' active';
                                }
                                echo '">';
                                echo '<input type="checkbox" id="god" autocomplete="off"><span id="god_mode">';
                                
                                if ($_SESSION['god'] == 1) {
                                    echo 'God Mode: On';
                                } else {
                                    echo 'God Mode: Off';
                                }

                                echo '</span></label>';
                                ?>

                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><a href="defaults.php">Edit Defaults</a> | <a href="deletion.php">Delete Stuff</a> (<a
                                    href="deletion_log.php">log</a>)</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php } if ($_SESSION['permission']== 'supervisor') { ?>
        <table class="table table-striped col-sm">
            <thead>
                <tr class="thead-dark">
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><a href="all-people.php">View All Members (and relevant actions)</a> | <a
                            href="all-council.php">View All council members</a> </td>
                </tr>
                <tr>
                    <td><a href="supervisor-apply-workshop.php?id=<?php echo $row['council_id']; ?>">Apply for
                            Workshops</a> | <a
                            href="supervisor-attendance.php?id=<?php echo $row['council_id']; ?>">Your Attendance</a> |
                        <a href="edit-council.php?id=<?php echo $row['council_id']; ?>">Update Profile</a></td>
                </tr>
                <tr>
                    <td><input type="text" class="form-control" name="inputID" id="inputID"
                            placeholder="Search Anything" autocomplete="off"></td>
                </tr>
                <tr>
                    <td id="results"></td>
                </tr>
            </tbody>
        </table>
        <table class="table table-striped col-sm">
            <thead>
                <tr class="thead-dark">
                    <th>Your Upcoming Workshops</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $query_applied = "SELECT * FROM `cutabove_workshop_supervisors_applied_fo` WHERE `supervisor_id` = ".$_SESSION['id']." AND `supervisor_attendance` = 0";
                    $result_applied = mysqli_query($link, $query_applied) or die();
                    while ($row_applied = mysqli_fetch_array($result_applied)) {
                        $query_workshopdetail = "SELECT * FROM `cutabove_workshop` WHERE `workshop_id` = ".$row_applied['workshop_id']." LIMIT 1";
                        $result_workshopdetail = mysqli_query($link, $query_workshopdetail);
                        $row_workshopdetail = mysqli_fetch_array($result_workshopdetail);
                        if ($row_workshopdetail['completed']== 0) {
                            echo '<tr><td><a href="supervisor_workshop.php?workshop_id='.$row_applied['workshop_id'].'">#'.$row_applied['workshop_id'].'</a>: <b>'.$row_workshopdetail['level_name'].'</b>- '.$row_workshopdetail['memorisable_name'].', (Date:'.$row_workshopdetail['date'].')</td></tr>';
                        }
                    } ?>
            </tbody>
        </table>
        <?php } ?>
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

    <script type="text/javascript">
    document.querySelector('#inputID').addEventListener('keyup', search, false);

    function search() {
        var id = $("#inputID").val();
        $.ajax({
            type: "POST",
            url: "council-dashboard.php",
            data: {
                id: id
            },
            success: function(result) {
                if (result != '') {
                    $("#results").show();
                    $("#results").html("<b>Results</b>:<br/>" + result);
                } else {
                    $("#results").hide();
                }
            }
        })
    }

    /* when the god mode button is clicked, it prompts user for a password which is sent via ajax
    /* if the passwords was correct, ajax returns God Mode: On, which wors great
    /* if wrong password, the returned value is God Mode: Off, which if found drops the active class on the label
    /* if the god mode was unclicked, it sends a fake password which disables god mode
    /* as it was an unclick action, active class will have already been removed on its own so no fret 
    */
    $(document).ready(function() {
        $('#god').change(function() {
            if (this.checked) {
                var password = prompt("Please enter your password:", "");
                $.ajax({
                    type: "POST",
                    url: "council-dashboard.php",
                    data: {
                        password: password
                    },
                    success: function(result) {
                        $("#god_mode").html(result);
                        if (result == 'God Mode: Off') {
                            $("#god_label").removeClass("active");
                        }
                    }
                })
            } else {
                $.ajax({
                    type: "POST",
                    url: "council-dashboard.php",
                    data: {
                        password: 'wrongpass'
                    },
                    success: function(result) {
                        $("#god_mode").html(result);
                    }
                })
            }
        });
    });
    </script>
</body>

</html>