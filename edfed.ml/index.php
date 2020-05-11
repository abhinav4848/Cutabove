<?php
session_start();
$error="";
include 'connect-db.php';
include 'assets/acknowledgements.php';

if (array_key_exists("logout", $_GET)) {
    session_unset($_SESSION);
} elseif (array_key_exists("id", $_SESSION) and $_SESSION['id']) {
    //if there is a session id indicating person is already logged in, take them to their appropriate page.
    if ($_SESSION['permission'] == 'member') {
        header("Location: one-person.php");
    }
    if ($_SESSION['permission'] == 'supervisor') {
        header("Location: council-dashboard.php");
    }
    if ($_SESSION['permission'] == 'admin') {
        header("Location: council-dashboard.php");
    }
}

if (array_key_exists("submit", $_POST)) {
    //gathering errors if any
    if (!$_POST['registration']) {
        $error .= "A club registration number is needed. <br />";
    }
    if (array_key_exists("dateofbirth", $_POST)) {
        if (!$_POST['dateofbirth']) {
            $error .= "A date of birth is needed. <br />";
        }
    } elseif (array_key_exists("adminpass", $_POST)) {
        if (!$_POST['adminpass']) {
            $error .= "A Password needed, admin! <br />";
        }
    }
    
    if ($error !="") {
        $error = "<p><strong>There were errors in you form:</strong></p>".$error;
    } else {
        //if there is no error, check if member, supervisor, or admin.
        if ($_POST['permission']=='member') {
            $query = "SELECT * FROM `cutabove` WHERE clg_reg = '".mysqli_real_escape_string($link, $_POST['registration'])."'";
            $result = mysqli_query($link, $query);
            $row = mysqli_fetch_array($result);
            if (isset($row)) {
                //through print_r($_POST) I found out how the date was being sent. The database stores date as a "date".
                if ($_POST['dateofbirth'] == $row['dob']) {
                    if ($row['kit']== 1) {
                        $_SESSION['id'] = $row['id'];
                        $_SESSION['permission'] = 'member';
                        $_SESSION['name'] = $row['name'];
                        $_SESSION['colour'] = 'success';
                        $_SESSION['theme'] = $row['theme'];
                        if (array_key_exists("redirect", $_GET)) {
                            header("Location: ".$_GET['redirect']);
                        } else {
                            header("Location: one-person.php");
                        }
                    } else {
                        $error= "Your membership is not currently activated.";
                    }
                } else {
                    $error= "That date of birth is incorrect.";
                }
            } else {
                $error= "That club registration number doesn't exist.";
            }
        } elseif ($_POST['permission']=='supervisor') {
            $query = "SELECT * FROM `cutabove_council` WHERE username = '".mysqli_real_escape_string($link, $_POST['registration'])."' AND permission= 'supervisor' LIMIT 1";
            $result = mysqli_query($link, $query);
            $row = mysqli_fetch_array($result);
            if (isset($row)) {
                if ($_POST['adminpass'] == $row['password']) {
                    $_SESSION['id'] = $row['council_id']; //needed so after logging in, index.php should not be seen and will redirect to required page
                    $_SESSION['name'] = $row['name'];
                    $_SESSION['permission'] = 'supervisor';
                    $_SESSION['colour'] = 'primary';
                    $_SESSION['theme'] = $row['theme'];
                    header("Location: council-dashboard.php");
                } else {
                    $error= "That password is incorrect.";
                }
            } else {
                $error= "That username doesn't exist.";
            }
        } elseif ($_POST['permission']=='admin') {
            $query = "SELECT * FROM `cutabove_council` WHERE username = '".mysqli_real_escape_string($link, $_POST['registration'])."'";
            $result = mysqli_query($link, $query);
            $row = mysqli_fetch_array($result);
            if (isset($row)) {
                if ($_POST['adminpass'] == $row['password'] and $row['permission']=='admin') {
                    $_SESSION['id'] = $row['council_id']; //needed so after logging in, index.php should not be seen and will redirect to required page
                    $_SESSION['name'] = $row['name'];
                    $_SESSION['permission'] = 'admin';
                    $_SESSION['colour'] = 'danger';
                    $_SESSION['theme'] = $row['theme'];
                    if (array_key_exists("redirect", $_GET)) {
                        header("Location: ".$_GET['redirect']);
                    } else {
                        header("Location: council-dashboard.php");
                    }
                } else {
                    $error= "That password is incorrect.";
                }
            } else {
                $error= "That admin username doesn't exist.";
            }
        } else {
            echo "Something wrong!";
        }
        if ($error !="") {
            $error = "<p><strong>There were errors in you form:</strong></p>".$error;
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.95, shrink-to-fit=no">
    <meta name="theme-color" content="#28a745">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Welcome to the login page</title>

    <style type="text/css">
    form {
        display: none;
        margin-top: 20px;
    }

    #memberform {
        display: block;
    }

    #options,
    #error {
        margin-top: 10px;
    }

    .container {
        text-align: center;
    }

    .outer {
        display: table;
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
    }

    .middle {
        display: table-cell;
        vertical-align: middle;
    }

    .inner {
        margin-left: auto;
        margin-right: auto;
        width: 380px;
        padding: 10px;
        /*whatever width you want*/
    }

    html {
        background: url() no-repeat center center fixed;
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
    }

    .logo {
        display: inline-block;
        height: 180px;
        width: 160px;
        margin: 0 0 -5px 0;
        background: url('assets/A Cut Above Logo and Moto.png');
        background-position: center;
        background-size: 160px;
        background-repeat: no-repeat;
    }
    </style>
</head>

<body>
    <div class="container">
        <!-- Nested divs for vertical centering: https://stackoverflow.com/a/6182661/2365231 -->
        <div class="outer">
            <div class="middle">
                <div class="inner">
                    <?php
                    $query = "SELECT `value` FROM `cutabove_misc` WHERE property = 'login_page_notice' LIMIT 1";
                    $result = mysqli_query($link, $query);
                    $row = mysqli_fetch_array($result);
                    if ($row['value']!='0' and $row['value']!='') {
                        echo '<div class="alert alert-warning mt-2">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <b>Site Notice:</b><br />
                        '.$row['value'].'
                        </div>';
                    }
                    
                    $query = "SELECT `value` FROM `cutabove_misc` WHERE property = 'registrations' LIMIT 1";
                    $result = mysqli_query($link, $query);
                    $row = mysqli_fetch_array($result);
                    if ($row['value']!='0' and $row['value']!='') {
                        echo '<div class="alert alert-success mt-2">Registrations Open! <a href="registrations.php">Click here</a> to register.</div>';
                    } else {
                        echo '<p class="text-muted small mt-2">Registrations Disabled</p>';
                    }
                    ?>
                    <div class="logo"></div>
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-outline-success active" id="member">
                            <input type="radio" name="options" autocomplete="off" checked> Member
                        </label>
                        <label class="btn btn-outline-primary" id="supervisor">
                            <input type="radio" name="options" autocomplete="off"> Supervisor
                        </label>
                        <label class="btn btn-outline-danger" id="admin">
                            <input type="radio" name="options" autocomplete="off"> Admin
                        </label>
                    </div>
                    <div id="error"><?php if ($error!="") {
                        echo '<div class="alert alert-danger" role="alert">'.$error.'</div>';
                    } ?></div>
                    <form method="post" id="memberform">
                        <h3>Welcome, fellow Member!</h3>
                        <p>Enter your membership number and date of birth.</p>
                        <div class="form-group">
                            <input type="text" class="form-control" name="registration"
                                placeholder="College Registration Number" value="<?php if (array_key_exists('submit', $_POST) and $_POST['registration']!='') {
                        echo $_POST['registration'];
                    } ?>">
                        </div>
                        <div class="form-group">
                            <input type="date" class="form-control" name="dateofbirth" placeholder="Date of Birth">
                        </div>
                        <input type="hidden" name="permission" value="member">
                        <button type="submit" name="submit" class="btn btn-success">Member Log In</button>
                    </form>
                    <form method="post" id="supervisorform">
                        <h3>Welcome, Supervisor!</h3>
                        <p>Enter your supervisor registration and password.</p>
                        <div class="form-group">
                            <input type="text" class="form-control" name="registration"
                                placeholder="Supervisor registration number" value="<?php if (array_key_exists('submit', $_POST) and $_POST['registration']!='') {
                        echo $_POST['registration'];
                    } ?>">
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" name="adminpass"
                                placeholder="Enter Your 12 Digit Password">
                        </div>
                        <input type="hidden" name="permission" value="supervisor">
                        <button type="submit" name="submit" class="btn btn-primary">Supervisor Log In</button>
                    </form>
                    <form method="post" id="adminform">
                        <h3>Welcome, Admin!</h3>
                        <p>Enter your admin registration and password.</p>
                        <div class="form-group">
                            <input type="text" class="form-control" name="registration"
                                placeholder="Admin registration number" value="<?php if (array_key_exists('submit', $_POST) and $_POST['registration']!='') {
                        echo $_POST['registration'];
                    } ?>">
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" name="adminpass"
                                placeholder="Enter Your 19 Digit Password">
                        </div>
                        <input type="hidden" name="permission" value="admin">
                        <button type="submit" name="submit" class="btn btn-danger">Admin Log In</button>
                    </form>
                </div><!-- Inner -->
            </div><!-- Middle -->
        </div><!-- outer -->
    </div><!-- container -->

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

    <script type="text/javascript">
    function changeColourPlusShow(hexadecimal, visible) {
        $('meta[name=theme-color]').remove();
        $('head').append('<meta name="theme-color" content="' + hexadecimal + '">');

        $("#memberform, #supervisorform, #adminform").each(function() {
            $(this).hide();
        });

        if (visible == 'member') {
            $("#memberform").show();
        }
        if (visible == 'supervisor') {
            $("#supervisorform").show();
        }
        if (visible == 'admin') {
            $("#adminform").show();
        }
    }

    $("#admin").click(function() {
        changeColourPlusShow('#000000', 'admin');
    });
    $("#supervisor").click(function() {
        changeColourPlusShow('#007bff', 'supervisor');
    });
    $("#member").click(function() {
        changeColourPlusShow('#28a745', 'member');
    });
    </script>
</body>

</html>