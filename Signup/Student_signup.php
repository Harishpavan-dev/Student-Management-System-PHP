<?php
include('../db.php'); 
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input_number = trim($_POST['reg_no']); 
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $contact    = trim($_POST['contact']);
    $email      = trim($_POST['email']);
    $password   = $_POST['password']; // plain text password
    $created_at = date('Y-m-d H:i:s');

    $prefix = "JAF/IT/2025/F/";
    $reg_no = $prefix . $input_number;

    // Profile Picture Upload
    $profile_pic = null;
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
        $upload_dir = "../uploads/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        $safe_reg_no = str_replace('/', '_', $reg_no);
        $profile_pic = time() . "_" . $safe_reg_no . "." . $ext;
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $upload_dir . $profile_pic);
    }

    // Check duplicates
    $check = $conn->prepare("SELECT reg_no, email FROM pending_students WHERE reg_no=? OR email=?");
    $check->bind_param("ss", $reg_no, $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $message = "❌ Register number or Email already exists!";
    } else {
        $stmt = $conn->prepare("INSERT INTO pending_students 
            (reg_no, first_name, last_name, contact, email, password, profile_pic, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $reg_no, $first_name, $last_name, $contact, $email, $password, $profile_pic, $created_at);

        if ($stmt->execute()) {
            $message = "✅ Student registered successfully! Pending approval from HOD.";
        } else {
            $message = "❌ Something went wrong!";
        }
        $stmt->close();
    }

    $check->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Registration</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex min-h-screen bg-gray-50">

<!-- WRAPPER FLEX -->
<div class="flex w-full md:flex-row-reverse">

    <!-- RIGHT SIDE IMAGE -->
    <div class="hidden md:flex w-1/3 bg-cover bg-center relative"
         style="background-image: url('../assets/ati-jaffna.jpg');">
        <div class="absolute inset-0 bg-black/60 flex items-center justify-center px-10">
            <div class="text-center text-white">
                <h2 class="text-3xl font-bold mb-4">Already Registered?</h2>
                <p class="mb-6 text-gray-200">Login to your account now and access your dashboard.</p>
                <a href="../Login/student_login.php"
                   class="border border-white px-6 py-2 rounded-full hover:bg-white hover:text-black transition">
                   LOGIN
                </a>
            </div>
        </div>
    </div>

    <!-- LEFT SIDE REGISTRATION FORM -->
    <div class="flex w-full md:w-2/3 justify-center items-center p-8">
        <div class="relative w-full max-w-lg rounded-2xl overflow-hidden shadow-xl">

            <!-- Background Photo -->
            <div class="absolute inset-0 bg-cover bg-center"
                 style="background-image: url('../assets/student-bg.jpg'); filter: brightness(0.6);">
            </div>

            <!-- Form Overlay -->
            <div class="relative bg-white/90 backdrop-blur-sm p-10 rounded-2xl">
                <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">Student Registration</h1>

                <?php if ($message): ?>
                    <p class="text-center mb-4 font-medium <?= str_contains($message, '✅') ? 'text-green-600' : 'text-red-600' ?>">
                        <?= htmlspecialchars($message) ?>
                    </p>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="space-y-5">

                    <!-- Register Number -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Registration Number</label>
                        <div class="flex items-center border-b-2 border-gray-300 focus-within:border-blue-600">
                            <span class="text-gray-500 pr-2 select-none">JAF/IT/2025/F/</span>
                            <input type="text" name="reg_no" required 
                                   placeholder="Enter last number, e.g. 23"
                                   class="flex-1 px-2 py-2 outline-none text-gray-700">
                        </div>
                    </div>

                    <!-- Name -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">First Name</label>
                            <input type="text" name="first_name" required 
                                   class="w-full px-4 py-2 border-b-2 border-gray-300 focus:border-blue-600 outline-none">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Last Name</label>
                            <input type="text" name="last_name" required 
                                   class="w-full px-4 py-2 border-b-2 border-gray-300 focus:border-blue-600 outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Contact Number</label>
                        <input type="text" name="contact" required maxlength="11" placeholder="Eg: 94764328867"
                               class="w-full px-4 py-2 border-b-2 border-gray-300 focus:border-blue-600 outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Email</label>
                        <input type="email" name="email" required placeholder="abc@gmail.com"
                               class="w-full px-4 py-2 border-b-2 border-gray-300 focus:border-blue-600 outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Password</label>
                        <input type="password" name="password" required
                               class="w-full px-4 py-2 border-b-2 border-gray-300 focus:border-blue-600 outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Profile Picture</label>
                        <input type="file" name="profile_pic" accept="image/*" 
                               class="w-full text-gray-700">
                    </div>

                    <button type="submit" 
                            class="w-full bg-blue-600 text-white py-3 rounded-full font-semibold hover:bg-blue-700 transition">
                      REGISTER
                    </button>
                </form>

                <p class="text-center mt-6 text-gray-700">
                    Already have an account? 
                    <a href="../Login/student_login.php" class="text-blue-500 hover:underline font-medium">Login</a>
                </p>

                <div class="text-center mt-4">
                    <a href="../index.php" class="text-sm text-blue-600 hover:underline">Back to Home</a>
                </div>
            </div>
        </div>
    </div>

</div>
</body>
</html>
