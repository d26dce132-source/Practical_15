<?php

require_once "db.php";

$email = "admin@gmail.com";
$new_password = "Admin@123";

$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "UPDATE users SET password = ? WHERE email = ?"
);

$stmt->bind_param(
    "ss",
    $hashed_password,
    $email
);

if ($stmt->execute()) {
    echo "Password reset successfully!";
} else {
    echo "Failed to reset password.";
}

?>