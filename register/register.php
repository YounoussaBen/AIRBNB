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
    // Check if required fields are set
    if (empty($_POST['firstName']) || empty($_POST['lastName']) || empty($_POST['email']) || 
        empty($_POST['phone']) || empty($_POST['nationality']) || empty($_POST['profession']) || 
        empty($_POST['age']) || empty($_POST['username']) || empty($_POST['password'])) {
        echo "<script>
            alert('Please fill in all required fields.');
            window.location.href = '../register/register.html';
        </script>";
        exit();
    }

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
    $checkEmail = "SELECT 1 FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $checkEmail);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_close($stmt);
        echo "<script>
            alert('Email already exists!');
            window.location.href = '../register/register.html';
        </script>";
        exit();
    }
    mysqli_stmt_close($stmt);

    // Check if username already exists
    $checkUsername = "SELECT 1 FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $checkUsername);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_close($stmt);
        echo "<script>
            alert('Username already taken!');
            window.location.href = '../register/register.html';
        </script>";
        exit();
    }
    mysqli_stmt_close($stmt);

    // Insert user data into database
    $sql = "INSERT INTO users (first_name, last_name, email, phone, nationality, profession, age, username, password) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssiss", 
        $firstName, $lastName, $email, $phone, $nationality, 
        $profession, $age, $username, $password);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
            alert('Registration successful!');
            window.location.href = '../home/home.html';
        </script>";
    } else {
        echo "<script>
            alert('Error: Could not complete registration. Please try again later.');
            window.location.href = '../register/register.html';
        </script>";
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>
