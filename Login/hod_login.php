<?php
session_start();
include('../db.php');

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM hod WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $hod = $result->fetch_assoc();

        if ($password === $hod['password']) {
            $_SESSION['hod_username'] = $hod['username'];
            $_SESSION['hod_name'] = $hod['first_name'] . " " . $hod['last_name'];

            header("Location: ../dashboards/Hod dashboard/hod_dashboard.php");
            exit();
        } else {
            $message = "Invalid password!";
        }
    } else {
        $message = "User not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HOD Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex min-h-screen bg-gray-50">

  <!-- LEFT SIDE IMAGE BACKGROUND -->
  <div class="hidden md:flex w-1/3 bg-cover bg-center relative"
       style="background-image: url('../assets/ati-jaffna.jpg');">

    <div class="absolute inset-0 bg-black/60 flex items-center justify-center px-10">
      <div class="text-center text-white">
        <h2 class="text-3xl font-bold mb-4">HOD Portal</h2>
        <p class="text-gray-200 mb-6">Access your dashboard and manage departments easily.</p>
        <a href="../index.php"
           class="border border-white px-6 py-2 rounded-full hover:bg-white hover:text-black transition">
          BACK TO HOME
        </a>
      </div>
    </div>
  </div>

  <!-- RIGHT SIDE LOGIN FORM -->
  <div class="flex w-full md:w-2/3 justify-center items-center p-8">
    <div class="bg-white shadow-xl rounded-2xl px-10 py-12 w-full max-w-md border border-gray-100">

      <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">
        HOD Login
      </h1>

      <?php if ($message): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded-lg text-center text-sm mb-4">
          <?php echo $message; ?>
        </div>
      <?php endif; ?>

      <form method="POST">

        <!-- USERNAME -->
        <div class="mb-6">
          <label class="block text-gray-700 font-medium mb-1">Username</label>
          <input type="text" name="username" required
            class="w-full border-b-2 border-gray-300 px-3 py-2 focus:border-blue-600 outline-none transition">
        </div>

        <!-- PASSWORD -->
        <div class="mb-8">
          <label class="block text-gray-700 font-medium mb-1">Password</label>
          <input type="password" name="password" required
            class="w-full border-b-2 border-gray-300 px-3 py-2 focus:border-blue-600 outline-none transition">
        </div>

        <!-- LOGIN BUTTON -->
        <button
          type="submit"
          class="w-full bg-blue-600 text-white py-3 rounded-full font-semibold hover:bg-blue-700 transition">
          LOGIN
        </button>

      </form>

      <div class="text-center mt-6">
        <a href="../index.php" class="text-blue-600 hover:underline text-sm">← Back to Home</a>
      </div>

    </div>
  </div>

</body>
</html>
