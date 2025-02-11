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
            width: 100%;
            max-width: 800px;
            height: auto;
            margin: 80px auto;
            background: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 30px;
        }

        .profile-header {
            background-color: #3c50c1;
            color: white;
            text-align: center;
            padding: 20px;
        }

        .profile-header h1 {
            margin: 0;
            font-size: 24px;
        }

        .form-container {
            margin-top: 30px;
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
            background-color: #dc3545;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form-actions .btn:hover {
            background-color: #c82333;
        }

        .form-actions .btn.cancel {
            background-color: #28a745;
        }

        .form-actions .btn.cancel:hover {
            background-color: #218838;
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
            <a href="profile.php" class="menu-item"><i class="fas fa-edit"></i> โปรไฟล์</a>
            <a href="user.php" class="menu-item"><i class="fas fa-search"></i> จองห้องประชุม</a>
            <a href="all_booking_information.php" class="menu-item"><i class="fas fa-calendar-check"></i> ข้อมูลการจองทั้งหมด</a>
            <a href="Change_Password.php" class="menu-item"><i class="fas fa-history"></i> เปลี่ยนรหัสผ่าน</a>
            <a href="Logout_u.php" class="menu-item"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
        </div>
    </div>

    <div class="profile-container">
        <div class="profile-header">
            <h1>ลบบัญชีของคุณ</h1>
        </div>

        <div class="form-container">
            <form action="delete_account.php" method="POST">
                <p>คุณแน่ใจหรือไม่ว่าต้องการลบบัญชีของคุณ? การกระทำนี้ไม่สามารถย้อนกลับได้.</p>
                
                <div class="form-actions">
                    <button type="Home_page.php" class="btn">ลบบัญชี</button>
                    <a href="profile.php" class="btn cancel">ยกเลิก</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
