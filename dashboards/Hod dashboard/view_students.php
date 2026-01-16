<?php
session_start();
include('../../db.php');

if (!isset($_SESSION['hod_username'])) {
    header("Location: ../../Login/hod_login.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $reg_no = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM students WHERE reg_no = ?");
    $stmt->bind_param("s", $reg_no);
    $stmt->execute();
    header("Location: view_students.php");
    exit();
}

// Fetch all students
$result = $conn->query("SELECT * FROM students");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Students List</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-6">
  <div class="container mx-auto">
    <h1 class="text-2xl font-semibold mb-4">Students</h1>
   
    <table class="min-w-full mt-4 bg-white shadow rounded overflow-hidden">
      <thead class="bg-green-600 text-white">
        <tr>
          <th class="px-4 py-2">Profile</th>
          <th class="px-4 py-2">Reg No</th>
          <th class="px-4 py-2">Name</th>
          <th class="px-4 py-2">Email</th>
          <th class="px-4 py-2">Contact</th>
          <th class="px-4 py-2">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr class="border-b hover:bg-gray-100">
          <td class="px-4 py-2">
            <?php if ($row['profile_pic']): ?>
              <img src="../../uploads/<?php echo $row['profile_pic']; ?>" alt="Profile" class="w-12 h-12 rounded-full object-cover">
            <?php else: ?>
              <span class="text-gray-500">No Image</span>
            <?php endif; ?>
          </td>
          <td class="px-4 py-2"><?php echo $row['reg_no']; ?></td>
          <td class="px-4 py-2"><?php echo $row['first_name'] . " " . $row['last_name']; ?></td>
          <td class="px-4 py-2"><?php echo $row['email']; ?></td>
          <td class="px-4 py-2"><?php echo $row['contact']; ?></td>
          <td class="px-4 py-2">
            <a href="view_students.php?delete=<?php echo $row['reg_no']; ?>" class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700" onclick="return confirm('Are you sure?')">Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
