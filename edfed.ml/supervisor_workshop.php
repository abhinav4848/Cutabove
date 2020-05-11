<?php
session_start();
include 'assets/acknowledgements.php';


$row='';
$view_Supervisor_Oriented_buttons = 0;
if ($_SESSION['permission'] == 'supervisor') {
    include('connect-db.php');
    $query = "SELECT * FROM `cutabove_workshop` WHERE workshop_id = '".mysqli_real_escape_string($link, $_GET['workshop_id'])."' LIMIT 1";
    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_array($result);
    $view_Supervisor_Oriented_buttons = 1;
} elseif ($_SESSION['permission'] == 'admin') {
    if (array_key_exists("workshop_id", $_GET)) {
        include('connect-db.php');
        $query = "SELECT * FROM `cutabove_workshop` WHERE workshop_id = '".mysqli_real_escape_string($link, $_GET['workshop_id'])."' LIMIT 1";
        $result = mysqli_query($link, $query);
        $row = mysqli_fetch_array($result);
        $view_Supervisor_Oriented_buttons = 1;
    } else {
        header("Location: admin-workshop.php");
    }
} else {
    header("Location: index.php?redirect=".$_SERVER['REQUEST_URI']);
}

if (mysqli_num_rows($result)==0) {
    echo 'Workshop does not exist. Go back to <a href="admin-workshop.php">Admin workshop</a> and choose again';
    die();
}

function commentInserter($comment, $heading)
{
    global $link, $row;
    $query = "UPDATE `cutabove_workshop`
	SET `comments` = '".mysqli_real_escape_string($link, $row['comments'])." <br /><small>[". date('d-m-Y h:i:sa') ."] ".mysqli_real_escape_string($link, $_SESSION['name'])." (ID# ".mysqli_real_escape_string($link, $_SESSION['id'])."):</small> <b>[".$heading."]</b> ".mysqli_real_escape_string($link, $comment)."'
	WHERE `cutabove_workshop`.`workshop_id` = '".mysqli_real_escape_string($link, $row['workshop_id'])."' LIMIT 1";

    if (mysqli_query($link, $query)) {
        header("Location: supervisor_workshop.php?workshop_id=".$row['workshop_id']."&successEdit=1");
    } else {
        echo '<div id="tablediv">';
        //print_r($_POST);
        echo "failed to edit";
        echo '</div>';
    }
}

$error= "";
$error_sup = [];
$error_mem = [];
if (array_key_exists("submit", $_POST)) {
    //echo '<div id="tablediv">';
    //print_r($_POST);
    //echo '</div>';
    if ($_POST['submit']== 'basicdata') {
        if (isset($_POST['memorisable_name']) and $_POST['memorisable_name']!= $row['memorisable_name']) {
            $query = "UPDATE `cutabove_workshop`
			SET `memorisable_name` = '".mysqli_real_escape_string($link, $_POST['memorisable_name'])."'
			WHERE `cutabove_workshop`.`workshop_id` = '".mysqli_real_escape_string($link, $_POST['workshop_id'])."' LIMIT 1";
            mysqli_query($link, $query);
        }

        if (isset($_POST['date'])) {
            $date = DateTime::createFromFormat('Y-m-d\TH:i', $_POST['date'])->format('Y-m-d H:i:s');
            if ($date!= $row['date']) {
                $query = "UPDATE `cutabove_workshop`
				SET `date` = '".mysqli_real_escape_string($link, $date)."'
				WHERE `cutabove_workshop`.`workshop_id` = '".mysqli_real_escape_string($link, $_POST['workshop_id'])."' LIMIT 1";
                mysqli_query($link, $query);
            }
        }

        if ($_SESSION['permission']== 'admin') {
            $query = "UPDATE `cutabove_workshop`
			SET `bonus_files` = '".mysqli_real_escape_string($link, $_POST['bonus_files'])."',
			`feedback_link` = '".mysqli_real_escape_string($link, $_POST['feedback_link'])."'
			WHERE `cutabove_workshop`.`workshop_id` = '".mysqli_real_escape_string($link, $_POST['workshop_id'])."' LIMIT 1";
            mysqli_query($link, $query);
        }

        $comment = '';
        if (isset($_POST['completed']) and $_POST['completed']!= $row['completed']) {
            $query = "UPDATE `cutabove_workshop`
			SET `supervisor_id` = '".mysqli_real_escape_string($link, $_SESSION['id'])."'
			WHERE `cutabove_workshop`.`workshop_id` = '".mysqli_real_escape_string($link, $_POST['workshop_id'])."' LIMIT 1";
            mysqli_query($link, $query);
            if ($_POST['completed']== 0) {
                $comment = 'De-Completed the workshop';
            } else {
                $comment = 'Completed the workshop';
            }
        }

        $query = "UPDATE `cutabove_workshop`
		SET `completed` = '".mysqli_real_escape_string($link, $_POST['completed'])."',
		`comments` = '".mysqli_real_escape_string($link, $row['comments'])." <br /><small>[". date('d-m-Y h:i:sa') ."] ".mysqli_real_escape_string($link, $_SESSION['name'])." (ID# ".mysqli_real_escape_string($link, $_SESSION['id'])."):</small> ".$comment.' '. mysqli_real_escape_string($link, $_POST['comments'])."'
		WHERE `cutabove_workshop`.`workshop_id` = '".mysqli_real_escape_string($link, $row['workshop_id'])."' LIMIT 1";

        if (mysqli_query($link, $query)) {
            header("Location: supervisor_workshop.php?workshop_id=".$row['workshop_id']."&successEdit=1");
        } else {
            echo '<div id="tablediv">';
            //print_r($_POST);
            echo "failed to edit";
            echo '</div>';
        }
    }
    if ($_POST['submit']== 'member-attendance') {
        $successfullyInserted = 0;
        $comment = '';
        for ($i=1; $i <31 ; $i++) {
            if ($row['member_id_'.$i]!= 0 and $_POST['member_id_'.$i.'a']!= $row['member_id_'.$i.'a']) {
                //if a member id of the workshop is not 0, and its attendance column is not same as current submitted attendance value,
                //set the attendance column as 1 or 0

                if ($_POST['member_id_'.$i.'a']== 1) {
                    $query = "UPDATE `cutabove_workshop`
					SET `member_id_".$i."a` = '1'
					WHERE `cutabove_workshop`.`workshop_id` = '".$row['workshop_id']."' LIMIT 1";
                    if (mysqli_query($link, $query)) {
                        //if attendance updated in the workshop, find the member id whose details has been sent through the form, and
                        $query3 = "UPDATE `cutabove`
						SET `".$row['level_name']."` = '".$row['workshop_id']."'
						WHERE `clg_reg` = '".$row['member_id_'.$i]."' LIMIT 1";
                        //mysqli_real_escape_string($link, $_POST['member_id_'.$i.'a'])
                        if (mysqli_query($link, $query3)) {
                            $successfullyInserted = 1;
                            $comment.= ' <u>Present</u>: '.$row['member_id_'.$i];
                        }
                    }
                } elseif ($_POST['member_id_'.$i.'a']== 0) {
                    $query = "UPDATE `cutabove_workshop`
					SET `member_id_".$i."a` = '0'
					WHERE `cutabove_workshop`.`workshop_id` = '".$row['workshop_id']."' LIMIT 1";
                    if (mysqli_query($link, $query)) {
                        //if attendance updated in the workshop, find the member id whose details has been sent through the form, and
                        $query3 = "UPDATE `cutabove`
						SET `".$row['level_name']."` = '0'
						WHERE `clg_reg` = '".$row['member_id_'.$i]."' LIMIT 1";
                        if (mysqli_query($link, $query3)) {
                            $successfullyInserted = 1;
                            $comment.= ' <u>Absent</u>: '.$row['member_id_'.$i];
                        }
                    }
                }
            }
        }
        if ($successfullyInserted == 1) {
            //success
            //now insert a comment mentioning who got present/absent and by whom.
            commentInserter($comment, "Member attendance");
        } else {
            echo '<div id="tablediv">';
            //print_r($_POST);
            echo "failed to edit";
            echo '</div>';
        }
    }
    if ($_POST['submit']== 'modify-member') {
        $successfullyInserted = 0;
        //print_r($_POST);

        //for loop runs to check if the non empty inputs are incorrect (i.e. not  existing in cutabove clg_reg column)
        for ($i=1; $i < 31 ; $i++) {
            if ($_POST['member_id_'.$i] != '' and $_POST['member_id_'.$i] != '0') {
                if (is_numeric($_POST['member_id_'.$i])) {
                    //$_POST['member_id_'.$i]) is a string
                    $query_check = "SELECT * FROM `cutabove` WHERE clg_reg='".mysqli_real_escape_string($link, $_POST['member_id_'.$i])."' LIMIT 1";
                    $result_check = mysqli_query($link, $query_check);
                    $row_check = mysqli_fetch_array($result_check);
                    if (mysqli_num_rows($result_check) == 0) {
                        //if there is no clg_reg matching that posted value, then 0 rows exist, so assign an error to the corresponding array index
                        $error_mem[$i] = "The registration #<b>".$_POST['member_id_'.$i]."</b> doesn't exist";
                    } elseif ($row_check[$row['level_name']]!=0 and $row_check[$row['level_name']]!=$row['workshop_id']) {
                        $error_mem[$i] = 'The entry <b>'.$_POST['member_id_'.$i].'</b> ('.$row_check['name'].') has already <b>completed</b> this level workshop at <a href="supervisor_workshop.php?workshop_id='.$row_check[$row['level_name']].'" target="_blank">#'.$row_check[$row['level_name']].'</a>';
                    } elseif ($row_check[$row['level_name'].'_applied_for']!=0 and $row_check[$row['level_name'].'_applied_for']!=$row['workshop_id']) {
                        $error_mem[$i] = 'The entry <b>'.$_POST['member_id_'.$i].'</b> ('.$row_check['name'].') has already <b>applied for</b> this level workshop at <a href="supervisor_workshop.php?workshop_id='.$row_check[$row['level_name'].'_applied_for'].'" target="_blank">#'.$row_check[$row['level_name'].'_applied_for'].'</a>';
                    }
                } else {
                    $error_mem[$i] = "The entry <b>".$_POST['member_id_'.$i]."</b> is not a number";
                }
            }
        }
        //runs only if $error_mem is empty. i.e. no errors in input
        $comment = '';
        if (empty($error_mem)) {
            for ($i=1; $i < 31 ; $i++) {
                if ($row['member_id_'.$i]!= $_POST['member_id_'.$i]) {
                    //update workshop seat with new value
                    $query = "UPDATE `cutabove_workshop`
					SET `member_id_".$i."` = '".mysqli_real_escape_string($link, $_POST['member_id_'.$i])."'
					WHERE `cutabove_workshop`.`workshop_id` = '".$row['workshop_id']."' LIMIT 1";
                    if (mysqli_query($link, $query)) {
                        //update old student of the seat with 0 for his "_applied_for" column
                        $query = "UPDATE `cutabove`
						SET `".$row['level_name']."_applied_for` = '0'
						WHERE `clg_reg` = '".$row['member_id_'.$i]."' LIMIT 1";
                        if (mysqli_query($link, $query)) {
                            //update new student's (levelname)"_applied_for" column
                            $query = "UPDATE `cutabove`
							SET `".$row['level_name']."_applied_for` = '".$row['workshop_id']."'
							WHERE `clg_reg` = '".$_POST['member_id_'.$i]."' LIMIT 1";
                            if (mysqli_query($link, $query)) {
                                $successfullyInserted = 1;
                                if ($row['member_id_'.$i] == 0) {
                                    $comment.= ' <u>New student</u> for seat #'.$i.', Roll: '.$_POST['member_id_'.$i];
                                } else {
                                    $comment.= ' <u>Replaced student</u> for seat #'.$i.', Old Roll: '.$row['member_id_'.$i].' New Roll: '.$_POST['member_id_'.$i];
                                }
                            }
                        }
                    }
                }
            }
            if ($successfullyInserted == 1) {
                //success
                //now insert a comment mentioning who got present/absent and by whom.
                commentInserter($comment, "Member Edit");
            } else {
                echo '<div id="tablediv">';
                //print_r($_POST);
                echo "failed to edit";
                echo '</div>';
            }
            //end if (empty($error_mem))
        } else {
            $error = '<ul class="mb-0"><li>There were errors in your input.</li>
			<li>Either <b><a href="supervisor_workshop.php?workshop_id='.$row['workshop_id'].'">Reload</a></b> the page to undo the errored inputs, or correct and submit again.</li>
			<li><span class="bg-warning">Notices</span>, if any, are displayed if any field was emptied while simultaneously any other field has an error. Can be safely ignored if you know what you&rsquo;re doing.</li>
			<li><span class="bg-success">Success</span> is displayed if no errors are found but there exists an error elsewhere. Correct all errors and submit again to register the seat.</li></ul>';
        }
    }
    /*Same code as for members, but this one is for supervisors */
    
    if ($_POST['submit']== 'supervisor-attendance') {
        $successfullyInserted = 0;
        $comment = '';
        for ($i=1; $i < 21 ; $i++) {
            if ($row['supervisor_id_'.$i]!= 0 and $_POST['supervisor_id_'.$i.'a']!= $row['supervisor_id_'.$i.'a']) {
                //if a supervisor id of the workshop is not 0, and its attendance column is not same as current submitted attendance value,
                //set the attendance column as 1 or 0

                if ($_POST['supervisor_id_'.$i.'a']== 1) {
                    $query = "UPDATE `cutabove_workshop`
					SET `supervisor_id_".$i."a` = '1'
					WHERE `cutabove_workshop`.`workshop_id` = '".$row['workshop_id']."' LIMIT 1";
                    if (mysqli_query($link, $query)) {
                        //if attendance updated in the workshop, find the supervisor id whose details has been sent through the form, and
                        $query3 = "UPDATE `cutabove_workshop_supervisors_applied_fo`
						SET `supervisor_attendance` = 1,
						attendance_updated_by = '".mysqli_real_escape_string($link, $_SESSION['id'])."'
						WHERE `workshop_id` = '".mysqli_real_escape_string($link, $row['workshop_id'])."' AND `supervisor_id` = '".mysqli_real_escape_string($link, $row['supervisor_id_'.$i])."' LIMIT 1";
                        
                        if (mysqli_query($link, $query3)) {
                            $successfullyInserted = 1;
                            $comment.= ' <u>Present</u>: id# '.$row['supervisor_id_'.$i];
                        }
                    }
                } elseif ($_POST['supervisor_id_'.$i.'a']== 0) {
                    $query = "UPDATE `cutabove_workshop`
					SET `supervisor_id_".$i."a` = '0'
					WHERE `cutabove_workshop`.`workshop_id` = '".$row['workshop_id']."' LIMIT 1";
                    if (mysqli_query($link, $query)) {
                        //if attendance updated in the workshop, find the supervisor id whose details has been sent through the form, and
                        $query3 = "UPDATE `cutabove_workshop_supervisors_applied_fo`
						SET `supervisor_attendance` = 0,
						attendance_updated_by = '".mysqli_real_escape_string($link, $_SESSION['id'])."'
						WHERE `workshop_id` = '".mysqli_real_escape_string($link, $row['workshop_id'])."' AND `supervisor_id` = '".mysqli_real_escape_string($link, $row['supervisor_id_'.$i])."' LIMIT 1";
                        
                        if (mysqli_query($link, $query3)) {
                            $successfullyInserted = 1;
                            $comment.= ' <u>Absent</u>: id# '.$row['supervisor_id_'.$i];
                        }
                    }
                }
            }
        }
        if ($successfullyInserted == 1) {
            //success
            //now insert a comment mentioning who got present/absent and by whom.
            commentInserter($comment, "Supervisor attendance");
        } else {
            echo '<div id="tablediv">';
            //print_r($_POST);
            echo "failed to edit";
            echo '</div>';
        }
    }

    if ($_POST['submit']== 'modify-supervisor') {
        $successfullyInserted = 0;
        //print_r($_POST);
        //Note. I hate code duplication. but the next few lines are duplicated again some lines below them.
        //Sorry. I can't live with myself knowing this exists. If a future maintainer sees this, find a smarter method please.


        //for loop runs to check if the non empty inputs are incorrect (i.e. not  existing in council database username column)
        for ($i=1; $i < 21 ; $i++) {
            if ($_POST['supervisor_id_'.$i] != '') {
                //if the Posted value is not empty, convert the post value i.e. username to council_id
                $query_convert_username_to_id = "SELECT `council_id` FROM `cutabove_council` WHERE username='".mysqli_real_escape_string($link, $_POST['supervisor_id_'.$i])."' LIMIT 1";
                $result_convert_username_to_id = mysqli_query($link, $query_convert_username_to_id);
                if (mysqli_num_rows($result_convert_username_to_id) == 0) {
                    //if there is no username matching that posted value, then 0 rows exist, so assign an error to the corresponding array index
                    $error_sup[$i] = "the username <b>".$_POST['supervisor_id_'.$i]."</b> doesn't exist";
                }
            }
        }
        //runs only if $error_sup is empty. i.e. no errors in input
        $comment = '';
        if (empty($error_sup)) {
            for ($i=1; $i < 21 ; $i++) {
                if ($_POST['supervisor_id_'.$i] != '') {
                    //if the Posted value is not empty, convert the post value i.e. username to council_id
                    $query_convert_username_to_id = "SELECT `council_id` FROM `cutabove_council` WHERE username='".mysqli_real_escape_string($link, $_POST['supervisor_id_'.$i])."' LIMIT 1";
                    $result_convert_username_to_id = mysqli_query($link, $query_convert_username_to_id);
                    if (mysqli_num_rows($result_convert_username_to_id) >0) {
                        //if there is a username matching that posted value, then an id will have been fetched
                        //so mysqli_num_rows will be >0, so assign the id to $row_convert_username_to_id
                        $row_convert_username_to_id = mysqli_fetch_array($result_convert_username_to_id);

                        if ($row['supervisor_id_'.$i]!= $row_convert_username_to_id['council_id']) {
                            //check that this fetched id is not same as pre existing value for that seat
                            //If not same, update workshop seat with new id and set corresponding attendance to 0
                            $query = "UPDATE `cutabove_workshop`
							SET `supervisor_id_".$i."` = ".mysqli_real_escape_string($link, $row_convert_username_to_id['council_id']).",
							`supervisor_id_".$i."a` = 0
							WHERE `cutabove_workshop`.`workshop_id` = '".$row['workshop_id']."' LIMIT 1";
                            if (mysqli_query($link, $query)) {
                                //if workshop seat successfully updated with new id,
                                if ($row['supervisor_id_'.$i] == 0) {
                                    //check that seat did not have preassigned person. if true, create new row in the
                                    //cutabove_workshop_supervisors_applied_fo table and populate values
                                    $query = "INSERT INTO `cutabove_workshop_supervisors_applied_fo` (`workshop_level`, `workshop_id`, `supervisor_id`, `supervisor_attendance`, `attendance_updated_by`)
									VALUES (
									'".mysqli_real_escape_string($link, $row['level_name'])."',
									'".mysqli_real_escape_string($link, $row['workshop_id'])."',
									'".mysqli_real_escape_string($link, $row_convert_username_to_id['council_id'])."',
									0,
									".mysqli_real_escape_string($link, $_SESSION['id']).")";
                                } else {
                                    //if the person was assigned to that seat, overwrite the seat value with new person's id
                                    //and reset the attendance to 0
                                    $query = "UPDATE `cutabove_workshop_supervisors_applied_fo`
									SET `supervisor_id` = ".mysqli_real_escape_string($link, $row_convert_username_to_id['council_id']).",
									`supervisor_attendance` = 0,
									`attendance_updated_by` = ".mysqli_real_escape_string($link, $_SESSION['id'])."
									WHERE `workshop_id` = '".mysqli_real_escape_string($link, $row['workshop_id'])."' AND `supervisor_id` = '".mysqli_real_escape_string($link, $row['supervisor_id_'.$i])."' LIMIT 1";
                                }
                                if (mysqli_query($link, $query)) {
                                    $successfullyInserted = 1;
                                    //the code below does not execute if the seat was emptied cuz parent 'if' statement throws it to 'else'
                                    if ($row['supervisor_id_'.$i] == 0) {
                                        $comment.= ' <u>New supervisor</u> for seat #'.$i.', id: '.$row_convert_username_to_id['council_id'].' (username: ('.$_POST['supervisor_id_'.$i].')';
                                    } else {
                                        $comment.= ' <u>Replaced supervisor</u> for seat #'.$i.', Old id: '.$row['supervisor_id_'.$i].' New id: '.$row_convert_username_to_id['council_id'].' (username: '.$_POST['supervisor_id_'.$i].')';
                                    }
                                }
                            }
                        }
                    }
                } elseif ($row['supervisor_id_'.$i] != 0) {
                    //here we know that the row was left empty (or the row was emptied) from the previous if statement
                    //so we check that the row wasn't empty beforehand itself, and if it indeed was not empty before
                    //it means to delete the previously existing data from `cutabove_workshop` and `cutabove_workshop_supervisors_applied_fo`
                    $query = "UPDATE `cutabove_workshop`
					SET `supervisor_id_".$i."` = 0, 
					`supervisor_id_".$i."a` = 0
					WHERE `cutabove_workshop`.`workshop_id` = '".$row['workshop_id']."' LIMIT 1";
                    if (mysqli_query($link, $query)) {
                        $query = "DELETE FROM `cutabove_workshop_supervisors_applied_fo` WHERE `workshop_id` = '".mysqli_real_escape_string($link, $row['workshop_id'])."' AND `supervisor_id` = '".mysqli_real_escape_string($link, $row['supervisor_id_'.$i])."' LIMIT 1";
                        if (mysqli_query($link, $query)) {
                            $successfullyInserted = 1;
                            $comment.= ' <u>Removed supervisor</u> for seat #'.$i.', Old id: '.$row['supervisor_id_'.$i];
                        }
                    }
                }
                //end for loop
            }
            if ($successfullyInserted == 1) {
                //success
                //now insert a comment mentioning who got present/absent and by whom.
                commentInserter($comment, "Supervisor Edit");
            } else {
                echo '<div id="tablediv">';
                //print_r($_POST);
                echo "failed to edit";
                echo '</div>';
            }
            //end if (empty($error_sup))
        } else {
            $error = '<ul class="mb-0"><li>There were errors in your input.</li>
			<li>Either <b><a href="supervisor_workshop.php?workshop_id='.$row['workshop_id'].'">Reload</a></b> the page to undo the errored inputs, or correct and submit again.</li>
			<li><span class="bg-warning">Notices</span>, if any, are displayed if any field was emptied while simultaneously any other field has an error. Can be safely ignored if you know what you&rsquo;re doing.</li>
			<li><span class="bg-success">Success</span> is displayed if no errors are found but there exists an error elsewhere. Correct all errors and submit again to register the seat.</li></ul>';
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

    <?php include 'header.php'; //css-theme detector?>

    <title>Workshop Manager!</title>
    <!--Hardcoded CSS for this page-->

    <!-- Icons -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css"
        integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">

    <style type="text/css">
    #member-seats {
        display: none;
    }

    #supervisor-seats {
        display: none;
    }

    #supervisor-attendance {
        display: none;
    }
    </style>

</head>

<body>
    <?php include 'navbar.php'; ?>
    <div id="tablediv">
        <div id="error"><?php if ($error!="") {
    echo '<div class="alert alert-danger mx-1" role="alert">'.$error.'</div>';
} ?></div>
        <form method="post">
            <h4>Viewing <?php echo ucfirst($row['level_name']).'-'.$row['memorisable_name'];?></h4>
            <table class="table" id="detailsTable">
                <thead class="thead-dark">
                    <tr>
                    <tr class="thead-dark">
                        <th>Parameter</th>
                        <th>Value</th>
                    </tr>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    echo '<tr><th scope="row">ID</th><td>' . $row['workshop_id'] . '<input type="hidden" name="workshop_id" value="'.$row['workshop_id'].'"></td></tr>';
                    if ($_SESSION['permission'] == 'admin') {
                        echo '<tr><th scope="row"><abbr title="Stage Name + User memorisable name">Name</abbr>: <span style="border-bottom:0px solid blue; text-decoration: blue underline">' .ucfirst($row['level_name']).'</span>-</th><td><input type="text" class="form-control" name="memorisable_name" placeholder="Memorisable Name"';

                        if ($row['memorisable_name']!='') {
                            echo ' value = "'.$row['memorisable_name'].'"';
                        }

                        echo '></td></tr>';

                        $date = DateTime::createFromFormat('Y-m-d H:i:s', $row['date'])->format('Y-m-d\TH:i');
                        echo '<tr><th scope="row">Date</th><td><input type="datetime-local" class="form-control" name="date" value="' . $date . '"></td></tr>';
                        echo '<tr><th scope="row">Feedback Link</th><td><input type="text" class="form-control" name="feedback_link" placeholder= "http://..." value="' . $row['feedback_link'] . '"></td></tr>';
                        echo '<tr><th scope="row">Bonus file link</th><td><input type="text" class="form-control" name="bonus_files" placeholder= "http://..." value="' . $row['bonus_files'] . '"></td></tr>';
                    } else {
                        echo '<tr><th scope="row"><abbr title="Stage Name + User memorisable name">Name</abbr></th><td>' . ucfirst($row['level_name']).'-'.$row['memorisable_name'] . '</td></tr>';
                        echo '<tr><th scope="row">Date</th><td>' . $row['date'] . '</td></tr>';
                        if ($row['completed'] == '1') {
                            echo '<tr><th scope="row">Feedback Link</th><td><a href="'.$row['feedback_link'].'" target="_blank">'.$row['feedback_link'].'</a></td></tr>';
                            echo '<tr><th scope="row">Bonus file link</th><td><a href="'.$row['bonus_files'].'" target="_blank">'.$row['bonus_files'].'</a></td></tr>';
                        }
                    }
                    $supervisorcount = $membercount = 0;
                    for ($i=1; $i < 31 ; $i++) {
                        if ($row['member_id_'.$i]!= 0) {
                            $membercount++;
                        }
                    }

                    for ($i=1; $i < 21 ; $i++) {
                        if ($row['supervisor_id_'.$i]!= 0) {
                            $supervisorcount++;
                        }
                    }
                    echo '<tr><th scope="row">Members Enrolled</th><td>'.$membercount.'/30</td></tr>';
                    echo '<tr><th scope="row">Supervisors Enrolled</th><td>'.$supervisorcount.'/20</td></tr>';

                    if ($_SESSION['permission'] == 'admin' or $row['completed'] != '1') {
                        echo '<tr><th scope="row">Completed</th><td><select class="form-control" name="completed">';
                        if ($row['completed'] == '1') {
                            echo '<option selected value="1">Yes</option><option value="0">No</option>';
                        } else {
                            echo '<option value="1">Yes</option><option selected value="0">No</option>';
                        }
                        echo '</select></td></tr>'; ?>
                    <tr>
                        <th scope="row">Actions</th>
                        <td>
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-outline-success active" title="Show Member attendance table">
                                    <input type="radio" name="showtables" value="member-attendance-show"
                                        autocomplete="off" checked> <i class="fas fa-check"></i>
                                </label>
                                <label class="btn btn-outline-success" title="Show Member Edit table">
                                    <input type="radio" name="showtables" value="member-edit-show" autocomplete="off">
                                    <i class="far fa-edit"></i>
                                </label>
                                <!--Supervisor has no use viewing supervisor editing buttons -->
                                <?php if ($_SESSION['permission'] == 'admin') {
                            ?>
                                <label class="btn btn-outline-primary" title="Show Supervisor attendance table">
                                    <input type="radio" name="showtables" value="supervisor-attendance-show"
                                        autocomplete="off"> <i class="fas fa-check"></i>
                                </label>
                                <label class="btn btn-outline-primary" title="Show Supervisor Edit table">
                                    <input type="radio" name="showtables" value="supervisor-edit-show"
                                        autocomplete="off"> <i class="far fa-edit"></i>
                                </label>
                                <?php
                        } ?>
                            </div>
                        </td>

                        <?php
                    }
                    if ($row['completed'] == '1') {
                        $query1 = "SELECT * FROM `cutabove_council` WHERE council_id = ".$row['supervisor_id']." LIMIT 1";
                        $result1 = mysqli_query($link, $query1);
                        $row1 = mysqli_fetch_array($result1);
                        if ($row1['permission'] == 'admin') {
                            echo '<tr><th scope="row">Completed By</th><td><a href="all-council.php?id='.$row1['council_id'].'" class="badge badge-danger">'.$row1['name'].'</a></td></tr>';
                        } else {
                            echo '<tr><th scope="row">Completed By</th><td><a href="all-council.php?id='.$row1['council_id'].'" class="badge badge-primary">'.$row1['name'].'</a></td></tr>';
                        }
                    }
                    echo '<tr><th scope="row">Comments</th><td><button type="button" id="show-hide-comments" class="btn btn-outline-'.$_SESSION['colour'].'">Toggle Comments</button><div id="comments">' . $row['comments'] . '</div></td></tr>';

                    if (($_SESSION['permission'] == 'admin' or $row['completed'] != '1') and $view_Supervisor_Oriented_buttons == 1) {
                        echo '<tr><th scope="row">Enter New Comment</th><td><textarea class="form-control" name="comments" placeholder="The new text you add will be appended to the previous pieces of text" onKeyDown="limitText(this.form.comments,this.form.countdown,280);" 
						onKeyUp="limitText(this.form.comments,this.form.countdown,280);"></textarea>';
                        echo '<font size="1">You have <input readonly type="text" name="countdown" size="3" value="280"> characters left. Same as Twitter</font></td></tr>';
                        echo '<tr><td><button type="submit" name="submit" class="btn btn-'.$_SESSION['colour'].'" value="basicdata">Submit Data</button></td><td></td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </form>
    </div>
    <!-- /* Member Management */ -->
    <form method="post" id="member-attendance">
        <h2>Members: Attendance</h2>
        <table class="table table-striped table-responsive-sm" id="detailsTable">
            <thead class="thead-dark">
                <tr>
                <tr class="thead-dark">
                    <th>Reg #</th>
                    <th>Name</th>
                    <th>Attendance</th>
                </tr>
                </tr>
            </thead>
            <tbody>
                <?php
                for ($i=1; $i < 31 ; $i++) {
                    if ($row['member_id_'.$i]!= 0) {
                        $query2 = "SELECT * FROM `cutabove` WHERE clg_reg=".$row['member_id_'.$i]." LIMIT 1";
                        $result2 = mysqli_query($link, $query2);
                        $row2= mysqli_fetch_array($result2);
                        
                        echo '<tr><td>'. $row['member_id_'.$i].'</td>';
                        //hacky method to know if the user exists in databse.
                        if (mysqli_num_rows($result2)==0) {
                            echo '<td><code>User Deleted</code></td>';
                        } else {
                            echo '<td><a href="one-person.php?id=' . $row2['id'] . '">' . $row2['name'] . '</a></td>';
                        }
                        echo '<td><select class="form-control" name="member_id_'.$i.'a">';
                        if ($row['member_id_'.$i.'a']!='0' and $row2[''.$row['level_name']]!='0') {
                            echo '<option selected value="1">Yes</option><option value="0">No</option>';
                        } else {
                            echo '<option value="1">Yes</option><option selected value="0">No</option>';
                        }
                        echo '</select>';
                        
                        /* eg: if the member's stg1w1!=0 (means he has completed stg1w1 (the database value for stg1w1 can be 0 or workshop-completed-number, the database value for stg1w1_applied_for is same logic)), AND his attendance for this workshop is 0, it's obvious he has completed in another workshop */
                        if ($row2[$row['level_name']]!='0' and $row['member_id_'.$i.'a']=='0') {
                            echo '<small class="text-muted">Previously has attended this level workshop at <a href="supervisor_workshop.php?workshop_id='.$row2[$row['level_name']].'" target="_blank">#'.$row2[$row['level_name']].'</a></small>';
                        }
                        echo '</td></tr>';
                    }
                }
                if (($_SESSION['permission'] == 'admin' or $row['completed'] != '1') and $view_Supervisor_Oriented_buttons == 1) {
                    echo '<tr><td></td><td></td><td><button type="submit" name="submit" class="btn btn-'.$_SESSION['colour'].'" value="member-attendance">Update Attendance</button></td></tr>';
                }
                ?>
            </tbody>
        </table>
    </form>

    <form method="post" id="member-seats">
        <h2>Members: Modify</h2>
        <table class="table table-striped table-responsive-sm" id="detailsTable">
            <thead class="thead-dark">
                <tr>
                <tr class="thead-dark">
                    <th>#</th>
                    <th>Name</th>
                    <th>Allocation</th>
                </tr>
                </tr>
            </thead>
            <tbody>
                <?php
                for ($i=1; $i < 31 ; $i++) {
                    if ($row['member_id_'.$i] != 0) {
                        // no need to search database if no member id is present in the first place
                        $query2 = "SELECT * FROM `cutabove` WHERE clg_reg =".$row['member_id_'.$i]." LIMIT 1";
                        $result2 = mysqli_query($link, $query2);
                        $row2= mysqli_fetch_array($result2);
                    } else {
                        $row2= [];
                    }
                    
                    echo '<tr><td>'.$i.'</td><td><a href="one-person.php?id=' . $row2['id'] . '">' . $row2['name'] . '</a></td><td>';

                    if (array_key_exists($i, $error_mem) and $error_mem[$i]!= '') {
                        //if correspong error exists, tell the error and let them know the previous working value
                        echo '<div id="error"><div class="alert alert-danger" role="alert"><b>Error</b>: '.$error_mem[$i];
                        if ($row['member_id_'.$i] != '') {
                            echo '. Prev entry was: <b>'.$row['member_id_'.$i].'</b>';
                        }
                        echo '</div></div>';
                    } elseif (isset($_POST['member_id_'.$i]) and ($_POST['member_id_'.$i]== '' or $_POST['member_id_'.$i]== 0) and $row['member_id_'.$i] != 0) {
                        /* will only display if no errors exist (cuz only then will header() will have run)
                        so if any error exists anywhere at all and this input was also emptied before submission,
                        it will let the user know that the field was emptied
                        normally, this would have gone through since leaving empty is not an error, but since there are other errors,
                        might as well notify of everything

                        code fix: was executing even on a fresh load page cuz if the post['var'] is not set, it is equal to ''.
                        So added that condition to check that the var is set
                        */
                        echo '<div class="alert alert-warning" role="alert"><b>Notice</b>: You emptied this row. Prev entry was <b>'.$row['member_id_'.$i].'</b></div>';
                    } elseif (isset($_POST['member_id_'.$i]) and $_POST['member_id_'.$i] != $row['member_id_'.$i]) {
                        /* same as above, but to confirm in the wake of an error, that this entry was successful.
                        You just need to hit submit after rectifying all errors
                        */
                        echo '<div class="alert alert-success" role="alert"><b>Success</b>: Modified entry. Prev entry was <b>'.$row['member_id_'.$i].'</b></div>';
                    }

                    echo '<input type="text" class="form-control" name="member_id_'.$i.'" value="';

                    if (isset($_POST['member_id_'.$i])) {
                        //whatever clg_reg has been entered
                        echo $_POST['member_id_'.$i];
                    } else {
                        echo $row['member_id_'.$i];
                    }

                    if ($row['member_id_'.$i.'a']!=0) {
                        echo '" readonly><small class="text-muted">disabled for edits because attendance marked.</small>';
                    } elseif ($row['member_id_'.$i]!=0 and $row2[$row['level_name']]!='0') {
                        echo '"><small class="text-muted">Attendance has been given at Workshop <a href="supervisor_workshop.php?workshop_id='.$row2[$row['level_name']].'" target="_blank">#'.$row2[$row['level_name']].'</a></small>';
                    } else {
                        echo '">';
                    }
                    echo '</td></tr>';
                }
                if ($row['completed'] != '1') {
                    echo '<tr><td></td><td></td><td><button type="submit" name="submit" class="btn btn-'.$_SESSION['colour'].'" value="modify-member">Update Member Seats</button></td></tr>';
                }
                ?>
            </tbody>
        </table>
    </form>

    <!-- /* Supervisor Management */ -->
    <form method="post" id="supervisor-attendance">
        <h2>Supervisors: Attendance</h2>
        <table class="table table-striped table-responsive-sm" id="detailsTable">
            <thead class="thead-dark">
                <tr>
                <tr class="thead-dark">
                    <th>#</th>
                    <th>Name</th>
                    <th>Attendance</th>
                </tr>
                </tr>
            </thead>
            <tbody>
                <?php
                for ($i=1; $i < 21 ; $i++) {
                    //generates list of names from the $row['supervisor_id_'.$i] id value, and creates select with name = the seat number
                    //sends 1 for yes, 0 for no
                    if ($row['supervisor_id_'.$i]!= 0) {
                        //query to obtain name of the associated id
                        $query3 = "SELECT `name` FROM `cutabove_council` WHERE council_id=".$row['supervisor_id_'.$i]." LIMIT 1";
                        $result3 = mysqli_query($link, $query3);
                        $row3= mysqli_fetch_array($result3);
                        echo '<tr><td>'. $row['supervisor_id_'.$i].'</td>';
                        if (mysqli_num_rows($result3)==0) {
                            echo '<td><code>User Deleted</code></td>';
                        } else {
                            echo '<td><a href="all-council.php?id=' . $row['supervisor_id_'.$i] . '">' . $row3['name'] . '</a></td>';
                        }
                        //query to the cutabove_workshop_supervisors_applied_fo database to see their attendance status
                        $query_confirm = "SELECT * FROM `cutabove_workshop_supervisors_applied_fo`
						WHERE `workshop_id` = '".mysqli_real_escape_string($link, $row['workshop_id'])."' AND `supervisor_id` = '".mysqli_real_escape_string($link, $row['supervisor_id_'.$i])."' LIMIT 1";
                        $result_confirm = mysqli_query($link, $query_confirm);
                        $row_confirm= mysqli_fetch_array($result_confirm);

                        //check that both the workshop and the cutabove_workshop_supervisors_applied_fo have the supervisor's attendance the same
                        //only then declare the attendance as yes or no
                        echo '<td><select class="form-control" name="supervisor_id_'.$i.'a">';
                        if ($row['supervisor_id_'.$i.'a']!='0' and $row_confirm['supervisor_attendance']!='0') {
                            echo '<option selected value="1">Yes</option><option value="0">No</option>';
                        } else {
                            echo '<option value="1">Yes</option><option selected value="0">No</option>';
                        }
                        echo '</select></td></tr>';
                    }
                }
                if (($_SESSION['permission'] == 'admin' or $row['completed'] != '1') and $view_Supervisor_Oriented_buttons == 1) {
                    echo '<tr><td></td><td></td><td><button type="submit" name="submit" class="btn btn-'.$_SESSION['colour'].'" value="supervisor-attendance">Update Attendance</button></td></tr>';
                }
                ?>
            </tbody>
        </table>
    </form>

    <form method="post" id="supervisor-seats">
        <h2>Supervisors: Modify</h2>
        <table class="table table-striped table-responsive-sm" id="detailsTable">
            <thead class="thead-dark">
                <tr>
                <tr class="thead-dark">
                    <th>#</th>
                    <th>Name</th>
                    <th>Allocation</th>
                </tr>
                </tr>
            </thead>
            <tbody>
                <?php
                for ($i=1; $i < 21 ; $i++) {
                    $query3 = "SELECT `name`, `username` FROM `cutabove_council` WHERE council_id=".$row['supervisor_id_'.$i]." LIMIT 1";
                    $result3 = mysqli_query($link, $query3);
                    $row3= mysqli_fetch_array($result3);
                    
                    echo '<tr><td>'.$i.'</td>';
                    if (mysqli_num_rows($result3)==0 and $row['supervisor_id_'.$i]!=0) {
                        //means no name member data was returned, but the seat has a value other than 0 (meaning someone did exist)
                        echo '<td><code>User Deleted. Their council_id was '.$row['supervisor_id_'.$i].'.</code><br/>';
                        echo '<small class="text-muted">If you modify supervisors, this info too will disappear.</small></td>';
                    } else {
                        echo '<td><a href="all-council.php?id=' . $row['supervisor_id_'.$i] . '">' . $row3['name'] . '</a></td>';
                    }
                    echo '<td>';
                    
                    if (array_key_exists($i, $error_sup) and $error_sup[$i]!= '') {
                        //if correspong error exists, tell the error and let them know the previous working value
                        echo '<div id="error"><div class="alert alert-danger" role="alert"><b>Error</b>: '.$error_sup[$i];
                        if ($row3['username'] != '') {
                            echo '. Prev entry was: <b>'.$row3['username'].'</b>';
                        }
                        echo '</div></div>';
                    } elseif (isset($_POST['supervisor_id_'.$i]) and $_POST['supervisor_id_'.$i]== '' and $row3['username'] != '') {
                        /* will only display if no errors exist (cuz only then will header() will have run)
                        so if any error exists anywhere at all and this input was also emptied before submission,
                        it will let the user know that the field was emptied
                        normally, this would have gone through since leaving empty is not an error, but since there are other errors,
                        might as well notify of everything

                        code fix: was executing even on a fresh load page cuz if the post['var'] is not set, it is equal to ''.
                        So added that condition to check that the var is set
                        */
                        echo '<div class="alert alert-warning" role="alert"><b>Notice</b>: You emptied this row. Prev entry was <b>'.$row3['username'].'</b></div>';
                    } elseif (isset($_POST['supervisor_id_'.$i]) and $_POST['supervisor_id_'.$i] != $row3['username']) {
                        /* same as above, but to confirm in the wake of an error, that this entry was successful.
                        You just need to hit submit after rectifying all errors
                        */
                        echo '<div class="alert alert-success" role="alert"><b>Success</b>: Modified entry. Prev entry was <b>'.$row3['username'].'</b></div>';
                    }
                    echo '<input type="text" class="form-control" name="supervisor_id_'.$i.'" value="';

                    if (isset($_POST['supervisor_id_'.$i])) {
                        //whatever username has been entered
                        echo $_POST['supervisor_id_'.$i];
                    } else {
                        echo $row3['username'];
                    }

                    echo '">';
                    echo '</td></tr>';
                }
                if ($row['completed'] != '1') {
                    echo '<tr><td></td><td></td><td><button type="submit" name="submit" class="btn btn-'.$_SESSION['colour'].'" value="modify-supervisor">Update Supervisor Seats</button></td></tr>';
                }
                ?>
            </tbody>
        </table>
    </form>

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
    $('input[type=radio][name=showtables]').change(function() {
        if (this.value == 'member-attendance-show') {
            $("#member-attendance").show();
            $("#member-seats").hide();
            $("#supervisor-attendance").hide();
            $("#supervisor-seats").hide();
        } else if (this.value == 'member-edit-show') {
            $("#member-attendance").hide();
            $("#member-seats").show();
            $("#supervisor-attendance").hide();
            $("#supervisor-seats").hide();
        } else if (this.value == 'supervisor-attendance-show') {
            $("#member-attendance").hide();
            $("#member-seats").hide();
            $("#supervisor-attendance").show();
            $("#supervisor-seats").hide();
        } else if (this.value == 'supervisor-edit-show') {
            $("#member-attendance").hide();
            $("#member-seats").hide();
            $("#supervisor-attendance").hide();
            $("#supervisor-seats").show();
        }
    });
    </script>
    <script type="text/javascript">
    function limitText(limitField, limitCount, limitNum) {
        if (limitField.value.length > limitNum) {
            limitField.value = limitField.value.substring(0, limitNum);
        } else {
            limitCount.value = limitNum - limitField.value.length;
        }
    }

    $("#show-hide-comments").click(function() {
        $("#comments").toggle();
        $("#show-hide-comments").button('toggle');
    });
    </script>
</body>

</html>