<?php
session_start(); // Make sure session is started to access user data

// จำนวนการจองที่จะแสดงต่อหน้า
$records_per_page = 5;

// คำนวณหน้าปัจจุบันจาก query string หรือใช้หน้า 1 เป็นค่าเริ่มต้น
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($page - 1) * $records_per_page;

// Ensure the user is logged in and their user_id is available
if (!isset($_SESSION['user_id'])) {
    die("User not logged in");
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// เชื่อมต่อกับฐานข้อมูล
$conn = new mysqli("localhost", "root", "", "meetingroom","4306");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$user_id = $_SESSION['user_id'];

// Modify query to fetch all users ordered by `picture_url`
$sql = "SELECT username, picture_url FROM users ORDER BY picture_url ASC";
$result = $conn->query($sql);

$user_profiles = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $user_profiles[] = $row;
    }
}


// Fetch current user's data for session display
$current_user_sql = "SELECT username, picture_url FROM users WHERE user_id = ?";
$stmt = $conn->prepare($current_user_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$current_user_result = $stmt->get_result();

if ($current_user_result->num_rows > 0) {
    $current_user = $current_user_result->fetch_assoc();
    $username = $current_user['username'];
    $picture_url = $current_user['picture_url'];

    // ตรวจสอบว่าไฟล์มีอยู่ในเซิร์ฟเวอร์หรือไม่
    if (empty($picture_url) || !file_exists($picture_url)) {
        $picture_url = ''; // รูปภาพเริ่มต้น
    }
} else {
    $username = 'Guest';
    $picture_url = '';
}

// SQL query to select bookings for the logged-in user with limit
$sql = "SELECT bookings.*, meeting_room.room_name 
        FROM bookings 
        LEFT JOIN meeting_room ON bookings.room_id = meeting_room.room_id
        WHERE bookings.user_id = ? 
        ORDER BY bookings.booking_date DESC, bookings.time DESC
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param("iii", $user_id, $start_from, $records_per_page);
if (!$stmt->execute()) {
    die("Error executing query: " . $stmt->error);
}
$result = $stmt->get_result();

// Count the total number of records for the logged-in user
$sql_count = "SELECT COUNT(*) FROM bookings WHERE user_id = ?";
$stmt_count = $conn->prepare($sql_count);
if ($stmt_count === false) {
    die("Error preparing count statement: " . $conn->error);
}
$stmt_count->bind_param("i", $user_id);
if (!$stmt_count->execute()) {
    die("Error executing count query: " . $stmt_count->error);
}
$result_count = $stmt_count->get_result();
$row_count = $result_count->fetch_row();
$total_records = $row_count[0];
$total_pages = ceil($total_records / $records_per_page);

// ยกเลิกการจองห้องประชุม
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql_delete = "DELETE FROM bookings WHERE booking_id = ? AND user_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    if ($stmt_delete === false) {
        die("Error preparing delete statement: " . $conn->error);
    }
    $stmt_delete->bind_param("ii", $delete_id, $user_id);
    if ($stmt_delete->execute()) {
        echo json_encode(['status' => 'deleted']); // ส่งสถานะการลบกลับไป
        exit();
    } else {
        echo json_encode(['status' => 'error']); // ส่งสถานะผิดพลาดกลับไป
        exit();
    }
}

// Display bookings in the table
$counter = $start_from + 1;
?>
<!DOCTYPE html>
<html lang="en">
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
        .main-container {
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }

        .content-container {
            width: 100%;
            max-width: 900px;
            background-color: white;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 20px;
        }

        .header h1 {
            font-size: 24px;
            text-align: center;
            color: #3c50c1;
            margin-bottom: 20px;
        }

        .table-container {
            overflow-x: auto;
        }

        .booking-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        .booking-table th, .booking-table td {
            text-align: left;
            padding: 12px;
            border: 1px solid #ddd;
        }

        .booking-table th {
            background-color: #3c50c1;
            color: white;
        }

        .menu-item:hover, .menu-item.active {
            background-color: #0066cc;
        }

        .btn {
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            margin: 5px;
        }
        .modal-title {
    color: #fff;  /* สีขาว */
    background-color: #007bff;  /* พื้นหลังฟ้า */
    padding: 10px;  /* เพิ่มพื้นที่รอบๆ ข้อความ */
    border-radius: 5px;  /* มุมมน */
}


        .btn.view { background-color: #17a2b8; color: white; }
        .btn.delete { background-color: #dc3545; color: white; }
        
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

<a href="user.php" class="menu-item">
  <img src="images/seo.png" alt="Search Icon" style="width: 33px; height: 33px;"> จองห้องประชุม
</a>

<a href="all_booking_information.php" class="menu-item active">
  <img src="images/time-management.png" alt="Calendar Check Icon" style="width: 33px; height: 33px;"> ประวัติการจองของฉัน
</a>

<a href="Review_Meeting_Room.php" class="menu-item">
  <img src="images/shooting-star.png" alt="Star Icon" style="width: 33px; height: 33px;"> รีวิวให้คะแนนห้องประชุม
</a>

<a href="room_reviews.php" class="menu-item ">
  <img src="images/chat-bubbles.png" alt="Comments Icon" style="width: 33px; height: 33px;"> ผลการรีวิวห้องประชุม
</a>

<a href="Logout_u.php" class="menu-item">
  <img src="uploads/check-out.png" alt="Sign Out Icon" style="width: 35px; height: 35px;"> ออกจากระบบ
</a>

    </div>
</div>

<div class="main-container">
    <div class="content-container">
        <h1 class="text-center text-primary">ระบบจองห้องประชุม</h1>
        <div class="table-container">
            <table class="booking-table table table-striped" id="booking-table">
            <thead>
    <tr>
        <th style="text-align: center;"><i class="fas fa-sort"></i> ลำดับ</th>
        <th style="text-align: center;"><i class="fas fa-door-open"></i> ห้องประชุม</th>
        <th style="text-align: center;"><i class="fas fa-calendar-alt"></i> วันที่</th>
        <th style="text-align: center;"><i class="fas fa-clock"></i> เวลา</th>
        <th style="text-align: center;"><i class="fas fa-clipboard-list"></i> เรื่อง</th>
        <th style="text-align: center;"><i class="fas fa-check-circle"></i> สถานะ</th>
        <th style="text-align: center;"><i class="fas fa-cogs"></i> จัดการ</th>
    </tr>
</thead>
        <tbody>
                    
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr data-id='" . $row["booking_id"] . "' id='booking-" . $row["booking_id"] . "'>";
                        echo "<td>" . $counter . "</td>";
                        echo "<td>" . $row["room_name"] . "</td>";
                        echo "<td>" . $row["booking_date"] . "</td>";
                        echo "<td>" . $row["time"]  ."</td>";
                        echo "<td>" . $row["purpose"] . "</td>";
                        echo "<td><span class='btn btn-warning'>" . $row['status'] . "</span></td>";
                        echo "<td>
                        <div class='btn-group'>
                            <button class='btn view' data-bs-toggle='modal' data-bs-target='#viewModal' onclick='viewDetails(" . json_encode($row) . ")'>แสดง</button>
                            <button class='btn delete' onclick='confirmDelete(this)' data-id=\"" . $row['booking_id'] . "\">ยกเลิก</button>
                        </div>
                      </td>";
                
                        echo "</tr>";
                        $counter++;
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>ไม่พบข้อมูลการจอง</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
        <!-- Pagination links -->
        <div class="pagination">
            <?php
            for ($i = 1; $i <= $total_pages; $i++) {
                echo "<a href='?page=" . $i . "' class='btn btn-primary'>" . $i . "</a> ";
            }
            ?>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">รายละเอียดการจอง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="view-details">
                <!-- Dynamic content will be inserted here -->
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function viewDetails(data) {
        var content = `
            <strong><i class="fas fa-door-open" style="color: #007bff;"></i> ห้องประชุม:</strong> ${data.room_name}<br>
            <strong><i class="fas fa-calendar-alt" style="color: #28a745;"></i> วันที่:</strong> ${data.booking_date}<br>
            <strong><i class="fas fa-clock" style="color: #fd7e14;"></i> เวลา:</strong> ${data.time} <br>
            <strong><i class="fas fa-clipboard-list" style="color: #ffc107;"></i> เรื่อง:</strong> ${data.purpose}<br>
            <strong><i class="fas fa-check-circle" style="color: #17a2b8;"></i> สถานะ:</strong> ${data.status}
        `;
        $("#view-details").html(content);
    }

    function confirmDelete(button) {
        const bookingId = button.getAttribute('data-id'); // ดึง booking_id จาก data-id

        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: 'การจองนี้จะถูกยกเลิกและไม่สามารถกู้คืนได้!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ใช่, ยกเลิกเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                // ทำการลบการจองผ่าน AJAX
                fetch(`?delete_id=${bookingId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'deleted') {
                            Swal.fire(
                                'ลบสำเร็จ!',
                                'การจองของคุณถูกลบแล้ว.',
                                'success'
                            ).then(() => {
                                // ลบแถวที่เกี่ยวข้องในตาราง
                                const row = document.getElementById(`booking-${bookingId}`);
                                if (row) row.remove();
                            });
                        } else {
                            Swal.fire('ผิดพลาด!', 'ไม่สามารถลบการจองได้.', 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('ผิดพลาด!', 'เกิดข้อผิดพลาดในการลบข้อมูล.', 'error');
                    });
            }
        });
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>