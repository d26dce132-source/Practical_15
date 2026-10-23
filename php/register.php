<?php

require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form data
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $mobile = trim($_POST["mobile"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $course = $_POST["course"];
    $year = $_POST["year"];
    $gender = $_POST["gender"];


    // =========================
    // BACKEND VALIDATION
    // =========================

    if (
        empty($name) ||
        empty($email) ||
        empty($mobile) ||
        empty($password) ||
        empty($confirm_password) ||
        empty($course) ||
        empty($year) ||
        empty($gender)
    ) {

        header("Location: ../pages/register.html?status=error");
        exit();

    }


    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        header("Location: ../pages/register.html?status=error");
        exit();

    }


    // Mobile validation
    if (!preg_match("/^[0-9]{10}$/", $mobile)) {

        header("Location: ../pages/register.html?status=error");
        exit();

    }


    // Password length
    if (strlen($password) < 6) {

        header("Location: ../pages/register.html?status=error");
        exit();

    }


    // Confirm password
    if ($password !== $confirm_password) {

        header("Location: ../pages/register.html?status=password");
        exit();

    }


    // =========================
// CHECK DUPLICATE EMAIL
// =========================

$check = $conn->prepare(
    "SELECT id FROM users WHERE email = ?"
);

$check->bind_param("s", $email);

$check->execute();

$result = $check->get_result();

if ($result->num_rows > 0) {

    header("Location: ../pages/register.html?status=duplicate");
    exit();
}

    // =========================
    // PASSWORD HASHING
    // =========================

    $hashed_password =
        password_hash($password, PASSWORD_DEFAULT);


    // =========================
    // INSERT DATA
    // =========================

    $stmt = $conn->prepare(
        "INSERT INTO users
        (name, email, mobile, password, course, year, gender)
        VALUES (?, ?, ?, ?, ?, ?, ?)"
    );


    $stmt->bind_param(
        "sssssss",
        $name,
        $email,
        $mobile,
        $hashed_password,
        $course,
        $year,
        $gender
    );


    if ($stmt->execute()) {

        header("Location: ../pages/register.html?status=success");
        exit();

    } else {

        header("Location: ../pages/register.html?status=error");
        exit();

    }


    $stmt->close();
    $check->close();
    $conn->close();
}

?>