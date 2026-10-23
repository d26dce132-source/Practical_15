// ==========================================
// PRACTICAL 5
// STUDENT REGISTRATION FORM VALIDATION
// USING REGULAR EXPRESSIONS
// ==========================================


document.getElementById("registrationForm")
    .addEventListener("submit", function(event) {

        // Stop form from submitting
        event.preventDefault();


        // ==========================================
        // GET VALUES
        // ==========================================

        const name =
            document.getElementById("name").value.trim();

        const email =
            document.getElementById("email").value.trim();

        const mobile =
            document.getElementById("mobile").value.trim();

        const password =
            document.getElementById("password").value;

        const confirmPassword =
            document.getElementById("confirmPassword").value;

        const course =
            document.getElementById("course").value;

        const year =
            document.getElementById("year").value;

        const terms =
            document.getElementById("terms").checked;

        const gender =
            document.querySelector(
                'input[name="gender"]:checked'
            );


        // ==========================================
        // REGULAR EXPRESSIONS
        // ==========================================

        // Name: only letters and spaces
        const nameRegex =
            /^[A-Za-z ]{2,50}$/;


        // Email validation
        const emailRegex =
            /^[^\s@]+@[^\s@]+\.[^\s@]+$/;


        // Mobile: 10 digits starting with 6-9
        const mobileRegex =
            /^[6-9][0-9]{9}$/;


        // Password:
        // Minimum 8 characters
        // One uppercase
        // One lowercase
        // One number
        // One special character

        const passwordRegex =
            /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[@$!%*?&])[A-Za-z0-9@$!%*?&]{8,}$/;


        // ==========================================
        // CLEAR OLD ERRORS
        // ==========================================

        document.getElementById("nameError").innerHTML = "";

        document.getElementById("emailError").innerHTML = "";

        document.getElementById("mobileError").innerHTML = "";

        document.getElementById("passwordError").innerHTML = "";

        document.getElementById("confirmPasswordError").innerHTML = "";

        document.getElementById("courseError").innerHTML = "";

        document.getElementById("yearError").innerHTML = "";

        document.getElementById("genderError").innerHTML = "";

        document.getElementById("termsError").innerHTML = "";

        document.getElementById("successMessage").innerHTML = "";


        let valid = true;


        // ==========================================
        // NAME VALIDATION
        // ==========================================

        if (!nameRegex.test(name)) {

            document.getElementById("nameError").innerHTML =
                "Please enter a valid name.";

            valid = false;
        }


        // ==========================================
        // EMAIL VALIDATION
        // ==========================================

        if (!emailRegex.test(email)) {

            document.getElementById("emailError").innerHTML =
                "Please enter a valid email address.";

            valid = false;
        }


        // ==========================================
        // MOBILE VALIDATION
        // ==========================================

        if (!mobileRegex.test(mobile)) {

            document.getElementById("mobileError").innerHTML =
                "Enter a valid 10 digit mobile number.";

            valid = false;
        }


        // ==========================================
        // PASSWORD VALIDATION
        // ==========================================

        if (!passwordRegex.test(password)) {

            document.getElementById("passwordError").innerHTML =
                "Password must contain 8 characters, uppercase, lowercase, number and special character.";

            valid = false;
        }


        // ==========================================
        // CONFIRM PASSWORD
        // ==========================================

        if (password !== confirmPassword) {

            document.getElementById("confirmPasswordError").innerHTML =
                "Passwords do not match.";

            valid = false;
        }


        // ==========================================
        // COURSE
        // ==========================================

        if (course === "") {

            document.getElementById("courseError").innerHTML =
                "Please select a course.";

            valid = false;
        }


        // ==========================================
        // YEAR
        // ==========================================

        if (year === "") {

            document.getElementById("yearError").innerHTML =
                "Please select your year.";

            valid = false;
        }


        // ==========================================
        // GENDER
        // ==========================================

        if (!gender) {

            document.getElementById("genderError").innerHTML =
                "Please select your gender.";

            valid = false;
        }


        // ==========================================
        // TERMS
        // ==========================================

        if (!terms) {

            document.getElementById("termsError").innerHTML =
                "Please accept the Terms and Conditions.";

            valid = false;
        }


        // ==========================================
        // SUCCESS
        // ==========================================

        if (valid) {

            document.getElementById("successMessage").innerHTML =
                "Registration Successful!";

            document.getElementById("registrationForm").reset();

        }

});