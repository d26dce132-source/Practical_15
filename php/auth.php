<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$timeout = 300; // 5 minutes

if (!isset($_SESSION["user_id"])) {
    header("Location: ../pages/login.html");
    exit();
}

if (isset($_SESSION["last_activity"])) {

    if (time() - $_SESSION["last_activity"] > $timeout) {

        session_unset();
        session_destroy();

        header("Location: ../pages/login.html?status=timeout");
        exit();
    }
}

$_SESSION["last_activity"] = time();

?>