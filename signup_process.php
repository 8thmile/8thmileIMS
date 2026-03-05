<?php
include('config.php');

if (isset($_POST['register'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; // In a real app, use password_hash()

    // Check if username already exists
    $checkUser = "SELECT * FROM users WHERE username='$username'";
    $runCheck = mysqli_query($conn, $checkUser);

    if (mysqli_num_rows($runCheck) > 0) {
        echo "<script>alert('Username already taken!'); window.location='signup.php';</script>";
    } else {
        // Insert new user
        $query = "INSERT INTO users (username, password, full_name) VALUES ('$username', '$password', '$full_name')";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Registration Successful! Please login.'); window.location='login.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>