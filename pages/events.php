<?php
session_start();

require_once "../php/auth.php";
require_once "../php/db.php";

if ($_SESSION["role"] !== "admin") {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Event Management</title>

    <link rel="stylesheet"
          href="../css/admin.css">

    <style>

        .event-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
        }

        .event-form {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .event-form h2 {
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .btn {
            padding: 11px 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #ff4f9a;
            color: white;
        }

        .btn-edit {
            background: #4f7cff;
            color: white;
        }

        .btn-delete {
            background: #e74c3c;
            color: white;
        }

        .event-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
        }

        .event-table th,
        .event-table td {
            padding: 14px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        .event-table th {
            background: #ff4f9a;
            color: white;
        }

        .poster {
            width: 90px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }

    </style>

</head>

<body>

<div class="event-container">

    <h1>🎉 Event Management</h1>


    <!-- =========================
         ADD / EDIT FORM
    ========================== -->

    <div class="event-form">

        <h2 id="formTitle">➕ Add New Event</h2>

        <form id="eventForm"
              enctype="multipart/form-data">

            <input type="hidden"
                   id="eventId"
                   name="id">


            <!-- Event Title -->

            <div class="form-group">

                <label>Event Title</label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    required
                >

            </div>


            <!-- Description -->

            <div class="form-group">

                <label>Description</label>

                <textarea
                    id="description"
                    name="description"
                    required
                ></textarea>

            </div>


            <!-- Date -->

            <div class="form-group">

                <label>Event Date</label>

                <input
                    type="date"
                    id="event_date"
                    name="event_date"
                    required
                >

            </div>


            <!-- Time -->

            <div class="form-group">

                <label>Event Time</label>

                <input
                    type="time"
                    id="event_time"
                    name="event_time"
                    required
                >

            </div>


            <!-- Venue -->

            <div class="form-group">

                <label>Venue</label>

                <input
                    type="text"
                    id="venue"
                    name="venue"
                    required
                >

            </div>


            <!-- Poster -->

            <div class="form-group">

                <label>Event Poster</label>

                <input
                    type="file"
                    id="poster"
                    name="poster"
                    accept=".jpg,.jpeg,.png,.webp"
                >

                <small>
                    Allowed: JPG, JPEG, PNG, WEBP |
                    Maximum: 2 MB
                </small>

            </div>


            <!-- Submit -->

            <button
                type="submit"
                id="submitBtn"
                class="btn btn-primary">

                Add Event

            </button>


            <!-- Cancel -->

            <button
                type="button"
                id="cancelBtn"
                class="btn btn-delete"
                onclick="resetForm()"
                style="display:none;">

                Cancel

            </button>

        </form>

    </div>


    <!-- =========================
         EVENT LIST
    ========================== -->

    <h2>📋 All Events</h2>

    <table class="event-table">

        <thead>

            <tr>

                <th>ID</th>

                <th>Poster</th>

                <th>Title</th>

                <th>Date</th>

                <th>Time</th>

                <th>Venue</th>

                <th>Actions</th>

            </tr>

        </thead>


        <tbody id="eventTableBody">

            <tr>

                <td colspan="7"
                    style="text-align:center;">

                    Loading events...

                </td>

            </tr>

        </tbody>

    </table>

</div>


<!-- =========================
     FETCH API JAVASCRIPT
========================== -->

<script src="../js/events.js"></script>

</body>

</html>