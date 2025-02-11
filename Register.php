<?php
session_start(); // เริ่มต้นการใช้งาน session

// เรียกไฟล์สำหรับเชื่อมต่อฐานข้อมูล
include 'db_connection.php';

// ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $department = $_POST['department'];
    $position = $_POST['position'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $picture_url = ''; // กำหนดค่าเริ่มต้นให้ picture_url

    // ตรวจสอบว่ารหัสผ่านและยืนยันรหัสผ่านตรงกัน
    if ($password === $confirm_password) {
        // ตรวจสอบชื่อผู้ใช้ซ้ำ
        $check_username_sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($check_username_sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['register_status'] = 'username_exists'; // ชื่อผู้ใช้ซ้ำ
            header("Location: register.php");
            exit();
        }

        // ตรวจสอบรหัสผ่านซ้ำในฐานข้อมูล โดยใช้ password_verify() เพื่อตรวจสอบว่าเป็นรหัสผ่านที่ถูกเข้ารหัสในฐานข้อมูล
        $check_password_sql = "SELECT * FROM users";
        $stmt = $conn->prepare($check_password_sql);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['register_status'] = 'password_exists'; // รหัสผ่านซ้ำ
                header("Location: register.php");
                exit();
            }
        }

        // หากไม่มีชื่อผู้ใช้หรือรหัสผ่านซ้ำ ให้ดำเนินการบันทึกข้อมูล
        $sql = "INSERT INTO users (username, first_name, last_name, email, picture_url, department, position, phone, password, role) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'user')";
        $stmt = $conn->prepare($sql);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // เข้ารหัสรหัสผ่าน
        $stmt->bind_param("sssssssss", $username, $first_name, $last_name, $email, $picture_url, $department, $position, $phone, $hashed_password);

        if ($stmt->execute()) {
            $_SESSION['register_status'] = 'success'; // บันทึกสถานะการลงทะเบียนสำเร็จ
            header("Location: register.php");
            exit();
        } else {
            $_SESSION['register_status'] = 'error'; // บันทึกสถานะการลงทะเบียนล้มเหลว
            header("Location: register.php");
            exit();
        }

        $stmt->close();
    } else {
        $_SESSION['register_status'] = 'password_mismatch'; // ถ้ารหัสผ่านไม่ตรงกัน
        header("Location: register.php");
        exit();
    }

    // ปิดการเชื่อมต่อฐานข้อมูล
    $conn->close();
}
?>




<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าลงทะเบียน</title>
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to bottom, #6a0dad, #d8b4fe);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }
        .register-card {
            width: 100%;
            max-width: 500px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
            background-color: white;
            padding: 2rem;
            border-top: 6px solid #6a0dad; 
        }
        .form-control {
            border-radius: 30px;
        }
        .btn-primary {
            background-color:  #9b59b6; 
            border: none;
            border-radius: 30px;
        }
        .btn-primary:hover {
            background-color: #520d83; /* สีม่วงเข้ม */
        }
        .register-card h3 {
            color: #6a0dad; 
            font-weight: bold;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
    <div class="container-fluid d-flex justify-content-center align-items-center">
        <div class="card register-card">
            <h3 class="text-center mb-4">สร้างบัญชีผู้ใช้</h3>
            <form method="POST" action="register.php">
                <div class="row mb-3">
                    <div class="col">
                        <input type="text" class="form-control" name="first_name" placeholder="ชื่อ" required>
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" name="last_name" placeholder="นามสกุล" required>
                    </div>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="username" placeholder="ชื่อผู้ใช้" required>
                </div>
                <div class="mb-3">
                    <input type="email" class="form-control" name="email" placeholder="ที่อยู่อีเมล์" required>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="department" placeholder="แผนก" required>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="position" placeholder="ตำแหน่ง (เช่น ผู้จัดการ, นักพัฒนา)">
                </div>
                <div class="mb-3">
                    <input type="tel" class="form-control" name="phone" placeholder="เบอร์โทรศัพท์" required>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <input type="password" class="form-control" name="password" placeholder="รหัสผ่าน" required>
                    </div>
                    <div class="col">
                        <input type="password" class="form-control" name="confirm_password" placeholder="ยืนยันรหัสผ่าน" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100">ลงทะเบียนบัญชี</button>
            </form>
        </div>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_SESSION['register_status'])): ?>
                <?php if ($_SESSION['register_status'] == 'success'): ?>
                    Swal.fire({
                        icon: 'success',
                        title: 'ลงทะเบียนสำเร็จ!',
                        text: 'คุณสามารถเข้าสู่ระบบได้แล้ว',
                        confirmButtonText: 'ตกลง'
                    }).then(() => {
                        window.location.href = 'login.php';
                    });
                <?php elseif ($_SESSION['register_status'] == 'error'): ?>
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถลงทะเบียนได้',
                        confirmButtonText: 'ตกลง'
                    });
                <?php elseif ($_SESSION['register_status'] == 'password_mismatch'): ?>
                    Swal.fire({
                        icon: 'error',
                        title: 'รหัสผ่านไม่ตรงกัน',
                        text: 'กรุณาตรวจสอบรหัสผ่านอีกครั้ง',
                        confirmButtonText: 'ตกลง'
                    });
                <?php elseif ($_SESSION['register_status'] == 'username_exists'): ?>
                    Swal.fire({
                        icon: 'error',
                        title: 'ชื่อผู้ใช้มีอยู่แล้ว',
                        text: 'กรุณาใช้ชื่อผู้ใช้อื่น',
                        confirmButtonText: 'ตกลง'
                    });
                <?php elseif ($_SESSION['register_status'] == 'password_exists'): ?>
                    Swal.fire({
                        icon: 'error',
                        title: 'รหัสผ่านมีอยู่แล้ว',
                        text: 'กรุณาใช้รหัสผ่านอื่น',
                        confirmButtonText: 'ตกลง'
                    });
                <?php endif; ?>
                <?php unset($_SESSION['register_status']); ?>
            <?php endif; ?>
        });
    </script>

</body>
</html>
