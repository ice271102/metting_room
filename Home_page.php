<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก | ระบบจองห้องประชุม</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('images/snapedit_.jpeg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Arial', sans-serif;
        }
        .main-card {
            background-color: rgba(255, 255, 255, 0.85);
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        .btn-main {
            background-color: #007bff;
            border: none;
            border-radius: 30px;
            padding: 12px 30px;
            font-size: 16px;
            color: white;
            width: 100%;
            margin-bottom: 10px;
        }
        .btn-main:hover {
            background-color: #0056b3;
        }
        h1, h3 {
            color: #007bff;
            font-weight: bold;
        }
        p {
            color: #555;
        }
        .logo {
            display: block;
            margin: 0 auto 20px;
            max-width: 100px;
        }
    </style>
</head>
<body>

    <div class="main-card">
        <img src="images/2.png" alt="PEA Logo" class="logo">
        <h1>ระบบจองห้องประชุม</h1>
        <h3>ยินดีต้อนรับ</h3>
        <p>กรุณาเลือกหนึ่งในตัวเลือกด้านล่างเพื่อเริ่มต้นใช้งาน</p>
        <a href="login.php" class="btn btn-main">เข้าสู่ระบบ</a>
        <a href="register.php" class="btn btn-main">สมัครสมาชิก</a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
