<?php
$server = "localhost";
$username = "root";
$password = "";
$database = "airbnb";

$conn = mysqli_connect($server, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input data
    $firstName = mysqli_real_escape_string($conn, $_POST['firstName']);
    $lastName = mysqli_real_escape_string($conn, $_POST['lastName']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $nationality = mysqli_real_escape_string($conn, $_POST['nationality']);
    $profession = mysqli_real_escape_string($conn, $_POST['profession']);
    $age = intval($_POST['age']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $checkEmail = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $checkEmail);

    if (mysqli_num_rows($result) > 0) {
        $error_message = "Email already exists!";
        include 'register.php';
        exit();
    }

    // Check if username already exists
    $checkUsername = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $checkUsername);

    if (mysqli_num_rows($result) > 0) {
        $error_message = "Username already taken!";
        include 'register.php';
        exit();
    }

    // Insert user data into database
    $sql = "INSERT INTO users (first_name, last_name, email, phone, nationality, profession, age, username, password) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssssss", 
        $firstName, $lastName, $email, $phone, $nationality, 
        $profession, $age, $username, $password);

    if (mysqli_stmt_execute($stmt)) {
        // Registration successful - redirect to home page with success message
        header("Location: ../home/home.html?registration=success&name=" . urlencode($firstName));
        exit();
    } else {
        $error_message = "Error: " . mysqli_error($conn);
        include 'register.php';
        exit();
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>
