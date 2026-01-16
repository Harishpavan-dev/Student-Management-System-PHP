<?php
session_start();
include('../../db.php');

if (!isset($_SESSION['hod_username'])) {
    header("Location: ../../Login/hod_login.php");
    exit();
}

$current_username = $_SESSION['hod_username'];

/* Fetch HOD info */
$stmt = $conn->prepare("SELECT first_name, last_name, email, profile_pic, username FROM hod WHERE username=?");
$stmt->bind_param("s", $current_username);
$stmt->execute();
$result = $stmt->get_result();
$hod = $result->fetch_assoc();

$message = '';
$error = '';

/* Handle form submit */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $new_username = trim($_POST['username']);
    $first_name   = trim($_POST['first_name']);
    $last_name    = trim($_POST['last_name']);
    $email        = trim($_POST['email']);
    $new_password = $_POST['password'];

    /* Check username already exists (except current user) */
    if ($new_username !== $current_username) {
        $check = $conn->prepare("SELECT id FROM hod WHERE username=?");
        $check->bind_param("s", $new_username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Username already exists!";
        }
    }

    /* Profile picture */
    $profile_pic = $hod['profile_pic'];
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
        $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        $new_name = 'hod_'.$new_username.'_'.time().'.'.$ext;
        $target = '../../uploads/'.$new_name;

        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target)) {
            $profile_pic = $new_name;
        }
    }

    if (empty($error)) {

        if (!empty($new_password)) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);

            $update = $conn->prepare(
                "UPDATE hod 
                 SET username=?, first_name=?, last_name=?, email=?, password=?, profile_pic=? 
                 WHERE username=?"
            );
            $update->bind_param(
                "sssssss",
                $new_username, $first_name, $last_name, $email, $hashed, $profile_pic, $current_username
            );

        } else {
            $update = $conn->prepare(
                "UPDATE hod 
                 SET username=?, first_name=?, last_name=?, email=?, profile_pic=? 
                 WHERE username=?"
            );
            $update->bind_param(
                "ssssss",
                $new_username, $first_name, $last_name, $email, $profile_pic, $current_username
            );
        }

        if ($update->execute()) {
            $message = "Profile updated successfully!";

            /* IMPORTANT: update session */
            $_SESSION['hod_username'] = $new_username;

            /* Update local data */
            $hod['username']   = $new_username;
            $hod['first_name'] = $first_name;
            $hod['last_name']  = $last_name;
            $hod['email']      = $email;
            $hod['profile_pic'] = $profile_pic;
        } else {
            $error = "Error updating profile!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit HOD Profile</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen p-8">

<h1 class="text-3xl font-semibold mb-6">Edit Profile</h1>

<?php if ($message): ?>
  <div class="mb-4 p-3 rounded bg-green-100 text-green-800"><?= $message ?></div>
<?php endif; ?>

<?php if ($error): ?>
  <div class="mb-4 p-3 rounded bg-red-100 text-red-800"><?= $error ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="bg-white shadow rounded-2xl p-6 max-w-lg">

  <!-- Profile Pic -->
  <div class="mb-6 flex items-center space-x-4">
    <img 
      src="<?= !empty($hod['profile_pic']) ? '../../uploads/'.$hod['profile_pic'] : '../../assets/default-avatar.png' ?>"
      class="w-20 h-20 rounded-full object-cover border"
    >
    <input type="file" name="profile_pic" class="border rounded px-3 py-2 w-full">
  </div>

  <!-- Username -->
  <div class="mb-4">
    <label class="block mb-1 font-semibold">Username</label>
    <input type="text" name="username"
           value="<?= htmlspecialchars($hod['username']) ?>"
           class="border rounded px-3 py-2 w-full" required>
  </div>

  <!-- First Name -->
  <div class="mb-4">
    <label class="block mb-1 font-semibold">First Name</label>
    <input type="text" name="first_name"
           value="<?= htmlspecialchars($hod['first_name']) ?>"
           class="border rounded px-3 py-2 w-full" required>
  </div>

  <!-- Last Name -->
  <div class="mb-4">
    <label class="block mb-1 font-semibold">Last Name</label>
    <input type="text" name="last_name"
           value="<?= htmlspecialchars($hod['last_name']) ?>"
           class="border rounded px-3 py-2 w-full" required>
  </div>

  <!-- Email -->
  <div class="mb-4">
    <label class="block mb-1 font-semibold">Email</label>
    <input type="email" name="email"
           value="<?= htmlspecialchars($hod['email']) ?>"
           class="border rounded px-3 py-2 w-full" required>
  </div>

  <!-- Password -->
  <div class="mb-6">
    <label class="block mb-1 font-semibold">
      New Password <span class="text-sm text-gray-500">(leave blank to keep current)</span>
    </label>
    <input type="password" name="password" class="border rounded px-3 py-2 w-full">
  </div>

  <button type="submit"
          class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
    Update Profile
  </button>

</form>

</body>
</html>
