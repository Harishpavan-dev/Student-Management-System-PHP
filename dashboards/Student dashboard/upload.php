<?php
session_start();
include('../db.php');

if(!isset($_SESSION['reg_no'])){
    header("Location: ../../Login/student_login.php");
    exit();
}

if(isset($_POST['upload'])){
    $reg_no = $_SESSION['reg_no'];
    $file = $_FILES['file'];

    $filename = time() . '_' . basename($file['name']);
    $target = '../uploads/' . $filename;

    if(move_uploaded_file($file['tmp_name'], $target)){
        $stmt = $conn->prepare("UPDATE students SET profile_pic=? WHERE reg_no=?");
        $stmt->bind_param("ss", $filename, $reg_no);
        if($stmt->execute()){
            header("Location: student_dashboard.php");
            exit();
        } else {
            echo "Error updating profile picture in database.";
        }
        $stmt->close();
    } else {
        echo "Error uploading file.";
    }
}
?>
