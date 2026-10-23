<?php


header("Content-Type: application/json");

require_once "auth.php";
require_once "db.php";


// ===============================
// ADMIN CHECK
// ===============================

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {

    echo json_encode([
        "success" => false,
        "message" => "Unauthorized access."
    ]);

    exit();
}


// ===============================
// GET EVENTS
// ===============================

if ($_SERVER["REQUEST_METHOD"] === "GET") {

    // Get single event
    if (isset($_GET["action"]) && $_GET["action"] === "get") {

        $id = intval($_GET["id"]);

        $stmt = $conn->prepare(
            "SELECT * FROM events WHERE id = ?"
        );

        $stmt->bind_param("i", $id);

        $stmt->execute();

        $result = $stmt->get_result();

        $event = $result->fetch_assoc();

        if ($event) {

            echo json_encode([
                "success" => true,
                "event" => $event
            ]);

        } else {

            echo json_encode([
                "success" => false,
                "message" => "Event not found."
            ]);
        }

        exit();
    }


    // Get all events
    $result = $conn->query(
        "SELECT * FROM events ORDER BY event_date DESC"
    );

    $events = [];

    while ($row = $result->fetch_assoc()) {

        $events[] = $row;
    }

    echo json_encode([
        "success" => true,
        "events" => $events
    ]);

    exit();
}


// ===============================
// POST OPERATIONS
// ===============================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $action = $_POST["action"] ?? "";


// ===============================
// ADD EVENT
// ===============================

    if ($action === "add") {

        $title = trim($_POST["title"] ?? "");
        $description = trim($_POST["description"] ?? "");
        $event_date = $_POST["event_date"] ?? "";
        $event_time = $_POST["event_time"] ?? "";
        $venue = trim($_POST["venue"] ?? "");

        $posterName = "";


        // Poster upload
        if (
            isset($_FILES["poster"]) &&
            $_FILES["poster"]["error"] === 0
        ) {

            $fileName = $_FILES["poster"]["name"];
            $fileSize = $_FILES["poster"]["size"];
            $tmpName = $_FILES["poster"]["tmp_name"];

            $allowedTypes = [
                "jpg",
                "jpeg",
                "png",
                "webp"
            ];

            $extension = strtolower(
                pathinfo($fileName, PATHINFO_EXTENSION)
            );

            if (!in_array($extension, $allowedTypes)) {

                echo json_encode([
                    "success" => false,
                    "message" =>
                    "Only JPG, JPEG, PNG and WEBP files are allowed."
                ]);

                exit();
            }

            if ($fileSize > 2 * 1024 * 1024) {

                echo json_encode([
                    "success" => false,
                    "message" =>
                    "Poster size must be less than 2 MB."
                ]);

                exit();
            }

            $posterName =
                time() . "_" . uniqid() . "." . $extension;

            $uploadPath =
                "../images/events/" . $posterName;

            if (!move_uploaded_file($tmpName, $uploadPath)) {

                echo json_encode([
                    "success" => false,
                    "message" => "Failed to upload poster."
                ]);

                exit();
            }
        }


        $stmt = $conn->prepare(
            "INSERT INTO events
            (title, description, event_date, event_time, venue, poster)
            VALUES (?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "ssssss",
            $title,
            $description,
            $event_date,
            $event_time,
            $venue,
            $posterName
        );


        if ($stmt->execute()) {

            echo json_encode([
                "success" => true,
                "message" => "Event added successfully!"
            ]);

        } else {

            echo json_encode([
                "success" => false,
                "message" => "Failed to add event."
            ]);
        }

        exit();
    }


// ===============================
// UPDATE EVENT
// ===============================

    if ($action === "update") {

        $id = intval($_POST["id"]);

        $title = trim($_POST["title"] ?? "");
        $description = trim($_POST["description"] ?? "");
        $event_date = $_POST["event_date"] ?? "";
        $event_time = $_POST["event_time"] ?? "";
        $venue = trim($_POST["venue"] ?? "");


        // Get old poster
        $stmt = $conn->prepare(
            "SELECT poster FROM events WHERE id = ?"
        );

        $stmt->bind_param("i", $id);

        $stmt->execute();

        $result = $stmt->get_result();

        $oldEvent = $result->fetch_assoc();

        if (!$oldEvent) {

            echo json_encode([
                "success" => false,
                "message" => "Event not found."
            ]);

            exit();
        }


        $posterName = $oldEvent["poster"];


        // New poster?
        if (
            isset($_FILES["poster"]) &&
            $_FILES["poster"]["error"] === 0
        ) {

            $fileName = $_FILES["poster"]["name"];
            $fileSize = $_FILES["poster"]["size"];
            $tmpName = $_FILES["poster"]["tmp_name"];

            $allowedTypes = [
                "jpg",
                "jpeg",
                "png",
                "webp"
            ];

            $extension = strtolower(
                pathinfo($fileName, PATHINFO_EXTENSION)
            );


            if (!in_array($extension, $allowedTypes)) {

                echo json_encode([
                    "success" => false,
                    "message" =>
                    "Only JPG, JPEG, PNG and WEBP files are allowed."
                ]);

                exit();
            }


            if ($fileSize > 2 * 1024 * 1024) {

                echo json_encode([
                    "success" => false,
                    "message" =>
                    "Poster size must be less than 2 MB."
                ]);

                exit();
            }


            // Delete old poster
            if (!empty($oldEvent["poster"])) {

                $oldPath =
                    "../images/events/" .
                    $oldEvent["poster"];

                if (file_exists($oldPath)) {

                    unlink($oldPath);
                }
            }


            $posterName =
                time() . "_" .
                uniqid() .
                "." .
                $extension;


            $uploadPath =
                "../images/events/" .
                $posterName;


            if (!move_uploaded_file(
                $tmpName,
                $uploadPath
            )) {

                echo json_encode([
                    "success" => false,
                    "message" =>
                    "Failed to upload new poster."
                ]);

                exit();
            }
        }


        $stmt = $conn->prepare(
            "UPDATE events
             SET title = ?,
                 description = ?,
                 event_date = ?,
                 event_time = ?,
                 venue = ?,
                 poster = ?
             WHERE id = ?"
        );


        $stmt->bind_param(
            "ssssssi",
            $title,
            $description,
            $event_date,
            $event_time,
            $venue,
            $posterName,
            $id
        );


        if ($stmt->execute()) {

            echo json_encode([
                "success" => true,
                "message" =>
                "Event updated successfully!"
            ]);

        } else {

            echo json_encode([
                "success" => false,
                "message" =>
                "Failed to update event."
            ]);
        }

        exit();
    }


// ===============================
// DELETE EVENT
// ===============================

    if ($action === "delete") {

        $id = intval($_POST["id"]);


        // Get poster
        $stmt = $conn->prepare(
            "SELECT poster FROM events WHERE id = ?"
        );

        $stmt->bind_param("i", $id);

        $stmt->execute();

        $result = $stmt->get_result();

        $event = $result->fetch_assoc();


        if ($event) {

            if (!empty($event["poster"])) {

                $posterPath =
                    "../images/events/" .
                    $event["poster"];

                if (file_exists($posterPath)) {

                    unlink($posterPath);
                }
            }
        }


        $stmt = $conn->prepare(
            "DELETE FROM events WHERE id = ?"
        );

        $stmt->bind_param("i", $id);


        if ($stmt->execute()) {

            echo json_encode([
                "success" => true,
                "message" =>
                "Event deleted successfully!"
            ]);

        } else {

            echo json_encode([
                "success" => false,
                "message" =>
                "Unable to delete event."
            ]);
        }

        exit();
    }
}


echo json_encode([
    "success" => false,
    "message" => "Invalid API request."
]);