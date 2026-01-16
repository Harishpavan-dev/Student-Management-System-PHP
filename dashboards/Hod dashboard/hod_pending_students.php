<?php
session_start();
include('../../db.php');

if (!isset($_SESSION['hod_username'])) {
    header("Location: ../../Login/hod_login.php");
    exit();
}
// Approve student
if (isset($_GET['approve'])) {
    $reg_no = $_GET['approve'];

    // Fetch student data
    $query = $conn->prepare("SELECT * FROM pending_students WHERE reg_no=?");
    $query->bind_param("s", $reg_no);
    $query->execute();
    $result = $query->get_result();
    $student = $result->fetch_assoc();

    if ($student) {
        // Insert into main students table
        $stmt = $conn->prepare("INSERT INTO students (reg_no, first_name, last_name, contact, email, password, profile_pic, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $student['reg_no'], $student['first_name'], $student['last_name'], $student['contact'], $student['email'], $student['password'], $student['profile_pic'], $student['created_at']);
        $stmt->execute();

        // Delete from pending
        $del = $conn->prepare("DELETE FROM pending_students WHERE reg_no=?");
        $del->bind_param("s", $reg_no);
        $del->execute();

        $message = "✅ Student approved successfully!";
    } else {
        $message = "❌ Student not found!";
    }
}

// Reject student
if (isset($_GET['reject'])) {
    $reg_no = $_GET['reject'];
    $stmt = $conn->prepare("DELETE FROM pending_students WHERE reg_no=?");
    $stmt->bind_param("s", $reg_no);
    $stmt->execute();
    $message = "❌ Student rejected!";
}

// Fetch all pending students
$result = $conn->query("SELECT * FROM pending_students ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HOD Dashboard - Pending Students</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-700 to-gray-900 min-h-screen text-gray-800">

<div class="max-w-6xl mx-auto py-10">
    <h1 class="text-3xl font-bold text-center text-white mb-8">🎓 Pending Student Approvals</h1>

    <?php if (!empty($message)): ?>
        <div class="mb-6 text-center text-lg font-semibold <?= str_contains($message, '✅') ? 'text-green-400' : 'text-red-400' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="overflow-x-auto bg-white/90 backdrop-blur-lg rounded-2xl shadow-lg p-6">
        <table class="min-w-full border border-gray-300 rounded-lg overflow-hidden">
            <thead class="bg-indigo-600 text-white">
                <tr>
                    <th class="py-3 px-4 text-left">Reg No</th>
                    <th class="py-3 px-4 text-left">Name</th>
                    <th class="py-3 px-4 text-left">Contact</th>
                    <th class="py-3 px-4 text-left">Email</th>
                    <th class="py-3 px-4 text-left">Profile</th>
                    <th class="py-3 px-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-100">
                            <td class="py-3 px-4 font-medium"><?= htmlspecialchars($row['reg_no']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($row['contact']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($row['email']) ?></td>
                            <td class="py-3 px-4">
                                <?php if (!empty($row['profile_pic'])): ?>
                                    <img src="../../uploads/<?= htmlspecialchars($row['profile_pic']) ?>" alt="Profile" class="w-12 h-12 rounded-full object-cover">
                                <?php else: ?>
                                    <span class="text-gray-500 italic">No photo</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 text-center space-x-2">
                                <a href="?approve=<?= urlencode($row['reg_no']) ?>" class="bg-green-600 text-white px-3 py-1 rounded-lg hover:bg-green-700 transition">Approve</a>
                                <a href="?reject=<?= urlencode($row['reg_no']) ?>" class="bg-red-600 text-white px-3 py-1 rounded-lg hover:bg-red-700 transition">Reject</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-6 text-gray-500">No pending students</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
