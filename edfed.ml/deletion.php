<?php
session_start();

$error="";

if ($_SESSION['permission'] == 'admin') {
    if ($_SESSION['god']!= 1) {
        echo '<html><head><meta http-equiv="refresh" content="1;url=council-dashboard.php"></head><body><h1>Go back to <a href="council-dashboard.php">Dashboard</a> and enable <b>God Mode</b></h1>Redirecting to dashboard</body></html>';
        die();
    } else {
        include('connect-db.php');
    }

    # protect against URLs that have complete deleting option hardcoded
    if (array_key_exists("malicious_link_protect", $_SESSION)) {
        # do nothing if it exists
        # else define it and then redirect the page to a non deleting instance
        # caution: Once the user visits this page even once normally, a 2nd click on the link will cause actions to be executed
    } else {
        $_SESSION['malicious_link_protect'] = 'mitigate direct URL deletion attempt since this page wasn\'t visited befpore';

        if (array_key_exists("clg_reg", $_GET)) {
            header("Location: deletion.php?clg_reg=".$_GET['clg_reg']);
        } elseif (array_key_exists("council_username", $_GET)) {
            header("Location: deletion.php?council_username=".$_GET['council_username']);
        } elseif (array_key_exists("wk_id", $_GET)) {
            header("Location: deletion.php?wk_id=".$_GET['wk_id']);
        } else {
            header("Location: deletion.php");
        }
    }
} else {
    header("Location: index.php?redirect=".$_SERVER['REQUEST_URI']);
}

function delete_member($stg_w_, $row_member, &$display_alert, $link)
{
    if ($row_member[$stg_w_.'_applied_for']!=0) {
        # check if his applied for status is set to a workshop id for any of those workshop stages
        # if yes, then get that workshop based on the workshop id and workshop level
        $query_workshop = "SELECT * FROM `cutabove_workshop` WHERE workshop_id = '".mysqli_real_escape_string($link, $row_member[$stg_w_.'_applied_for'])."' AND level_name = '".$stg_w_."' LIMIT 1";
        $result_workshop = mysqli_query($link, $query_workshop);
        $row_workshop = mysqli_fetch_array($result_workshop);
        for ($i=1; $i <21; $i++) {
            # now loop through all 20 workshop seats and see if any seat matches the college registration of that member (means he applied)
            if ($row_workshop['member_id_'.$i]==mysqli_real_escape_string($link, $_GET['clg_reg'])) {
                # check what instructions are given. i.e. Remove only applied?, or both applied and completed?
                if ($_GET['applied-completed']=='only-applied') {
                    if ($row_workshop['member_id_'.$i.'a']==0) {
                        # he didn't complete the workshop (even though he applied). Therefore, remove all evidence of applying
                        $display_alert.= 'Removing "applied for" for Member:'.$_GET['clg_reg'].'. Match found at seat #'.$i.' for Workshop#'.$row_member[$stg_w_.'_applied_for'].' (Workshop level: '.$stg_w_.')<br />';

                        $query_update_workshop = "UPDATE `cutabove_workshop` SET `member_id_".$i."` = '0' WHERE workshop_id = '".mysqli_real_escape_string($link, $row_workshop['workshop_id'])."' LIMIT 1";
                        if (mysqli_query($link, $query_update_workshop)) {
                            $display_alert.='--> done <br />';
                        } else {
                            $display_alert.='--> failed ('.$query_update_workshop.')<br />';
                        }
                    }
                } elseif ($_GET['applied-completed']=='applied-completed') {
                    $display_alert.= 'Removing "completed" and "applied for" for Member:'.$_GET['clg_reg'].'. Match found at seat #'.$i.' for Workshop#'.$row_member[$stg_w_.'_applied_for'].' (Workshop level: '.$stg_w_.')<br />';

                    $query_update_workshop = "UPDATE `cutabove_workshop` SET `member_id_".$i."` = '0', `member_id_".$i."a` = '0' WHERE workshop_id = '".mysqli_real_escape_string($link, $row_workshop['workshop_id'])."' LIMIT 1";
                    if (mysqli_query($link, $query_update_workshop)) {
                        $display_alert.='--> done <br />';
                    } else {
                        $display_alert.='--> failed ('.$query_update_workshop.')<br />';
                    }
                }
            }
        }
    }
}

$display_alert = '';
if (array_key_exists("type", $_GET)) {
    if ($_GET['type']=='member' and $_GET['clg_reg']!='') {
        $display_alert.= '<h3>Member deletion</h3>';
        $display_alert.= 'In process to remove Reg #'.$_GET['clg_reg'].'...<br />';
        if (isset($_GET['applied-completed'])) {
            $query_member = "SELECT * FROM `cutabove` WHERE clg_reg = '".mysqli_real_escape_string($link, $_GET['clg_reg'])."' LIMIT 1";
            $result_member = mysqli_query($link, $query_member);
            
            if (mysqli_num_rows($result_member)!= 0) {
                # if such a user exists (means rows returned>0), then only proceed
                $row_member = mysqli_fetch_array($result_member);
                $display_alert.= 'Removing ClgReg: '.$_GET['clg_reg'].', Name: '.$row_member['name'].', ID# '.$row_member['id'].'<br />';

                # stg1
                for ($i=1; $i <5; $i++) {
                    # loop through all workshops of that stage
                    delete_member('stg1w'.$i, $row_member, $display_alert, $link);
                }
                #stg2
                for ($i=1; $i <6; $i++) {
                    # loop through all workshops of that stage
                    delete_member('stg2w'.$i, $row_member, $display_alert, $link);
                }
                # after all stages are cleared, delete the user himself here
                $query_delete_member = "DELETE FROM `cutabove` WHERE clg_reg = '". $_GET['clg_reg'] ."' LIMIT 1";
                if (mysqli_query($link, $query_delete_member)) {
                    $display_alert.='Deleted Member Successfully <br />';
                } else {
                    $display_alert.='-->member Deletion failed ('.$query_delete_member.')<br />';
                }
            } else {
                $display_alert.= 'User Does not exist';
            }
        }
    }


    if ($_GET['type']=='council' and $_GET['council_username']!= '') {
        $display_alert.= '<h3>Council deletion</h3>';
        $display_alert.= 'In process to remove Core username: '.$_GET['council_username'].'...<br />';
        if (isset($_GET['workshop'])) {
            $query_council = "SELECT * FROM `cutabove_council` WHERE username = '".mysqli_real_escape_string($link, $_GET['council_username'])."' LIMIT 1";
            $result_council = mysqli_query($link, $query_council);
            
            # make sure the username exists
            if (mysqli_num_rows($result_council)!= 0) {
                $row_council = mysqli_fetch_array($result_council);
                $display_alert.= 'Removing Username: '.$_GET['council_username'].', ID#'.$row_council['council_id'].'<br />';

                # collect all his workshop applications (regardless of completed or not)
                $query_council_registered = "SELECT * FROM `cutabove_workshop_supervisors_applied_fo` WHERE supervisor_id = '".mysqli_real_escape_string($link, $row_council['council_id'])."'";
                $result_council_registered = mysqli_query($link, $query_council_registered);

                # check that at least there is one entry for application
                if (mysqli_num_rows($result_council_registered)!= 0) {

                    # go over every application
                    while ($row_council_registered = mysqli_fetch_array($result_council_registered)) {

                        # collect full workshop data for that workshop application
                        # Technical: The workshop query is run for every applied/completed entry found. This way, code duplication is less.
                        $query_workshop_supervisors = "SELECT * FROM `cutabove_workshop` WHERE workshop_id = '".mysqli_real_escape_string($link, $row_council_registered['workshop_id'])."' AND level_name = '".$row_council_registered['workshop_level']."' LIMIT 1";
                        $result_workshop_supervisors = mysqli_query($link, $query_workshop_supervisors);
                        $row_workshop_supervisors = mysqli_fetch_array($result_workshop_supervisors);

                        if ($_GET['workshop'] == 'applied') {
                            // Vacating their seat from any workshop they applied to (but did not or have not yet attended)

                            # make sure he hasn't attended
                            if ($row_council_registered['supervisor_attendance']==0) {
                                $display_alert.='Removing from workshop #  <a href="supervisor_workshop.php?workshop_id='.$row_council_registered['workshop_id'].'" target="_blank">'.$row_council_registered['workshop_id'].'</a> (Applied to, but didn\'t or has\'t yet attended)';

                                if (mysqli_num_rows($row_workshop_supervisors)==0) {
                                    $display_alert.='-->Workshop does not exist.<br />';
                                } else {
                                    for ($i=1; $i <21; $i++) {
                                        # In ['supervisor_id_'.$i.'a'], 'a' at the end means 'attended' (value is 0 for not, 1 for yes)
                                        # But in ['supervisor_id_'.$i], value is 0 for empty seat, any other number is the council_id

                                        # loop over all seats and find the one with his council_id, AND also check again that he hasn't attended
                                        if ($row_workshop_supervisors['supervisor_id_'.$i]== $row_council['council_id'] and $row_workshop_supervisors['supervisor_id_'.$i.'a']==0) {
                                            $display_alert.='--->Found on seat #'.$i.'<br />';
                                            $query_update_workshop = "UPDATE `cutabove_workshop` SET `supervisor_id_".$i."` = 0, `supervisor_id_".$i."a` = 0 WHERE workshop_id = '".mysqli_real_escape_string($link, $row_workshop_supervisors['workshop_id'])."' LIMIT 1";
                                            if (mysqli_query($link, $query_update_workshop)) {
                                                $display_alert.='--> done <br />';
                                            } else {
                                                $display_alert.='--> failed ('.$query_update_workshop.')<br />';
                                            }
                                        }
                                    }
                                }
                            }
                        } elseif ($_GET['workshop'] == 'completed') {
                            // Erasing their existance from all workshops. (applied and attended workshops)

                            if ($row_council_registered['supervisor_attendance']==1) {
                                $display_alert.='Removing from workshop # <a href="supervisor_workshop.php?workshop_id='.$row_council_registered['workshop_id'].'" target="_blank">'.$row_council_registered['workshop_id'].'</a> (Applied and attended)';
                            } elseif ($row_council_registered['supervisor_attendance']==0) {
                                $display_alert.='Removing from workshop # <a href="supervisor_workshop.php?workshop_id='.$row_council_registered['workshop_id'].'" target="_blank">'.$row_council_registered['workshop_id'].'</a> (Applied to, but didn\'t or has\'t yet attended)';
                            }

                            if (mysqli_num_rows($row_workshop_supervisors)==0) {
                                $display_alert.='-->Workshop does not exist.<br />';
                            } else {
                                for ($i=1; $i <21; $i++) {
                                    # In ['supervisor_id_'.$i.'a'], 'a' at the end means 'attended' (value is 0 for not, 1 for yes)
                                    # But in ['supervisor_id_'.$i], value is 0 for empty seat, any other number is the council_id
                                    if ($row_workshop_supervisors['supervisor_id_'.$i]==mysqli_real_escape_string($link, $row_council['council_id'])) {
                                        $display_alert.='--->Found on seat #'.$i.'<br />';

                                        $query_update_workshop = "UPDATE `cutabove_workshop` SET `supervisor_id_".$i."` = 0, `supervisor_id_".$i."a` = 0 WHERE workshop_id = '".mysqli_real_escape_string($link, $row_workshop_supervisors['workshop_id'])."' LIMIT 1";
                                        if (mysqli_query($link, $query_update_workshop)) {
                                            $display_alert.='--> done <br />';
                                        } else {
                                            $display_alert.='--> failed ('.$query_update_workshop.')<br />';
                                        }
                                    }
                                }
                            }
                        }
                        # delete The redundant entry about workshop + supervisor id relation, since it was relevant for supervisor only. Not workshop
                        $query_delete_relations = "DELETE FROM `cutabove_workshop_supervisors_applied_fo` WHERE supervisor_id = '".mysqli_real_escape_string($link, $row_council['council_id'])."' AND workshop_id=".$row_council_registered['workshop_id']." LIMIT 1";
                        if (mysqli_query($link, $query_delete_relations)) {
                            $display_alert.='..--> Removed relation for workshop # <a href="supervisor_workshop.php?workshop_id='.$row_council_registered['workshop_id'].'" target="_blank">'.$row_council_registered['workshop_id'].'</a><br />';
                        } else {
                            $display_alert.='..--> failed to remove relation for workshop # <a href="supervisor_workshop.php?workshop_id='.$row_council_registered['workshop_id'].'" target="_blank">'.$row_council_registered['workshop_id'].'</a> ('.$query_delete_relations.')<br />';
                        }
                    }
                } else {
                    $display_alert.='The user has never applied for a workshop. <br />';
                }

                # delete the user here
                $query_delete_council = "DELETE FROM `cutabove_council` WHERE username = '".mysqli_real_escape_string($link, $_GET['council_username'])."' LIMIT 1";
                if (mysqli_query($link, $query_delete_council)) {
                    $display_alert.='Removed Core Member<br />';
                } else {
                    $display_alert.='Failed to remove core ('.$query_delete_council.')<br />';
                }
            } else {
                $display_alert.= 'Username Does not exist';
            }
        }
    }

    if ($_GET['type']=='workshop' and $_GET['wk_id']!='' and $_GET['wk_id']!=1) {
        //They chose to delete a workshop, and gave a workshop id and didn't give the ID as 1 (genesis workshop should not be deleted)
        $display_alert.= '<h3>Workshop deletion</h3>';
        $display_alert.= 'In process to remove Workshop #'.$_GET['wk_id'].'...<br />';
        if (isset($_GET['member']) and isset($_GET['supervisor'])) {
            $query_workshop = "SELECT * FROM `cutabove_workshop` WHERE workshop_id = '".mysqli_real_escape_string($link, $_GET['wk_id'])."' LIMIT 1";
            $result_workshop = mysqli_query($link, $query_workshop);
            
            # make sure the workshop exists
            if (mysqli_num_rows($result_workshop)!= 0) {
                $row_workshop = mysqli_fetch_array($result_workshop);
                $display_alert.= 'Removing Workshop #'.$_GET['wk_id'].'. Level: '.$row_workshop['level_name'].', Name: '.$row_workshop['memorisable_name'].'<br />';

                for ($i=1; $i < 21; $i++) {
                    if ($_GET['member']=='applied') {
                        if ($row_workshop['member_id_'.$i]!=0) {
                            //seat not empty (aka, applied)
                            if ($row_workshop['member_id_'.$i.'a']==0) {
                                //seat not completed
                                $display_alert.= 'Removing from member\'s page that they applied for this workshop. (but only if not attended): Clg Reg# <a href="one-person.php?clg_reg='.$row_workshop['member_id_'.$i].'" target="_blank">'.$row_workshop['member_id_'.$i].'</a><br />';

                                # only the applied_for will have a value, because reaching here means attended is 0,
                                # setting a parameter to execute the removal query later on and prevent code duplication
                                $go_ahead_with_removal = 1;
                            } else {
                                //seat completed
                                $display_alert.= 'Member attended this workshop, hence their personal record not removed: Clg Reg# <a href="one-person.php?clg_reg='.$row_workshop['member_id_'.$i].'" target="_blank">'.$row_workshop['member_id_'.$i].'</a><br />';
                            }
                        }
                    } elseif ($_GET['member']=='completed') {
                        if ($row_workshop['member_id_'.$i]!=0) {
                            $display_alert.= 'Removing from member\'s page that they applied for, or completed this workshop: Clg Reg# <a href="one-person.php?clg_reg='.$row_workshop['member_id_'.$i].'" target="_blank">'.$row_workshop['member_id_'.$i].'</a><br />';
                            
                            $go_ahead_with_removal = 1;
                        }
                    }

                    if (isset($go_ahead_with_removal) and $go_ahead_with_removal == 1) {
                        $query_wk_member = "UPDATE `cutabove` 
                            SET ".mysqli_real_escape_string($link, $row_workshop['level_name'])." = 0, 
                            ".mysqli_real_escape_string($link, $row_workshop['level_name'])."_applied_for = 0
                            WHERE clg_reg = ".mysqli_real_escape_string($link, $row_workshop['member_id_'.$i])." LIMIT 1";

                        ## extra where clause will not work for "applied" condition
                        # AND ".mysqli_real_escape_string($link, $row_workshop['level_name'])."= ".mysqli_real_escape_string($link, $row_workshop['workshop_id'])."
                            
                        if (mysqli_query($link, $query_wk_member)) {
                            $display_alert.='--> done<br />';
                        } else {
                            $display_alert.='--> failed ('.$query_wk_member.')<br />';
                        }
                        $go_ahead_with_removal = 0;
                    }
                }
                for ($i=1; $i < 21; $i++) {
                    # go over every seat
                    # check if the seat has a supervisor
                    # assess what is requested. (applied only?, or applied and completed?) (This is just for conceptual understanding, and not a line of code)
                    ## if "applied" only and supervisor has not attended,
                    ### proceed to remove his registration entry (here, only those who have not attended will be removed if such request is made)
                    ## else if "completed"
                    ### proceed to remove his registration entry (here, everyone is removed if such request is made)
                    
                    if ($row_workshop['supervisor_id_'.$i]!=0) {
                        if ($_GET['supervisor']=='applied' and $row_workshop['supervisor_id_'.$i.'a']==0) {
                            $display_alert.= 'Removing from supervisor\'s page that they applied for this workshop. (but only if not attended): ID# <a href="edit-council.php?id='.$row_workshop['supervisor_id_'.$i].'" target="_blank">'.$row_workshop['supervisor_id_'.$i].'</a><br />';

                            // Dry Run without actually deleting anything
                            /* $query_wk_sup="SELECT * FROM `cutabove_workshop_supervisors_applied_fo` WHERE supervisor_id= ".mysqli_real_escape_string($link, $row_workshop['supervisor_id_'.$i])." AND workshop_id=".mysqli_real_escape_string($link, $row_workshop['workshop_id'])." LIMIT 1";
                            $result_wk_sup = mysqli_query($link, $query_wk_sup);

                            if (mysqli_num_rows($result_wk_sup)!= 0) {
                                while ($row_wk_sup = mysqli_fetch_array($result_wk_sup)) {
                                    // print_r($row_wk_sup);
                                    // echo "<br>";
                                }
                            }
                            */
                            $query_delete_relation = "DELETE FROM `cutabove_workshop_supervisors_applied_fo` WHERE supervisor_id= ".mysqli_real_escape_string($link, $row_workshop['supervisor_id_'.$i])." AND workshop_id=".mysqli_real_escape_string($link, $row_workshop['workshop_id'])." AND supervisor_attendance = 0 LIMIT 1";
                            if (mysqli_query($link, $query_delete_relation)) {
                                $display_alert.='--> done<br />';
                            } else {
                                $display_alert.='--> failed ('.$query_delete_relation.')<br />';
                            }
                        } elseif ($_GET['supervisor']=='completed') {
                            $display_alert.= 'Removing from supervisor\'s page that they applied or completed this workshop: ID# <a href="edit-council.php?id='.$row_workshop['supervisor_id_'.$i].'" target="_blank">'.$row_workshop['supervisor_id_'.$i].'</a><br />';

                            $query_delete_relation = "DELETE FROM `cutabove_workshop_supervisors_applied_fo` WHERE supervisor_id= ".mysqli_real_escape_string($link, $row_workshop['supervisor_id_'.$i])." AND workshop_id=".mysqli_real_escape_string($link, $row_workshop['workshop_id'])." LIMIT 1";

                            if (mysqli_query($link, $query_delete_relation)) {
                                $display_alert.='--> done<br />';
                            } else {
                                $display_alert.='--> failed ('.$query_delete_relation.')<br />';
                            }
                        }
                    }
                }
                $query_delete_workshop = "DELETE FROM `cutabove_workshop` WHERE workshop_id = '".mysqli_real_escape_string($link, $_GET['wk_id'])."' LIMIT 1";
                if (mysqli_query($link, $query_delete_workshop)) {
                    $display_alert.='Workshop Deleted<br />';
                } else {
                    $display_alert.='Failed to delete Workshop ('.$query_delete_workshop.')<br />';
                }
            } else {
                $display_alert.= 'The workshop does not exist';
            }
        }
    } elseif ($_GET['wk_id']==1) {
        $display_alert.='Thou Shalt not delete the <b>Genesis Workshop</b>.<br />';
    }
    
    if ($display_alert!='') {
        $query_log ="INSERT INTO `cutabove_deletion_log` (`log`, `council_id`, `comments`, `datetime`) 
        VALUES (
            '".mysqli_real_escape_string($link, $display_alert)."', 
            '".mysqli_real_escape_string($link, $_SESSION['id'])."', 
            '".mysqli_real_escape_string($link, $_GET['comment'])."', 
            '".date('Y-m-d H:i:s')."'
            )";
            
        if (mysqli_query($link, $query_log)) {
            //success
            // header("Location: deletion.php?successEdit=1");
            $display_alert.='<br />Added to <a href="deletion_log.php" target="_blank">Log</a>.';
        } else {
            // echo '<div id="tablediv">';
            // echo "failed to update log.";
            // echo '</div>';
            $display_alert.='Failed to update log';
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

    <title>Deletion</title>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container-fluid" id="tablediv">
        <?php
    if ($display_alert!='') {
        echo $display_alert;
    }
    ?>
        <div class="alert alert-<?php echo $_SESSION['colour'];?>" role="alert" id="alert">Warning! Permanent Deletion.
            All attempts (success or fail) are recorded in the <b><a href="deletion_log.php" target="_blank">Deletion
                    Log</a></b> with your user Id. Read <a href="/assets" target="_blank">this</a> for some
            documentation on how deletion works.</div>
        <div class="row" style="margin-top: 3px">
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Remove Member</h5>
                        <form method="get">
                            <div class="form-group">
                                <input type="hidden" name="type" value="member">
                                <div class="form-group row">
                                    <label for="clg_reg" class="col-sm-2 col-form-label">Reg Number</label>
                                    <div class="col-sm-10">
                                        <input type="number" class="form-control" id="clg_reg" name='clg_reg'
                                            placeholder="140201306" value="<?php
                                            if (isset($_GET['clg_reg'])) {
                                                echo $_GET['clg_reg'];
                                            }
                                            ?>">
                                        <?php
                                            if (isset($_GET['clg_reg'])) {
                                                echo '<small>Please look through the <a href="one-person.php?clg_reg='.$_GET['clg_reg'].'" target="_blank">user</a> once to see what all will be lost.</small>';
                                            }
                                            ?>
                                    </div>
                                </div>
                                <div class="form-group form-check">
                                    <input type="radio" class="form-check-input" id="applied-mem"
                                        name="applied-completed" value="only-applied" checked>
                                    <label class="form-check-label" for="applied-mem">
                                        <!--Remove applied_for value from
                                        workshops? (only done if applied, but not completed)-->Info will be retained in
                                        the workshops the <span class="text-success">member</span> attended</label>
                                </div>
                                <!-- <div class="form-group form-check">
                                    <input type="radio" class="form-check-input" id="completed-mem"
                                        name="applied-completed" value="applied-completed">
                                    <label class="form-check-label" for="completed-mem">Remove completed status from
                                        workshops? (deletes both applied for, and attended values)</label>
                                </div> -->
                                <button type="submit" class="btn btn-success submission">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Remove Core</h5>
                        <form method="get">
                            <div class="form-group">
                                <input type="hidden" name="type" value="council">
                                <div class="form-group row">
                                    <label for="clg_reg" class="col-sm-2 col-form-label">Username</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="council_username"
                                            name="council_username" placeholder="ak" value=<?php if (isset($_GET['council_username'])) {
                                                echo $_GET['council_username'];
                                            }?>>
                                        <?php
                                            if (isset($_GET['council_username'])) {
                                                echo '<small>Please look through the <a href="edit-council.php?username='.$_GET['council_username'].'" target="_blank">council</a> once to see what all will be lost.</small>';
                                            }
                                            ?>
                                    </div>
                                </div>
                                <div class="form-group form-check">
                                    <input type="radio" class="form-check-input" id="applied-sup" name="workshop"
                                        value="applied" checked>
                                    <label class="form-check-label" for="applied-sup">Info will be retained in
                                        the workshops the <span class="text-danger">Core member</span> attended</label>
                                </div>
                                <!-- <div class="form-group form-check">
                                    <input type="radio" class="form-check-input" id="completed-sup" name="workshop"
                                        value="completed">
                                    <label class="form-check-label" for="completed-sup">Erase their application as well
                                        as successful attendance from every workshop?</label>
                                    <small class="form-text text-muted">(Technical: The redundant entry about workshop +
                                        supervisor id relation is deleted either way, since it was relevat for
                                        supervisor only. Not workshop)</small>
                                </div> -->
                                <button type="submit" class="btn btn-danger submission">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Remove Workshop</h5>
                        <form method="get">
                            <div class="form-group">
                                <input type="hidden" name="type" value="workshop">
                                <div class="form-group row">
                                    <label for="clg_reg" class="col-sm-2 col-form-label">Workshop id</label>
                                    <div class="col-sm-10">
                                        <input type="number" class="form-control" id="wk_id" name="wk_id"
                                            placeholder="1" value=<?php if (isset($_GET['wk_id'])) {
                                                echo $_GET['wk_id'];
                                            }?>>
                                        <?php
                                            if (isset($_GET['wk_id'])) {
                                                echo '<small>Please look through the <a href="supervisor_workshop.php?workshop_id='.$_GET['wk_id'].'" target="_blank">workshop</a> once to see what all will be lost.</small>';
                                            }
                                            ?>
                                    </div>
                                </div>
                                <u>
                                    <h4>Member:</h4>
                                </u>
                                <div class="form-group form-check">
                                    <input type="radio" class="form-check-input" id="workshop_mem1" name="member"
                                        value="applied" checked>
                                    <label class="form-check-label" for="workshop_mem1">Workshop info will be retained
                                        on <span class="text-success">member</span>'s page if he attended it</label>
                                </div>
                                <div class="form-group form-check">
                                    <input type="radio" class="form-check-input" id="workshop_mem2" name="member"
                                        value="completed">
                                    <label class="form-check-label" for="workshop_mem2">No workshop info will be
                                        retained at
                                        all</label>
                                </div>
                                <hr />
                                <u>
                                    <h4>Core:</h4>
                                </u>
                                <div class="form-group form-check">
                                    <input type="radio" class="form-check-input" id="workshop_sup1" name="supervisor"
                                        value="applied" checked>
                                    <label class="form-check-label" for="workshop_sup1">Workshop info will be retained
                                        on <span class="text-danger">Core member</span>'s page if he attended it</label>
                                </div>
                                <div class="form-group form-check">
                                    <input type="radio" class="form-check-input" id="workshop_sup2" name="supervisor"
                                        value="completed">
                                    <label class="form-check-label" for="workshop_sup2">Erase all trace from core
                                        members' profile</label>
                                </div>
                                <button type="submit" class="btn btn-primary submission">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>

    <script>
    $(document).ready(function() {
        $("#workshop_mem1").click(function() {
            $("#workshop_sup1").prop("checked", true);
        });
        $("#workshop_sup1").click(function() {
            $("#workshop_mem1").prop("checked", true);
        });

        $("#workshop_mem2").click(function() {
            $("#workshop_sup2").prop("checked", true);
        });
        $("#workshop_sup2").click(function() {
            $("#workshop_mem2").prop("checked", true);
        });
    });

    //system to ask for a comment before any deletion is finalised
    $(document).ready(function() {
        $('.submission').click(function() {
            var comment = prompt(
                "Please enter your comment: (Deletion will proceed even if you cancel. To stop deletion, close this page)",
                "");
            if (comment == '') {
                comment = 'None Provided'
            }
            var input = $("<input>")
                .attr("type", "hidden")
                .attr("name", "comment").val(comment);
            $('form').append(input);
        });
    });
    </script>
</body>

</html>