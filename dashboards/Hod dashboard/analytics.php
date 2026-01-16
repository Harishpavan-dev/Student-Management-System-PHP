<?php
session_start();
include('../../db.php');

if (!isset($_SESSION['hod_username'])) {
    header("Location: ../../Login/hod_login.php");
    exit();
}
// --- Define subjects ---
$subjects = ['IM&IS','VAP','IT PROJECT','WD','CNS','CS'];

// --- Fetch all students ---
$students = [];
$studentStmt = $conn->prepare("SELECT reg_no, first_name, last_name FROM students ORDER BY CAST(SUBSTRING_INDEX(reg_no,'/',-1) AS UNSIGNED) ASC");
$studentStmt->execute();
$studentResult = $studentStmt->get_result();
while($row = $studentResult->fetch_assoc()){
    $students[$row['reg_no']] = [
        'reg_no' => $row['reg_no'],
        'name' => $row['first_name'].' '.$row['last_name']
    ];
}

// --- Fetch subject-wise attendance counts ---
$data = [];
foreach($subjects as $sub){
    $stmt = $conn->prepare("
        SELECT s.reg_no,
               COUNT(a.status) AS total,
               SUM(CASE WHEN a.status='Present' THEN 1 ELSE 0 END) AS present
        FROM students s
        LEFT JOIN attende a
            ON s.reg_no = a.reg_no AND a.subject=?
        GROUP BY s.reg_no
    ");
    $stmt->bind_param("s", $sub);
    $stmt->execute();
    $res = $stmt->get_result();
    while($r = $res->fetch_assoc()){
        $total = $r['total'];
        $present = $r['present'];
        $percentage = ($total > 0) ? round(($present / $total) * 100, 1) : 0;
        $data[$r['reg_no']][$sub] = $percentage;
    }
}

// --- Calculate overall percentage ---
$analytics = [];
foreach($students as $reg => $stu){
    $totalPerc = 0;
    $count = 0;
    foreach($subjects as $sub){
        if(isset($data[$reg][$sub])){
            $totalPerc += $data[$reg][$sub];
            $count++;
        }
    }
    $overall = ($count > 0) ? round($totalPerc / $count, 1) : 0;
    $analytics[] = [
        'reg_no' => $stu['reg_no'],
        'name' => $stu['name'],
        'subjects' => $data[$reg] ?? [],
        'overall' => $overall
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Subject-wise Attendance Dashboard</title>
<style>
body{font-family:Poppins,sans-serif;background:#f5f7fa;padding:20px;}
.card{background:#fff;border-radius:12px;padding:25px;margin:auto;max-width:1200px;box-shadow:0 5px 20px rgba(0,0,0,0.05);}
table{width:100%;border-collapse:collapse;margin-top:15px;}
th,td{padding:10px;text-align:center;border-bottom:1px solid #eee;}
th{background:#2575fc;color:#fff;position:sticky;top:0;}
tr.present-row{background:#d4edda;}
tr.warning-row{background:#fff3cd;}
tr.absent-row{background:#f8d7da;}
tr:hover{background:#f1f5f9;}
input{padding:8px 12px;border-radius:8px;border:1px solid #ddd;margin-bottom:10px;}
@media(max-width:768px){th,td{font-size:0.8rem;padding:6px;}}
</style>
</head>
<body>

<div class="card">
<a href="index.php" style="text-decoration:none;color:#fff;padding:8px 15px;background:#555;border-radius:8px;">Back</a>
<h2>Subject-wise Attendance Analytics</h2>
<input type="text" id="searchBar" placeholder="Search by Reg No or Name">

<table id="attendanceTable">
<tr>
    <th>Reg No</th>
    <th>Name</th>
    <?php foreach($subjects as $sub): ?>
        <th><?=$sub?></th>
    <?php endforeach; ?>
    <th>Overall %</th>
</tr>
<?php foreach($analytics as $a): 
    if($a['overall']>=75) $rowClass='present-row';
    elseif($a['overall']>=50) $rowClass='warning-row';
    else $rowClass='absent-row';
?>
<tr class="<?=$rowClass?>">
    <td><?=htmlspecialchars($a['reg_no'])?></td>
    <td><?=htmlspecialchars($a['name'])?></td>
    <?php foreach($subjects as $sub): ?>
        <td><?=isset($a['subjects'][$sub]) ? $a['subjects'][$sub].'%' : '—'?></td>
    <?php endforeach; ?>
    <td><?=$a['overall']?>%</td>
</tr>
<?php endforeach; ?>
</table>
</div>

<script>
// Search Filter
const searchInput=document.getElementById('searchBar');
searchInput.addEventListener('keyup',()=>{
    const filter=searchInput.value.toLowerCase();
    const rows=document.querySelectorAll('#attendanceTable tr:not(:first-child)');
    rows.forEach(row=>{
        const reg=row.cells[0].innerText.toLowerCase();
        const name=row.cells[1].innerText.toLowerCase();
        row.style.display=(reg.includes(filter)||name.includes(filter))?'':'none';
    });
});
</script>

<footer style="text-align:center;margin-top:40px;padding:15px;font-size:1rem;color:#fff;background:#2575fc;border-radius:12px;">
Developed with ❤️ by <a href="https://harishpavan-dev.vercel.app" target="_blank" style="color:#fff;">Bavananthan Harishpavan</a>
</footer>

</body>
</html>
