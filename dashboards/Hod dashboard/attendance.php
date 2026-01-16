<?php
session_start();
include('../../db.php');

if (!isset($_SESSION['hod_username'])) {
    header("Location: ../../Login/hod_login.php");
    exit();
}
// Subjects ENUM
$subjects = ['IM&IS','VAP','IT PROJECT','WD','CNS','CS'];

// Get filters: date, subject, period
$searchDate = $_GET['date'] ?? date('Y-m-d');
$subject = $_GET['subject'] ?? 'IM&IS';
$period = isset($_GET['period']) ? intval($_GET['period']) : 1;

// Fetch attendance with numeric order by Reg No
$stmt = $conn->prepare("
    SELECT s.reg_no, s.first_name, s.last_name, 
           IFNULL(a.status,'Not Marked') AS status
    FROM students s
    LEFT JOIN attende a 
    ON s.reg_no=a.reg_no AND a.date=? AND a.subject=? AND a.period=?
    ORDER BY CAST(SUBSTRING_INDEX(s.reg_no,'/',-1) AS UNSIGNED) ASC
");
$stmt->bind_param("ssi", $searchDate, $subject, $period);
$stmt->execute();
$attendanceResult = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Attendance</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-4 font-sans">

<div class="max-w-5xl mx-auto bg-white rounded-2xl shadow-lg p-6 mt-8">
    <div class="flex flex-wrap justify-between items-center mb-6 gap-4">
        <a href="index.php" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">Back to Dashboard</a>
        <h3 class="text-xl font-semibold text-gray-700">
            Attendance for <span class="font-medium text-blue-600"><?= htmlspecialchars($searchDate) ?></span> — 
            <span class="font-medium text-blue-600"><?= htmlspecialchars($subject) ?></span> — Period <span class="font-medium text-blue-600"><?= $period ?></span>
        </h3>
    </div>

    <!-- Filters -->
    <form method="get" class="flex flex-wrap gap-3 mb-6 items-center">
        <input type="date" name="date" value="<?= htmlspecialchars($searchDate) ?>" 
               class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">

        <select name="subject" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            <?php foreach($subjects as $sub): ?>
                <option value="<?= $sub ?>" <?= ($subject==$sub)?'selected':'' ?>><?= $sub ?></option>
            <?php endforeach; ?>
        </select>

        <select name="period" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            <?php for($i=1;$i<=4;$i++): ?>
                <option value="<?= $i ?>" <?= ($period==$i)?'selected':'' ?>>Period <?= $i ?></option>
            <?php endfor; ?>
        </select>

        <input type="submit" value="Filter" 
               class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 cursor-pointer transition">
        <button type="button" onclick="window.location='analytics.php'" 
                class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">View Analytics</button>
    </form>

    <!-- Attendance Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
            <thead class="bg-blue-500 text-white">
                <tr>
                    <th class="px-4 py-2 text-center">Reg No</th>
                    <th class="px-4 py-2 text-center">Full Name</th>
                    <th class="px-4 py-2 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php while($row = $attendanceResult->fetch_assoc()): 
                    $class = '';
                    if($row['status']=='Present') $class='bg-green-100';
                    if($row['status']=='Absent') $class='bg-red-100';
                ?>
                <tr class="<?= $class ?> hover:bg-gray-50">
                    <td class="px-4 py-2 text-center"><?= htmlspecialchars($row['reg_no']) ?></td>
                    <td class="px-4 py-2 text-center"><?= htmlspecialchars($row['first_name'].' '.$row['last_name']) ?></td>
                    <td class="px-4 py-2 text-center font-medium"><?= htmlspecialchars($row['status']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- Footer -->
<footer class="w-full mt-10 py-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white text-center rounded-xl shadow-md">
    Developed with <span class="text-red-500">❤️</span> by 
    <a href="https://harishpavan-dev.vercel.app" target="_blank" class="underline font-semibold">Bavananthan Harishpavan</a>
</footer>
</body>
</html>
