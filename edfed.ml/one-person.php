<?php
session_start();

$stg1c=0; //for enabling stage 2, this value is checked instead of database
$stg2c=$stg3c=$stg4c=0;

//if admin has accessed the page, see if the id/clg_reg has been sent by $_GET, then show the user data by $_GET['id']/['clg_reg']
//if user has logged in, show his own data using $_SESSION['id']
if (($_SESSION['permission'] == 'admin') or ($_SESSION['permission'] == 'supervisor')) {
    if (array_key_exists("id", $_GET) or array_key_exists("clg_reg", $_GET)) {
        include('connect-db.php');
        if (array_key_exists("id", $_GET)) {
            $query = "SELECT * FROM `cutabove` WHERE id = '".mysqli_real_escape_string($link, $_GET['id'])."' LIMIT 1";
        } elseif (array_key_exists("clg_reg", $_GET)) {
            $query = "SELECT * FROM `cutabove` WHERE clg_reg = '".mysqli_real_escape_string($link, $_GET['clg_reg'])."' LIMIT 1";
        }
        $result = mysqli_query($link, $query) or die(mysql_error());
        if (mysqli_num_rows($result)!=0) {
            $row = mysqli_fetch_array($result);
        } else {
            echo "User doesn't exist. Use the <a href='all-people.php'>Admin mode</a> to click on a user for details.";
            die();
        }
    } else {
        echo "You didn't specify which id to fetch. You are admin. Use the <a href='all-people.php'>Admin mode</a> to click on a user for details.";
        die();
    }
} elseif (($_SESSION['permission'] == 'member') and array_key_exists("id", $_SESSION)) {
    include('connect-db.php');
    $query = "SELECT * FROM `cutabove` WHERE id = '".mysqli_real_escape_string($link, $_SESSION['id'])."' LIMIT 1";
    $result = mysqli_query($link, $query) or die(mysql_error());
    $row = mysqli_fetch_array($result);
} else {
    header("Location: index.php?redirect=".$_SERVER['REQUEST_URI']);
}

if (array_key_exists("action", $_GET) && $_GET['action']== 'cancel') {
    $query_cancel = "SELECT * FROM `cutabove_workshop` WHERE workshop_id = ".mysqli_real_escape_string($link, $_POST['workshop_id'])." LIMIT 1";
    $result_cancel = mysqli_query($link, $query_cancel) or die(mysql_error());
    $row_cancel = mysqli_fetch_array($result_cancel);
    for ($i=1; $i < 21 ; $i++) {
        echo $i;
        if ($row_cancel['member_id_'.$i.'']== $row['id']) {
            echo $query_cancel = "UPDATE `cutabove_workshop` SET `member_id_".$i."` = '".mysqli_real_escape_string($link, $row['id'])."' WHERE workshop_id = ".mysqli_real_escape_string($link, $_POST['workshop_id'])." LIMIT 1";
            if (mysqli_query($link, $query_cancel)) {
                echo "1";
            } else {
                echo "2";
            }
            break;
        }
    }
    die();
}

function displayStage($stg)
{
    global $link, $row;
    echo '<tr><th scope="row">Stage '.$stg.' Fee</th><td>';
    if ($row['stg'.$stg.'fee'] != '0') {
        echo 'Paid (Rs. '. $row['stg'.$stg.'fee'].')';
    } else {
        echo '<span class ="text-danger">Not yet paid</span>';
    }
    echo '</td></tr>';

    if ($stg==1) {
        $max=4;
    } else {
        $max=5;
    }
    
    for ($i=1; $i <= $max; $i++) {
        echo '<tr><th scope="row">Workshop '.$i.'</th><td>';
        if ($row['stg'.$stg.'w'.$i] == '0') {
            //not completed this workshop

            if ($row['stg'.$stg.'w'.$i.'_applied_for']== 0) {
                                    
                //not even applied for this level workshop
                echo 'Not completed';
            } else {
                //applied for this level workshop

                //fetch the workshop detail that he applied for
                $query1 = "SELECT * FROM `cutabove_workshop` WHERE workshop_id = '".$row['stg'.$stg.'w'.$i.'_applied_for']."'";
                $result1 = mysqli_query($link, $query1);
                $row1 = mysqli_fetch_array($result1);

                echo 'Applied for <a href="supervisor_workshop.php?workshop_id='.$row['stg'.$stg.'w'.$i.'_applied_for'].'" title="Only admins can view the workshop.">#'.$row['stg'.$stg.'w'.$i.'_applied_for'].'</a> ';
                                    
                if (mysqli_num_rows($result1)!=0) {
                    echo '<code>'.$row1['memorisable_name'].'</code> <small>(Date: '.date("d-M-Y h:i A", strtotime($row1['date'])).')</small>';
                //echo ' <button type="button" class="close" data-choice="cancel" data-workshop_id="'.$row['stg'.$stg.'w'.$i.'_applied_for'].'">&times;</button>';
                } else {
                    echo ' ( <code>Workshop Deleted</code> )';
                }
            }
        } else {
            //completed the workshop

            //fetch the workshop detail that he completed
            $query1 = "SELECT * FROM `cutabove_workshop` WHERE workshop_id = '".$row['stg'.$stg.'w'.$i]."'";
            $result1 = mysqli_query($link, $query1);
            $row1 = mysqli_fetch_array($result1);

            echo 'Completed at <a href="supervisor_workshop.php?workshop_id='.$row['stg'.$stg.'w'.$i].'" title="Only admins can view the workshop.">#'.$row['stg'.$stg.'w'.$i].'</a>';

            if (mysqli_num_rows($result1)!=0) {
                echo ' <code>'.$row1['memorisable_name'].'</code> <small>(Date: '.date("d-M-Y h:i A", strtotime($row1['date'])).')</small> | <a href="'.$row1['feedback_link'].'" target="_blank">Feedback</a> | <a href="'.$row1['bonus_files'].'" target="_blank">Files</a>';
            } else {
                echo ' ( <code>Workshop Deleted</code> )';
            }
        }
        echo '</td></tr>';
    }
    //echo '<tr><th scope="row">Workshop 2</th><td>'; if ($row['stg1w2'] != '0') { echo 'Yes, ID# '.$row['stg1w2'].''; } else { echo 'No';} echo '</td></tr>';
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

    <title>View User</title>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div id="tablediv" class="container-fluid">

        <!-- display general data -->
        <table class="table table-responsive-sm">
            <thead class="thead-dark">
                <th>Property</th>
                <th>Value</th>
            </thead>
            <tbody>
                <?php
                if ($_SESSION['permission'] == 'admin') {
                    echo '<tr><th scope="row">ID</th><th scope="row">' . $row['id'] . '</th>';
                }
                echo '<tr><th scope="col">College Registration</th><td>' . $row['clg_reg'] . '</td></tr>';
                echo '<tr><th scope="row">Name</th><td>' . $row['name'] . '</td></tr>';
                echo '<tr><th scope="row">DOB</th><td>' . date('d-M-Y', strtotime($row['dob'])) . '</td></tr>';
                echo '<tr><th scope="row">Semester</th><td>' . $row['semester'] . '</td></tr>';
                echo '<tr><th scope="row">Status</th><td>'; if ($row['kit'] == '0') {
                    echo 'Awaiting Verification';
                } if ($row['kit'] == '1') {
                    echo '<span class ="text-success">Verified</span>';
                } if ($row['kit'] == '2') {
                    echo 'Graduated';
                } if ($row['kit'] == '3') {
                    echo '<span class ="text-danger">N/A</span>';
                }
                include('awardSystem.php');
                echo ' ('.awardSystem($row).')</td></tr>';
                if ($_SESSION['permission'] == 'admin' or $_SESSION['permission'] == 'supervisor') {
                    //code to prettily display phone number or whatsapp number or both.
                    echo '<tr><th scope="row">Phone</th><td>';
                    //https://stackoverflow.com/a/2220529/2365231
                    if ($row['phone']!= 0) {
                        echo '<a href="tel:' . $row['phone'] . '">+91-' . $row['phone'] . ' (Call)</a>';
                    }
                    if ($row['phone']!= 0 and $row['phone_whatsapp']!= 0) {
                        echo ',<br />';
                    }
                    if ($row['phone_whatsapp']!= 0) {
                        echo '<a href="https://wa.me/+91' . $row['phone_whatsapp'] . '?text=Hi '.strtok($row['name'], " ").',%0A%0A%0A%0AYours Sincerely,%0A'.$_SESSION['name'].' ('.$_SESSION['permission'].')%0ACut Above%0AKMC, Mangalore">+91-' . $row['phone_whatsapp'] . ' (WA)</a>';
                    }
                    echo '</td></tr>';
                    echo '<tr><th scope="row">Email</th><td><a href="mailto:' . $row['email'] . '?subject=A Cut Above&body=Hi '.strtok($row['name'], " ").',%0A%0A%0A%0AYours Sincerely,%0A'.$_SESSION['name'].' ('.$_SESSION['permission'].')%0ACut Above%0AKMC, Mangalore" target="_top">' . $row['email'] . '</a></td></tr>';
                    echo '<tr><th scope="row">Comments</th><td><button type="button" id="show-hide" class="btn btn-outline-'.$_SESSION['colour'].'">Toggle Comments</button><div id="comments">' . $row['comments'] . '</div></td></tr>';
                //htmlspecialchars($row['comments'], ENT_QUOTES)
                } else {
                    echo '<tr><th scope="row">Phone</th><td>' . $row['phone'];
                    if ($row['phone']!= $row['phone_whatsapp'] and $row['phone_whatsapp']!= 0) {
                        echo ' (Call)<br />'.$row['phone_whatsapp'].' (WhatsApp)';
                    }
                    echo '</td></tr>';
                    echo '<tr><th scope="row">Email</th><td>'.$row['email'].'</td></tr>';
                }
                ?>
            </tbody>
        </table>

        <!-- pretty-fy with putting stuff inside div row. -->
        <div class="row">
            <!-- display stage 1 data -->
            <table class="table col-sm">
                <thead>
                    <tr class="thead-dark">
                        <th>Stage 1</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    displayStage(1);
                    echo '<tr><th scope="row">Completion</th><td>';
                    if ($row['stg1w1'] != '0' and $row['stg1w2'] != '0' and $row['stg1w3'] != '0' and $row['stg1w4'] != '0') {
                        echo '<span class ="text-success">Yes</span>';
                        $stg1c='1';
                    } else {
                        echo '<span class ="text-danger">No</span>';
                    }
                    echo '</td></tr>';
                    ?>
                </tbody>
            </table>

            <!-- display stage 2 data -->
            <table class="table col-sm">
                <thead>
                    <tr class="thead-dark">
                        <th>Stage 2</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($stg1c != '0') {
                        displayStage(2);
                        echo '<tr><th scope="row">Completion</th><td>';
                        if ($row['stg2w1'] != '0' and $row['stg2w2'] != '0' and $row['stg2w3'] != '0' and $row['stg2w4'] != '0' and $row['stg2w5'] != '0') {
                            echo '<span class="text-success">Yes</span>';
                            $stg2c='1';
                        } else {
                            echo '<span class="text-danger">No</span>';
                        }
                        echo '</td></tr>';
                    } else {
                        echo '<tr><td>Complete Stage 1 first</td><td></td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="row">
            <!-- display stage 3 data -->
            <table class="table col-sm">
                <thead>
                    <tr class="thead-dark">
                        <th>Stage 3</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($stg2c != '0') {
                        displayStage(3);
                        echo '<tr><th scope="row">Completion</th><td>';
                        if ($row['stg3w1'] != '0' and $row['stg3w2'] != '0' and $row['stg3w3'] != '0' and $row['stg3w4'] != '0' and $row['stg3w5'] != '0') {
                            echo '<span class="text-success">Yes</span>';
                            $stg3c='1';
                        } else {
                            echo '<span class="text-danger">No</span>';
                        }
                        echo '</td></tr>';
                    } else {
                        echo '<tr><td>Complete Stage 2 first</td><td></td></tr>';
                    }
                        
                    ?>
                </tbody>
            </table>

            <!-- display stage 4 data -->
            <table class="table col-sm">
                <thead>
                    <tr class="thead-dark">
                        <th>Stage 4</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($stg3c != '0') {
                        displayStage(4);
                        echo '<tr><th scope="row">Completion</th><td>';
                        if ($row['stg4w1'] != '0' and $row['stg4w2'] != '0' and $row['stg4w3'] != '0' and $row['stg4w4'] != '0' and $row['stg4w5'] != '0') {
                            echo '<span class="text-success">Yes</span>';
                            $stg4c='1';
                        } else {
                            echo '<span class="text-danger">No</span>';
                        }
                        echo '</td></tr>';
                    } else {
                        echo '<tr><td>Complete Stage 3 first</td><td></td></tr>';
                    }
                        
                    ?>
                </tbody>
            </table>
        </div>

        <div class="row">
            <!-- display stage 5 data -->
            <table class="table col-sm">
                <thead>
                    <tr class="thead-dark">
                        <th>Stage 5</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    //had to write the whole code for function somce fee is calculated for each workshop in stage 5
                    if ($stg4c != '0') {
                        for ($i=1; $i <= 5; $i++) {
                            if ($row['stg5w'.$i.'fee'] !=0) {
                                echo '<tr><th scope="row">Workshop '.$i.'</th><td>';
                                if ($row['stg5w'.$i] == '0') {
                                    //not completed this workshop
                        
                                    if ($row['stg5w'.$i.'_applied_for']== 0) {
                                                            
                                        //not even applied for this level workshop
                                        echo 'Not completed';
                                    } else {
                                        //applied for this level workshop
                        
                                        //fetch the workshop detail that he applied for
                                        $query1 = "SELECT * FROM `cutabove_workshop` WHERE workshop_id = '".$row['stg5w'.$i.'_applied_for']."'";
                                        $result1 = mysqli_query($link, $query1);
                                        $row1 = mysqli_fetch_array($result1);
                        
                                        echo 'Applied for <a href="supervisor_workshop.php?workshop_id='.$row['stg5w'.$i.'_applied_for'].'" title="Only admins can view the workshop.">#'.$row['stg5w'.$i.'_applied_for'].'</a> ';
                                                            
                                        if (mysqli_num_rows($result1)!=0) {
                                            echo '<code>'.$row1['memorisable_name'].'</code> <small>(Date: '.date("d-M-Y h:i A", strtotime($row1['date'])).')</small>';
                                        //echo ' <button type="button" class="close" data-choice="cancel" data-workshop_id="'.$row['stg'.$stg.'w'.$i.'_applied_for'].'">&times;</button>';
                                        } else {
                                            echo ' ( <code>Workshop Deleted</code> )';
                                        }
                                    }
                                } else {
                                    //completed the workshop
                        
                                    //fetch the workshop detail that he completed
                                    $query1 = "SELECT * FROM `cutabove_workshop` WHERE workshop_id = '".$row['stg5w'.$i]."'";
                                    $result1 = mysqli_query($link, $query1);
                                    $row1 = mysqli_fetch_array($result1);
                        
                                    echo 'Completed at <a href="supervisor_workshop.php?workshop_id='.$row['stg5w'.$i].'" title="Only admins can view the workshop.">#'.$row['stg5w'.$i].'</a>';
                        
                                    if (mysqli_num_rows($result1)!=0) {
                                        echo ' <code>'.$row1['memorisable_name'].'</code> <small>(Date: '.date("d-M-Y h:i A", strtotime($row1['date'])).')</small> | <a href="'.$row1['feedback_link'].'" target="_blank">Feedback</a> | <a href="'.$row1['bonus_files'].'" target="_blank">Files</a>';
                                    } else {
                                        echo ' ( <code>Workshop Deleted</code> )';
                                    }
                                }
                                echo '</td></tr>';
                            } else {
                                echo '<tr><th scope="row">Workshop'.$i.'</th><td>Fee Not Paid</td></tr>';
                            }
                        }


                        echo '<tr><th scope="row">Completion</th><td>';
                        if ($row['stg5w1'] != '0' and $row['stg5w2'] != '0' and $row['stg5w3'] != '0' and $row['stg5w5'] != '0' and $row['stg5w5'] != '0') {
                            echo '<span class="text-success">Yes</span>';
                            $stg5c='1';
                        } else {
                            echo '<span class="text-danger">No</span>';
                        }
                        echo '</td></tr>';
                    } else {
                        echo '<tr><td>Complete Stage 4 first</td><td></td></tr>';
                    }
                        
                    ?>
                </tbody>
            </table>
        </div>

    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>

    <script type="text/javascript">
    $("#show-hide").click(function() {
        $("#comments").toggle();
        $("#show-hide").button('toggle');
    });

    $(".close").click(function() {
        var choice = $(this).attr("data-choice");
        var workshop_id = $(this).attr("data-workshop_id");
        $.ajax({
            type: "POST",
            url: "one-person.php?action=cancel&id=<?php echo $row['id'];?>",
            data: {
                choice: choice,
                workshop_id: workshop_id
            },
            success: function(result) {
                /*if (result == "1") {
                  $("button[data-workshop_id='"+workshop_id+"']").html("<span class='text-success'>Cancelled</span>");
                } else if (result == "2"){
                  alert("fail");
                }*/
                console.log(result);
            }
        })
    })
    </script>
</body>

</html>