<?php

session_start();

require_once "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (empty($email) || empty($password)) {

        $error = "Please enter email and password.";

    } else {

        $stmt = $conn->prepare(
            "SELECT id, name, email, password, role
             FROM users
             WHERE email = ?"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            /*
             * If your database stores normal passwords:
             */
            if ($password === $user["password"]) {

                session_regenerate_id(true);

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["name"] = $user["name"];
                $_SESSION["email"] = $user["email"];
                $_SESSION["role"] = $user["role"];
                $_SESSION["last_activity"] = time();

                if ($user["role"] === "admin") {

                    header("Location: ../pages/admin.php");
                    exit();

                } else {

                    header("Location: ../pages/dashboard.php");
                    exit();
                }

            } else {

                $error = "Invalid password.";
            }

        } else {

            $error = "Invalid email or password.";
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Login - StudentHub</title>

    <link rel="stylesheet"
          href="../css/login.css">

</head>

<body>

<div class="login-container">

    <div class="login-box">

        <h2>StudentHub</h2>

        <p>Login to your account</p>

        <?php if ($error != ""): ?>

            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="form-group">

                <label>Email</label>

                <input
                    type="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                >

            </div>

            <div class="form-group">

                <label>Password</label>

                <input
                    type="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >

            </div>

            <button type="submit">
                Login
            </button>

        </form>

    </div>

</div>

</body>

</html>