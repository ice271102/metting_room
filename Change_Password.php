<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เปลี่ยนรหัสผ่าน</title>
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
        .menu-item:hover, .menu-item.active {
      background-color: #0066cc;
    }
        /* เนื้อหา */
        .content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .form-container {
            width: 100%;
            max-width: 400px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-header h2 {
            margin: 0;
            font-size: 20px;
            color: #3c50c1;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 95%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-actions {
            text-align: center;
        }

        .btn {
            padding: 10px 20px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 10px;
            transition: background-color 0.3s;
        }

        .btn.confirm {
            background-color: #28a745;
            color: white;
        }

        .btn.confirm:hover {
            background-color: #218838;
        }

        .btn.cancel {
            background-color: #dc3545;
            color: white;
        }

        .btn.cancel:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <!-- เมนูด้านซ้าย -->
    <div class="sidebar">
    <div class="logo">
        <img src="images/2.png" alt="PEANE3 Logo" class="logo-img">
        PEANE3
    </div>
    <div class="menu">
    <a href="profile.php" class="menu-item"><i class="fas fa-edit"></i> โปรไฟล์</a>
        <a href="user.php" class="menu-item "><i class="fas fa-search"></i> จองห้องประชุม</a>
        <a href="all_booking_information.php" class="menu-item"><i class="fas fa-calendar-check"></i> ข้อมูลการจองทั้งหมด</a>
        <a href="Change_Password.php" class="menu-item active"><i class="fas fa-history"></i> เปลี่ยนรหัสผ่าน</a>
        <a href="Logout_u.php" class="menu-item"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
    </div>
</div>

    <!-- เนื้อหาด้านขวา -->
    <div class="content">
        <div class="form-container">
            <div class="form-header">
                <h2>เปลี่ยนรหัสผ่าน</h2>
            </div>
            <form action="#" method="POST">
                <div class="form-group">
                    <label for="new-password">รหัสผ่านใหม่</label>
                    <input type="password" id="new-password" name="new-password" required>
                </div>
                <div class="form-group">
                    <label for="confirm-password">ยืนยันรหัสผ่านใหม่</label>
                    <input type="password" id="confirm-password" name="confirm-password" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn confirm">ยืนยัน</button>
                    <button type="button" class="btn cancel" onclick="window.location.href='dashboard.php'">ยกเลิก</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>