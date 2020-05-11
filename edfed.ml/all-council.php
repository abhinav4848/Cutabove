<?php
session_start();
include 'assets/acknowledgements.php';

if ($_SESSION['permission'] == 'admin' or $_SESSION['permission'] == 'supervisor') {
    include('connect-db.php');
    if (array_key_exists('list', $_GET)) {
        if ($_GET['list']=='admin') {
            $query = "SELECT * FROM `cutabove_council` WHERE permission = 'admin' ORDER BY council_id";
        }
        if ($_GET['list']=='supervisor') {
            $query = "SELECT * FROM `cutabove_council` WHERE permission = 'supervisor' ORDER BY council_id";
        }
        if ($_GET['list']=='retired') {
            $query = "SELECT * FROM `cutabove_council` WHERE permission = 'retired' ORDER BY council_id";
        }
    } else {
        $query = "SELECT * FROM `cutabove_council` WHERE permission = 'admin' OR permission = 'supervisor' ORDER BY council_id";
    }
    $result = mysqli_query($link, $query) or die(mysql_error());
} else {
    header("Location: index.php?redirect=".$_SERVER['REQUEST_URI']);
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

    <title>Core Members</title>


</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container-fluid" id="tablediv">
        <h1>Core Members</h1>
        <a href="<?=basename(__FILE__)?>">all</a> | <a href="?list=admin">Admins</a> | <a
            href="?list=supervisor">Supervisors</a>
        | <a href="?list=retired">Retired</a>
        <table class="table table-striped table-responsive-sm" id="detailsTable">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Username</th>
                    <th scope="col">Name</th>
                    <th scope="col">Permission</th>
                    <th scope="col">Phone</th>
                    <th scope="col">Email</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                //loop through results of database query, displaying them in the table
                while ($row = mysqli_fetch_array($result)) {
                    //echo out the contents of each row into a table
                    echo '<tr>';
                    echo '<td scope="row" data-title="ID">' . $row['council_id'] . '</td>';
                    echo '<td data-title="Username">' . $row['username'] . '</td>';
                    echo '<td data-title="Name">' . $row['name'] . '</td>';
                    echo '<td data-title="Permission">';
                    if ($row['permission'] == 'admin') {
                        echo '<span class="badge badge-danger">Admin</span>';
                    } elseif ($row['permission'] == 'supervisor') {
                        echo ' <span class="badge badge-primary">Supervisor</span>';
                    } else {
                        echo ' <span class="badge badge-secondary">Retired</span>';
                    }
                    echo '</td>';
                    echo '<td data-title="Phone">';
                    //https://stackoverflow.com/a/2220529/2365231
                    if ($row['phone']!= 0) {
                        echo '<a href="tel:' . $row['phone'] . '">' . $row['phone'] . '</a> (Call)';
                    }
                    if ($row['phone']!= 0 and $row['phone_whatsapp']!= 0) {
                        echo ',<br />';
                    }
                    if ($row['phone_whatsapp']!= 0) {
                        echo '<a href="https://wa.me/+91' . $row['phone_whatsapp'] . '">' . $row['phone_whatsapp'] . '</a> (WA)';
                    }
                    echo '</td>';
                    echo '<td data-title="Email"><a href="mailto:' . $row['email'] . '?subject=A Cut Above&body=Hi '.strtok($row['name'], " ").',%0A%0A%0A%0A Yours Sincerely,%0A'.$_SESSION['name'].' ('.$_SESSION['permission'].')%0ACut Above%0AKMC, Mangalore" target="_top">' . $row['email'] . '</a></td>';
                    echo '<td><a href="edit-council.php?id=' . $row['council_id'] . '">Edit</a> | <a href="supervisor-apply-workshop.php?id=' . $row['council_id'] . '">Workshop</a> | <a href="supervisor-attendance.php?id=' . $row['council_id'] . '">Attendance</a>';
                    if ($_SESSION['god']== 1) {
                        echo ' | <a href="deletion.php?council_username='.$row['username'].'">Delete</a>';
                    }
                    echo "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
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

    document.querySelector('#searchDetails').addEventListener('keyup', filterTable, false);
    </script>
</body>

</html>