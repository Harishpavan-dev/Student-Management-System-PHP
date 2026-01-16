<?php


include('../../db.php'); // Adjust path

if(!isset($_SESSION['reg_no'])){
    header("Location: ../../Login/student_login.php");
    exit();
}

$reg_no = $_SESSION['reg_no'];
$message = "";

// Fetch current student info
$stmt = $conn->prepare("SELECT first_name, last_name, email, contact, profile_pic, password FROM students WHERE reg_no=?");
$stmt->bind_param("s", $reg_no);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $email, $contact, $profile_pic, $password);
$stmt->fetch();
$stmt->close();

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $first_name_new = trim($_POST['first_name']);
    $last_name_new  = trim($_POST['last_name']);
    $email_new      = trim($_POST['email']);
    $contact_new    = trim($_POST['contact']);
    $password_new   = trim($_POST['password']); // plain text

    // Handle profile picture upload
    if(isset($_FILES['profile_pic']) && $_FILES['profile_pic']['name'] !== ''){
        $file = $_FILES['profile_pic'];
        $filename = time() . '_' . basename($file['name']);
        $target = '../../uploads/' . $filename;


        if(move_uploaded_file($file['tmp_name'], $target)){
            $profile_pic = $filename;
        } else {
            $message = "❌ Error uploading profile picture!";
        }
    }

    if($first_name_new && $last_name_new && $email_new && $contact_new){
        if($password_new){
            // Update password as plain text
            $stmt = $conn->prepare("UPDATE students SET first_name=?, last_name=?, email=?, contact=?, profile_pic=?, password=? WHERE reg_no=?");
            $stmt->bind_param("sssssss", $first_name_new, $last_name_new, $email_new, $contact_new, $profile_pic, $password_new, $reg_no);
        } else {
            // Do not update password
            $stmt = $conn->prepare("UPDATE students SET first_name=?, last_name=?, email=?, contact=?, profile_pic=? WHERE reg_no=?");
            $stmt->bind_param("ssssss", $first_name_new, $last_name_new, $email_new, $contact_new, $profile_pic, $reg_no);
        }

        if($stmt->execute()){
            $message = "✅ Profile updated successfully!";
            $first_name = $first_name_new;
            $last_name  = $last_name_new;
            $email      = $email_new;
            $contact    = $contact_new;
            if($password_new) $password = $password_new;
        } else {
            $message = "❌ Something went wrong!";
        }
        $stmt->close();
    } else {
        $message = "❌ All fields are required!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Profile</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
    <h2 class="text-2xl font-bold text-center mb-6">Edit Profile</h2>

    <?php if($message): ?>
        <div class="mb-4 text-center text-sm font-medium <?= str_contains($message,'✅') ? 'text-green-600' : 'text-red-600' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="space-y-4">
        <!-- Profile Picture Preview -->
        <div class="text-center">
           <img id="profilePreview" src="<?= $profile_pic ? '../../uploads/' . $profile_pic : 'https://via.placeholder.com/100' ?>"

                 class="w-24 h-24 rounded-full mx-auto mb-3 border-2 border-indigo-500 object-cover">
            <input type="file" name="profile_pic" class="mt-2" onchange="previewImage(event)">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
            <input type="text" name="first_name" value="<?= htmlspecialchars($first_name) ?>" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
            <input type="text" name="last_name" value="<?= htmlspecialchars($last_name) ?>" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Contact</label>
            <input type="text" name="contact" value="<?= htmlspecialchars($contact) ?>" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
            <input type="text" name="password" placeholder="Leave blank to keep current password" 
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>

        <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-lg font-medium hover:bg-indigo-700 transition">Update Profile</button>
    </form>

    <p class="text-center mt-4 text-gray-600">
        <a href="student_dashboard.php" class="text-indigo-600 hover:underline">Back to Dashboard</a>
    </p>
</div>

<script>
function previewImage(event){
    const reader = new FileReader();
    reader.onload = function(){
        const output = document.getElementById('profilePreview');
        output.src = reader.result; // live preview
    }
    reader.readAsDataURL(event.target.files[0]);
}
</script>

</body>
</html>
