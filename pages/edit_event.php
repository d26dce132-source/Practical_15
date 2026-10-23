<?php

require_once "../php/auth.php";
require_once "../php/db.php";

if ($_SESSION["role"] !== "admin") {
    header("Location: dashboard.php");
    exit();
}

$id = intval($_GET["id"] ?? 0);

$sql = "SELECT * FROM events WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$event = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$event) {
    die("Event not found.");
}

$message = "";
$message_type = "";


/* UPDATE */

if (isset($_POST["update_event"])) {

    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $event_date = $_POST["event_date"];
    $event_time = $_POST["event_time"];
    $venue = trim($_POST["venue"]);

    $poster_name = $event["poster"];


    /* New poster */

    if (isset($_FILES["poster"]) &&
        $_FILES["poster"]["error"] == 0) {

        $file_name = $_FILES["poster"]["name"];
        $file_size = $_FILES["poster"]["size"];
        $tmp_name = $_FILES["poster"]["tmp_name"];

        $allowed_types = [
            "jpg",
            "jpeg",
            "png",
            "webp"
        ];

        $extension = strtolower(
            pathinfo($file_name, PATHINFO_EXTENSION)
        );

        if (!in_array($extension, $allowed_types)) {

            $message =
                "Only JPG, JPEG, PNG and WEBP files are allowed.";

            $message_type = "error";

        } elseif ($file_size > 2 * 1024 * 1024) {

            $message =
                "Poster size must be less than 2 MB.";

            $message_type = "error";

        } else {

            /* Delete old poster */

            if (!empty($event["poster"])) {

                $old_path =
                    "../images/events/" .
                    $event["poster"];

                if (file_exists($old_path)) {
                    unlink($old_path);
                }
            }


            $poster_name =
                time() . "_" .
                uniqid() . "." .
                $extension;

            move_uploaded_file(
                $tmp_name,
                "../images/events/" . $poster_name
            );
        }
    }


    if ($message_type != "error") {

        $sql = "UPDATE events
                SET title = ?,
                    description = ?,
                    event_date = ?,
                    event_time = ?,
                    venue = ?,
                    poster = ?
                WHERE id = ?";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "ssssssi",
            $title,
            $description,
            $event_date,
            $event_time,
            $venue,
            $poster_name,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {

            header("Location: events.php");
            exit();

        } else {

            $message = "Failed to update event.";
            $message_type = "error";
        }
    }
}

?>

<!DOCTYPE html>

<html>

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Edit Event</title>

    <link rel="stylesheet"
          href="../css/admin.css">

    <style>

        .edit-container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        textarea {
            min-height: 120px;
        }

        .poster-preview {
            width: 150px;
            margin: 10px 0;
            border-radius: 10px;
        }

        button,
        .back-btn {
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
        }

        button {
            background: #8b5cf6;
            color: white;
        }

        .back-btn {
            background: #eee;
            color: #333;
        }

    </style>

</head>

<body>

<div class="edit-container">

    <h1>✏️ Edit Event</h1>

    <?php if ($message != "") { ?>

        <p>
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php } ?>


    <form method="POST"
          enctype="multipart/form-data">


        <div class="form-group">

            <label>Event Title</label>

            <input
                type="text"
                name="title"
                value="<?php
                    echo htmlspecialchars($event["title"]);
                ?>"
                required>

        </div>


        <div class="form-group">

            <label>Description</label>

            <textarea
                name="description"
                required><?php
                    echo htmlspecialchars(
                        $event["description"]
                    );
                ?></textarea>

        </div>


        <div class="form-group">

            <label>Event Date</label>

            <input
                type="date"
                name="event_date"
                value="<?php
                    echo $event["event_date"];
                ?>"
                required>

        </div>


        <div class="form-group">

            <label>Event Time</label>

            <input
                type="time"
                name="event_time"
                value="<?php
                    echo $event["event_time"];
                ?>"
                required>

        </div>


        <div class="form-group">

            <label>Venue</label>

            <input
                type="text"
                name="venue"
                value="<?php
                    echo htmlspecialchars(
                        $event["venue"]
                    );
                ?>"
                required>

        </div>


        <div class="form-group">

            <label>Current Poster</label>

            <?php if (!empty($event["poster"])) { ?>

                <br>

                <img
                    src="../images/events/<?php
                        echo htmlspecialchars(
                            $event["poster"]
                        );
                    ?>"
                    class="poster-preview">

            <?php } else { ?>

                <p>No poster uploaded.</p>

            <?php } ?>

        </div>


        <div class="form-group">

            <label>Upload New Poster</label>

            <input
                type="file"
                name="poster"
                accept=".jpg,.jpeg,.png,.webp">

            <small>
                JPG, JPEG, PNG, WEBP — Maximum 2 MB
            </small>

        </div>


        <button
            type="submit"
            name="update_event">

            <i class="fa-solid fa-save"></i>
            Update Event

        </button>


        <a
            href="events.php"
            class="back-btn">

            Cancel

        </a>

    </form>

</div>

</body>

</html>