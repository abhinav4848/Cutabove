<?php
session_start();
include 'assets/acknowledgements.php';

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
    // If user wants to cancel a workshop he applied for.
    // This is not yet implemented - 2019 Dec
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
    // echo '<b>Fee: </b>';
    // if ($row['stg'.$stg.'fee'] != '0') {
    //     echo 'Paid (&#8377;. '. $row['stg'.$stg.'fee'].')';
    // } else {
    //     echo '<span class ="text-danger">Not yet paid</span>';
    // }
    // echo '<br />';

    if ($stg==1) {
        $max=4;
    } else {
        $max=5;
    }
    
    for ($i=1; $i <= $max; $i++) {
        echo '<b>Workshop '.$i.': </b>';
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

                echo 'Applied for #'.$row['stg'.$stg.'w'.$i.'_applied_for'].'';
                                    
                if (mysqli_num_rows($result1)!=0) {
                    echo '<code>'.$row1['memorisable_name'].'</code> <small>(Date: '.date("d-M-Y h:i A", strtotime($row1['date'])).')</small>';
                //echo ' <button type="button" class="close" data-choice="cancel" data-workshop_id="'.$row['stg'.$stg.'w'.$i.'_applied_for'].'">&times;</button>';
                } else {
                    echo ' ( <code>Workshop Deleted</code> )';
                }
                echo '<br />';
            }
        } else {
            //completed the workshop

            //fetch the workshop detail that he completed
            $query1 = "SELECT * FROM `cutabove_workshop` WHERE workshop_id = '".$row['stg'.$stg.'w'.$i]."'";
            $result1 = mysqli_query($link, $query1);
            $row1 = mysqli_fetch_array($result1);

            echo 'Completed at #'.$row['stg'.$stg.'w'.$i];

            if (mysqli_num_rows($result1)!=0) {
                echo ' <code>'.$row1['memorisable_name'].'</code> <small>(Date: '.date("d-M-Y h:i A", strtotime($row1['date'])).')</small>';
            } else {
                echo ' ( <code>Workshop Deleted</code> )';
            }
        }
        echo '<br />';
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

    <script src="https://kit.fontawesome.com/9c2d6b042e.js"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">

    <style type="text/css">
    .card {
        margin-top: 3px;
        margin-bottom: 3px;
    }

    #screen_suggestion {
        display: none;
    }

    img {
        margin: 0px 5px 5px 5px !important;
        width: 200px;
    }

    @media screen and (min-width: 480px) {}

    @media screen and (max-width: 480px) {
        img {
            display: none;
        }

        #screen_suggestion {
            display: block;
        }
    }

    @media print {
        img {
            width: 200px;
        }

        #print_suggestion,
        #screen_suggestion {
            display: none;
        }

        .container {
            margin-top: -0.5rem !important;
        }

        .jumbotron {
            margin-bottom: -2rem;
            margin-left: -2rem;
        }
    }

    /* Sticky footer styles
    -------------------------------------------------- */
    html {
        position: relative;
        min-height: 100%;
    }

    body {
        margin-bottom: 70px;
        /* Margin bottom by footer height */
    }

    .footer {
        position: absolute;
        bottom: 0;
        width: 100%;
        height: 60px;
        /* Set the fixed height of the footer here */
        line-height: 60px;
        /* Vertically center the text there */
        background-color: #f5f5f5;
    }
    </style>

    <title>Progress Report #<?=$row['clg_reg'].' ('.$row['name'].')'?></title>
</head>

<body>
    <div class="container mt-2">

        <div class="alert alert-info" id="screen_suggestion">
            Best Viewed on a computer Screen.
        </div>

        <div class="alert alert-warning" id="print_suggestion">
            While Printing, disable <b>Background graphics</b> and <b>Headers and Footers</b>.
        </div>

        <!-- display general data -->
        <div class="jumbotron">
            <div class="container">
                <h1 class="display-4">A Cut Above, Surgery Club</h1>
                <img src="assets/A Cut Above Logo and Moto.png" class="float-right">
                <p class="lead">Performance report</p>
                <p>
                    <?php
                    echo '<b>Name: </b>' . $row['name'].'<br />';
                    echo '<b>College Registration: </b>' . $row['clg_reg'].'<br />';
                    echo '<b>DOB: </b>' . date('d-M-Y', strtotime($row['dob'])).'<br />';
                    echo '<b>Semester: </b>' . $row['semester'].'<br />';
                    echo '<b>Status: </b>'; if ($row['kit'] == '1') {
                        echo '<span class ="text-success">Verified</span>';
                    } elseif ($row['kit'] == '2') {
                        echo 'Graduated';
                    } else {
                        echo '<span class ="text-danger">N/A</span>';
                    }
                    include('awardSystem.php');
                    echo ' ('.awardSystem($row).')<br />';
                    
                    echo '<b>Phone: </b>';
                    if ($row['phone']!= 0) {
                        echo '+91-' . $row['phone'];
                    }
                    if ($row['phone']!= 0 and $row['phone_whatsapp']!= 0) {
                        echo ', ';
                    }
                    if ($row['phone_whatsapp']!= 0) {
                        echo '+91-' . $row['phone_whatsapp'] . '';
                    }

                    if ($row['email']!= '') {
                        echo '<br />';
                        echo '<b>Email: </b>'. $row['email'] . '</a><br />';
                    }
                    ?>
                </p>
            </div>
        </div>
        <!-- display stage 1 data -->

        <div class="card">
            <!-- <img class="card-img-top" src="..." alt="Card image cap"> -->
            <div class="card-body">
                <h2 class="card-title">Stage 1</h2>
                <p class="card-text">
                    <?php
                        displayStage(1);
                        echo '<b>Completed: </b>';
                        if ($row['stg1w1'] != '0' and $row['stg1w2'] != '0' and $row['stg1w3'] != '0' and $row['stg1w4'] != '0') {
                            echo '<span class="text-success"><i class="fas fa-check-circle"></i> Yes</span>';
                            $stg1c='1';
                        } else {
                            echo '<span class="text-danger"><i class="fas fa-times-circle"></i> No</span>';
                        }
                    ?>
                </p>
            </div>
        </div>


        <!-- display stage 2 data -->
        <div class="card">
            <!-- <img class="card-img-top" src="..." alt="Card image cap"> -->
            <div class="card-body">
                <h2 class="card-title">Stage 2</h2>
                <p class="card-text">
                    <?php
                        if ($stg1c != '0') {
                            displayStage(2);
                            echo '<b>Completed: </b>';
                            if ($row['stg2w1'] != '0' and $row['stg2w2'] != '0' and $row['stg2w3'] != '0' and $row['stg2w4'] != '0' and $row['stg2w5'] != '0') {
                                echo '<span class="text-success"><i class="fas fa-check-circle"></i> Yes</span>';
                                $stg2c='1';
                            } else {
                                echo '<span class="text-danger"><i class="fas fa-times-circle"></i> No</span>';
                            }
                        } else {
                            echo 'Previous stage not yet completed.';
                        }
                    ?>
                </p>
            </div>
        </div>

        <!-- display stage 3 data -->
        <div class="card">
            <!-- <img class="card-img-top" src="..." alt="Card image cap"> -->
            <div class="card-body">
                <h2 class="card-title">Stage 3</h2>
                <p class="card-text">
                    <?php
                        if ($stg2c != '0') {
                            displayStage(3);
                            echo '<b>Completed: </b>';
                
                            if ($row['stg3w1'] != '0' and $row['stg3w2'] != '0' and $row['stg3w3'] != '0' and $row['stg3w4'] != '0' and $row['stg3w5'] != '0') {
                                echo '<span class="text-success"><i class="fas fa-check-circle"></i> Yes</span>';
                                $stg3c='1';
                            } else {
                                echo '<span class="text-danger"><i class="fas fa-times-circle"></i> No</span>';
                            }
                        } else {
                            echo 'Previous stage not yet completed.';
                        }
                    ?>
                </p>
            </div>
        </div>


        <!-- display stage 4 data -->
        <div class="card">
            <!-- <img class="card-img-top" src="..." alt="Card image cap"> -->
            <div class="card-body">
                <h2 class="card-title">Stage 4</h2>
                <p class="card-text">
                    <?php
                        if ($stg3c != '0') {
                            displayStage(4);
                            echo '<b>Completed: </b>';
                
                            if ($row['stg4w1'] != '0' and $row['stg4w2'] != '0' and $row['stg4w3'] != '0' and $row['stg4w4'] != '0' and $row['stg4w5'] != '0') {
                                echo '<span class="text-success"><i class="fas fa-check-circle"></i> Yes</span>';
                                $stg4c='1';
                            } else {
                                echo '<span class="text-danger"><i class="fas fa-times-circle"></i> No</span>';
                            }
                        } else {
                            echo 'Previous stage not yet completed.';
                        }
                    ?>
                </p>
            </div>
        </div>

        <!-- display stage 5 data -->
        <div class="card">
            <!-- <img class="card-img-top" src="..." alt="Card image cap"> -->
            <div class="card-body">
                <h2 class="card-title">Stage 5</h2>
                <p class="card-text">
                    <?php
                //had to write the whole code for function since fee is calculated for each workshop in stage 5
                if ($stg4c != '0') {
                    for ($i=1; $i <= 5; $i++) {
                        if ($row['stg5w'.$i.'fee'] !=0) {
                            echo '<b>Workshop '.$i.': </b>';
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
                    
                                    echo 'Applied for #'.$row['stg5w'.$i.'_applied_for'].' ';
                                                        
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
                    
                                echo 'Completed at #'.$row['stg5w'.$i].'';
                    
                                if (mysqli_num_rows($result1)!=0) {
                                    echo ' <code>'.$row1['memorisable_name'].'</code> <small>(Date: '.date("d-M-Y h:i A", strtotime($row1['date'])).')</small>';
                                } else {
                                    echo ' ( <code>Workshop Deleted</code> )';
                                }
                            }
                            echo '<br />';
                        } else {
                            echo 'Workshop'.$i.' Fee Not Paid';
                        }
                    }

                    echo '<b>Completed: </b>';

                    if ($row['stg5w1'] != '0' and $row['stg5w2'] != '0' and $row['stg5w3'] != '0' and $row['stg5w5'] != '0' and $row['stg5w5'] != '0') {
                        echo '<span class="text-success"><i class="fas fa-check-circle"></i> Yes</span>';
                        $stg5c='1';
                    } else {
                        echo '<span class="text-danger"><i class="fas fa-times-circle"></i> No</span>';
                    }
                } else {
                    echo 'Previous stage not yet completed.';
                }
                ?>
                </p>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <span class="text-muted">A Cut Above, Surgery Club of Kasturba Medical College, Mangalore (Manipal
                University). <span class="small float-right">Generated on <?=date("d-M-Y")?>.</span></span>
        </div>
    </footer>

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
    </script>
</body>

</html>