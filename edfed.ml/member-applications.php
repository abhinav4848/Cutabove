<!--
    Abandoned
    This was the old "all-people.php". Go to that page instead.
-->


<?php
session_start();
if ($_SESSION['permission'] == 'admin' or $_SESSION['permission'] == 'supervisor') {
    include('connect-db.php');

    if ($_SESSION['permission'] == 'supervisor' and $_GET['kit']!=1) {
        header("Location: {$_SERVER['PHP_SELF']}?kit=1");
    }
    /*The function below is a paginator. It accepts the kit value and calculates how many rows with that kit value exist, then using filtering, it only fetches those particular rows which are required based on the page number and rows per page.*/
    function getData($kit)
    {
        //https://stackoverflow.com/questions/3705318/simple-php-pagination-script/9975616#9975616
        global $link, $result, $currentpage, $totalpages;
        // https://stackoverflow.com/a/38979558/2365231
        // find out how many rows are in the table
        $sql = "SELECT COUNT(*) FROM `cutabove` WHERE `kit` = ".$kit;
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
        $query = "SELECT * FROM `cutabove` WHERE `kit` = ".$kit." ORDER BY id LIMIT $offset, $rowsperpage";
        $result = mysqli_query($link, $query) or die(mysql_error());
    }


    /*Find out which kit people ar requested. If none provided, assume kit = 1*/
    if (array_key_exists('kit', $_GET) and is_numeric($_GET['kit']) and $_GET['kit'] <7) {
        //fetch list of that kit
        getData($_GET['kit']);
    } else {
        //If no kit defined, redirect.
        //I could've assumed kit = 1 and done getData(1), but pagination links wouldn't know the $_GET['kit'] since it wouldn't have been set. So if you set $_GET['kit'] = 1 manually here and move on, it works perfectly well, but doesn't feel right.
        header("Location: {$_SERVER['PHP_SELF']}?kit=1");
    }
} else {
    header("Location: index.php");
}

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

    <!-- Bootstrap CSS -->
    <?php include 'header.php'; //css-theme detector?>

    <!-- Icons -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css"
        integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">

    <title>View All People</title>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div id="tablediv">
        <div id="error">
            <?php
            if ($error!="") {
                echo '<div class="alert alert-danger" role="alert">'.$error.'</div>';
            }
            ?>
        </div>
        <table class="table table-striped table-responsive-sm" id="detailsTable">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">College ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">DOB</th>
                    <?php
                    if (array_key_exists('kit', $_GET) and $_GET['kit']== 0) {
                        echo '<th scope="col">Sem</th><th scope="col">Phone</th><th scope="col">Email</th><th scope="col">Comment</th>';
                    }
                    ?>
                    <th scope="col">View</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                //loop through results of database query, displaying them in the table
                // while there are rows to be fetched..
                while ($row = mysqli_fetch_array($result)) {
                    //echo out the contents of each row into a table
                    echo '<tr>';
                    echo '<th scope="row">' . $row['id'] . '</th>';
                    echo '<td>' . $row['clg_reg'] . '</td>';
                    echo '<td>' . $row['name'] . '</td>';
                    echo '<td>' . $row['dob'] . '</td>';
                    if (array_key_exists('kit', $_GET) and $_GET['kit']== 0) {
                        echo '<td>' . $row['semester'] . '</td><td>';

                        if ($row['phone']!= 0) {
                            echo '<a href="tel:' . $row['phone'] . '">+91-' . $row['phone'] . ' (Call)</a>';
                        }
                        if ($row['phone']!= 0 and $row['phone_whatsapp']!= 0) {
                            echo ',<br />';
                        }
                        if ($row['phone_whatsapp']!= 0) {
                            echo '<a href="https://wa.me/+91' . $row['phone_whatsapp'] . '">+91-' . $row['phone_whatsapp'] . ' (WA)</a>';
                        }

                        echo '</td>';

                        echo '<td><a href="mailto:' . $row['email'] . '?subject=A Cut Above&body=Hi '.strtok($row['name'], " ").',%0A%0A%0A%0A Yours Sincerely,%0A'.$_SESSION['name'].' ('.$_SESSION['permission'].')%0ACut Above%0AKMC, Mangalore" target="_top">' . $row['email'] . '</a></td>';
                        //echo '<td>' . htmlspecialchars($row['comments'], ENT_QUOTES) . '</td>';
                        echo '<td>';
                        echo strlen($row['comments']) > 100 ? substr($row['comments'], 0, 100)."... (click view to read all)" : $row['comments'];
                        echo '</td>';
                    }
                    echo '<td><a href="one-person.php?id=' . $row['id'] . '">View</a></td>';
                    echo '<td>';
                    if ($_SESSION['permission'] == 'admin') {
                        //supervisors are only allowed to view, and sign people up for workshop
                        echo '<a href="edit.php?id=' . $row['id'] . '">Edit</a> | <a href="fee.php?id=' . $row['id'] . '">Fee</a> | ';
                    }
                    echo '<a href="member-workshop.php?id=' . $row['id'] . '">Workshop</a>';
                    if ($_SESSION['god']== 1) {
                        echo ' | <a href="deletion.php?clg_reg='.$row['clg_reg'].'">Delete</a>';
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
        echo " <a class='page-link' href='{$_SERVER['PHP_SELF']}?kit=".$_GET['kit']."&currentpage=1'><<</a> ";
        echo '</li>';
        // get previous page num
        $prevpage = $currentpage - 1;
        // show < link to go back to 1 page
        echo '<li class="page-item">';
        echo " <a class='page-link' href='{$_SERVER['PHP_SELF']}?kit=".$_GET['kit']."&currentpage=$prevpage'>Prev</a> ";
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
                echo " <a class='page-link' href='{$_SERVER['PHP_SELF']}?kit=".$_GET['kit']."&currentpage=$x'>$x</a> ";
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
          echo " <a class='page-link' href='{$_SERVER['PHP_SELF']}?kit=".$_GET['kit']."&currentpage=$nextpage'>Next</a> ";
          echo '</li>';
          // echo forward link for lastpage
          echo '<li class="page-item">';
          echo " <a class='page-link' href='{$_SERVER['PHP_SELF']}?kit=".$_GET['kit']."&currentpage=$totalpages'>>></a> ";
          echo '</li>';
      } // end if
      echo '</ul>
      </nav>';
      /****** end build pagination links ******/
      ?>
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

    <script>
    function filterTable(event) {
        var filter = event.target.value.toUpperCase();
        var rows = document.querySelector("#detailsTable tbody").rows;

        for (var i = 0; i < rows.length; i++) {
            var firstCol = rows[i].cells[1].textContent.toUpperCase();
            var secondCol = rows[i].cells[2].textContent.toUpperCase();
            var thirdCol = rows[i].cells[3].textContent.toUpperCase();
            if (firstCol.indexOf(filter) > -1 || secondCol.indexOf(filter) > -1 || thirdCol.indexOf(filter) > -1) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        }
    }
    /*tracks the changes in the search box of navbar and summons filterTable to narrow down results. Funny anecdote. When I was porting the navbar from every page into one common include, I accidentally forgot to port over the searchDetails box. This caused the ajax query below to fail to function. I think it was because querySelector was not able to find any element on the page with that name so status.clic also glitched. It took me 3 hours to identify the flaw. Use a text comparison tool to analyse the before port and after port code changes and finally found the missing searchDetails box. All along I was blaming the select function of the navbar for possibly breaing things but I tested everything and there was a problem even without the suspicious code. BEcause the last line containing searchDetails was missing. Should start using version control soon. Luckily I had a zip file of the old codes I'd lent to my successor developer of edfed.*/
    document.querySelector('#searchDetails').addEventListener('keyup', filterTable, false);

    $(".status").click(function(e) {
        e.preventDefault();
        var object = this;
        $.ajax({
                type: "POST",
                url: "manage-applications.php",
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