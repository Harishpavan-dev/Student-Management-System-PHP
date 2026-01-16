<?php

include('../../db.php'); // DB connection

if(!isset($_SESSION['reg_no'])){
    header("Location: ../../../Login/student_login.php");
    exit();
}

$reg_no = $_SESSION['reg_no'];

// HNDIT subjects
$subjects = ['IM&IS','VAP','IT PROJECT','WD','CNS','CS'];

// Attendance
$attendance_records = [];
foreach($subjects as $subject){
    $stmt = $conn->prepare("SELECT status, date FROM attende WHERE reg_no=? AND subject=? ORDER BY date DESC");
    $stmt->bind_param("ss", $reg_no, $subject);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()){
        $attendance_records[] = ['date'=>$row['date'], 'subject'=>$subject, 'status'=>$row['status']];
    }
    $stmt->close();
}

// Filter by subject
$filter_subject = isset($_GET['subject']) ? $_GET['subject'] : '';
$filtered_records = $attendance_records;
if($filter_subject != ''){
    $filtered_records = array_filter($attendance_records, function($att) use ($filter_subject){
        return $att['subject'] == $filter_subject;
    });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Attendance Records</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 font-sans">

<div class="bg-white p-6 rounded-2xl shadow">
    <h2 class="text-xl font-semibold mb-4">Attendance Records</h2>

    <form method="get" class="mb-4">
        <label class="mr-2 font-medium">Filter by Subject:</label>
        <select name="subject" onchange="this.form.submit()" class="border px-3 py-1 rounded">
            <option value="">All Subjects</option>
            <?php foreach($subjects as $sub): ?>
                <option value="<?= $sub ?>" <?= ($sub==$filter_subject)?'selected':'' ?>><?= $sub ?></option>
            <?php endforeach; ?>
        </select>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
            <thead>
                <tr class="bg-gray-200 text-gray-700">
                    <th class="px-4 py-2">Date</th>
                    <th class="px-4 py-2">Subject</th>
                    <th class="px-4 py-2">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($filtered_records)==0): ?>
                    <tr>
                        <td colspan="3" class="text-center py-4 text-gray-500">No attendance records found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach($filtered_records as $att): ?>
                        <tr class="<?= ($att['status']=='Present')?'bg-green-100':'bg-red-100' ?> border-b">
                            <td class="px-4 py-2"><?= htmlspecialchars($att['date']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($att['subject']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($att['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
