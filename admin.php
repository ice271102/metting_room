<?php
session_start(); 
include('db_connection.php');

// Middleware: ตรวจสอบ session และ role
function checkUserSession() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }

    if ($_SESSION['role'] !== 'admin') {
        header('Location: user.php');
        exit;
    }
}

// Call Middleware to validate session
checkUserSession();

// ฟังก์ชันสำหรับจัดการการอัปโหลดไฟล์
function handleFileUpload($file) {
    $allowed_types = ['image/jpeg', 'image/png'];
    if ($file['error'] == 0 && in_array($file['type'], $allowed_types)) {
        $filename = uniqid() . '_' . basename($file['name']); // Generating unique name
        $upload_path = 'uploads/' . $filename;
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            return $filename;
        } else {
            return null; // If file move failed
        }
    }
    return null;
}

// รับค่าจากฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_name = $_POST['room_name'] ?? '';
    $capacity = $_POST['capacity'] ?? '';
    $location = $_POST['location'] ?? '';
    $screen = $_POST['screen'] ?? '';
    $microphone = $_POST['microphone'] ?? '';
    $picture = null;

    // ตรวจสอบการอัปโหลดไฟล์
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] == 0) {
        $picture = handleFileUpload($_FILES['picture']);
    }

    // ตรวจสอบว่าเป็นการอัปเดตห้องประชุมหรือเพิ่มห้องประชุมใหม่
    if (isset($_POST['action']) && $_POST['action'] == 'update') {
        $room_id = $_POST['room_id'];

        // คิวรีเพื่อดึงค่ารูปภาพเดิมในฐานข้อมูล
        $stmt = $conn->prepare("SELECT picture FROM meeting_room WHERE room_id = ?");
        $stmt->bind_param("i", $room_id);
        $stmt->execute();
        $stmt->bind_result($existing_picture);
        $stmt->fetch();
        $stmt->close();

        // หากไม่มีไฟล์อัปโหลด ให้ใช้ค่ารูปภาพเดิม
        if (!$picture) {
            $picture = $existing_picture;
        }

        // SQL สำหรับการอัปเดตข้อมูลห้องประชุม
        $sql = "UPDATE meeting_room SET room_name=?, capacity=?, location=?, screen=?, microphone=?, picture=? WHERE room_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sissssi", $room_name, $capacity, $location, $screen, $microphone, $picture, $room_id);

        if ($stmt->execute()) {
            header('Location: admin.php?success=update'); // Redirect กลับไปที่หน้า admin.php
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        // SQL สำหรับการเพิ่มข้อมูลห้องประชุมใหม่
        $sql = "INSERT INTO meeting_room (room_name, capacity, location, screen, microphone, picture) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sissss", $room_name, $capacity, $location, $screen, $microphone, $picture);

        if ($stmt->execute()) {
            header('Location: admin.php?success=insert'); // หลังจากบันทึกข้อมูลแล้วกลับไปยังหน้าหลัก
            exit;
        } else {
            echo "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $stmt->error;
        }
    }
}
// Fetch meeting rooms from the database
$sql = "SELECT * FROM meeting_room";  // Query to fetch rooms
$result = $conn->query($sql);  // Execute the query

if ($result === false) {
    echo "Error fetching rooms: " . $conn->error;  // Display error if query fails
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจองห้องประชุม PEANE3</title>
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


        .main-content {
            flex: 1;
            padding: 40px 30px;
            background-color: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin: 30px;
            overflow-y: auto;
        }
        .menu-item:hover, .menu-item.active {
      background-color: #0066cc;
    }
        .main-content {
            flex: 1;
            padding: 40px 30px;
            background-color: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin: 30px;
            overflow-y: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .header .title {
            font-size: 1.8em;
            font-weight: 700;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f4f6f9;
            font-weight: bold;
        }

        .btn-unavailable {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px;
            cursor: not-allowed;
        }
        .header-buttons {
    display: flex;
    gap: 10px; /* เว้นระยะห่างระหว่างปุ่ม */
}

.header-buttons button {
    padding: 10px 20px;
    font-size: 1em;
    cursor: pointer;
}
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
}

.header .title {
    font-size: 1.8em;
    font-weight: 700;
    color: #333;
}

.header-buttons {
    display: flex;
    gap: 10px; /* เว้นระยะห่างระหว่างปุ่ม */
}

.header-buttons button {
    padding: 10px 20px;
    font-size: 1em;
    cursor: pointer;
}

/* สไตล์ปุ่มแก้ไข */
#edit-btn {
    background-color: #ffc107; /* สีเหลืองสำหรับปุ่มแก้ไข */
    border: none;
    color: white;
}


.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
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
    <a href="admin.php" class="menu-item active">
    <img src="images/meeting-room (2).png" alt="Meeting Room Icon" width="33" height="33">เช็คห้องประชุม</a>

 
    <a href="Booking_history.php" class="menu-item">
    <img src="images/search.png" alt="Booking History Icon" width="33" height="33">ประวัติการจองของผู้ใช้</a>

    <a href="Booking_statistics.php" class="menu-item">
 <img src="images/analytics.png" alt="Booking Statistics Icon" width="33" height="33">สถิติการจอง</a>

 <a href="name.php" class="menu-item">
    <img src="images/teamwork.png" alt="User Info Icon" width="33" height="33"> ข้อมูลผู้ใช้งาน</a>

    <a href="admin_room_reviews.php" class="menu-item">
    <img src="images/chat-box.png" alt="Review Icon" width="33" height="33">ผลการรีวิวห้องประชุมของผู้ใช้</a>

    <a href="Logout_u.php" class="menu-item">
  <img src="uploads/check-out.png" alt="Sign Out Icon" style="width: 33px; height: 33px;"> ออกจากระบบ</a>
    </div>
</div>
<div class="main-content">
    <div class="header">
        <div class="title">ห้องประชุม</div>
        <div class="header-buttons">
            <!-- ปุ่ม "เพิ่มอุปกรณ์" -->
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDeviceModal">
                <i class="fas fa-plus"></i> เพิ่มห้องประชุม
            </button>
        </div>
    </div>
    <!-- ตารางห้องประชุม -->
    <table class="table">
<thead>
<tr>
    <th style="background-color: #007bff; color: white; text-align: center; vertical-align: middle;">
        <i class="fas fa-sort" style="color: white;"></i> ลำดับที่
    </th>
    <th style="background-color: #007bff; color: white; text-align: center; vertical-align: middle;">
        <i class="fas fa-door-open" style="color: white;"></i> ห้องประชุม
    </th>
    <th style="background-color: #007bff; color: white; text-align: center; vertical-align: middle;">
        <i class="fas fa-users" style="color: white;"></i> ความจุ
    </th>
    <th style="background-color: #007bff; color: white; text-align: center; vertical-align: middle;">
        <i class="fas fa-map-marker-alt" style="color: white;"></i> สถานที่
    </th>
    <th style="background-color: #007bff; color: white; text-align: center; vertical-align: middle;">
        <i class="fas fa-tv" style="color: white;"></i> จอภาพ
    </th>
    <th style="background-color: #007bff; color: white; text-align: center; vertical-align: middle;">
        <i class="fas fa-microphone" style="color: white;"></i> ไมโครโฟน
    </th>
    <th style="background-color: #007bff; color: white; text-align: center; vertical-align: middle;">
        <i class="fas fa-image" style="color: white;"></i> รูปภาพ
    </th>
    <th style="background-color: #007bff; color: white; text-align: center; vertical-align: middle;">
        <i class="fas fa-cogs" style="color: white;"></i> การจัดการ
    </th>
</tr>


</thead>
<tbody>
    <?php
        $index = 1;
        while ($room = $result->fetch_assoc()) { ?>
            <tr>
                <td style="text-align: center; vertical-align: middle;"><?= $index++ ?></td>
                <td style="text-align: center; vertical-align: middle;"><?= $room['room_name'] ?></td>
                <td style="text-align: center; vertical-align: middle;"><?= $room['capacity'] ?> คน</td>
                <td style="text-align: center; vertical-align: middle;"><?= $room['location'] ?></td>
                <td style="text-align: center; vertical-align: middle;"><?= $room['screen'] ?></td>
                <td style="text-align: center; vertical-align: middle;"><?= $room['microphone'] ?></td>
                <td style="text-align: center; vertical-align: middle;">
                    <?php if (!empty($room['picture'])) { ?>
                        <img src="uploads/<?= $room['picture'] ?>" alt="<?= $room['room_name'] ?>" style="width: 150px; height: auto;">
                    <?php } else { ?>
                        <span>ไม่มีรูปภาพ</span>
                    <?php } ?>
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    <button class="btn btn-warning btn-sm edit-btn" 
                            data-room="<?= htmlspecialchars(json_encode($room)) ?>" 
                            data-bs-toggle="modal" data-bs-target="#editRoomModal">
                        <i class="fas fa-edit"></i> แก้ไข
                    </button>
                </td>
            </tr>
        <?php } ?>
</tbody>
</table>
<!-- Add Meeting Room Modal -->
<div class="modal fade" id="addDeviceModal" tabindex="-1" aria-labelledby="addDeviceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header" style="background-color:#007BFF;">
    <h5 class="modal-title" id="addDeviceModalLabel" style="color: white;">เพิ่มห้องประชุม</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

            <div class="modal-body">
                <form action="admin.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="room_name" class="form-label">ห้องประชุม</label>
                        <input type="text" name="room_name" class="form-control" id="room_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="capacity" class="form-label">ความจุ</label>
                        <input type="number" name="capacity" class="form-control" id="capacity" required>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">สถานที่</label>
                        <input type="text" name="location" class="form-control" id="location" required>
                    </div>
                    <div class="mb-3">
                        <label for="screen" class="form-label">จอภาพ</label>
                        <input type="number" name="screen" class="form-control" id="screen" required>
                    </div>
                    <div class="mb-3">
                        <label for="microphone" class="form-label">ไมโครโฟน</label>
                        <input type="number" name="microphone" class="form-control" id="microphone" required>
                    </div>
                    <div class="mb-3">
                        <label for="picture" class="form-label">รูปภาพ</label>
                        <input type="file" name="picture" class="form-control" id="picture">
                    </div>
                    <button type="submit" class="btn btn-primary">เพิ่มห้องประชุม</button>
                </form>
            </div>
        </div>
    </div>
</div><!-- Edit Meeting Room Modal -->
<div class="modal fade" id="editRoomModal" tabindex="-1" aria-labelledby="editRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header" style="background-color: #007BFF;">
    <h5 class="modal-title" id="editRoomModalLabel" style="color: white;">แก้ไขข้อมูลห้องประชุม</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

            <div class="modal-body">
            <form action="admin.php" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="action" value="update">
                <input type="hidden" name="room_id" id="edit-room-id">
                    <div class="mb-3">
                        <label for="edit-room-name" class="form-label">ห้องประชุม</label>
                        <input type="text" name="room_name" class="form-control" id="edit-room-name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-capacity" class="form-label">ความจุ</label>
                        <input type="number" name="capacity" class="form-control" id="edit-capacity" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-location" class="form-label">สถานที่</label>
                        <input type="text" name="location" class="form-control" id="edit-location" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-screen" class="form-label">จอภาพ</label>
                        <input type="number" name="screen" class="form-control" id="edit-screen" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-microphone" class="form-label">ไมโครโฟน</label>
                        <input type="number" name="microphone" class="form-control" id="edit-microphone" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-picture" class="form-label">อัพโหลดรูปภาพ</label>
                        <input type="file" name="picture" class="form-control" id="edit-picture">
                    </div>
                    <button type="submit" <a href="admin.php?room_id=<?= $room['room_id'] ?>"class="btn btn-warning">อัพเดตห้องประชุม</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    // Trigger the modal with the data from the clicked row
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function () {
            const room = JSON.parse(this.getAttribute('data-room'));
            document.getElementById('edit-room-id').value = room.room_id;
            document.getElementById('edit-room-name').value = room.room_name;
            document.getElementById('edit-capacity').value = room.capacity;
            document.getElementById('edit-location').value = room.location;
            document.getElementById('edit-screen').value = room.screen;
            document.getElementById('edit-microphone').value = room.microphone;
        });
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>