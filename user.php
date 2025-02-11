<?php
session_start();
include('db_connection.php');

// Middleware: ตรวจสอบ session และ role
function checkUserSession() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }

    if ($_SESSION['role'] !== 'user') {
        header('Location: admin.php');
        exit;
    }
}

// Call Middleware to validate session
checkUserSession();

// Get the current date and the maximum allowed booking date (7 days from now)
$current_date = date('Y-m-d');
$max_booking_date = date('Y-m-d', strtotime('+30 days'));

// Handle user-selected date, ensuring it's within the valid range
$selected_date = $current_date;
if (isset($_POST['selected_date'])) {
    $user_selected_date = $_POST['selected_date'];
    // Validate that the selected date is within the valid range
    if ($user_selected_date >= $current_date && $user_selected_date <= $max_booking_date) {
        $selected_date = $user_selected_date;
    } else {
        echo "<script>alert('วันที่เลือกต้องไม่เกิน 1เดือนจากวันนี้');</script>";
    }
}

// Fetch available rooms
$sql = "
    SELECT 
        mr.room_id, 
        mr.room_name, 
        mr.capacity, 
        mr.location, 
        mr.screen, 
        mr.microphone, 
        mr.picture,
        (
            SELECT GROUP_CONCAT(time SEPARATOR ',') 
            FROM bookings 
            WHERE bookings.room_id = mr.room_id 
            AND bookings.booking_date = ? 
            AND bookings.status = 'อนุมัติ'
        ) AS booked_times,
        IF(
            EXISTS (
                SELECT 1
                FROM bookings 
                WHERE bookings.room_id = mr.room_id 
                AND bookings.booking_date = ? 
                AND bookings.time = 'เต็มวัน'
                AND bookings.status = 'อนุมัติ'
            ), 1, 0
        ) AS is_full_day_booked
    FROM meeting_room mr";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $selected_date, $selected_date);
$stmt->execute();
$result = $stmt->get_result();

// Handle Booking Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_room_id'])) {
    $room_id = intval($_POST['book_room_id']);
    $purpose = trim($_POST['purpose']);
    $time = $_POST['time'];

    if (empty($purpose) || empty($time)) {
        echo "<script>alert('กรุณากรอกข้อมูลให้ครบ');</script>";
    } else {
        // Check if the full-day booking exists
        $check_full_day_sql = "SELECT * FROM bookings WHERE room_id = ? AND booking_date = ? AND time = 'เต็มวัน' AND status = 'อนุมัติ'";
        $stmt_check_full_day = $conn->prepare($check_full_day_sql);
        $stmt_check_full_day->bind_param("is", $room_id, $selected_date);
        $stmt_check_full_day->execute();
        $full_day_result = $stmt_check_full_day->get_result();

        if ($full_day_result->num_rows > 0 && $time === 'เต็มวัน') {
            // If full-day is booked and trying to book full day, show error
            echo "<script>alert('ห้องประชุมนี้จองเต็มวันแล้ว');</script>";
        } else {
            // Check room availability for selected time
            $check_sql = "SELECT * FROM bookings WHERE room_id = ? AND booking_date = ? AND time = ? AND status = 'อนุมัติ'";
            $stmt_check = $conn->prepare($check_sql);
            $stmt_check->bind_param("iss", $room_id, $selected_date, $time);
            $stmt_check->execute();
            $check_result = $stmt_check->get_result();

            if ($check_result->num_rows > 0) {
                echo "<script>alert('ช่วงเวลาที่เลือกไม่ว่าง');</script>";
            } else {
                // Insert booking
                $insert_booking_sql = "INSERT INTO bookings (user_id, room_id, booking_date, time, purpose, status) 
                                       VALUES (?, ?, ?, ?, ?, 'อนุมัติ')";
                $stmt_booking = $conn->prepare($insert_booking_sql);
                $stmt_booking->bind_param("iisss", $_SESSION['user_id'], $room_id, $selected_date, $time, $purpose);

                if (!$stmt_booking->execute()) {
                    echo "<script>alert('ไม่สามารถจองห้องประชุมได้');</script>";
                } else {
                    echo "<script>alert('จองสำเร็จ!');</script>";
                    header("Location: all_booking_information.php");
                    exit;
                }
            }
        }
    }
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
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet"> <!-- เพิ่ม Flatpickr CSS -->
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
    .user-profile {
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    flex-direction: column;
    text-align: center;
}

.profile-img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    margin-bottom: 10px;
    object-fit: cover;
}

.username {
    font-size: 1.2em;
    font-weight: 600;
    color: #ffffff;
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

    .meeting-rooms {
        display: grid;
        grid-template-columns: repeat(2, 1fr); /* 2 ห้องต่อแถว */
        gap: 30px;
        margin-top: 40px;
    }

    .room-card {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        padding: 20px;
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .room-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.15);
    }

    .room-card h3 {
        font-size: 1.4em;
        font-weight: 700;
        color: #003366;
        margin-bottom: 20px;
    }

    .room-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .room-card p {
        font-size: 0.9em;
        color: #555;
        margin-bottom: 10px;
    }

    .room-card .btn-book, .room-card .btn-unavailable {
        width: 100%;
        padding: 12px;
        font-size: 1.1em;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .btn-book {
        background-color: #28a745;
    }

    .btn-book:hover {
        background-color: #218838;
    }

    .btn-unavailable {
        background-color: #dc3545;
        cursor: not-allowed;
    }

    .btn-unavailable:hover {
        background-color: #c82333;
    }

    footer {
        text-align: center;
        font-size: 0.9em;
        color: #777;
        margin-top: 50px;
    }

    .menu-item:hover, .menu-item.active {
        background-color: #0066cc;
    }

    @media (max-width: 768px) {
        .meeting-rooms {
            grid-template-columns: repeat(1, 1fr); /* 1 ห้องต่อแถว */
        }
    }

    .popup {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        align-items: center;
        justify-content: center;
    }

    .popup-content {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        width: 500px;
    }

    .popup .popup-close {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 1.5em;
        cursor: pointer;
        color: #aaa;
    }

    .popup .popup-close:hover {
        color: #333;
    }

    /* กำหนดสีไอคอน */
    label i {
        margin-right: 8px;  /* ระยะห่างระหว่างไอคอนกับข้อความ */
        color: #007bff;     /* กำหนดสีฟ้าให้กับไอคอน */
    }

    /* ตัวอย่างเพิ่มเติมสำหรับการใช้สีไอคอนที่แตกต่างกัน */
    label .fa-door-open {
        color: #28a745; /* สีเขียวสำหรับไอคอนห้องประชุม */
    }

    label .fa-clipboard-list {
        color: #ff6347; /* สีส้มสำหรับไอคอนจุดประสงค์ */
    }

    label .fa-clock {
        color: #ffc107; /* สีเหลืองสำหรับไอคอนเวลา */
    }

    /* เพิ่มระยะห่างระหว่างแต่ละช่อง */
    .form-group {
        margin-bottom: 15px; /* เพิ่มระยะห่างระหว่างช่อง */
    }

    .form-control {
        width: 100%;
        padding: 10px;
        font-size: 16px;
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
    <a href="profile.php" class="menu-item">
  <img src="images/verified-account (1).png" alt="Icon" style="width: 33px; height: 33px;"> โปรไฟล์
</a>

<a href="user.php" class="menu-item active">
  <img src="images/seo.png" alt="Search Icon" style="width: 33px; height: 33px"> จองห้องประชุม
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

<div class="main-content">
    <div class="header">
<div class="title">การจองห้องประชุม</div>
<form method="POST" action="">
<div class="time-selector">
            <label for="date">เลือกวันที่:(ปี/เดือน/วัน)</label>
            <input type="text" id="calendar" name="selected_date" class="form-control" value="<?= $selected_date ?>" />
        </div>
        <button type="submit" class="btn btn-primary">ดูห้องประชุม</button>
    </form>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/th.js"></script>

<script>
    flatpickr("#datetime-picker", {
        locale: "th",  // ใช้ภาษาไทย
        dateFormat: "d/m/Y",  // รูปแบบการแสดงผลเป็นวัน/เดือน/ปี
        minDate: "today",  // ไม่อนุญาตให้เลือกวันที่ในอดีต
        onChange: function(selectedDates, dateStr, instance) {
            // แปลงปีจาก ค.ศ. เป็น พ.ศ.
            let year = selectedDates[0].getFullYear();
            let buddhistYear = year + 543;  // เพิ่ม 543 เพื่อแปลงเป็น พ.ศ.
            let formattedDate = dateStr.replace(year, buddhistYear);  // แทนที่ปี ค.ศ. ด้วย พ.ศ.
            instance.input.value = formattedDate;  // ตั้งค่าให้กับ input
        },
        onReady: function(selectedDates, dateStr, instance) {
            // แปลงปีในปฏิทินจาก ค.ศ. เป็น พ.ศ.
            let calendar = instance.calendarContainer;
            let yearElements = calendar.querySelectorAll('.flatpickr-year');
            yearElements.forEach(function(yearElement) {
                let currentYear = parseInt(yearElement.innerText, 10);
                yearElement.innerText = currentYear + 543;  // แปลงปี ค.ศ. เป็น พ.ศ.
            });
        },
    });
</script>
</div>
    <div class="meeting-rooms">
    <?php while ($room = $result->fetch_assoc()) { 
    // แยกช่วงเวลาที่จองแล้วออกมา
    $booked_times = explode(",", $room['booked_times']);
    ?>
    <div class="room-card">
        <h3><?= htmlspecialchars($room['room_name']) ?></h3>
        <img src="uploads/<?= htmlspecialchars($room['picture']); ?>" alt="Room Image">
        <p><i class="fas fa-users"></i> ความจุ: <?= $room['capacity'] ?> คน</p>
        <p><i class="fas fa-map-marker-alt"></i> ตำแหน่ง: <?= $room['location'] ?></p>
        <p><i class="fas fa-tv"></i> จอ: <?= $room['screen'] ?>, <i class="fas fa-microphone"></i> ไมค์: <?= $room['microphone'] ?></p> 
     <!-- ตรวจสอบสถานะเต็มวัน -->
     <?php if ($room['is_full_day_booked'] == 1 || (in_array('ช่วงเช้า', $booked_times) && in_array('ช่วงบ่าย', $booked_times))) { ?>
    <!-- หากห้องถูกจองเต็มวันหรือจองครบสองช่วงเวลา ให้แสดงปุ่มสีแดง -->
    <button class="btn-unavailable" style="background-color: red;" disabled>
        ห้องประชุมนี้จองครบทั้งวันแล้ว
    </button>
<?php } else { ?>
    <!-- แสดงปุ่มการจองสำหรับช่วงเวลา -->
    <?php if (!in_array('ช่วงเช้า', $booked_times)) { ?>
        <button class="btn-book" onclick="openBookingPopup(<?= $room['room_id'] ?>, '<?= $room['room_name'] ?>', 'ช่วงเช้า')">
            จองช่วงเช้า (08:30-12:00)
        </button>
    <?php } else { ?>
        <button class="btn-unavailable" disabled>ช่วงเช้าถูกจองแล้ว</button>
    <?php } ?>

    <?php if (!in_array('ช่วงบ่าย', $booked_times)) { ?>
        <button class="btn-book" onclick="openBookingPopup(<?= $room['room_id'] ?>, '<?= $room['room_name'] ?>', 'ช่วงบ่าย')">
            จองช่วงบ่าย (13:00-16:30)
        </button>
    <?php } else { ?>
        <button class="btn-unavailable" disabled>ช่วงบ่ายถูกจองแล้ว</button>
    <?php } ?>

    <?php if (!in_array('ช่วงเช้า', $booked_times) && !in_array('ช่วงบ่าย', $booked_times)) { ?>
        <button class="btn-book" onclick="openBookingPopup(<?= $room['room_id'] ?>, '<?= $room['room_name'] ?>', 'เต็มวัน')">
            จองเต็มวัน (08:30-16:30)
        </button>
    <?php } ?>
<?php } ?>
</div>
<?php } ?>
    <!-- Booking Popup -->
    <div id="booking-popup" class="popup">
    <div class="popup-content">
        <span class="popup-close" onclick="closePopup()">×</span>
        <h3>กรุณากรอกข้อมูลการจองห้องประชุม</h3>
        <form action="" method="POST">
            <input type="hidden" id="book_room_id" name="book_room_id" value="">
            <div class="form-group">
                <label for="room_name"><i class="fas fa-door-open"></i> ชื่อห้องประชุม</label>
                <input type="text" id="room_name" name="room_name" class="form-control" readonly>
            </div>
            <div class="form-group">
                <label for="purpose"><i class="fas fa-clipboard-list"></i> จุดประสงค์ของการจอง</label>
                <input type="text" id="purpose" name="purpose" class="form-control" required>
            </div>
            <!-- เพิ่มส่วนนี้สำหรับการเลือกวันที่ -->
            <div class="form-group">
            <label for="selected_date">เลือกวันที่จอง:(ปี/เดือน/วัน)</label>
        <input type="text" id="selected_date" name="selected_date" class="form-control" 
               value="<?php echo htmlspecialchars($selected_date); ?>" 
               placeholder="เลือกวันที่ (ไม่เกิน 1 เดือน)" required 
               data-input type="text">  </div>
            <div class="mb-3">
                <label for="time" class="form-label">ช่วงเวลา</label>
                <select class="form-select" id="time" name="time" required>
                    <option value="" disabled selected>เลือกช่วงเวลา</option>
                    <option value="ช่วงเช้า">ช่วงเช้า (08:30-12:00)</option>
                    <option value="ช่วงบ่าย">ช่วงบ่าย (13:00-16:30)</option>
                    <option value="เต็มวัน">เต็มวัน (08:30-16:30)</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success mt-3" onclick="showSuccessPopup(event)">ยืนยันการจอง</button>

        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/th.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    // Initialize flatpickr for meeting date
    flatpickr("#meeting-date", {
        dateFormat: "Y-m-d",
        minDate: "today", // Prevent selecting past dates
        locale: "th",
    });

    // Initialize flatpickr for time inputs
    flatpickr(".timepicker", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        locale: "th",
    });
    flatpickr("#calendar", {
        minDate: "today",  // Disable past dates
        dateFormat: "Y-m-d", // Set the date format
    });
    // Function to open booking popup and pre-fill data
    function openBookingPopup(roomId, roomName, time) {
    document.getElementById("book_room_id").value = roomId;
    document.getElementById("room_name").value = roomName;
    document.getElementById("time").value = time;
    document.getElementById("booking-popup").style.display = "flex";
}
    // Close the booking popup
    function closePopup() {
        document.getElementById("booking-popup").style.display = "none";
    }

    // Close popup if clicking outside of it
    window.onclick = function (event) {
        if (event.target == document.getElementById("booking-popup")) {
            closePopup();
        }
    };
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function showSuccessPopup(event) {
    event.preventDefault(); // ป้องกันการ submit form ทันที

    // แสดงป๊อปอัพแจ้งเตือนด้วย SweetAlert2
    Swal.fire({
        title: "จองสำเร็จ!",
        text: "จองห้องประชุมเรียบร้อยแล้ว",
        icon: "success",
        confirmButtonText: "ตกลง"
    }).then((result) => {
        if (result.isConfirmed) {
            // ดำเนินการ submit form หลังจากผู้ใช้กด "ตกลง"
            event.target.closest("form").submit();
        }
    });
}
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelector("button[type='submit']").addEventListener("click", function(event) {
            const selectedDateInput = document.querySelector("input[name='selected_date']");
            if (!selectedDateInput) return; // ถ้าไม่มี input วันที่ ให้ return

            const selectedDate = new Date(selectedDateInput.value);
            const today = new Date();
            const maxDate = new Date();
            maxDate.setMonth(today.getMonth() + 1); // ตั้งค่าวันที่ให้เป็น 1 เดือนจากวันนี้

            if (selectedDate > maxDate) {
                event.preventDefault(); // ป้องกันการ submit ฟอร์ม
                Swal.fire({
                    icon: "error",
                    title: "วันที่เลือกไม่ถูกต้อง",
                    text: "วันที่เลือกต้องไม่เกิน 1 เดือนจากวันนี้!",
                    confirmButtonText: "ตกลง"
                });
            }
        });
    });
</script>

</body>
</html>