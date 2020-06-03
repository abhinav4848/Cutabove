<?php
session_start();

$error="";

if ($_SESSION['permission'] == 'admin' or $_SESSION['permission'] == 'supervisor') {
    include('connect-db.php');
} else {
    header("Location: index.php?redirect=".$_SERVER['REQUEST_URI']);
}

$redirect=false;
$final_workshop_name="stg1w1";

if (array_key_exists("kit", $_GET) and $_GET['kit']==100) {
    $where_clause=" WHERE kit=1 ";
} elseif (array_key_exists("kit", $_GET) and $_GET['kit']!=1) {
    $where_clause=" WHERE kit=".mysqli_real_escape_string($link, $_GET['kit'])." ";
} elseif (array_key_exists("kit", $_GET) and $_GET['kit']==1 and array_key_exists("stage", $_GET) and is_numeric($_GET['stage']) and $_GET['stage']>0 and $_GET['stage']<6) {
    $where_clause=" WHERE kit=1 ";
    //stage between 1-5
    if ($_GET['stage']>0) {
        // for stg1, no need to check for any prev fees paid
        $check_prev_fees="";
        $check_prev_workshops="";
    }
    if ($_GET['stage']>1) {
        // for stages>1, check that stg1fee is paid and all prev workshops are complete
        $check_prev_fees.=" AND stg1fee!=0";
        $check_prev_workshops.=" AND stg1w1!=0 AND stg1w2!=0 AND stg1w3!=0 AND stg1w4!=0";
    }
    if ($_GET['stage']>2) {
        // for stages>2, check that stg1fee and stg2fee is paid and all prev workshops are complete
        $check_prev_fees.=" AND stg2fee!=0";
        $check_prev_workshops.=" AND stg2w1!=0 AND stg2w2!=0 AND stg2w3!=0 AND stg2w4!=0 AND stg2w5!=0";
    }
    if ($_GET['stage']>3) {
        // for stages>3, check that stg1fee and stg2fee and stg3fee is paid and all prev workshops are complete
        $check_prev_fees.=" AND stg3fee!=0";
        $check_prev_workshops.=" AND stg3w1!=0 AND stg3w2!=0 AND stg3w3!=0 AND stg3w4!=0 AND stg3w5!=0";
    }
    if ($_GET['stage']>4) {
        // for stages>4, check that stg1fee and stg2fee and stg3fee and stg4fee is paid and all prev workshops are complete
        $check_prev_fees.=" AND stg4fee!=0";
        $check_prev_workshops.=" AND stg4w1!=0 AND stg4w2!=0 AND stg4w3!=0 AND stg4w4!=0 AND stg4w5!=0";
    }

    if (array_key_exists("fee", $_GET)) {
        //make sure fee is present else redirect
        if ($_GET['fee']=='paid') {
            //make sure fee is paid for that stage
            
            if (array_key_exists("workshop", $_GET) and is_numeric($_GET['workshop']) and $_GET['workshop']>0 and $_GET['workshop']<6) {
                $final_workshop_name="stg".$_GET['stage']."w".$_GET['workshop'];

                if ($_GET['stage']<5) {
                    // all stages except stg5 are stg1fee, stg2fee, etc...
                    $where_clause.= " AND stg".mysqli_real_escape_string($link, $_GET['stage'])."fee!=0 ".$check_prev_fees.$check_prev_workshops;
                } else {
                    // stage 5 has stg1w1fee, stg2w2fee, etc...
                    $where_clause.= " AND ".$final_workshop_name."fee!=0 ".$check_prev_fees.$check_prev_workshops;
                }
                                
                if (array_key_exists("application", $_GET)) {
                    if ($_GET['application']=='not-applied') {
                        // applied_for= 0, completed=0 (imp to check completed cuz Genesis Workshop completion skips applying for step)
                        $where_clause.= " AND ".$final_workshop_name."_applied_for = 0 AND ".$final_workshop_name."= 0";
                    } elseif ($_GET['application']=='absent') {
                        // applied_for!=0, completed=0, workshop_completion=1
                        $where_clause= " INNER JOIN `cutabove_workshop` ON cutabove.".$final_workshop_name."_applied_for=cutabove_workshop.workshop_id AND cutabove_workshop.completed=1 AND cutabove.".$final_workshop_name."_applied_for!= 0 AND cutabove.".$final_workshop_name."= 0 ".$where_clause;
                    } elseif ($_GET['application']=='applied') {
                        // applied_for!=0, completed=0, workshop_completion=0
                        $where_clause= " INNER JOIN `cutabove_workshop` ON cutabove.".$final_workshop_name."_applied_for=cutabove_workshop.workshop_id AND cutabove_workshop.completed=0 AND cutabove.".$final_workshop_name."_applied_for!= 0 AND cutabove.".$final_workshop_name."= 0 ".$where_clause;
                    } elseif ($_GET['application']=='completed') {
                        // applied_for!=0, completed!=0
                        $where_clause.= " AND ".$final_workshop_name."!= 0";
                    } else {
                        //application was not a valid value
                        $redirect=true;
                    }
                } else {
                    //application does not exist
                    $redirect=true;
                }
            } else {
                //workshop doesn't exist, or is non-numeric, or is not <6
                $redirect=true;
            }
        } else {
            // fee not paid
            // (this 'if-else' block's "logic" is just repeated from the "if" section above)
            if (array_key_exists("workshop", $_GET) and is_numeric($_GET['workshop']) and $_GET['workshop']>0 and $_GET['workshop']<6) {
                $final_workshop_name="stg".$_GET['stage']."w".$_GET['workshop'];

                if ($_GET['stage']<5) {
                    $where_clause.= "AND stg".mysqli_real_escape_string($link, $_GET['stage'])."fee=0 ".$check_prev_fees.$check_prev_workshops;
                } else {
                    $where_clause.= " AND ".$final_workshop_name."fee!=0 ".$check_prev_fees.$check_prev_workshops;
                }
            } else {
                //workshop doesn't exist, or is non-numeric, or is not <6
                $redirect=true;
            }
        }
    } else {
        //fee does not exist
        $redirect=true;
    }
} else {
    //kit does not exist, or kit!=1, or
    //stage does not exist, or is non-numeric, or is not b/w 0-6
    $redirect=true;
}

/** Redirection Method */
if ($redirect) {
    if (array_key_exists("kit", $_GET) and $_GET['kit']>0 and ($_GET['kit']<7 or $_GET['kit']==100)) {
        //kit exist, is > 0 and is (either <7 or 100)
        $kit=$_GET['kit'];
    } else {
        $kit=100;
    }

    if (array_key_exists("stage", $_GET) and $_GET['stage']>0 and $_GET['stage']<6) {
        $stage=$_GET['stage'];
    } else {
        $stage=1;
    }

    if (array_key_exists("workshop", $_GET) and $_GET['workshop']>0 and $_GET['workshop']<6) {
        $workshop=$_GET['workshop'];
    } else {
        $workshop=1;
    }

    if (array_key_exists("application", $_GET)) {
        $application=$_GET['application'];
    } else {
        $application='not-applied';
    }
    header("Location: all-people.php?kit={$kit}&stage={$stage}&fee=paid&workshop={$workshop}&application={$application}");
}
/** End Redirection Method */

$sql = "SELECT COUNT(*) FROM `cutabove` ".$where_clause;
$resultCount = mysqli_query($link, $sql) or trigger_error("SQL", E_USER_ERROR);
$r = mysqli_fetch_row($resultCount);
$numrows = $r[0];

// number of rows to show per page
$rowsperpage = 10;
// find out total pages
$totalpages = ceil($numrows / $rowsperpage);

// get the current page or set a default
if (isset($_GET['currentpage']) && is_numeric($_GET['currentpage'])) {
    // cast var as int
    $currentpage = (int) $_GET['currentpage'];
} else {
    // default page num
    $currentpage = 1;
}

// if current page is greater than total pages...
if ($currentpage > $totalpages) {
    // set current page to last page
    $currentpage = $totalpages;
}

// if current page is less than first page...
if ($currentpage < 1) {
    // set current page to first page
    $currentpage = 1;
}

// the offset of the list, based on current page
$offset = ($currentpage - 1) * $rowsperpage;

// get the info from the db
$query = "SELECT * FROM `cutabove` ".$where_clause." ORDER BY id ASC LIMIT $offset, $rowsperpage";
$result = mysqli_query($link, $query) or die(mysql_error());

//receive ajax requests for updating Approval Pending members
if (array_key_exists("status", $_POST)) {
    $query = "UPDATE `cutabove` SET `kit` = '".mysqli_real_escape_string($link, $_POST['status'])."' WHERE id = ".mysqli_real_escape_string($link, $_POST['id'])." LIMIT 1";
    if (mysqli_query($link, $query)) {
        echo "success";
    } else {
        echo "fail";
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

    <title>Manage Members</title>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container-fluid" id="tablediv">

        <?php
        $parameters ='kit='.$_GET['kit'].'&stage='.$_GET['stage'].'&fee='.$_GET['fee'].'&workshop='.$_GET['workshop'].'&application='.$_GET['application'];
        /******  build the pagination links ******/
        echo '<nav aria-label="page navigation">
        <ul class="pagination justify-content-center">';

        // range of num links to show
        $range = 1;

        // if not on page 1, don't show back links
        if ($currentpage > 1) {
            // show << link to go back to page 1
            echo '<li class="page-item">';
            echo " <a class='page-link' href='{$_SERVER['PHP_SELF']}?{$parameters}&currentpage=1'><<</a> ";
            echo '</li>';
            // get previous page num
            $prevpage = $currentpage - 1;
            // show < link to go back to 1 page
            echo '<li class="page-item">';
            echo " <a class='page-link' href='{$_SERVER['PHP_SELF']}?{$parameters}&currentpage=$prevpage'>Prev</a> ";
            echo '</li>';
        } // end if

        // loop to show links to range of pages around current page
        for ($x = ($currentpage - $range); $x < (($currentpage + $range) + 1); $x++) {
            // if it's a valid page number...
            if (($x > 0) && ($x <= $totalpages)) {
                // if we're on current page...
                if ($x == $currentpage) {
                    // 'highlight' it but don't make a link
                    echo '<li class="page-item active">';
                    echo " <a class='page-link' href='#'>$x</a> ";
                    echo '</li>';
                // if not current page...
                } else {
                    // make it a link
                    echo '<li class="page-item">';
                    echo " <a class='page-link' href='{$_SERVER['PHP_SELF']}?{$parameters}&currentpage=$x'>$x</a> ";
                    echo '</li>';
                } // end else
            } // end if
        } // end for

        // if not on last page, show forward and last page links
        if ($currentpage != $totalpages) {
            // get next page
            $nextpage = $currentpage + 1;
            // echo forward link for next page
            echo '<li class="page-item">';
            echo " <a class='page-link' href='{$_SERVER['PHP_SELF']}?{$parameters}&currentpage=$nextpage'>Next</a> ";
            echo '</li>';
            // echo forward link for lastpage
            echo '<li class="page-item">';
            echo " <a class='page-link' href='{$_SERVER['PHP_SELF']}?{$parameters}&currentpage=$totalpages'>>></a> ";
            echo '</li>';
        } // end if
            echo '</ul>
            </nav>';
            /****** end build pagination links ******/
    ?>

        <?php
    /* Build Select Boxes for filtering */
    /** Set Kit Value. By default, kit=100 is chosen for showing unfiltered list
     * In the above Php code, it checks if kit==100, and makes sql query choose kit=1
     */
    echo '<form class="form-inline" method="get"><select class="form-control" name="kit" onchange="this.form.submit()">
	<option value="0"'; if (array_key_exists('kit', $_GET) and $_GET['kit']== 0) {
        echo 'selected';
    } echo '>Approval Pending</option>

    <option value="100"'; if (array_key_exists('kit', $_GET) and $_GET['kit']== 100) {
        echo 'selected';
    }echo '>Approved (all)</option>

        <option value="1"'; if (array_key_exists('kit', $_GET) and $_GET['kit']== 1) {
        echo 'selected';
    } echo '>Approved (filter)</option>

		<option value="2"'; if (array_key_exists('kit', $_GET) and $_GET['kit']== 2) {
        echo 'selected';
    } echo '>Rejected</option>

		<option value="3"'; if (array_key_exists('kit', $_GET) and $_GET['kit']== 3) {
        echo 'selected';
    } echo '>Debarred</option>

		<option value="4"'; if (array_key_exists('kit', $_GET) and $_GET['kit']== 4) {
        echo 'selected';
    } echo '>Completed</option>

		<option value="5"'; if (array_key_exists('kit', $_GET) and $_GET['kit']== 5) {
        echo 'selected';
    } echo '>Discontinued</option>

		<option value="6"'; if (array_key_exists('kit', $_GET) and $_GET['kit']== 6) {
        echo 'selected';
    } echo '>Status Unknown</option>
    </select>';
    /** End Kit Selector */
    
    /** Only if kit==1, care to build remaining select boxes */
    if (array_key_exists('kit', $_GET) and $_GET['kit']== 1) {
        /**Build stage selector */
        echo '<select class="form-control" name="stage" onchange="this.form.submit()">
        <option value="1"';
        if (array_key_exists('stage', $_GET) and $_GET['stage']== 1) {
            echo 'selected';
        } elseif (array_key_exists('stage', $_GET)) {
        } else {
            echo 'selected';
        }
        echo '>Stage 1</option>

        <option value="2"';
        if (array_key_exists('stage', $_GET) and $_GET['stage']== 2) {
            echo 'selected';
        }
        echo '>Stage 2</option>

        <option value="3"';
        if (array_key_exists('stage', $_GET) and $_GET['stage']== 3) {
            echo 'selected';
        }
        echo '>Stage 3</option>

        <option value="4"';
        if (array_key_exists('stage', $_GET) and $_GET['stage']== 4) {
            echo 'selected';
        }
        echo '>Stage 4</option>

        <option value="5"';
        if (array_key_exists('stage', $_GET) and $_GET['stage']== 5) {
            echo 'selected';
        }
        echo '>Stage 5</option>

        </select>';
        /** End Stage selector */
    
        /** Start fee selector */
        echo '<select class="form-control" name="fee" onchange="this.form.submit()">
        <option value="paid"';
        if (array_key_exists('fee', $_GET) and $_GET['fee']== 'paid') {
            echo 'selected';
        } elseif (array_key_exists('fee', $_GET)) {
        } else {
            echo 'selected';
        }
        echo '>Paid</option>
        <option value="unpaid"';
        if (array_key_exists('fee', $_GET) and $_GET['fee']== 'unpaid') {
            echo 'selected';
        }
        echo '>Unpaid</option>
        </select>';
        /** End fee selector */

        /** Build Workshop selector only if fee is paid
        * without fee being paid, there's no need for workshop# or application status
        */
        if (array_key_exists('fee', $_GET) and $_GET['fee']=='paid') {
            /** Start Workshop Selector */
            echo '<select class="form-control" name="workshop" onchange="this.form.submit()">
            <option value="1"';
            if (array_key_exists('workshop', $_GET) and $_GET['workshop']== 1) {
                echo 'selected';
            } elseif (array_key_exists('workshop', $_GET)) {
            } else {
                echo 'selected';
            }
            echo '>Workshop 1</option>

            <option value="2"';
            if (array_key_exists('workshop', $_GET) and $_GET['workshop']== 2) {
                echo 'selected';
            }
            echo '>Workshop 2</option>

            <option value="3"';
            if (array_key_exists('workshop', $_GET) and $_GET['workshop']== 3) {
                echo 'selected';
            }
            echo '>Workshop 3</option>

            <option value="4"';
            if (array_key_exists('workshop', $_GET) and $_GET['workshop']== 4) {
                echo 'selected';
            }
            echo '>Workshop 4</option>

            <option value="5"';
            if (array_key_exists('workshop', $_GET) and $_GET['workshop']== 5) {
                echo 'selected';
            }
            echo '>Workshop 5</option>
            </select>';
            /** End Workshop Selector */

            /**Start Application status Selector */
            echo '<select class="form-control" name="application" onchange="this.form.submit()">
            <option value="not-applied"';
            if (array_key_exists('application', $_GET) and $_GET['application']== "not-applied") {
                echo 'selected';
            } elseif (array_key_exists('application', $_GET)) {
            } else {
                echo 'selected';
            }
            echo '>Not Applied</option>

            <option value="absent"';
            if (array_key_exists('application', $_GET) and $_GET['application']== "absent") {
                echo 'selected';
            }
            echo '>Absent</option>

            <option value="applied"';
            if (array_key_exists('application', $_GET) and $_GET['application']== "applied") {
                echo 'selected';
            }
            echo '>Applied</option>
        
            <option value="completed"';
            if (array_key_exists('application', $_GET) and $_GET['application']== "completed") {
                echo 'selected';
            }
            echo '>Completed</option>
            </select>';
            /** End Application status selector */
        }
        /**End if fee='paid' check */
    }
    /** End if kit==1 check */

    echo '<span class="ml-2">Total results obtained: <b>'.$numrows.'</b>.</span>';
    echo '<!--<button type="submit" class="btn btn-danger">Get</button>-->
    </form>';

    if ($_SESSION['god']==1) {?>
        <label class="mt-1">
            <input type="checkbox"
                onclick="document.getElementById('fullquery').style.display=this.checked? 'block': 'none' ;">
            Show the Query
        </label>
        <div class="card my-2 w-100 border-primary" id="fullquery" style="display: none">
            <div class="card-body">
                <p class="card-text"><?=$query?></p>
            </div>
        </div>
        <?php }

    ?>

        <table class="table table-striped table-responsive-sm" id="detailsTable">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">College Id</th>
                    <th scope="col">Name</th>
                    <th scope="col">Semester</th>
                    <th scope="col">Info</th>
                    <th scope="col">Phone</th>
                    <th scope="col">Email</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    include('awardSystem.php');

                    //loop through results of database query, displaying them in the table
                    // while there are rows to be fetched..
                    while ($row = mysqli_fetch_array($result)) {
                        echo '<tr>';
                        echo '<td scope="row" data-title="ID">' . $row['id'] . '</td>';
                        echo '<td data-title="College Id">' . $row['clg_reg'] . '</td>';
                        echo '<td data-title="Name">' . $row['name'] . '</td>';
                        echo '<td data-title="Semester">' . $row['semester'] . '</td>';
                        echo '<td data-title="Info" style="white-space: nowrap;">';
                        if (array_key_exists('fee', $_GET) and $_GET['fee']=='paid' and $_GET['kit']==1) {
                            if ($_GET['application']=='not-applied') {
                                echo 'No application for '.$final_workshop_name;
                            } elseif ($_GET['application']=='absent') {
                                echo 'Had applied for # <a href="supervisor_workshop.php?workshop_id='.$row[$final_workshop_name.'_applied_for'].'" target="_blank">'.$row[$final_workshop_name.'_applied_for'].'</a>';
                            } elseif ($_GET['application']=='applied') {
                                echo 'Has applied for # <a href="supervisor_workshop.php?workshop_id='.$row[$final_workshop_name.'_applied_for'].'" target="_blank">'.$row[$final_workshop_name.'_applied_for'].'</a>';
                            } elseif ($_GET['application']=='completed') {
                                echo 'Completed # <a href="supervisor_workshop.php?workshop_id='.$row[$final_workshop_name].'" target="_blank">'.$row[$final_workshop_name].'</a>';
                            } else {
                                echo awardSystem($row);
                            }
                        } else {
                            echo awardSystem($row);
                        }
                        echo '</td>';
                        echo '<td data-title="Phone">';

                        if ($row['phone']!= 0) {
                            echo '<a href="tel:' . $row['phone'] . '">+91-' . $row['phone'] . ' (Call)</a>';
                        }
                        if ($row['phone']!= 0 and $row['phone_whatsapp']!= 0) {
                            echo ',<br />';
                        }
                        if ($row['phone_whatsapp']!= 0) {
                            echo '<a href="https://wa.me/+91' . $row['phone_whatsapp'] . '?text=Hi '.strtok($row['name'], " ").',%0A%0A%0A%0AYours Sincerely,%0A'.$_SESSION['name'].' ('.$_SESSION['permission'].')%0ACut Above%0AKMC, Mangalore">+91-' . $row['phone_whatsapp'] . ' (WA)</a>';
                        }

                        echo '</td>';

                        echo '<td data-title="Email"><a href="mailto:' . $row['email'] . '?subject=A Cut Above&body=Hi '.strtok($row['name'], " ").',%0A%0A%0A%0A Yours Sincerely,%0A'.$_SESSION['name'].' ('.$_SESSION['permission'].')%0ACut Above%0AKMC, Mangalore" target="_top">' . $row['email'] . '</a></td>';
            
                        echo '<td><a href="one-person.php?id=' . $row['id'] . '">View</a>';
                        if ($_SESSION['permission'] == 'admin') {
                            //supervisors are only allowed to view, and sign people up for workshop
                            echo ' | <a href="edit.php?id=' . $row['id'] . '">Edit</a> | <a href="fee.php?id=' . $row['id'] . '">Fee</a> | ';
                        }
                        echo '<a href="member-workshop.php?id=' . $row['id'] . '">Workshop</a>';
                        if (array_key_exists('god', $_SESSION) and $_SESSION['god']== 1) {
                            echo ' | <a href="deletion.php?clg_reg='.$row['clg_reg'].'"><i class="fas fa-times text-danger"></i></a>';
                        }
                        if (array_key_exists('kit', $_GET) and $_GET['kit']== 0) {
                            echo ' ';
                            echo '<form method="post">';
                            echo '<input type="hidden" value="' . $row['id'] . '">';
                            echo '<div class="btn-group btn-group-sm" role="button">';
                            echo '<button class="btn btn-success status" value="1"><i class="fas fa-check"></i></button>';
                            echo '<button class="btn btn-danger status" value="3"><i class="fas fa-times"></i></button>';
                            echo '</div>';
                            echo '</form>';
                        }
                        echo "</td></tr>";
                    }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>

    <script>
    $(".status").click(function(e) {
        e.preventDefault();
        var object = this;
        $.ajax({
                type: "POST",
                url: "all-people.php",
                data: {
                    status: $(this).val(),
                    id: $(this).closest('form').find('input[type="hidden"]').val()
                }
            })
            .done(function(msg) {
                //need to actually replace this with a text saying success but can't get the code right. Plus the class "status" of the buttons helps make the code work. Don't know how or why
                object.closest('tr').remove();
            })
            .fail(function(msg) {

            });
    });
    </script>
</body>

</html>