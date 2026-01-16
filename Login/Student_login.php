<?php
// student_login.php
include('../db.php');
session_start();

$error = "";

if (isset($_POST['login'])) {
    $input_number = trim($_POST['reg_no']); // e.g. 23
    $password = $_POST['password'];

    // Fixed prefix for all students
    $prefix = "JAF/IT/2025/F/";

    // Build full registration number
    $reg_no = $prefix . $input_number;

    $stmt = $conn->prepare("SELECT * FROM students WHERE reg_no=? AND password=?");
    $stmt->bind_param("ss", $reg_no, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $_SESSION['reg_no'] = $reg_no;
        header("Location: ../dashboards/Student dashboard/Student_dashboard.php");
        exit();
    } else {
        $error = "Invalid Registration Number or Password!";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex min-h-screen bg-gray-50">

  <!-- LEFT SIDE IMAGE BACKGROUND -->
  <div class="hidden md:flex w-1/3 bg-cover bg-center relative"
       style="background-image: url('../assets/ati-jaffna.jpg');">
    <div class="absolute inset-0 bg-black/60 flex items-center justify-center px-10">
      <div class="text-center text-white">
        <h2 class="text-3xl font-bold mb-4">Student Portal</h2>
        <p class="text-gray-200 mb-6">Access your dashboard, view courses, and manage your profile.</p>
        <a href="../index.php"
           class="border border-white px-6 py-2 rounded-full hover:bg-white hover:text-black transition">
          BACK TO HOME
        </a>
      </div>
    </div>
  </div>

  <!-- RIGHT SIDE LOGIN FORM WITH BACKGROUND PHOTO -->
  <div class="flex w-full md:w-2/3 justify-center items-center p-8">
    <div class="relative w-full max-w-md rounded-2xl overflow-hidden shadow-xl">

      <!-- Background Image -->
      <div class="absolute inset-0 bg-cover bg-center"
           style="background-image: url('../assets/student-bg.jpg'); filter: brightness(0.6);">
      </div>

      <!-- Overlay Form -->
      <div class="relative bg-white/90 backdrop-blur-sm p-10 rounded-2xl">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">
          Student Login
        </h1>

        <?php if ($error): ?>
          <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded-lg text-center text-sm mb-4">
            <?php echo $error; ?>
          </div>
        <?php endif; ?>

        <form method="POST">

          <!-- Registration Number -->
          <div class="mb-6">
            <label class="block text-gray-700 font-medium mb-1">Registration Number (Last Digits)</label>
            <input type="text" name="reg_no" placeholder="Enter last number, e.g. 23" required
              class="w-full border-b-2 border-gray-300 px-3 py-2 focus:border-blue-600 outline-none transition">
          </div>

          <!-- Password -->
          <div class="mb-8">
            <label class="block text-gray-700 font-medium mb-1">Password</label>
            <input type="password" name="password" required
              class="w-full border-b-2 border-gray-300 px-3 py-2 focus:border-blue-600 outline-none transition">
          </div>

          <!-- Login Button -->
          <button type="submit" name="login"
            class="w-full bg-blue-600 text-white py-3 rounded-full font-semibold hover:bg-blue-700 transition">
            LOGIN
          </button>
        </form>

        <p class="text-center mt-6 text-gray-700">
          Don’t have an account? 
          <a href="../Signup/Student_signup.php" class="text-blue-500 hover:underline font-medium">Register</a>
        </p>

        <div class="text-center mt-4">
          <a href="../index.php" class="text-sm text-blue-600 hover:underline">Back to Home</a>
        </div>

      </div>
    </div>
  </div>

</body>
</html>
