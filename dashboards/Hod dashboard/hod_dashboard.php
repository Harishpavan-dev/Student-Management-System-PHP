<?php
session_start();
include('../../db.php');

if (!isset($_SESSION['hod_username'])) {
    header("Location: ../../Login/hod_login.php");
    exit();
}

$hod_username = $_SESSION['hod_username'];

/* Fetch HOD details */
$stmt = $conn->prepare("SELECT first_name, last_name, email, profile_pic FROM hod WHERE username = ?");
$stmt->bind_param("s", $hod_username);
$stmt->execute();
$result = $stmt->get_result();
$hod = $result->fetch_assoc();

/* Default profile pic if empty */
$profilePic = !empty($hod['profile_pic']) 
    ? "../../uploads/" . $hod['profile_pic'] 
    : "../../assets/default-avatar.png";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HOD Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex h-screen bg-gray-100 font-sans overflow-hidden">

<!-- ================= SIDEBAR ================= -->
<aside class="w-64 bg-gradient-to-b from-gray-900 to-gray-800 text-white flex flex-col p-6 shadow-2xl">

  <!-- Profile Section -->
  <div class="flex flex-col items-center text-center mb-10">
    <img 
      src="<?php echo $profilePic; ?>" 
      class="w-24 h-24 rounded-full border-4 border-white shadow-lg object-cover"
      alt="HOD Profile">

    <h3 class="mt-4 font-semibold text-lg">
      <?php echo htmlspecialchars($hod['first_name'] . " " . $hod['last_name']); ?>
    </h3>

    <p class="text-sm text-gray-300 break-all">
      <?php echo htmlspecialchars($hod['email']); ?>
    </p>
  </div>

  <!-- Navigation -->
  <nav class="flex flex-col space-y-2 text-sm">
    <a target="frame" href="hod_pending_students.php"
       class="hover:bg-white/10 hover:pl-4 transition-all duration-300 p-3 rounded-lg">
       ⏳ Pending Students
    </a>

    <a target="frame" href="view_students.php"
       class="hover:bg-white/10 hover:pl-4 transition-all duration-300 p-3 rounded-lg">
       👨‍🎓 View Students
    </a>

    <a target="frame" href="attendance.php"
       class="hover:bg-white/10 hover:pl-4 transition-all duration-300 p-3 rounded-lg">
       📊 Attendance
    </a>

    <a target="frame" href="analytics.php"
       class="hover:bg-white/10 hover:pl-4 transition-all duration-300 p-3 rounded-lg">
       📈 Analytics
    </a>

    <a target="frame" href="edit_hod.php"
       class="hover:bg-white/10 hover:pl-4 transition-all duration-300 p-3 rounded-lg">
       ⚙️ Edit Profile
    </a>
  </nav>

  <!-- Logout -->
  <a href="hod_logout.php"
     class="mt-auto bg-red-600 hover:bg-red-700 transition-all duration-300 p-3 rounded-xl text-center font-semibold shadow-lg">
     🚪 Logout
  </a>
</aside>

<!-- ================= MAIN CONTENT ================= -->
<div class="flex-1 flex flex-col">

  <!-- Header -->
  <header class="bg-white/80 backdrop-blur-md shadow-md px-6 py-4 flex justify-between items-center border-b">
    <div>
      <h1 class="text-xl font-semibold text-gray-800">
        Welcome, <?php echo htmlspecialchars($hod['first_name']); ?>
      </h1>
      <p class="text-xs text-gray-500">HOD Dashboard</p>
    </div>

    <span class="text-sm text-gray-400">
      <?php echo date("l, d M Y"); ?>
    </span>
  </header>

  <!-- Page Content -->
  <main class="flex-1 p-4">
    <iframe 
      name="frame"
      src="hod_pending_students.php"
      class="w-full h-full rounded-xl border shadow-inner bg-white">
    </iframe>
  </main>

</div>

</body>
</html>
