<?php
session_start();
include('../../db.php');

if(!isset($_SESSION['reg_no'])){
    header("Location: ../../Login/student_login.php");
    exit();
}

$reg_no = $_SESSION['reg_no'];

// Fetch student info
$stmt = $conn->prepare("SELECT first_name, last_name, email, contact, profile_pic FROM students WHERE reg_no=?");
$stmt->bind_param("s", $reg_no);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $email, $contact, $profile_pic);
$stmt->fetch();
$stmt->close();

// HNDIT subjects
$subjects = ['IM&IS','VAP','IT PROJECT','WD','CNS','CS'];

// Attendance
$attendance_percentages = [];
$attendance_records = [];

foreach($subjects as $subject){
    $stmt = $conn->prepare("SELECT status, date FROM attende WHERE reg_no=? AND subject=? ORDER BY date DESC");
    $stmt->bind_param("ss", $reg_no, $subject);
    $stmt->execute();
    $result = $stmt->get_result();
    $total = $present = 0;
    while($row = $result->fetch_assoc()){
        $total++;
        if($row['status']=='Present') $present++;
        $attendance_records[] = ['subject'=>$subject,'date'=>$row['date'],'status'=>$row['status']];
    }
    $percentage = ($total>0) ? round(($present/$total)*100,1) : 0;
    $attendance_percentages[$subject] = $percentage;
    $stmt->close();
}

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="flex min-h-screen bg-gray-100 font-sans">

<!-- Sidebar -->
<aside class="w-72 bg-gray-900 text-white flex flex-col fixed h-full shadow-lg">
    <div class="p-6 text-center border-b border-gray-800">
        <img src="<?= $profile_pic ? '../../uploads/' . $profile_pic : 'https://via.placeholder.com/120' ?>" 
             class="w-28 h-28 rounded-full mx-auto mb-3 border-4 border-indigo-500 object-cover">
        <h2 class="text-xl font-bold"><?= htmlspecialchars($first_name.' '.$last_name) ?></h2>
        <p class="text-gray-400 text-sm"><?= htmlspecialchars($reg_no) ?></p>
    </div>
    <nav class="flex-1 mt-6">
        <ul class="space-y-2">
            <?php
            $nav_items = [
                'dashboard' => 'Dashboard',
                'edit_profile' => 'Edit Profile',
                'attendance' => 'Attendance'
            ];
            foreach($nav_items as $key => $label):
            ?>
            <li>
                <a href="?page=<?= $key ?>" 
                   class="block py-3 px-6 rounded-r-full transition hover:bg-indigo-600 <?= $page==$key ? 'bg-indigo-700 font-semibold' : '' ?>">
                   <?= $label ?>
                </a>
            </li>
            <?php endforeach; ?>
            <li>
                <a href="logout.php" class="block py-3 px-6 rounded-r-full mt-6 bg-red-500 hover:bg-red-600 transition text-white font-semibold">Logout</a>
            </li>
        </ul>
    </nav>
</aside>

<!-- Main Content -->
<main class="flex-1 ml-72 p-8">
    <?php if($page == 'dashboard'): ?>
        <h1 class="text-3xl font-bold mb-8 text-gray-800">Attendance Overview</h1>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach($attendance_percentages as $subject => $percentage): 
                $chart_id = "chart_".preg_replace('/[^a-zA-Z0-9]/','',$subject);
            ?>
            <div class="bg-white p-6 rounded-2xl shadow-lg flex flex-col items-center justify-center hover:scale-105 transition-transform">
                <canvas id="<?= $chart_id ?>" class="w-36 h-36"></canvas>
                <span class="mt-4 font-semibold text-gray-700 text-lg"><?= $subject ?>: <?= $percentage ?>%</span>
            </div>
            <?php endforeach; ?>
        </div>

    <?php elseif($page=='edit_profile'): ?>
        <?php include('edit_profile.php'); ?>

    <?php elseif($page=='attendance'): ?>
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Attendance Records</h1>
        <form method="get" class="mb-6 flex items-center gap-4">
            <input type="hidden" name="page" value="attendance">
            <label class="font-medium text-gray-700">Filter by Subject:</label>
            <select name="subject" onchange="this.form.submit()" class="border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">All Subjects</option>
                <?php foreach($subjects as $sub): ?>
                    <option value="<?= $sub ?>" <?= (isset($_GET['subject']) && $_GET['subject']==$sub)?'selected':'' ?>><?= $sub ?></option>
                <?php endforeach; ?>
            </select>
        </form>

        <div class="overflow-x-auto bg-white p-6 rounded-2xl shadow-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-gray-600 font-medium">Date</th>
                        <th class="px-6 py-3 text-left text-gray-600 font-medium">Subject</th>
                        <th class="px-6 py-3 text-left text-gray-600 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php 
                    $filter_subject = $_GET['subject'] ?? '';
                    $records = $attendance_records;
                    if($filter_subject){
                        $records = array_filter($attendance_records, fn($a)=>$a['subject']==$filter_subject);
                    }
                    if(count($records)==0): ?>
                        <tr>
                            <td colspan="3" class="text-center py-6 text-gray-500">No attendance records found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($records as $att): ?>
                            <tr class="<?= ($att['status']=='Present')?'bg-green-50':'bg-red-50' ?>">
                                <td class="px-6 py-3"><?= htmlspecialchars($att['date']) ?></td>
                                <td class="px-6 py-3"><?= htmlspecialchars($att['subject']) ?></td>
                                <td class="px-6 py-3 font-semibold <?= ($att['status']=='Present')?'text-green-600':'text-red-600' ?>"><?= htmlspecialchars($att['status']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

<script>
<?php foreach($attendance_percentages as $subject => $percentage): 
    $chart_id = "chart_".preg_replace('/[^a-zA-Z0-9]/','',$subject);
?>
new Chart("<?= $chart_id ?>", {
    type: 'doughnut',
    data: {
        labels: ['Present','Absent'],
        datasets: [{
            data: [<?= $percentage ?>, <?= 100-$percentage ?>],
            backgroundColor: ['#34D399','#F87171'],
            borderWidth: 0
        }]
    },
    options: {
        cutout: '70%',
        plugins: { legend: { display: false }, tooltip: { enabled: true } },
        responsive: false,
        maintainAspectRatio: false
    }
});
<?php endforeach; ?>
</script>

</body>
</html>
