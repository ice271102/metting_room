<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ออกจากระบบ</title>
    <!-- ใช้ SweetAlert2 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Reset และการตั้งค่าเบื้องต้น */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #3c50c1, #28a745);
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Container หลัก */
        .logout-container {
            max-width: 400px;
            background: #fff;
            padding: 30px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            border-radius: 15px;
            text-align: center;
        }

        /* Header */
        .logout-header {
            margin-bottom: 20px;
        }

        .logout-header h1 {
            font-size: 28px;
            color: #3c50c1;
        }

        .logout-header i {
            font-size: 50px;
            color: #28a745;
            margin-bottom: 10px;
        }

        /* ข้อความ */
        .logout-message {
            font-size: 16px;
            color: #666;
            margin-bottom: 20px;
        }

        /* ปุ่ม */
        .logout-actions .btn {
            padding: 12px 25px;
            font-size: 16px;
            border: none;
            border-radius: 25px;
            color: white;
            background-color: #28a745;
            text-decoration: none;
            cursor: pointer;
            margin: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .logout-actions .btn:hover {
            background-color: #218838;
            transform: scale(1.05);
        }

        /* ปุ่มยกเลิก */
        .btn.cancel {
            background-color: #dc3545;
        }

        .btn.cancel:hover {
            background-color: #c82333;
            transform: scale(1.05);
        }
    </style>
    <script>
        function confirmLogout() {
            // ใช้ SweetAlert2 เพื่อแสดงป็อปอัพ
            Swal.fire({
                title: 'คุณต้องการออกจากระบบหรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ออกจากระบบ',
            cancelButtonText: 'ยกเลิก',
            }).then((result) => {
                if (result.isConfirmed) {
                    // ถ้าผู้ใช้กด "ใช่, ออกจากระบบ!"
                    window.location.href = 'Home_page.php'; // เปลี่ยนเส้นทางไปยังหน้า Home_page.php
                }
            });
        }
    </script>
    
</head>
<body>
    <div class="logout-container">
        <!-- Header -->
        <div class="logout-header">
            <i class="fas fa-sign-out-alt"></i>
            <h1>ออกจากระบบ</h1>
        </div>

        <div class="logout-message">
            <p>คุณต้องการออกจากระบบหรือไม่?</p>
        </div>

        <!-- ปุ่มออกจากระบบและยกเลิก -->
        <div class="logout-actions">
            <!-- ปุ่มออกจากระบบ -->
            <button class="btn" onclick="confirmLogout();">
                ออกจากระบบ
            </button>
            <!-- ปุ่มยกเลิก -->
            <button class="btn cancel" onclick="window.history.back();">
    ยกเลิก
</button>

        </div>
    </div>

    <!-- FontAwesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
