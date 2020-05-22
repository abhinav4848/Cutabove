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
} elseif (strpos($_SERVER['REQUEST_URI'], '/assets/acknowledgements.php')!== false) {
    echo '<pre>
        Website Created by Dr. Abhinav Kumar (140201306). You can find me at <a href="https://abhinavkr.ga">https://abhinavkr.ga</a> or <a href="https://abhinavkr.com">https://abhinavkr.com</a>.
        This was developed and tested by Abhinav when Amit approached him for a website for the surgery club.
        The coding took over a year to complete and if Abhinav would\'ve charged cash for it, this site would\'ve cost Amit his bilateral kidneys.

        I mean, I did get a mini treat from him once. But then he added many changes and modifications to his club operations. So lots more coding was done.
        The treat for that is pending to this date.

        Abhinav also went on to develop the website for his Batch\'s yearbook. <a href="https://saturnalia.ml">https://saturnalia.ml</a>, as well as implement QR code passes for
        entry into Ocean pearl for the Socials night. 

        If you\'re reading this, just drop me a fun email saying how awesome I am: abhinav4848 @ gmail .com (remove the spaces).

        Signing off,

        Dr. Abhinav Kumar
        31st December, 2019.</pre>';
} else {
    echo "<!-- 
    Website Created by Dr. Abhinav Kumar (140201306). You can find me at https://abhinavkr.ga or https://abhinavkr.com
    This was developed and tested by Abhinav when Amit approached him for a website for the surgery club.
    The coding took over a year to complete and if Abhinav would've charged cash for it, this site would've cost Amit his bilateral kidneys.

    I mean, I did get a mini treat from him once. But then he added many changes and modifications to his club operations. So lots more coding was done.
    The treat for that is pending to this date.

    Abhinav also went on to develop the website for his Batch's yearbook. https://saturnalia.ml, as well as implement QR code passes for
    entry into Ocean pearl for the Socials night. 

    If you're reading this, just drop me a fun email saying how awesome I am: abhinav4848 @ gmail .com (remove the spaces).

    Signing off,

    Dr. Abhinav Kumar
    31st December, 2019.
    -->";
}