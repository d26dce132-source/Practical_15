// ===============================
// LOAD EVENTS
// ===============================
async function loadEvents() {

    try {
        const response = await fetch("../php/events_api.php");

        const data = await response.json();

        if (!data.success) {
            alert(data.message);
            return;
        }

        const tbody = document.getElementById("eventTableBody");

        tbody.innerHTML = "";

        if (data.events.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" style="text-align:center;">
                        No events found.
                    </td>
                </tr>
            `;
            return;
        }

        data.events.forEach(event => {

            const poster = event.poster
                ? `<img src="../images/events/${event.poster}" class="poster">`
                : "No Poster";

            tbody.innerHTML += `
                <tr>
                    <td>${event.id}</td>

                    <td>${poster}</td>

                    <td>${escapeHTML(event.title)}</td>

                    <td>${event.event_date}</td>

                    <td>${event.event_time}</td>

                    <td>${escapeHTML(event.venue)}</td>

                    <td>
                        <button
                            class="btn btn-edit"
                            onclick="editEvent(${event.id})">
                            Edit
                        </button>

                        <button
                            class="btn btn-delete"
                            onclick="deleteEvent(${event.id})">
                            Delete
                        </button>
                    </td>
                </tr>
            `;
        });

    } catch (error) {
        console.error(error);
        alert("Unable to load events.");
    }
}


// ===============================
// ADD EVENT
// ===============================
async function addEvent(event) {

    event.preventDefault();

    const form = document.getElementById("eventForm");

    const formData = new FormData(form);

    formData.append("action", "add");

    try {

        const response = await fetch("../php/events_api.php", {
            method: "POST",
            body: formData
        });

        console.log("HTTP STATUS:", response.status);

        const text = await response.text();

        console.log("RAW API RESPONSE:", text);

        if (!response.ok) {
            throw new Error(
                "HTTP " + response.status + ": " + text
            );
        }

        let data;

        try {

            data = JSON.parse(text);

        } catch (jsonError) {

            console.error("JSON ERROR:", jsonError);
            console.error("SERVER RESPONSE:", text);

            alert(
                "PHP/API did not return valid JSON.\n\n" +
                text.substring(0, 500)
            );

            return;
        }

        if (data.success) {

            alert(data.message);

            form.reset();

            loadEvents();

        } else {

            alert(data.message);
        }

    } catch (error) {

        console.error("FETCH ERROR:", error);

        alert(
            "Request failed:\n\n" +
            error.message
        );
    }
}

// ===============================
// EDIT EVENT
// ===============================
// ===============================
// EDIT EVENT
// ===============================
async function editEvent(id) {

    try {

        const response = await fetch(
            `../php/events_api.php?action=get&id=${id}`
        );

        const data = await response.json();

        if (!data.success) {

            alert(data.message);
            return;
        }

        const event = data.event;

        document.getElementById("eventId").value =
            event.id;

        document.getElementById("title").value =
            event.title;

        document.getElementById("description").value =
            event.description;

        document.getElementById("event_date").value =
            event.event_date;

        document.getElementById("event_time").value =
            event.event_time;

        document.getElementById("venue").value =
            event.venue;


        // IMPORTANT:
        // Change form action from ADD to UPDATE
        document.getElementById("eventForm").onsubmit =
            updateEvent;


        document.getElementById("formTitle").innerText =
            "✏️ Update Event";

        document.getElementById("submitBtn").innerText =
            "Update Event";

        document.getElementById("cancelBtn").style.display =
            "inline-block";


        document.getElementById("eventForm").scrollIntoView({
            behavior: "smooth"
        });

    } catch (error) {

        console.error(error);

        alert("Unable to get event.");
    }
}

// ===============================
// UPDATE EVENT
// ===============================
async function updateEvent(event) {

    event.preventDefault();

    const form = document.getElementById("eventForm");

    const formData = new FormData(form);

    formData.append("action", "update");

    try {

        const response = await fetch("../php/events_api.php", {
            method: "POST",
            body: formData
        });

        const data = await response.json();

        if (data.success) {

            alert(data.message);

            resetForm();

            loadEvents();

        } else {

            alert(data.message);
        }

    } catch (error) {

        console.error(error);
        alert("Unable to update event.");
    }
}


// ===============================
// DELETE EVENT
// ===============================
async function deleteEvent(id) {

    if (!confirm("Are you sure you want to delete this event?")) {
        return;
    }

    const formData = new FormData();

    formData.append("action", "delete");
    formData.append("id", id);

    try {

        const response = await fetch("../php/events_api.php", {
            method: "POST",
            body: formData
        });

        const data = await response.json();

        if (data.success) {

            alert(data.message);

            loadEvents();

        } else {

            alert(data.message);
        }

    } catch (error) {

        console.error(error);
        alert("Unable to delete event.");
    }
}


// ===============================
// RESET FORM
// ===============================
function resetForm() {

    document.getElementById("eventForm").reset();

    document.getElementById("eventId").value = "";

    document.getElementById("eventForm").onsubmit =
    updateEvent;

    document.getElementById("formTitle").innerText =
        "➕ Add New Event";

    document.getElementById("submitBtn").innerText =
        "Add Event";

    document.getElementById("cancelBtn").style.display =
        "none";

    document.getElementById("eventForm").onsubmit =
        addEvent;
}


// ===============================
// SECURITY
// ===============================
function escapeHTML(value) {

    const div = document.createElement("div");

    div.textContent = value;

    return div.innerHTML;
}


// ===============================
// LOAD WHEN PAGE OPENS
// ===============================
document.addEventListener("DOMContentLoaded", function () {

    loadEvents();

    document.getElementById("eventForm").onsubmit =
        addEvent;

});