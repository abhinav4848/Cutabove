<?php
session_start();
$error="";
if ($_SESSION['permission'] == 'admin') {
    include('connect-db.php');
} else {
    header("Location: index.php");
}

if (array_key_exists("submit", $_POST)) {
    foreach ($_POST as $key => $value) {
        //echo 'key: '.$key;
        //echo ' | value: '.$value;
        //echo '<br />';
        $query = "UPDATE `cutabove_misc` SET `value` = '".mysqli_real_escape_string($link, $value)."' WHERE `property` = '".$key."' LIMIT 1";
        if (!mysqli_query($link, $query)) {
            echo 'Fatal Error';
            break;
        }
    }
    header("Location: defaults.php?successEdit=1");
}
?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php
        if ($_SESSION['god']!= 1) {
            echo '<meta http-equiv="refresh" content="1;url=council-dashboard.php?id.php">';
        }
    ?>

    <!-- Bootstrap CSS -->
    <?php include 'header.php'; //css-theme detector?>

    <title>Cutabove- Defaults</title>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container-fluid" id="tablediv">
        <?php
        if ($_SESSION['god']!= 1) {
            echo '<h1>Go back to <a href="council-dashboard.php">Dashboard</a> and enable <b>God Mode</b></h1>';
            echo 'Redirecting to dashboard';
            die();
        }
        ?>
        <div id="error"><?php if ($error!="") {
            echo '<div class="alert alert-danger" role="alert">'.$error.'</div>';
        } ?></div>
        <form method="post">
            <h2>Site Wide Defaults</h2>
            <p>Any changes made here will impact the site as a whole. Know what you're doing.</p>
            <table class="table table-responsive-sm">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Property</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM `cutabove_misc`";
                    $result = mysqli_query($link, $query);
                    while ($row = mysqli_fetch_array($result)) {
                        echo '<tr><th scope="row">'.$row['id'].'</th><th scope="row">'.$row['property'].'</th><td><input type="text" class="form-control" name="'.$row['property'].'" value="' . htmlspecialchars($row['value']) . '"><small class="text-muted">'.$row['help'].'</small></td></tr>';
                    }
                    ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td><button type="submit" name="submit" class="btn btn-danger">Submit Page</button></td>
                    </tr>
                </tbody>
            </table>
        </form>
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
</body>

</html>