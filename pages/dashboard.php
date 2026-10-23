
<?php

session_start();

require_once "../php/auth.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit();
}

?>

<?php if ($_SESSION["role"] === "admin"): ?>

    <a href="admin.php">⚙️ Admin Dashboard</a>
    <a href="events.php">🎉 Manage Events</a>

<?php endif; ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard | StudentHub</title>

<link rel="stylesheet" href="../css/style.css">

</head>
<body>

<header>

    <div class="logo">
        <img src="../images/logo.png" alt="Logo">
        <h2>StudentHub</h2>
    </div>

    <nav>
        <a href="../index.html">Home</a>
        <a href="dashboard.html">Dashboard</a>
        <a href="profile.html">Profile</a>
        <a href="courses.html">Courses</a>
        <a href="attendance.html">Attendance</a>
        <a href="assignment.html">Assignments</a>
        <a href="result.html">Results</a>
        <a href="contact.html">Contact</a>
        <a href="login.html">Login</a>
        <a href="../php/logout.php">Logout</a>
    </nav>

</header>

<section class="about">

<h2>Student Dashboard</h2>

<div class="cards">

<div class="card">
<h3>Courses</h3>
<p>6 Enrolled Courses</p>
</div>

<div class="card">
<h3>Attendance</h3>
<p>92%</p>
</div>

<div class="card">
<h3>Assignments</h3>
<p>2 Pending</p>
</div>

<div class="card">
<h3>Results</h3>
<p>Semester 6</p>
</div>

</div>

</section>

<footer>
<p>© 2026 StudentHub Portal</p>
</footer>

</body>
</html>