<?php
session_start();
include('db_connection.php'); // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าผู้ใช้ได้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // ดึง user_id จาก session

// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
$sql = "SELECT first_name, last_name, email, phone, department, position, picture_url FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Roboto', sans-serif;
        background-color: #f4f6f9;
        display: flex;
        min-height: 100vh;
    }

    .sidebar {
        width: 260px;
        background-color: #6699FF;
        color: white;
        padding: 30px 20px;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        flex-shrink: 0;
    }

    .logo {
        font-size: 1.5em;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
    }

    .logo-img {
        width: 40px;
        height: auto;
        margin-right: 10px;
    }

    .menu {
        padding: 30px 20px;
        background-color: #6699FF;
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
    }

    .menu-item {
        font-size: 1.1em;
        color: white;
        text-decoration: none;
        padding: 12px;
        margin-bottom: 12px;
        border-radius: 6px;
        transition: background-color 0.3s ease;
    }

    .menu-item:hover {
        background-color: #A8D8FF;
    }

    .profile-container {
        width: 100%; /* Full width */
        max-width: 800px; /* Set the maximum width */
        height: 500px; /* Set a fixed height */
        margin: 80px auto; /* Center the container */
        background: #fff;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        overflow: hidden;
    }

    /* Header */
    .profile-header {
        background-color: #3c50c1;
        color: white;
        padding: 20px;
        text-align: center;
    }

    .profile-header h1 {
        margin: 0;
        font-size: 24px;
    }

    /* รูปโปรไฟล์ */
    .profile-image {
        text-align: center;
        margin-top: 0px;
    }

    .profile-image img {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 5px solid #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* ข้อมูลโปรไฟล์ */
    .profile-info {
        margin-top: -10px;
        padding: 20px;
        text-align: center;
    }

    .profile-info h2 {
        margin: 10px 0;
        font-size: 20px;
        color: #3c50c1;
    }

    .profile-info p {
        margin: 5px 0;
        font-size: 16px;
        color: #666;
    }

    /* ปุ่มแก้ไข */
    .profile-actions {
        margin-top: -20px;
        text-align: center;
    }

    .profile-actions .btn {
    display: inline-block;
    padding: 10px 20px;
    font-size: 16px;
    border: none;
    border-radius: 5px;
    color: white;
    background-color: #28a745;
    text-decoration: none;
    cursor: pointer;
    margin: 0 10px;
    transition: background-color 0.3s, transform 0.3s; /* เพิ่ม transition สำหรับการขยับ */
}

.profile-actions .btn:hover {
    background-color: #218838;
}

    .btn.delete {
        background-color: #dc3545;
    }

    .btn.delete:hover {
        background-color: #c82333;
    }

    .menu-item:hover, .menu-item.active {
        background-color: #0066cc;
    }
</style>

</head>
<body>
<div class="sidebar">
    <div class="logo">
        <img src="images/2.png" alt="PEANE3 Logo" class="logo-img">
        PEANE3
    </div>
    <div class="menu">
    <a href="profile.php" class="menu-item active">
  <img src="images/verified-account (1).png" alt="Icon" style="width: 33px; height: 33px;"> โปรไฟล์
</a>

<a href="user.php" class="menu-item">
  <img src="images/seo.png" alt="Search Icon" style="width: 33px; height: 33px;"> จองห้องประชุม
</a>

<a href="all_booking_information.php" class="menu-item">
  <img src="images/time-management.png" alt="Calendar Check Icon" style="width: 33px; height: 33px;"> ประวัติการจองของฉัน
</a>

<a href="Review_Meeting_Room.php" class="menu-item">
  <img src="images/shooting-star.png" alt="Star Icon" style="width: 33px; height: 33px;"> รีวิวให้คะแนนห้องประชุม
</a>

<a href="room_reviews.php" class="menu-item ">
  <img src="images/chat-bubbles.png" alt="Comments Icon" style="width: 33px; height: 33px;"> ผลการรีวิวห้องประชุม
</a>

<a href="Logout_u.php" class="menu-item">
  <img src="uploads/check-out.png" alt="Sign Out Icon" style="width: 33px; height: 33px;"> ออกจากระบบ
</a>

    </div>
</div>
    <div class="profile-container">
        <!-- Header -->
        <div class="profile-header">
            <h1>โปรไฟล์ผู้ใช้</h1>
        </div>
        <!-- รูปโปรไฟล์ -->
        <div class="profile-image">
    <?php
    $profilePicturePath = 'uploads/' . htmlspecialchars($user['picture_url']);
    if (!empty($user['picture_url']) && file_exists($profilePicturePath)) {
        echo '<img src="' . $profilePicturePath . '" alt="Profile Picture">';
    } else {
        echo '<img src="images/default-profile.png" alt="Profile Picture">';
    }
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
    $fileName = $_FILES['profile_picture']['name'];
    $fileSize = $_FILES['profile_picture']['size'];
    $fileType = $_FILES['profile_picture']['type'];

    // กำหนดพาธที่จัดเก็บไฟล์
    echo 'Path: ' . $profilePicturePath;
if (move_uploaded_file($fileTmpPath, $uploadPath)) {
    echo "Upload success";
} else {
    echo "Error uploading file";
}
    $uploadPath = 'uploads/' . basename($fileName);
    // ย้ายไฟล์จาก temp ไปยังโฟลเดอร์ที่กำหนด
    if (move_uploaded_file($fileTmpPath, $uploadPath)) {
        // อัปเดตฐานข้อมูลให้เก็บชื่อไฟล์ของรูปที่อัปโหลด
        $sql = "UPDATE users SET picture_url = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $fileName, $user_id);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "เกิดข้อผิดพลาดในการอัปโหลดไฟล์";
    }
}
    ?>
</div>
  
<div class="profile-info">
    <h2><i class="fas fa-user" style="color: #007bff;"></i> ชื่อ: <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
    <p><i class="fas fa-envelope" style="color: #28a745;"></i> อีเมล: <?php echo htmlspecialchars($user['email']); ?></p>
    <p><i class="fas fa-phone-alt" style="color: #fd7e14;"></i> เบอร์โทรศัพท์: <?php echo htmlspecialchars($user['phone']); ?></p>
    <p><i class="fas fa-building" style="color: #ffc107;"></i> แผนก: <?php echo htmlspecialchars($user['department']); ?></p>
    <p><i class="fas fa-briefcase" style="color: #17a2b8;"></i> ตำแหน่ง: <?php echo htmlspecialchars($user['position']); ?></p>
</div>
    <div class="profile-actions">
        <a href="Edit_Profile.php" class="btn edit">แก้ไขโปรไฟล์</a>
    </div>
    </div>
</body>
</html> 