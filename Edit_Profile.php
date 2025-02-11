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

// ตรวจสอบการอัปโหลดไฟล์รูปโปรไฟล์ใหม่
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับข้อมูลจากฟอร์ม
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $department = $_POST['department'];
    $position = $_POST['position'];
    $picture_url = $_POST['picture_url'];
    
    // ตรวจสอบการอัปโหลดไฟล์รูปโปรไฟล์ใหม่
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = time() . "_" . basename($_FILES['profile_picture']['name']); // ใช้เวลาเพื่อป้องกันชื่อซ้ำ
        $targetDir = "uploads/";
        $targetFile = $targetDir . $fileName;

        // ตรวจสอบว่าไฟล์ที่อัปโหลดมีขนาดที่เหมาะสมหรือไม่ (ขนาดไม่เกิน 2MB)
        if ($_FILES['profile_picture']['size'] > 2097152) {
            echo "ขนาดไฟล์ใหญ่เกินไป, กรุณาอัปโหลดไฟล์ที่มีขนาดไม่เกิน 2MB";
        } else {
            // ย้ายไฟล์จาก temp ไปยังโฟลเดอร์ที่กำหนด
            if (move_uploaded_file($fileTmpPath, $targetFile)) {
                // อัปเดตรูปโปรไฟล์ในฐานข้อมูล
                $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, department = ?, position = ?, picture_url = ? WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssssi", $first_name, $last_name, $email, $phone, $department, $position, $fileName, $user_id);

                if ($stmt->execute()) {
                    $_SESSION['message'] = "ข้อมูลโปรไฟล์ถูกอัปเดตแล้ว!";
                    header("Location: profile.php");
                    exit();
                } else {
                    echo "เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . $conn->error;
                }
                $stmt->close();
            } else {
                echo "เกิดข้อผิดพลาดในการอัปโหลดไฟล์";
            }
        }
    } else {
        // หากไม่มีการอัปโหลดไฟล์ใหม่ ให้ใช้รูปเดิม
        $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, department = ?, position = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $first_name, $last_name, $email, $phone, $department, $position, $user_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "ข้อมูลโปรไฟล์ถูกอัปเดตแล้ว!";
            header("Location: profile.php");
            exit();
        } else {
            echo "เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . $conn->error;
        }
        $stmt->close();
    }
}

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
            width: 50%;
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 20px;
        }

        .profile-header {
            background-color: #3c50c1;
            color: white;
            text-align: center;
            padding: 15px;
            border-radius: 7px;
        }

        .profile-header h1 {
            margin: 0;
            font-size: 22px;
        }

        .profile-image {
            text-align: center;
            margin-top: 20px;
        }

        .profile-image img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 5px solid #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            cursor: pointer;
        }

        .form-container {
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 15px;
            margin-top: 15px;
        }

        .form-group label {
            font-size: 14px;
            color: #333;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 5px;
        }

        .form-actions {
            text-align: center;
            margin-top: 30px;
        }

        .form-actions .btn {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            color: white;
            background-color: #28a745;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form-actions .btn:hover {
            background-color: #218838;
        }

        .form-actions .btn.cancel {
            background-color: #dc3545;
        }

        .form-actions .btn.cancel:hover {
            background-color: #c82333;
        }

        .file-input {
            display: none;
        }
        .menu-item:hover, .menu-item.active {
            background-color: #0066cc;
        }
        .profile-image img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 5px solid #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
        <div class="profile-header">
            <h1>แก้ไขโปรไฟล์</h1>
        </div>
        <!-- ฟอร์มสำหรับการอัปเดตข้อมูลโปรไฟล์ -->
        <form id="profileForm" action="Edit_Profile.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
            <input type="hidden" name="old_profile_picture" value="<?php echo htmlspecialchars($user['picture_url'], ENT_QUOTES, 'UTF-8'); ?>">

            <div class="form-group">
    <label for="first_name"><i class="fas fa-user"></i> ชื่อ:</label>
    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name'], ENT_QUOTES, 'UTF-8'); ?>" required>
</div>

<div class="form-group">
    <label for="last_name"><i class="fas fa-user-tag"></i> นามสกุล:</label>
    <input type="text" id="last_name" name="last_name" value="<?php echo isset($user['last_name']) ? htmlspecialchars($user['last_name'], ENT_QUOTES, 'UTF-8') : ''; ?>" required>
</div>

<div class="form-group">
    <label for="email"><i class="fas fa-envelope"></i> อีเมล:</label>
    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?>" required>
</div>

<div class="form-group">
    <label for="phone"><i class="fas fa-phone-alt"></i> เบอร์โทรศัพท์:</label>
    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'], ENT_QUOTES, 'UTF-8'); ?>" required>
</div>

<div class="form-group">
    <label for="department"><i class="fas fa-briefcase"></i> แผนก:</label>
    <input type="text" id="department" name="department" value="<?php echo htmlspecialchars($user['department'], ENT_QUOTES, 'UTF-8'); ?>" required>
</div>

<div class="form-group">
    <label for="position"><i class="fas fa-id-badge"></i> ตำแหน่ง:</label>
    <input type="text" id="position" name="position" value="<?php echo htmlspecialchars($user['position'], ENT_QUOTES, 'UTF-8'); ?>" required>
</div>

<div class="form-group">
    <label for="profile_picture"><i class="fas fa-camera"></i> รูปโปรไฟล์:</label>
    <input type="file" name="profile_picture" accept="image/*">
</div>

            
            <div class="form-actions">
                <button type="submit" class="btn">บันทึก</button>
                <a href="profile.php" class="btn cancel">ยกเลิก</a>
            </div>
        </form>
    </div>

    <script>
        function previewImage(event) {
            var output = document.getElementById('profileImage');
            output.src = URL.createObjectURL(event.target.files[0]);
        }
    </script>

<script>
    // Function to preview the selected image
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('profileImage');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    // Event listener for form submission
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        e.preventDefault(); // ป้องกันการส่งฟอร์มชั่วคราว

        // เรียกป็อปอัพ SweetAlert2
        Swal.fire({
            icon: 'success',
            title: 'บันทึกสำเร็จ',
            text: 'ข้อมูลของคุณได้รับการบันทึกแล้ว!', // เปลี่ยนข้อความที่นี่
            confirmButtonText: 'ตกลง'
        }).then((result) => {
            if (result.isConfirmed) {
                // ส่งฟอร์มหลังจากผู้ใช้กดปุ่ม "ตกลง"
                e.target.submit();
            }
        });
    });

    // เพิ่มข้อความที่มาจาก session ถ้ามี
    const message = "<?php echo isset($_SESSION['message']) ? $_SESSION['message'] : ''; ?>";
    if (message) {
        alert(message);  // แสดงข้อความใน alert ถ้ามี
    }
</script>

<!-- SweetAlert2 library สำหรับการแสดงป็อปอัพ -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


</body>
</html>