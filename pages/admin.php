<?php



require_once "../php/auth.php";

require_once "../php/db.php";



if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {

    header("Location: dashboard.php");

    exit();

}

// ===============================

// DASHBOARD STATISTICS

// ===============================



// Total Users

$total_users = 0;



$result = mysqli_query(

    $conn,

    "SELECT COUNT(*) AS total FROM users"

);



if ($result) {

    $row = mysqli_fetch_assoc($result);

    $total_users = $row["total"];

}





// Total Students

$total_students = 0;



$result = mysqli_query(

    $conn,

    "SELECT COUNT(*) AS total

     FROM students"

);



if ($result) {

    $row = mysqli_fetch_assoc($result);

    $total_students = $row["total"];

}





// Total Events

$total_events = 0;



$result = mysqli_query(

    $conn,

    "SELECT COUNT(*) AS total

     FROM events"

);



if ($result) {

    $row = mysqli_fetch_assoc($result);

    $total_events = $row["total"];

}





// Latest Registrations

$latest_users = mysqli_query(

    $conn,

    "SELECT *

     FROM users

     ORDER BY id DESC

     LIMIT 5"

);





// Recent Audit Logs

$audit_logs = mysqli_query(

    $conn,

    "SELECT *

     FROM audit_logs

     ORDER BY created_at DESC

     LIMIT 10"

);







$message = "";

$message_type = "";





if (isset($_POST["add_student"])) {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $course = trim($_POST["course"] ?? "");

    $sql = "INSERT INTO students (name, email, course) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {

        mysqli_stmt_bind_param($stmt, "sss", $name, $email, $course);

        if (mysqli_stmt_execute($stmt)) {

            $message = "Student added successfully!";
            $message_type = "success";

            // AUDIT LOG
            $admin_id = isset($_SESSION["user_id"]) ? (int)$_SESSION["user_id"] : 0;

            if ($admin_id > 0) {
                $log = mysqli_prepare(
                    $conn,
                    "INSERT INTO audit_logs (user_id, action, description) VALUES (?, ?, ?)"
                );

                if ($log) {
                    $action = "ADD_STUDENT";
                    $description = "Added student: " . $name;

                    mysqli_stmt_bind_param($log, "iss", $admin_id, $action, $description);

                    if (!mysqli_stmt_execute($log)) {
                        $message .= " Audit error: " . mysqli_stmt_error($log);
                    }

                    mysqli_stmt_close($log);
                } else {
                    $message .= " Audit prepare error: " . mysqli_error($conn);
                }
            }

        } else {
            $message = "Failed to add student: " . mysqli_stmt_error($stmt);
            $message_type = "error";
        }

        mysqli_stmt_close($stmt);

    } else {
        $message = "Failed to prepare student insert: " . mysqli_error($conn);
        $message_type = "error";
    }
}

/* =========================

   DELETE STUDENT

========================= */



if (isset($_POST["delete_student"])) {



    $id = $_POST["id"];



    $sql = "DELETE FROM students WHERE id = ?";



    $stmt = mysqli_prepare($conn, $sql);



    if ($stmt) {



        mysqli_stmt_bind_param($stmt, "i", $id);



        if (mysqli_stmt_execute($stmt)) {



            $message = "Student deleted successfully!";

            $message_type = "success";



            $log = mysqli_prepare(

    $conn,

    "INSERT INTO audit_logs

    (user_id, action, description)

    VALUES (?, ?, ?)"

);



$action = "DELETE_STUDENT";

$description = "Deleted student ID: " . $id;



mysqli_stmt_bind_param(

    $log,

    "iss",

    $_SESSION["user_id"],

    $action,

    $description

);



mysqli_stmt_execute($log);

mysqli_stmt_close($log);



        } else {



            $message = "Failed to delete student!";

            $message_type = "error";



        }



        mysqli_stmt_close($stmt);

    }

}





/* =========================

   UPDATE STUDENT

========================= */



if (isset($_POST["update_student"])) {



    $id = $_POST["id"];

    $name = trim($_POST["name"]);

    $email = trim($_POST["email"]);

    $course = trim($_POST["course"]);



    $sql = "UPDATE students

            SET name = ?, email = ?, course = ?

            WHERE id = ?";



    $stmt = mysqli_prepare($conn, $sql);



    if ($stmt) {



        mysqli_stmt_bind_param(

            $stmt,

            "sssi",

            $name,

            $email,

            $course,

            $id

        );



       if (mysqli_stmt_execute($stmt)) {



    $message = "Student updated successfully!";

    $message_type = "success";



    // AUDIT LOG

    $log = mysqli_prepare(

        $conn,

        "INSERT INTO audit_logs (user_id, action, description)

         VALUES (?, ?, ?)"

    );



    if (!$log) {

        die("Audit prepare error: " . mysqli_error($conn));

    }



    $user_id = $_SESSION["user_id"];

    $action = "UPDATE_STUDENT";

    $description = "Updated student: " . $name;



    mysqli_stmt_bind_param(

        $log,

        "iss",

        $user_id,

        $action,

        $description

    );



    if (!mysqli_stmt_execute($log)) {

        die("Audit insert error: " . mysqli_stmt_error($log));

    }



    mysqli_stmt_close($log);

} else {



            $message = "Failed to update student!";

            $message_type = "error";



        }



        mysqli_stmt_close($stmt);

    }

}





/* =========================

   SEARCH + FILTER

========================= */



$search = $_GET["search"] ?? "";

$course_filter = $_GET["course"] ?? "";



$sql = "SELECT * FROM students WHERE 1=1";



if ($search != "") {



    $sql .= " AND (name LIKE ? OR email LIKE ?)";

}



if ($course_filter != "") {



    $sql .= " AND course = ?";

}



$sql .= " ORDER BY id DESC";





$stmt = mysqli_prepare($conn, $sql);



if ($stmt) {



    if ($search != "" && $course_filter != "") {



        $search_value = "%" . $search . "%";



        mysqli_stmt_bind_param(

            $stmt,

            "sss",

            $search_value,

            $search_value,

            $course_filter

        );



    } elseif ($search != "") {



        $search_value = "%" . $search . "%";



        mysqli_stmt_bind_param(

            $stmt,

            "ss",

            $search_value,

            $search_value

        );



    } elseif ($course_filter != "") {



        mysqli_stmt_bind_param(

            $stmt,

            "s",

            $course_filter

        );

    }



    mysqli_stmt_execute($stmt);



    $result = mysqli_stmt_get_result($stmt);



} else {



    $result = false;

}





/* =========================

   TOTAL STUDENTS

========================= */



$count_query = mysqli_query(

    $conn,

    "SELECT COUNT(*) AS total FROM students"

);



$count_data = mysqli_fetch_assoc($count_query);



$total_students = $count_data["total"];





/* =========================

   TOTAL COURSES

========================= */



$course_query = mysqli_query(

    $conn,

    "SELECT COUNT(DISTINCT course) AS total FROM students"

);



$course_data = mysqli_fetch_assoc($course_query);



$total_courses = $course_data["total"];



?>



<!DOCTYPE html>



<html>



<head>



    <meta charset="UTF-8">



    <meta name="viewport"

          content="width=device-width, initial-scale=1.0">



    <title>StudentHub Admin</title>



    <link

        rel="stylesheet"

        href="../css/admin.css"

    >



    <!-- Icons -->



    <link

        rel="stylesheet"

        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"

    >



</head>





<body>





<!-- =========================

     SIDEBAR

========================= -->



<div class="sidebar">



    <div class="logo">



        <div class="logo-icon">

            <i class="fa-solid fa-graduation-cap"></i>

        </div>



        <div>

            <h2>StudentHub</h2>

            <span>Admin Panel</span>

        </div>



    </div>





    <div class="menu-title">

        MAIN MENU

    </div>





    <a href="#" class="menu-item active">



        <i class="fa-solid fa-chart-pie"></i>



        <span>Dashboard</span>



    </a>





    <a href="#students" class="menu-item">



        <i class="fa-solid fa-user-graduate"></i>



        <span>Students</span>



    </a>





    <a href="#courses" class="menu-item">



        <i class="fa-solid fa-book"></i>



        <span>Courses</span>



    </a>



    <a href="events.php" class="menu-item">

    <i class="fa-solid fa-calendar-days"></i>

    <span>Events</span>

</a>





    <a href="#" class="menu-item">



        <i class="fa-solid fa-calendar-check"></i>



        <span>Attendance</span>



    </a>





    <a href="#" class="menu-item">



        <i class="fa-solid fa-chart-column"></i>



        <span>Results</span>



    </a>





    <div class="menu-title">

        SYSTEM

    </div>





    <a href="#" class="menu-item">



        <i class="fa-solid fa-gear"></i>



        <span>Settings</span>



    </a>





    <div class="sidebar-bottom">



        <a

            href="../php/logout.php"

            class="logout-menu"

        >



            <i class="fa-solid fa-right-from-bracket"></i>



            Logout



        </a>



    </div>



</div>







<!-- =========================

     MAIN

========================= -->



<div class="main">





    <!-- TOP BAR -->



    <div class="topbar">



        <div>



            <h1>Dashboard</h1>



            <p>

                Welcome back, Admin 👋

            </p>



        </div>





        <div class="profile">



            <div class="notification">



                <i class="fa-regular fa-bell"></i>



            </div>





            <div class="avatar">



                A



            </div>





            <div class="profile-info">



                <strong>

                    <?php

                    echo htmlspecialchars($_SESSION["name"]);

                    ?>

                </strong>



                <span>

                    Administrator

                </span>



            </div>



        </div>



    </div>







    <!-- =========================

         MESSAGE

    ========================= -->



    <?php if ($message != "") { ?>



        <div class="message

            <?php

            echo ($message_type == "success")

                ? "success"

                : "error";

            ?>">



            <i class="fa-solid

                <?php

                echo ($message_type == "success")

                    ? "fa-circle-check"

                    : "fa-circle-xmark";

                ?>">

            </i>



            <?php echo htmlspecialchars($message); ?>



        </div>



    <?php } ?>







    <!-- =========================

         STATISTICS

    ========================= -->



    <div class="stat-card">



    <div class="stat-icon">

        👥

    </div>



    <div class="stat-info">



        <span>Total Users</span>



        <h2>

            <?= $total_users ?>

        </h2>



        <small>

            Registered users

        </small>



    </div>



</div>





<div class="stat-card">



    <div class="stat-icon">

        🎉

    </div>



    <div class="stat-info">



        <span>Total Events</span>



        <h2>

            <?= $total_events ?>

        </h2>



        <small>

            Available events

        </small>



    </div>



</div>



<div class="section-card">



    <h2>📝 Latest Registrations</h2>



    <p>Recently registered users</p>



    <div class="table-container">



        <table>



            <thead>



                <tr>

                    <th>ID</th>

                    <th>Name</th>

                    <th>Email</th>

                    <th>Role</th>

                </tr>



            </thead>



            <tbody>



                <?php if ($latest_users && mysqli_num_rows($latest_users) > 0): ?>



                    <?php while ($user = mysqli_fetch_assoc($latest_users)): ?>



                        <tr>



                            <td>

                                <?= htmlspecialchars($user["id"]) ?>

                            </td>



                            <td>

                                <?= htmlspecialchars($user["name"] ?? "N/A") ?>

                            </td>



                            <td>

                                <?= htmlspecialchars($user["email"] ?? "N/A") ?>

                            </td>



                            <td>

                                <?= htmlspecialchars($user["role"] ?? "N/A") ?>

                            </td>



                        </tr>



                    <?php endwhile; ?>



                <?php else: ?>



                    <tr>



                        <td colspan="4">

                            No registrations found.

                        </td>



                    </tr>



                <?php endif; ?>



            </tbody>



        </table>



    </div>



</div>



<div class="section-card">



    <h2>📋 Recent Activity</h2>



    <p>Latest admin activities</p>



    <div class="table-container">



        <table>



            <thead>



                <tr>

                    <th>User ID</th>

                    <th>Action</th>

                    <th>Description</th>

                    <th>Date & Time</th>

                </tr>



            </thead>



            <tbody>



                <?php if ($audit_logs && mysqli_num_rows($audit_logs) > 0): ?>



                    <?php while ($log = mysqli_fetch_assoc($audit_logs)): ?>



                        <tr>



                            <td>

                                <?= htmlspecialchars($log["user_id"]) ?>

                            </td>



                            <td>

                                <?= htmlspecialchars($log["action"]) ?>

                            </td>



                            <td>

                                <?= htmlspecialchars($log["description"]) ?>

                            </td>



                            <td>

                                <?= htmlspecialchars($log["created_at"]) ?>

                            </td>



                        </tr>



                    <?php endwhile; ?>



                <?php else: ?>



                    <tr>



                        <td colspan="4">

                            No activity recorded yet.

                        </td>



                    </tr>



                <?php endif; ?>



            </tbody>



        </table>



    </div>



</div>





    <!-- =========================

         ADD STUDENT

    ========================= -->



    <div class="section-card" id="courses">



        <div class="section-header">



            <div>



                <h2>Add New Student</h2>



                <p>

                    Register a new student in the system

                </p>



            </div>



            <div class="section-icon">



                <i class="fa-solid fa-user-plus"></i>



            </div>



        </div>





        <form method="POST">



            <div class="form-grid">





                <div class="form-group">



                    <label>

                        Student Name

                    </label>



                    <div class="input-box">



                        <i class="fa-solid fa-user"></i>



                        <input

                            type="text"

                            name="name"

                            placeholder="Enter student name"

                            required

                        >



                    </div>



                </div>







                <div class="form-group">



                    <label>

                        Email Address

                    </label>



                    <div class="input-box">



                        <i class="fa-solid fa-envelope"></i>



                        <input

                            type="email"

                            name="email"

                            placeholder="Enter email address"

                            required

                        >



                    </div>



                </div>







                <div class="form-group">



                    <label>

                        Course

                    </label>



                    <div class="input-box">



                        <i class="fa-solid fa-book"></i>



                        <select

                            name="course"

                            required

                        >



                            <option value="">

                                Select Course

                            </option>



                            <option value="Computer Engineering">

                                Computer Engineering

                            </option>



                            <option value="IT">

                                Information Technology

                            </option>



                            <option value="Data Science">

                                Data Science

                            </option>



                            <option value="AI">

                                Artificial Intelligence

                            </option>



                        </select>



                    </div>



                </div>





            </div>





            <button

                type="submit"

                name="add_student"

                class="primary-btn"

            >



                <i class="fa-solid fa-plus"></i>



                Add Student



            </button>



        </form>



    </div>







    <!-- =========================

         SEARCH

    ========================= -->



    <div class="section-card">



        <div class="section-header">



            <div>



                <h2>Student Directory</h2>



                <p>

                    Search and manage student records

                </p>



            </div>



            <div class="section-icon">



                <i class="fa-solid fa-users"></i>



            </div>



        </div>





        <form

            method="GET"

            class="search-form"

        >



            <div class="search-input">



                <i class="fa-solid fa-magnifying-glass"></i>



                <input

                    type="text"

                    name="search"

                    placeholder="Search by name or email..."

                    value="<?php

                    echo htmlspecialchars($search);

                    ?>"

                >



            </div>





            <select name="course">



                <option value="">

                    All Courses

                </option>



                <option

                    value="Computer Engineering"

                    <?php

                    if ($course_filter ==

                        "Computer Engineering") {

                        echo "selected";

                    }

                    ?>

                >

                    Computer Engineering

                </option>



                <option

                    value="IT"

                    <?php

                    if ($course_filter == "IT") {

                        echo "selected";

                    }

                    ?>

                >

                    Information Technology

                </option>



                <option

                    value="Data Science"

                    <?php

                    if ($course_filter == "Data Science") {

                        echo "selected";

                    }

                    ?>

                >

                    Data Science

                </option>



                <option

                    value="AI"

                    <?php

                    if ($course_filter == "AI") {

                        echo "selected";

                    }

                    ?>

                >

                    Artificial Intelligence

                </option>



            </select>





            <button

                type="submit"

                class="search-btn"

            >



                Search



            </button>





            <a

                href="admin.php"

                class="clear-btn"

            >



                Clear



            </a>



        </form>







        <!-- =========================

             TABLE

        ========================= -->



        <div class="table-container">



            <table>



                <thead>



                    <tr>



                        <th>ID</th>



                        <th>Student</th>



                        <th>Email</th>



                        <th>Course</th>



                        <th>Status</th>



                        <th>Actions</th>



                    </tr>



                </thead>





                <tbody>



                <?php



                if (

                    $result &&

                    mysqli_num_rows($result) > 0

                ) {



                    while (

                        $student =

                        mysqli_fetch_assoc($result)

                    ) {



                ?>



                    <tr>



                        <td>



                            <span class="student-id">



                                #<?php

                                echo $student["id"];

                                ?>



                            </span>



                        </td>





                        <td>



                            <div class="student-name">



                                <div class="student-avatar">



                                    <?php

                                    echo strtoupper(

                                        substr(

                                            $student["name"],

                                            0,

                                            1

                                        )

                                    );

                                    ?>



                                </div>



                                <strong>



                                    <?php

                                    echo htmlspecialchars(

                                        $student["name"]

                                    );

                                    ?>



                                </strong>



                            </div>



                        </td>





                        <td>



                            <?php

                            echo htmlspecialchars(

                                $student["email"]

                            );

                            ?>



                        </td>





                        <td>



                            <span class="course-badge">



                                <?php

                                echo htmlspecialchars(

                                    $student["course"]

                                );

                                ?>



                            </span>



                        </td>





                        <td>



                            <span class="status">



                                <span></span>



                                Active



                            </span>



                        </td>





                        <td>



                            <div class="actions">





                                <!-- EDIT -->



                                <details>



                                    <summary class="edit-btn">



                                        <i class="fa-solid fa-pen"></i>



                                    </summary>





                                    <div class="edit-panel">



                                        <form method="POST">



                                            <input

                                                type="hidden"

                                                name="id"

                                                value="<?php

                                                echo $student["id"];

                                                ?>"

                                            >





                                            <input

                                                type="text"

                                                name="name"

                                                value="<?php

                                                echo htmlspecialchars(

                                                    $student["name"]

                                                );

                                                ?>"

                                                required

                                            >





                                            <input

                                                type="email"

                                                name="email"

                                                value="<?php

                                                echo htmlspecialchars(

                                                    $student["email"]

                                                );

                                                ?>"

                                                required

                                            >





                                            <select name="course">



                                                <option>

                                                    Computer Engineering

                                                </option>



                                                <option>

                                                    IT

                                                </option>



                                                <option>

                                                    Data Science

                                                </option>



                                                <option>

                                                    AI

                                                </option>



                                            </select>





                                            <button

                                                type="submit"

                                                name="update_student"

                                                class="save-btn"

                                            >



                                                Save



                                            </button>



                                        </form>



                                    </div>



                                </details>







                                <!-- DELETE -->



                                <form

                                    method="POST"

                                    onsubmit="return confirm('Delete this student?');"

                                >



                                    <input

                                        type="hidden"

                                        name="id"

                                        value="<?php

                                        echo $student["id"];

                                        ?>"

                                    >



                                    <button

                                        type="submit"

                                        name="delete_student"

                                        class="delete-btn"

                                    >



                                        <i class="fa-solid fa-trash"></i>



                                    </button>



                                </form>



                            </div>



                        </td>



                    </tr>



                <?php



                    }



                } else {



                ?>



                    <tr>



                        <td

                            colspan="6"

                            class="empty"

                        >



                            <i class="fa-solid fa-folder-open"></i>



                            <br>



                            No student records found.



                        </td>



                    </tr>



                <?php



                }



                ?>



                </tbody>



            </table>



        </div>



    </div>







    <!-- FOOTER -->



    <footer>



        © 2026 StudentHub Portal

        <span>•</span>

        Student Management System



    </footer>





</div>





</body>



</html>