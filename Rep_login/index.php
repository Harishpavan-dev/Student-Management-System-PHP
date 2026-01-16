<?php
include 'config.php';

// Handle local lockout
if(isset($_POST['lockout'])){
    session_destroy();
    header("Location: login.php");
    exit();
}

if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

// Subjects ENUM
$subjects = ['IM&IS','VAP','IT PROJECT','WD','CNS','CS'];
$subject = isset($_GET['subject']) ? $_GET['subject'] : 'IM&IS';
$period = isset($_GET['period']) ? intval($_GET['period']) : 1;

// Function to send WhatsApp message via Node.js bot
function sendWhatsappMsg($number, $message){
    $url = "http://localhost:3000/send-message";
    $data = ['number'=>$number,'message'=>$message];

    $options = [
        'http'=>[
            'header'=>"Content-type: application/json\r\n",
            'method'=>'POST',
            'content'=>json_encode($data)
        ]
    ];
    $context = stream_context_create($options);
    @file_get_contents($url,false,$context);
}

// Handle AJAX attendance toggle
if(isset($_GET['reg_no'], $_GET['status'], $_GET['subject'], $_GET['period'])){
    $reg_no = $_GET['reg_no'];
    $status = $_GET['status'];
    $subject = $_GET['subject'];
    $period = intval($_GET['period']);
    $date = date('Y-m-d');

    if($status === 'None'){
        $stmt = $conn->prepare("DELETE FROM attende WHERE reg_no=? AND date=? AND subject=? AND period=?");
        $stmt->bind_param("sssi", $reg_no, $date, $subject, $period);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("
            INSERT INTO attende (reg_no,date,subject,period,status)
            VALUES (?,?,?,?,?)
            ON DUPLICATE KEY UPDATE status=VALUES(status)
        ");
        $stmt->bind_param("sssis", $reg_no, $date, $subject, $period, $status);
        $stmt->execute();

        // Fetch student contact
        $stmt2 = $conn->prepare("SELECT first_name,last_name,contact FROM students WHERE reg_no=?");
        $stmt2->bind_param("s",$reg_no);
        $stmt2->execute();
        $res = $stmt2->get_result()->fetch_assoc();

      if($res && !empty($res['contact'])){
    $timeMarked = date('Y-m-d H:i:s'); // Current timestamp

    // Set emoji based on status
    $statusEmoji = ($status === 'Present') ? '✅' : (($status === 'Absent') ? '❌' : '⚪');

  $msg = "📋 Attendance Notice:\n"
     . "👤 Student: {$res['first_name']} {$res['last_name']}\n"
     . "📚 Subject: {$subject}\n"
     . "⏰ Period: {$period}\n"
     . "Status: {$statusEmoji} {$status}\n"
     . "🗓 Date & Time: {$timeMarked}\n\n"
     . "— Department of IT, SLIATE ATI Jaffna";

    sendWhatsappMsg($res['contact'], $msg);
}


    }
    exit();
}

// Fetch students with today's attendance
$dateToday = date('Y-m-d');
$stmt = $conn->prepare("
    SELECT s.*, a.status AS attendance_status
    FROM students s
    LEFT JOIN attende a
    ON s.reg_no=a.reg_no AND a.date=? AND a.subject=? AND a.period=?
    ORDER BY CAST(SUBSTRING_INDEX(s.reg_no,'/',-1) AS UNSIGNED) ASC
");
$stmt->bind_param("ssi", $dateToday, $subject, $period);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Attendance Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-4 font-sans">

<!-- Header -->
<header class="flex flex-col md:flex-row items-center justify-center gap-4 mb-6 text-center">
    <img src="images/SLIATE_logo.png" class="h-20 w-20">
    <h1 class="text-2xl font-bold text-blue-600">Advanced Technological Institute, Jaffna <br>Subject & Period-wise Attendance</h1>
    <img src="images/atijaffna_logo.jpg" class="h-20 w-20 rounded-full">
</header>

<!-- Controls -->
<div class="bg-white p-5 rounded-lg shadow mb-6 text-center flex flex-col md:flex-row justify-center items-center gap-4">
    <a href="../index.php" class="px-6 py-3 bg-yellow-400 text-black font-semibold rounded-xl hover:bg-yellow-500 transition">
        Back to Home
    </a>

    <form method="get" class="flex gap-2">
        <select name="subject" onchange="this.form.submit()" class="border rounded px-3 py-2">
            <?php foreach($subjects as $sub): ?>
                <option value="<?= $sub ?>" <?= ($subject==$sub)?'selected':'' ?>><?= $sub ?></option>
            <?php endforeach; ?>
        </select>

        <select name="period" onchange="this.form.submit()" class="border rounded px-3 py-2">
            <?php for($i=1;$i<=4;$i++): ?>
                <option value="<?= $i ?>" <?= ($period==$i)?'selected':'' ?>>Period <?= $i ?></option>
            <?php endfor; ?>
        </select>
    </form>

    <input type="text" id="regSearch" placeholder="Search by Reg No" class="border rounded px-3 py-2">
     <a href="view.php" class="px-6 py-3 bg-yellow-400 text-black font-semibold rounded-xl hover:bg-yellow-500 transition">
        Analytics
    </a>
    <a href="logout.php" class="px-6 py-3 bg-red-400 text-black font-semibold rounded-xl hover:bg-red-500 transition">
        Logout
    </a>
</div>

<!-- Attendance Table -->
<div class="bg-white p-5 rounded-lg shadow">
    <h3 class="text-xl font-semibold mb-3">Mark Attendance (<?= date('Y-m-d') ?>) — <?= htmlspecialchars($subject) ?> — Period <?= $period ?></h3>
    <div class="overflow-x-auto">
        <table id="attendanceTable" class="min-w-full border-collapse">
            <thead>
                <tr class="bg-blue-600 text-white">
                    <th class="px-4 py-2">Reg No</th>
                    <th class="px-4 py-2">Full Name</th>
                    <th class="px-4 py-2">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()):
                    $rowClass = '';
                    if($row['attendance_status']=='Present') $rowClass='bg-green-200';
                    if($row['attendance_status']=='Absent') $rowClass='bg-red-200';
                ?>
                <tr id="row-<?= $row['reg_no'] ?>" class="<?= $rowClass ?>">
                    <td class="border px-4 py-2"><?= $row['reg_no'] ?></td>
                    <td class="border px-4 py-2"><?= $row['first_name'].' '.$row['last_name'] ?></td>
                    <td class="border px-4 py-2">
                        <button class="present px-3 py-1 rounded <?= ($row['attendance_status']=='Present')?'bg-green-500 text-white':'' ?>" onclick="toggleAttendance('<?= $row['reg_no'] ?>','Present', this)">Present</button>
                        <button class="absent px-3 py-1 rounded <?= ($row['attendance_status']=='Absent')?'bg-red-500 text-white':'' ?>" onclick="toggleAttendance('<?= $row['reg_no'] ?>','Absent', this)">Absent</button>
                    </td>
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

<script>
function toggleAttendance(reg_no,status,btn){
    let subject = document.querySelector('select[name="subject"]').value;
    let period = document.querySelector('select[name="period"]').value;
    let row = document.getElementById('row-'+reg_no);
    let presentBtn = row.querySelector('.present');
    let absentBtn = row.querySelector('.absent');
    let newStatus;

    if((status=='Present' && presentBtn.classList.contains('bg-green-500')) || 
       (status=='Absent' && absentBtn.classList.contains('bg-red-500'))){
        newStatus='None';
        row.classList.remove('bg-green-200','bg-red-200');
        presentBtn.classList.remove('bg-green-500','text-white');
        absentBtn.classList.remove('bg-red-500','text-white');
    } else {
        newStatus=status;
        if(status=='Present'){
            row.classList.add('bg-green-200');
            row.classList.remove('bg-red-200');
            presentBtn.classList.add('bg-green-500','text-white');
            absentBtn.classList.remove('bg-red-500','text-white');
        } else {
            row.classList.add('bg-red-200');
            row.classList.remove('bg-green-200');
            absentBtn.classList.add('bg-red-500','text-white');
            presentBtn.classList.remove('bg-green-500','text-white');
        }
    }

    let xhr = new XMLHttpRequest();
    xhr.open('GET','?reg_no='+reg_no+'&status='+newStatus+'&subject='+encodeURIComponent(subject)+'&period='+period,true);
    xhr.send();
}

// Search by Reg No
document.getElementById('regSearch').addEventListener('keyup',function(){
    let filter = this.value.toLowerCase();
    document.querySelectorAll('#attendanceTable tbody tr').forEach(row=>{
        let reg = row.cells[0].innerText.toLowerCase();
        row.style.display = reg.includes(filter)?'':'none';
    });
});
</script>

</body>
</html>
