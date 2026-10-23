<?php

/* =====================================================
   CONTACT FORM - PRACTICAL 7
   ===================================================== */


/* -----------------------------------------------------
   CHECK REQUEST METHOD
   ----------------------------------------------------- */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header(
        "Location: ../pages/contact.html"
    );

    exit();

}


/* -----------------------------------------------------
   GET FORM DATA
   ----------------------------------------------------- */

$name =
    trim($_POST["name"] ?? "");

$email =
    trim($_POST["email"] ?? "");

$subject =
    trim($_POST["subject"] ?? "");

$message =
    trim($_POST["message"] ?? "");


/* -----------------------------------------------------
   SERVER-SIDE VALIDATION
   ----------------------------------------------------- */


/* NAME */

if (!preg_match(
    "/^[A-Za-z ]{2,50}$/",
    $name
)) {

    header(
        "Location: ../pages/contact.html?status=error"
    );

    exit();

}


/* EMAIL */

if (!filter_var(
    $email,
    FILTER_VALIDATE_EMAIL
)) {

    header(
        "Location: ../pages/contact.html?status=error"
    );

    exit();

}


/* SUBJECT */

if (
    strlen($subject) < 3 ||
    strlen($subject) > 100
) {

    header(
        "Location: ../pages/contact.html?status=error"
    );

    exit();

}


/* MESSAGE */

if (
    strlen($message) < 5 ||
    strlen($message) > 1000
) {

    header(
        "Location: ../pages/contact.html?status=error"
    );

    exit();

 }


/* -----------------------------------------------------
   SANITIZE INPUT
   ----------------------------------------------------- */

$name = htmlspecialchars(
    $name,
    ENT_QUOTES,
    "UTF-8"
);

$email = htmlspecialchars(
    $email,
    ENT_QUOTES,
    "UTF-8"
);

$subject = htmlspecialchars(
    $subject,
    ENT_QUOTES,
    "UTF-8"
);

$message = htmlspecialchars(
    $message,
    ENT_QUOTES,
    "UTF-8"
);


/* -----------------------------------------------------
   DATA FOLDER
   ----------------------------------------------------- */

$dataFolder =
    dirname(__DIR__) . "/Data";


if (!is_dir($dataFolder)) {

    mkdir(
        $dataFolder,
        0777,
        true
    );

}


/* -----------------------------------------------------
   CSV FILE
   ----------------------------------------------------- */

$file =
    $dataFolder . "/contacts.csv";


/* -----------------------------------------------------
   OPEN FILE
   ----------------------------------------------------- */

$fileExists =
    file_exists($file);


$handle =
    fopen($file, "a");


if ($handle === false) {

    header(
        "Location: ../pages/contact.html?status=error"
    );

    exit();

}


/* -----------------------------------------------------
   CSV HEADER
   ----------------------------------------------------- */

if (
    !$fileExists ||
    filesize($file) === 0
) {

    fputcsv(
        $handle,
        [
            "Name",
            "Email",
            "Subject",
            "Message",
            "Date"
        ]
    );

}


/* -----------------------------------------------------
   SAVE CONTACT RECORD
   ----------------------------------------------------- */

fputcsv(
    $handle,
    [

        $name,

        $email,

        $subject,

        $message,

        date("Y-m-d H:i:s")

    ]
);


/* -----------------------------------------------------
   CLOSE FILE
   ----------------------------------------------------- */

fclose($handle);


/* -----------------------------------------------------
   SUCCESS
   ----------------------------------------------------- */

header(
    "Location: ../pages/contact.html?status=success"
);

exit();

?>