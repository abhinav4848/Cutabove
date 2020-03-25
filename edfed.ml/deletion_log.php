<?php
session_start();
$error="";

if ($_SESSION['permission'] == 'admin') {
    if ($_SESSION['god']!= 1) {
        echo '<html><head><meta http-equiv="refresh" content="1;url=council-dashboard.php?id.php"></head><body><h1>Go back to <a href="council-dashboard.php">Dashboard</a> and enable <b>God Mode</b></h1>Redirecting to dashboard</body></html>';
    } else {
        include('connect-db.php');
    }
} else {
    header("Location: index.php?redirect=".$_SERVER['REQUEST_URI']);
}

// find out how many rows are in the table
$sql = "SELECT COUNT(*) FROM `cutabove_deletion_log`";
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
$query = "SELECT * FROM `cutabove_deletion_log` ORDER BY id DESC LIMIT $offset, $rowsperpage";
$result = mysqli_query($link, $query) or die(mysql_error());

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

    <title>Deletion</title>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container-fluid" id="tablediv">

        <?php
    /******  build the pagination links ******/
    echo '<nav aria-label="page navigation">
    <ul class="pagination justify-content-center">';

    // range of num links to show
    $range = 1;

    // if not on page 1, don't show back links
    if ($currentpage > 1) {
        // show << link to go back to page 1
        echo '<li class="page-item">';
        echo " <a class='page-link' href='{$_SERVER['PHP_SELF']}?currentpage=1'><<</a> ";
        echo '</li>';
        // get previous page num
        $prevpage = $currentpage - 1;
        // show < link to go back to 1 page
        echo '<li class="page-item">';
        echo " <a class='page-link' href='{$_SERVER['PHP_SELF']}?currentpage=$prevpage'>Prev</a> ";
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
                echo " <a class='page-link' href='{$_SERVER['PHP_SELF']}?currentpage=$x'>$x</a> ";
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
        echo " <a class='page-link' href='{$_SERVER['PHP_SELF']}?currentpage=$nextpage'>Next</a> ";
        echo '</li>';
        // echo forward link for lastpage
        echo '<li class="page-item">';
        echo " <a class='page-link' href='{$_SERVER['PHP_SELF']}?currentpage=$totalpages'>>></a> ";
        echo '</li>';
    } // end if
      echo '<li class="page-item"><a class="page-link" href="deletion.php">Deletion</a></li>';
      echo '</ul>
      </nav>';
      /****** end build pagination links ******/
      ?>

        <table class="table table-striped table-responsive-sm" id="detailsTable">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Log</th>
                    <th scope="col">Council Name</th>
                    <th scope="col">Comments</th>
                    <th scope="col">Date Time</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    //loop through results of database query, displaying them in the table
                    // while there are rows to be fetched..
                    while ($row = mysqli_fetch_array($result)) {
                        //echo out the contents of each row into a table
                        echo '<tr>';
                        echo '<td data-title="ID">' . $row['id'] . '</td>';
                        echo '<td data-title="Log">' . $row['log'] . '</td>';

                        $query_council = 'SELECT `name` FROM `cutabove_council` WHERE council_id = '.$row['council_id'].' LIMIT 1';
                        $result_council = mysqli_query($link, $query_council);
                        $row_council = mysqli_fetch_array($result_council);

                        if (array_key_exists("name", $row_council)) {
                            echo '<td data-title="Name"><a href="edit-council.php?id=' . $row['council_id'] . '" target="_blank">' . $row_council['name'] . '</a></td>';
                        } else {
                            echo '<td data-title="Name"><code>User deleted.<br />ID was #'.$row['council_id'].'</code></td>';
                        }
                        echo '<td data-title="Comments">' . $row['comments'] . '</td>';
                        echo '<td data-title="Date">' .date('d-M-Y h:i A', strtotime($row['datetime'])). '</td>';
                        echo '</tr>';
                    }
                ?>
            </tbody>
        </table>
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
</body>

</html>