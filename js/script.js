function toggleMenu() {

    const nav = document.getElementById("mainNav");

    if (nav.style.display === "flex") {
        nav.style.display = "none";

    }

    else {
        nav.style.display = "flex";
    }

}


function toggleTheme() {

    const button =
        document.getElementById("themeButton");

    document.body.classList.toggle("dark-mode");


    if (document.body.classList.contains("dark-mode")) {
        button.innerHTML = "☀️ Light Mode";

    }

    else {
        button.innerHTML = "🌙 Dark Mode";
    }

}

function showNotification() {

    const notification =
        document.getElementById("notification");

    notification.style.display = "block";

    setTimeout(function () {

        notification.style.display = "none";

    }, 3000);

}

function openModal() {

    const modal =document.getElementById("myModal");
    modal.style.display = "block";

}

function closeModal() {

    const modal =
        document.getElementById("myModal");


    modal.style.display = "none";

}

window.onclick = function(event) {

    const modal =
        document.getElementById("myModal");

    if (event.target === modal) {

        modal.style.display = "none";

    }

};

let images = [

    "images/student.png",
    "images/college-bg.jpg",
    "images/graduation.png"

];


let currentImage = 0;

function nextImage() {

    currentImage++;

    if (currentImage >= images.length) {
        currentImage = 0;

    }

    document.getElementById("sliderImage").src =images[currentImage];

}

function previousImage() {
    currentImage--;

    if (currentImage < 0) {
        currentImage = images.length - 1;

    }


    document.getElementById("sliderImage").src =
        images[currentImage];

}

function toggleFAQ(id) {

    const answer =
        document.getElementById(id);


    if (answer.style.display === "block") {
        answer.style.display = "none";

    }

    else {
        answer.style.display = "block";

    }

}
// =====================================================
// PRACTICAL 6
// FETCH API + JSON
// SEARCH + FILTER + SORT + PAGINATION
// =====================================================

let events = [];
let students = [];
let notices = [];
let faqs = [];

let eventPage = 1;
let studentPage = 1;

const itemsPerPage = 3;


// =====================================================
// FETCH EVENTS
// =====================================================

fetch("data/events.json")

    .then(response => response.json())

    .then(data => {

        events = data;

        renderEvents();

    })

    .catch(error => {

        console.error("Error loading events:", error);

    });


// =====================================================
// FETCH STUDENTS
// =====================================================

fetch("data/students.json")

    .then(response => response.json())

    .then(data => {

        students = data;

        renderStudents();

    })

    .catch(error => {

        console.error("Error loading students:", error);

    });


// =====================================================
// FETCH NOTICES
// =====================================================

fetch("data/notices.json")

    .then(response => response.json())

    .then(data => {

        notices = data;

        renderNotices();

    })

    .catch(error => {

        console.error("Error loading notices:", error);

    });


// =====================================================
// FETCH FAQs
// =====================================================

fetch("data/faqs.json")

    .then(response => response.json())

    .then(data => {

        faqs = data;

        renderFAQs();

    })

    .catch(error => {

        console.error("Error loading FAQs:", error);

    });


// =====================================================
// EVENTS
// =====================================================

function renderEvents() {

    const search =
        document.getElementById("eventSearch")
        .value
        .toLowerCase();

    const filter =
        document.getElementById("eventFilter")
        .value;

    const sort =
        document.getElementById("eventSort")
        .value;


    let filteredEvents = events.filter(event => {

        const matchesSearch =
            event.title
            .toLowerCase()
            .includes(search);

        const matchesFilter =
            filter === "All" ||
            event.category === filter;

        return matchesSearch && matchesFilter;

    });


    // SORT

    if (sort === "dateAsc") {

        filteredEvents.sort(
            (a, b) =>
                new Date(a.date) -
                new Date(b.date)
        );

    }


    if (sort === "dateDesc") {

        filteredEvents.sort(
            (a, b) =>
                new Date(b.date) -
                new Date(a.date)
        );

    }


    if (sort === "titleAsc") {

        filteredEvents.sort(
            (a, b) =>
                a.title.localeCompare(b.title)
        );

    }


    if (sort === "titleDesc") {

        filteredEvents.sort(
            (a, b) =>
                b.title.localeCompare(a.title)
        );

    }


    // PAGINATION

    const totalPages =
        Math.ceil(
            filteredEvents.length /
            itemsPerPage
        );


    if (eventPage > totalPages) {

        eventPage = 1;

    }


    const start =
        (eventPage - 1) *
        itemsPerPage;


    const pageData =
        filteredEvents.slice(
            start,
            start + itemsPerPage
        );


    const container =
        document.getElementById("eventList");

    container.innerHTML = "";


    if (pageData.length === 0) {

        container.innerHTML =
            "<p>No events found.</p>";

        return;

    }


    pageData.forEach(event => {

        container.innerHTML += `

            <div class="dynamic-card">

                <h3>${event.title}</h3>

                <p>📅 ${event.date}</p>

                <p>📍 ${event.location}</p>

                <p>🏷️ ${event.category}</p>

            </div>

        `;

    });


    createPagination(
        "eventPagination",
        totalPages,
        eventPage,
        page => {

            eventPage = page;

            renderEvents();

        }
    );

}


// =====================================================
// STUDENTS
// =====================================================

function renderStudents() {

    const search =
        document.getElementById("studentSearch")
        .value
        .toLowerCase();

    const filter =
        document.getElementById("studentFilter")
        .value;

    const sort =
        document.getElementById("studentSort")
        .value;


    let filteredStudents =
        students.filter(student => {

            const matchesSearch =
                student.name
                .toLowerCase()
                .includes(search);

            const matchesFilter =
                filter === "All" ||
                student.course === filter;

            return matchesSearch && matchesFilter;

        });


    // SORT

    if (sort === "nameAsc") {

        filteredStudents.sort(
            (a, b) =>
                a.name.localeCompare(b.name)
        );

    }


    if (sort === "nameDesc") {

        filteredStudents.sort(
            (a, b) =>
                b.name.localeCompare(a.name)
        );

    }


    if (sort === "yearAsc") {

        filteredStudents.sort(
            (a, b) =>
                a.year.localeCompare(b.year)
        );

    }


    // PAGINATION

    const totalPages =
        Math.ceil(
            filteredStudents.length /
            itemsPerPage
        );


    if (studentPage > totalPages) {

        studentPage = 1;

    }


    const start =
        (studentPage - 1) *
        itemsPerPage;


    const pageData =
        filteredStudents.slice(
            start,
            start + itemsPerPage
        );


    const container =
        document.getElementById("studentList");

    container.innerHTML = "";


    if (pageData.length === 0) {

        container.innerHTML =
            "<p>No students found.</p>";

        return;

    }


    pageData.forEach(student => {

        container.innerHTML += `

            <div class="dynamic-card">

                <h3>${student.name}</h3>

                <p>🎓 ${student.course}</p>

                <p>📚 ${student.year}</p>

                <p>📧 ${student.email}</p>

            </div>

        `;

    });


    createPagination(
        "studentPagination",
        totalPages,
        studentPage,
        page => {

            studentPage = page;

            renderStudents();

        }
    );

}


// =====================================================
// NOTICES
// =====================================================

function renderNotices() {

    const search =
        document.getElementById("noticeSearch")
        .value
        .toLowerCase();


    const filteredNotices =
        notices.filter(notice => {

            return notice.title
                .toLowerCase()
                .includes(search);

        });


    const container =
        document.getElementById("noticeList");

    container.innerHTML = "";


    if (filteredNotices.length === 0) {

        container.innerHTML =
            "<p>No notices found.</p>";

        return;

    }


    filteredNotices.forEach(notice => {

        container.innerHTML += `

            <div class="notice-card">

                <h3>${notice.title}</h3>

                <p>${notice.message}</p>

                <small>
                    📅 ${notice.date}
                </small>

            </div>

        `;

    });

}


// =====================================================
// FAQs
// =====================================================

function renderFAQs() {

    const search =
        document.getElementById("faqSearch")
        .value
        .toLowerCase();


    const filteredFAQs =
        faqs.filter(faq => {

            return faq.question
                .toLowerCase()
                .includes(search);

        });


    const container =
        document.getElementById("faqList");

    container.innerHTML = "";


    if (filteredFAQs.length === 0) {

        container.innerHTML =
            "<p>No FAQs found.</p>";

        return;

    }


    filteredFAQs.forEach(faq => {

        container.innerHTML += `

            <div class="faq-json-card">

                <button
                    onclick="toggleDynamicFAQ(${faq.id})">

                    ${faq.question}

                </button>

                <p
                    id="dynamicFaq${faq.id}"
                    style="display:none;">

                    ${faq.answer}

                </p>

            </div>

        `;

    });

}


// =====================================================
// FAQ TOGGLE
// =====================================================

function toggleDynamicFAQ(id) {

    const answer =
        document.getElementById(
            "dynamicFaq" + id
        );


    if (answer.style.display === "none") {

        answer.style.display = "block";

    }

    else {

        answer.style.display = "none";

    }

}


// =====================================================
// PAGINATION
// =====================================================

function createPagination(
    containerId,
    totalPages,
    currentPage,
    callback
) {

    const container =
        document.getElementById(containerId);

    container.innerHTML = "";


    if (totalPages <= 1) {

        return;

    }


    for (
        let i = 1;
        i <= totalPages;
        i++
    ) {

        const button =
            document.createElement("button");

        button.innerText = i;


        if (i === currentPage) {

            button.classList.add(
                "active-page"
            );

        }


        button.onclick = function() {

            callback(i);

        };


        container.appendChild(button);

    }

}