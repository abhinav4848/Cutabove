<?php
date_default_timezone_set("Asia/Kolkata");
$link = mysqli_connect("localhost", "user", "password", "database");

// Check connection
if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}

if (!empty($_POST)) {
    foreach ($_POST as $key => $value) {
        $_POST[$key] = is_array($key) ? $_POST[$key]: strip_tags($_POST[$key], '<a><b><u><i>');
    }
}