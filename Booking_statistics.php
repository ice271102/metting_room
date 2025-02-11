<?php
include('db_connection.php');

// ดึงข้อมูลห้องประชุมทั้งหมด
$sqlRooms = "SELECT room_id, room_name FROM meeting_room";
$resultRooms = $conn->query($sqlRooms);
$rooms = [];
while ($row = $resultRooms->fetch_assoc()) {
    $rooms[] = $row;
}

// ดึงข้อมูลการจองห้องประชุมในช่วงเวลาที่เลือก
$bookingData = [];
if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];

    // ใช้ LEFT JOIN เพื่อให้ห้องที่ไม่มีการจองแสดงเป็น 0
    $sqlBookingData = "
        SELECT meeting_room.room_name, 
               COUNT(bookings.booking_id) AS booking_count
        FROM meeting_room
        LEFT JOIN bookings ON bookings.room_id = meeting_room.room_id 
                           AND bookings.booking_date BETWEEN '$startDate' AND '$endDate'
        GROUP BY meeting_room.room_name";
} else {
    // ดึงข้อมูลการจองทั้งหมด
    $sqlBookingData = "
        SELECT meeting_room.room_name, 
               COUNT(bookings.booking_id) AS booking_count
        FROM meeting_room
        LEFT JOIN bookings ON bookings.room_id = meeting_room.room_id
        GROUP BY meeting_room.room_name";
}
$resultBookingData = $conn->query($sqlBookingData);

while ($row = $resultBookingData->fetch_assoc()) {
    $bookingData[] = $row;
}

// ดึงข้อมูลจากตาราง users
$sqlUsers = "SELECT COUNT(*) AS total_users FROM users";
$resultUsers = $conn->query($sqlUsers);
$totalUsers = $resultUsers->fetch_assoc()['total_users'];

// ดึงข้อมูลจากตาราง meeting_room
$sqlRooms = "SELECT COUNT(*) AS total_rooms FROM meeting_room";
$resultRooms = $conn->query($sqlRooms);
$totalRooms = $resultRooms->fetch_assoc()['total_rooms'];

// ดึงข้อมูลการจองทั้งหมด
$sqlBookings = "SELECT COUNT(*) AS total_bookings FROM bookings";
$resultBookings = $conn->query($sqlBookings);
$totalBookings = $resultBookings->fetch_assoc()['total_bookings'];

// ดึงห้องที่จองมากที่สุด
$sqlMostBookedRoom = "
    SELECT meeting_room.room_name, COUNT(bookings.booking_id) AS booking_count
    FROM bookings
    JOIN meeting_room ON bookings.room_id = meeting_room.room_id
    GROUP BY meeting_room.room_name
    ORDER BY booking_count DESC LIMIT 1";
$resultMostBookedRoom = $conn->query($sqlMostBookedRoom);
$mostBookedRoom = $resultMostBookedRoom->fetch_assoc();

// ดึงข้อมูลเกี่ยวกับความจุ, จอภาพ, ไมโครโฟน และ รูปภาพ
$sqlRoomDetails = "
    SELECT SUM(capacity) AS total_capacity, 
           SUM(screen) AS total_projectors, 
           SUM(microphone) AS total_microphones, 
           COUNT(picture) AS total_pictures 
    FROM meeting_room";
$resultRoomDetails = $conn->query($sqlRoomDetails);
$roomDetails = $resultRoomDetails->fetch_assoc();

// ปิดการเชื่อมต่อ
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สถิติการจองห้องประชุม</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* CSS ปรับปรุงใหม่ */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Roboto', sans-serif; background-color: #f4f6f9; display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background-color: #6699FF; color: white; padding: 30px 20px; display: flex; flex-direction: column; justify-content: flex-start; flex-shrink: 0; height: 100vh; position: fixed; }
        .logo { font-size: 1.5em; font-weight: 700; color: #ffffff; margin-bottom: 30px; display: flex; align-items: center; }
        .logo-img { width: 40px; height: auto; margin-right: 10px; }
        .menu { padding: 30px 20px; background-color: #6699FF; color: white; display: flex; flex-direction: column; justify-content: flex-start; }
        .menu-item { font-size: 1.1em; color: white; text-decoration: none; padding: 12px; margin-bottom: 12px; border-radius: 6px; transition: background-color 0.3s ease; }
        .menu-item:hover { background-color: #A8D8FF; }
        .menu-item.active { background-color: #0066cc; }
        .section-title { font-size: 14px; font-weight: bold; margin: 15px 0 5px 20px; }
        .content { margin-left: 260px; flex-grow: 1; padding: 20px; }
        .statistics-container { max-width: 100%; margin: 0 auto; padding: 20px; background: #fff; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border-radius: 10px; overflow-x: auto; }
        h1 { text-align: center; color: #3c50c1; }
        .stat-summary { margin-bottom: 20px; }
        .stat-summary p { font-size: 16px; margin: 5px 0; }
        .filter-section { margin-top: 20px; text-align: center; }
        .filter-section label { margin-right: 10px; }
        canvas { max-width: 100%; height: auto; }
        .charts-container { display: flex; flex-direction: column; justify-content: space-between; gap: 20px; margin-top: 40px; }
        .chart { display: flex; justify-content: center; align-items: center; flex: 1; min-width: 300px; max-width: 800px; max-height: 500px; margin-bottom: 20px; }
        .info-box-container { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-top: 20px; }
        .info-box { padding: 20px; background-color:rgb(230, 248, 250); border: 1px solid #ddd; border-radius: 8px; text-align: center; }
        .info-box h4 { margin: 0; font-size: 16px; color: #4b0082; }
        .info-box p { margin: 10px 0 0; font-size: 20px; color: #6a0dad; font-weight: bold; }
        
        /* ปรับปุ่มกรองข้อมูล */
        button {
            background-color:#42bd41; 
            color: white; 
            padding: 10px 20px; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #45a049;
        }

        /* สีสันให้กับแต่ละปุ่มกรอง */
        input[type="date"] {
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-right: 10px;
            transition: border-color 0.3s ease;
        }
        input[type="date"]:focus {
            border-color:#42bd41;
        }
        .info-box i {
        font-size: 25px; /* ขนาดไอคอนเท่าเดิม */
        margin-bottom: 8px;
    }
    /* กำหนดสีเฉพาะให้ไอคอนแต่ละอัน */
    .info-box i.fa-users {
        color: #17a2b8; /* สีฟ้า */
    }

    .info-box i.fa-door-open {
        color: #28a745; /* สีเขียว */
    }

    .info-box i.fa-tv {
        color: #ffc107; /* สีเหลือง */
    }

    .info-box i.fa-microphone {
        color: #dc3545; /* สีแดง */
    }

    .info-box i.fa-images {
        color: #6f42c1; /* สีม่วง */
    }

    .info-box h4 {
        margin: 10px 0;
        font-size: 18px;
        font-weight: bold;
    }

    .info-box p {
        font-size: 16px;
        margin: 0;
        color: #333;
    }
    .info-box {
        background-color:rgb(239, 255, 253); /* พื้นหลังสีเทาอ่อน */
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .info-box-link {
    text-decoration: none; /* ลบขีดเส้นใต้จากลิงค์ */
    color: inherit; /* ใช้สีจาก parent */
}

    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="images/2.png" alt="PEANE3 Logo" class="logo-img">
            PEANE3
        </div>
        <div class="menu">
    <a href="admin.php" class="menu-item ">
    <img src="images/meeting-room (2).png" alt="Meeting Room Icon" width="33" height="33">เช็คห้องประชุม</a>

 
    <a href="Booking_history.php" class="menu-item ">
    <img src="images/search.png" alt="Booking History Icon" width="33" height="33">ประวัติการจองของผู้ใช้</a>

    <a href="Booking_statistics.php" class="menu-item active">
 <img src="images/analytics.png" alt="Booking Statistics Icon" width="33" height="33">สถิติการจอง</a>

 <a href="name.php" class="menu-item">
    <img src="images/teamwork.png" alt="User Info Icon" width="33" height="33"> ข้อมูลผู้ใช้งาน</a>

    <a href="admin_room_reviews.php" class="menu-item">
    <img src="images/chat-box.png" alt="Review Icon" width="33" height="33">ผลการรีวิวห้องประชุมของผู้ใช้</a>

    <a href="Logout_u.php" class="menu-item">
  <img src="uploads/check-out.png" alt="Sign Out Icon" style="width: 33px; height: 33px;"> ออกจากระบบ</a>
    </div>
    </div>

    <div class="content">
        <div class="statistics-container">
            <h1>สถิติการจองห้องประชุม</h1>

            <div class="stat-summary">
                <p>จำนวนการจองทั้งหมด: <span id="total-bookings"><?php echo $totalBookings; ?></span></p>
                <p>ห้องที่ถูกจองมากที่สุด: <span id="most-booked-room"><?php echo $mostBookedRoom['room_name']; ?></span></p>
            </div>

            <div class="info-box-container">
    <a href="name.php" class="info-box-link">
        <div class="info-box">
            <i class="fas fa-users"></i>
            <h4>จำนวนผู้ใช้</h4>
            <p id="total-users"><?php echo $totalUsers; ?></p>
        </div>
    </a>

    <a href="admin.php" class="info-box-link">
        <div class="info-box">
            <i class="fas fa-door-open"></i>
            <h4>ห้องประชุม</h4>
            <p id="rooms"><?php echo $totalRooms; ?></p>
        </div>
    </a>

    <a href="admin.php" class="info-box-link">
        <div class="info-box">
            <i class="fas fa-users"></i>
            <h4>ความจุ</h4>
            <p id="capacity"><?php echo $roomDetails['total_capacity']; ?></p>
        </div>
    </a>

    <a href="admin.php" class="info-box-link">
        <div class="info-box">
            <i class="fas fa-tv"></i>
            <h4>จอภาพ</h4>
            <p id="projector"><?php echo $roomDetails['total_projectors']; ?></p>
        </div>
    </a>

    <a href="admin.php" class="info-box-link">
        <div class="info-box">
            <i class="fas fa-microphone"></i>
            <h4>ไมโครโฟน</h4>
            <p id="microphone"><?php echo $roomDetails['total_microphones']; ?></p>
        </div>
    </a>

    <a href="admin.php" class="info-box-link">
        <div class="info-box">
            <i class="fas fa-images"></i>
            <h4>รูปภาพ</h4>
            <p id="pictures"><?php echo $roomDetails['total_pictures']; ?></p>
        </div>
    </a>
</div>


        </div>
            <div class="charts-container">
                <div class="chart">
                    <canvas id="roomBookingChart"></canvas>
                </div>
            </div>

            <div class="filter-section">
                <form method="POST" action="Booking_statistics.php">
                    <label for="start_date">เลือกช่วงเวลา:</label>
                    <input type="date" name="start_date" id="start_date">
                    <input type="date" name="end_date" id="end_date">
                    <button type="submit">แสดงข้อมูล</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        const roomBookingData = <?php echo json_encode($bookingData); ?>;
        const roomLabels = roomBookingData.map(item => item.room_name);
        const roomBookingCounts = roomBookingData.map(item => item.booking_count);

        const ctx = document.getElementById('roomBookingChart').getContext('2d');
        const roomBookingChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: roomLabels,
                datasets: [{
                    label: 'จำนวนการจองห้องประชุม',
                    data: roomBookingCounts,
                    backgroundColor: '#ad1457',
                    borderColor: '#ad1457',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { 
                        beginAtZero: true 
                    }
                }
            }
        });
    </script>
</body>
</html>