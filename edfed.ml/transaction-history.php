<?php
session_start();

if ($_SESSION['permission'] == 'admin') {
    include('connect-db.php');
    
    if (array_key_exists('start_date', $_GET) and array_key_exists("end_date", $_GET)) {
        $whereclause="WHERE last_edited_at >= '".date("Y-m-d 00:00:00", strtotime($_GET['start_date']))."' AND last_edited_at <='".date("Y-m-d 23:59:59", strtotime($_GET['end_date']))."'";
        $start_date = date("d-M-Y", strtotime($_GET['start_date']));
        $end_date = date('d-M-Y', strtotime($_GET['end_date']));
    } else {
        //default is for current financial year
        if (date('m')<4) {
            //if current month is <April, set start date to 1st april of last year
            $whereclause="WHERE last_edited_at >= '".date("Y-04-01 00:00:00", strtotime('last year'))."' AND last_edited_at <='".date("Y-m-d H:i:s")."'";
            $start_date = date("01-M-Y", strtotime('1st April, last year'));
        } else {
            //else set start date to 1st April of this year
            $whereclause="WHERE last_edited_at >= '".date("Y-04-01 00:00:00")."' AND last_edited_at <='".date("Y-m-d H:i:s")."'";
            $start_date = date("01-M-Y", strtotime('1st April'));
        }
        $end_date = date('d-M-Y');
    }
    
    $sql = "SELECT COUNT(*) FROM `cutabove_fee` " .$whereclause;
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

    $final=$net=$stg1total=$stg2total=$stg3total=$stg4total=0;
    $query_feeCheck = "SELECT `stage`, `old_value`,`new_value` FROM `cutabove_fee` ".$whereclause;
    $result_feeCheck = mysqli_query($link, $query_feeCheck);
    while ($row_feeCheck = mysqli_fetch_array($result_feeCheck)) {
        $net = $row_feeCheck['new_value']-$row_feeCheck['old_value'];
        $final = $final + $net;
        if ($row_feeCheck['stage'] == 'stg1fee') {
            $stg1total+=$net;
        }
        if ($row_feeCheck['stage'] == 'stg2fee') {
            $stg2total+=$net;
        }
        if ($row_feeCheck['stage'] == 'stg3fee') {
            $stg3total+=$net;
        }
        if ($row_feeCheck['stage'] == 'stg4fee') {
            $stg4total+=$net;
        }
    }

    $query = "SELECT * FROM `cutabove_fee` " .$whereclause. " ORDER BY id DESC LIMIT $offset, $rowsperpage";
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

    <!-- Bootstrap CSS -->
    <?php include 'header.php'; //css-theme detector
    mediaQueryforTable(); //function in header.php?>

    <!-- Icons -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css"
        integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
    <!-- Datepicker -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

    <title>View All Fees</title>

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
                        <h5 class="card-title">Fee Curated</h5>
                        <form>
                            <label>
                                <input type="checkbox"
                                    onclick="document.getElementsByClassName('datepick')[0].readOnly=this.checked; document.getElementsByClassName('datepick')[1].readOnly=this.checked;">
                                Disable Direct Edit
                            </label>
                            <div class="input-daterange">
                                <input type="text" class="form-control datepick" name="start_date"
                                    value="<?php echo $start_date; ?>">
                                <p class="text-center mb-0">to</p>
                                <input type="text" class="form-control datepick" name="end_date"
                                    value="<?php echo $end_date; ?>">
                            </div>
                            <button type="submit" class="btn btn-primary mt-1">Refresh</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="column">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Fee Breakdown</h5>
                        <p class="card-text">
                            <?php
                            echo "Total: ₹ <b>" . $final . "</b>/-";
                            echo "<hr />";
                            echo "Stage 1: ₹ <b>" . $stg1total . "</b>/- <br />";
                            echo "Stage 2: ₹ <b>" . $stg2total . "</b>/- <br />";
                            echo "Stage 3: ₹ <b>" . $stg3total . "</b>/- <br />";
                            echo "Stage 4: ₹ <b>" . $stg4total . "</b>/- <br />";
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
    <table class="table table-striped table-responsive-sm" id="detailsTable">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Transact ID</th>
                <th scope="col">User</th>
                <th scope="col">Stage</th>
                <th scope="col">Old Value</th>
                <th scope="col">New Value</th>
                <th scope="col">Edited by</th>
                <th scope="col">Edited at</th>
                <!--<th scope="col">Comments</th>-->
            </tr>
        </thead>
        <tbody>
            <?php
                //loop through results of database query, displaying them in the table
                while ($row = mysqli_fetch_array($result)) {
                    //echo out the contents of each row into a table

                    $query1 = "SELECT `name` FROM `cutabove` WHERE id=".$row['user_id']." LIMIT 1";
                    $result1 = mysqli_query($link, $query1);
                    $row1 = mysqli_fetch_array($result1);

                    $query2 = "SELECT `name` FROM `cutabove_council` WHERE council_id=".$row['edited_by']." LIMIT 1";
                    $result2 = mysqli_query($link, $query2);
                    $row2 = mysqli_fetch_array($result2);

                    echo '<tr>';
                    echo '<td scope="row" data-title="Transact ID">' . $row['id'] . '</td>';
                    if (mysqli_num_rows($result1)!=0) {
                        echo '<td data-title="Name"><a href="one-person.php?id=' . $row['user_id'] . '">'.$row1['name'].'</td>';
                    } else {
                        echo '<td data-title="Name"><code>User Deleted. (Id was #'.$row['user_id'].')</code></td>';
                    }

                    echo '<td data-title="Stage">' . $row['stage'] . '</td>';

                    echo '<td data-title="Old Value">' . $row['old_value'] . '</td>';
                    echo '<td data-title="New Value">' . $row['new_value'] . '</td>';

                    if (mysqli_num_rows($result2)!=0) {
                        echo '<td data-title="Edited By"><a href="edit-council.php?id=' . $row['edited_by'] . '">'.$row2['name'].'</td>';
                    } else {
                        echo '<td data-title="Edited By"><code>User Deleted. (Id was #'.$row['edited_by'].')</code></td>';
                    }

                    echo '<td data-title="Edited at">' . date("d-M-Y h:i A", strtotime($row['last_edited_at'])) . '</td>';
                    // echo '<td>' . $row['comments'] . '</td>';
                    echo "</td></tr>";
                }
                ?>
        </tbody>
    </table>

    <?php
        /******  build the pagination links ******/
        $parameters = 'start_date='.$start_date.'&end_date='.$end_date;
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

    </div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <!-- https://stackoverflow.com/questions/44212202/my-javascript-is-returning-this-error-ajax-is-not-a-function -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js">
    </script>

    <script>
    // https://uxsolutions.github.io/bootstrap-datepicker/?markup=range&format=dd-M-yyyy&weekStart=&startDate=&endDate=&startView=0&minViewMode=0&maxViewMode=4&todayBtn=linked&clearBtn=false&language=en&orientation=auto&multidate=false&multidateSeparator=&autoclose=on&todayHighlight=on&keyboardNavigation=on&forceParse=on#sandbox
    $('.input-daterange').datepicker({
        format: "dd-M-yyyy",
        todayBtn: "linked",
        multidate: false,
        autoclose: true,
        todayHighlight: true
    });

    function filterTable(event) {
        var filter = event.target.value.toUpperCase();
        var rows = document.querySelector("#detailsTable tbody").rows;

        for (var i = 0; i < rows.length; i++) {
            var firstCol = rows[i].cells[1].textContent.toUpperCase();
            var secondCol = rows[i].cells[2].textContent.toUpperCase();
            var thirdCol = rows[i].cells[5].textContent.toUpperCase();
            if (firstCol.indexOf(filter) > -1 || secondCol.indexOf(filter) > -1 || thirdCol.indexOf(filter) > -1) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        }
    }
    document.querySelector('#searchDetails').addEventListener('keyup', filterTable, false);
    </script>
</body>

</html>