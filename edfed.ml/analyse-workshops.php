<?php
session_start();
if ($_SESSION['permission'] == 'admin') {
    include('connect-db.php');
    if (array_key_exists('start_date', $_GET) and array_key_exists("end_date", $_GET)) {
        $whereclause="WHERE date >= '".date("Y-m-d 00:00:00", strtotime($_GET['start_date']))."' AND date <='".date("Y-m-d 23:59:59", strtotime($_GET['end_date']))."'";
        $start_date = date("d-M-Y", strtotime($_GET['start_date']));
        $end_date = date('d-M-Y', strtotime($_GET['end_date']));
    } else {
        if (date('m')<4) {
            //if current month is <April, set start date to 1st april of last year
            $whereclause="WHERE date >= '".date("Y-04-01 00:00:00", strtotime('last year'))."' AND date <='".date("Y-m-d H:i:s", strtotime('31st March, this year'))."'";
            $start_date = date("01-M-Y", strtotime('1st April, last year'));
            $end_date = date('31-M-Y', strtotime('31st March, this year'));
        } else {
            //else set start date to 1st April of this year
            $whereclause="WHERE date >= '".date("Y-04-01 00:00:00")."' AND date <='".date("Y-m-d 23:59:59", strtotime('31st March, next year'))."'";
            $start_date = date("01-M-Y", strtotime('1st April, this year'));
            $end_date = date('d-M-Y', strtotime('31st March, next year'));
        }
    }
    
    if (array_key_exists('completion', $_GET) and $_GET['completion']=='on') {
        $whereclause.=" AND completed = 1";
        $orderby = 'DESC';
        //helps for pagination building. (parameters list to pass to next page)
        $completion = $_GET['completion'];
    } else {
        $whereclause.=" AND completed = 0";
        $orderby = 'ASC';
        //helps for pagination building. (parameters list to pass to next page)
        $completion = 'off';
    }

    $sql = "SELECT COUNT(*) FROM `cutabove_workshop` " .$whereclause;
    $resultCount = mysqli_query($link, $sql);
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

    $levels = [];
    $query_summation = "SELECT `level_name` FROM `cutabove_workshop` ".$whereclause;
    $result_summation = mysqli_query($link, $query_summation);

    while ($row_summation = mysqli_fetch_array($result_summation)) {
        if (array_key_exists($row_summation['level_name'], $levels)) {
            //increment the index already existing for this level
            $levels[$row_summation['level_name']]+=1;
        } else {
            //create index for this level cuz of first occurance of this level
            $levels[$row_summation['level_name']]=1;
        }
    }

    $total=0;
    foreach ($levels as $key => $value) {
        $total+=$value;
    }

    $query = "SELECT * FROM `cutabove_workshop` " .$whereclause. " ORDER BY date ". $orderby ." LIMIT $offset, $rowsperpage";
    $result = mysqli_query($link, $query);
} else {
    header("Location: index.php");
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

    <!-- Datepicker -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

    <title>Analyse Workshops</title>

    <!--Hardcoded CSS for this page-->
    <style type="text/css">
    .card {
        margin: 4px 4px;
        width: 19rem;
    }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container" id="tablediv">
        <div class="row">
            <div class="column">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Workshops Curated</h5>
                        <form>
                            <label>
                                <input type="checkbox"
                                    onclick="document.getElementsByClassName('datepick')[0].readOnly=this.checked; document.getElementsByClassName('datepick')[1].readOnly=this.checked;">
                                Disable Direct Edit
                            </label>
                            <div class="input-daterange">
                                <input type="text" class="form-control datepick" name="start_date"
                                    value="<?=$start_date?>">
                                <p class="text-center mb-0">to</p>
                                <input type="text" class="form-control datepick" name="end_date" value="<?=$end_date?>">
                            </div>
                            <label><input type="checkbox" name="completion" <?php
                            if (array_key_exists('completion', $_GET) and $_GET['completion']=='on') {
                                echo 'checked';
                            }
                            ?>>Completed</label>
                            <button type="submit" class="btn btn-primary mt-1">Refresh</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="column">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Workshop Breakdown</h5>
                        <p class="card-text">
                            <?php
                            echo 'Total: <b><span class="badge badge-pill badge-success">'.$total.'</span> Workshops</b>';
                            echo "<pre>";
                            print_r($levels);
                            echo "</pre>";
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="column">
                <label class="mt-1">
                    <input type="checkbox" onclick="document.getElementById('fullquery').style.display=this.checked? 'block'
                        : 'none' ;">
                    Show the Query
                </label>
                <div class="card border-primary" id="fullquery" style="display: none">
                    <div class="card-body">
                        <h5 class="card-title">Query</h5>
                        <p class="card-text">
                            <?=$query?>
                        </p>
                    </div>
                </div>
            </div> <!-- column -->
        </div> <!-- row -->
    </div> <!-- Container -->
    <?php
    if (array_key_exists('completion', $_GET) and $_GET['completion']=='on') {
        echo '<h2>Completed Workshops</h2>';
        echo '<small class="text-muted">Sorted by most recent completed workshop first</small>';
    } else {
        echo '<h2>Upcoming Workshops</h2>';
        echo '<small class="text-muted">Sorted by earliest workshop first</small>';
    }
    ?>
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
            while ($row = mysqli_fetch_array($result)) {
                //echo out the contents of each row into a table
                
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
                echo '</td>';
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
            ?>
        </tbody>
    </table>

    <?php
        /******  build the pagination links ******/
        $parameters = 'start_date='.$start_date.'&end_date='.$end_date.'&completion='.$completion;
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
        }// end if

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js">
    </script>

    <script>
    $('.input-daterange').datepicker({
        format: "dd-M-yyyy",
        todayBtn: "linked",
        multidate: false,
        autoclose: true,
        todayHighlight: true
    });
    </script>
</body>

</html>