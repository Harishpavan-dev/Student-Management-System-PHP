<?php
include 'config.php'; // Include DB connection


$message = "";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Use prepared statement for security
    $stmt = $conn->prepare("SELECT * FROM rep WHERE username = ? AND password = ? LIMIT 1");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];
        header("Location: index.php"); // Redirect to dashboard
        exit();
    } else {
        $message = "<p class='text-red-500 mb-4'>Invalid username or password!</p>";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Attendance System
</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen flex flex-col justify-between items-center bg-gray-100 px-4">

<!-- Header -->
<header class="flex flex-col md:flex-row items-center justify-center gap-4 mt-8 text-center">
    <img src="images/SLIATE_logo.png" alt="SLIATE Logo" class="w-20 h-20">
    <h1 class="text-2xl md:text-3xl font-bold text-blue-600">
        Advanced Technological Institute, Jaffna <br>
        Student Attendance System
    </h1>
    <img src="images/atijaffna_logo.jpg" alt="ATI Logo" class="w-20 h-20">
</header>

<!-- Login Form -->
<div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-8 mt-10">
    <div class="absolute -top-10 left-1/2 transform -translate-x-1/2 w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-md">
        <img src="images/atijaffna_logo.jpg" alt="Logo" class="w-16 h-16 rounded-full">
    </div>

    <h2 class="mt-12 text-2xl font-bold text-gray-700 text-center mb-6">Login</h2>

    <?= $message ?>

    <form method="post" class="space-y-4">
        <div class="relative">
            <i class="fa fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            <input type="text" name="username" placeholder="Enter your username" required
                   class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="relative">
            <i class="fa fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            <input type="password" name="password" placeholder="Enter your password" required
                   class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <input type="submit" name="login" value="Login"
               class="w-full py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold rounded-xl hover:from-purple-600 hover:to-blue-500 transition-all cursor-pointer">

        <a href="../index.php" class="block mt-3 text-center bg-yellow-400 text-black font-semibold py-2 rounded-xl hover:bg-yellow-500 transition">Back to Home</a>

        <a href="https://wa.me/94764328827?text=Hello%2C%20I%20want%20to%20reset%20my%20Student%20Attendance%20System%20password."
           target="_blank" class="block mt-4 text-blue-600 text-center hover:text-purple-600 transition">Forgot Password?</a>
    </form>
</div>

<!-- Footer -->
<footer class="w-full mt-10 py-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white text-center rounded-xl shadow-md">
    Developed with <span class="text-red-500">❤️</span> by 
    <a href="https://harishpavan-dev.vercel.app" target="_blank" class="underline font-semibold">Bavananthan Harishpavan</a>
</footer>

</body>
</html>
