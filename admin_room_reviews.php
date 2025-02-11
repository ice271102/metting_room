<?php
session_start();
include('db_connection.php'); 
// Fetch meeting rooms with aggregated reviews
$sql = "
    SELECT 
        m.room_id, 
        m.room_name, 
        m.capacity, 
        m.location, 
        COALESCE(m.screen, 'ไม่มีข้อมูล') AS screen, 
        COALESCE(m.microphone, 'ไม่มีข้อมูล') AS microphone,
        m.picture, 
        COALESCE(AVG(r.rating), 0) AS avg_rating,
        COUNT(r.id) AS total_reviews
    FROM meeting_room m
    LEFT JOIN reviews r ON m.room_id = r.room_id
    GROUP BY m.room_id";
$result = $conn->query($sql);

// Fetch reviews when room_id is provided via GET
$room_reviews = [];
if (isset($_GET['room_id'])) {
    $room_id = (int)$_GET['room_id'];
    $sqlReviews = "
        SELECT r.rating, r.comment, r.review_date
        FROM reviews r
        WHERE r.room_id = $room_id
        ORDER BY r.review_date DESC";
    $reviewsResult = $conn->query($sqlReviews);
    while ($review = $reviewsResult->fetch_assoc()) {
        $room_reviews[] = $review;
    }
}

$conn->close();
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
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

.menu-item.active {
    background-color: #0066cc;
}

.main-content {
    margin: 20px;
}

.review-title {
    text-align: center;
    font-size: 2em;
    font-weight: bold;
    margin-bottom: 20px;
}

.container {
    display: grid;
    grid-template-columns: repeat(3, 1fr); /* แสดงแถวละ 3 ห้องประชุม */
    gap: 20px;
    margin-top: 20px;
}

.room {
    background-color: #ffffff;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.room img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-bottom: 1px solid #ddd;
}

.room:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
}

.room-details {
    padding: 15px;
}
.room-name {
    font-size: 1.5em;
    font-weight: bold;
    color: #333;
    margin-bottom: 10px;
}
.room-info {
    color: #555;
    margin-bottom: 15px;
}

.btn {
    display: inline-block;
    margin-top: 15px;
    padding: 10px 15px;
    color: white;
    background-color: #007BFF;
    border: none;
    border-radius: 5px;
    text-align: center;
    text-decoration: none;
    cursor: pointer;
}

.btn:hover {
    background-color: #0056b3;
}

.modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 90%;
    max-width: 600px;
    background: white;
    border-radius: 10px;
    box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.3);
    z-index: 1000;
}

.modal-header {
    padding: 15px;
    background-color: #007BFF;
    color: white;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
}

.modal-body {
    padding: 20px;
    max-height: 400px;
    overflow-y: auto;
}

.modal-close {
    position: absolute;
    top: 15px;
    right: 20px;
    font-size: 18px;
    color: white;
    cursor: pointer;
}

.modal-close:hover {
    color: #ddd;
}

.overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
}
.delete-btn {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
        border-radius: 5px;
        transition: 0.3s;
    }

    .delete-btn:hover {
        background-color: #c82333;
    }
    .fa-user {
        color: #007bff; /* สีน้ำเงิน */
    }
    .fa-comment {
        color: #28a745; /* สีเขียว */
    }
    .fa-star {
        color: #ffc107; /* สีเหลือง */
    }
    .fa-calendar-alt {
        color: #17a2b8; /* สีฟ้า */
    }
    </style>
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

    <a href="Booking_statistics.php" class="menu-item ">
 <img src="images/analytics.png" alt="Booking Statistics Icon" width="33" height="33">สถิติการจอง</a>

 <a href="name.php" class="menu-item ">
    <img src="images/teamwork.png" alt="User Info Icon " widt="33" height="33"> ข้อมูลผู้ใช้งาน</a>

    <a href="admin_room_reviews.php" class="menu-item active">
    <img src="images/chat-box.png" alt="Review Icon" width="33" height="33">ผลการรีวิวห้องประชุมของผู้ใช้</a>

    <a href="Logout_u.php" class="menu-item">
  <img src="uploads/check-out.png" alt="Sign Out Icon" style="width: 33px; height: 33px;"> ออกจากระบบ</a>
    </div>
</div>

    <script>
        function showDetails(roomId) {
            const overlay = document.querySelector('.overlay');
            const modal = document.querySelector('.modal');
            const modalBody = document.querySelector('.modal-body');

            fetch(`room_reviews.php?room_id=${roomId}`)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const reviews = doc.querySelector('#reviews-container');
                    modalBody.innerHTML = reviews.innerHTML;
                    overlay.style.display = 'block';
                    modal.style.display = 'block';
                });
        }

        function closeModal() {
            document.querySelector('.overlay').style.display = 'none';
            document.querySelector('.modal').style.display = 'none';
        }
        function safeDisplay($key, $default = "ไม่มีข้อมูล") {
    return isset($key) && !empty($key) ? htmlspecialchars($key) : $default;
}
    </script>
</head>
<body>
<div class="main-content">
    <h2 class="review-title">ผลการรีวิวห้องประชุม</h2>
    <div class="container">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($room = $result->fetch_assoc()): ?>
            <div class="room">
                <img src="uploads/<?= htmlspecialchars($room['picture']); ?>" alt="Room Image">
                <div class="room-details">
                    <div class="room-name"><?= htmlspecialchars($room['room_name']) ?></div>
                    <div class="room-info">
                        <p><i class="fas fa-users" style="color: #007bff;"></i> ความจุ: <?= $room['capacity'] ?> คน</p>
                        <p><i class="fas fa-map-marker-alt" style="color:#dc3545;"></i> ตำแหน่ง: <?= $room['location'] ?></p>
                        <p><i class="fas fa-tv" style="color: #CC66FF;"></i> จอ: <?= htmlspecialchars($room['screen']) ?>, 
                           <i class="fas fa-microphone" style="color:#FFA500;"></i> ไมค์: <?= htmlspecialchars($room['microphone']) ?></p>
                        <p><i class="fas fa-star" style="color:  #ffc107;"></i> คะแนนรีวิว: <?= number_format($room['avg_rating'], 1) ?>/5</p>
                        <p><i class="fas fa-comment" style="color: #28a745;"></i> รีวิวทั้งหมด: <?= $room['total_reviews'] ?></p>
                    </div>
                    <button class="btn" onclick="showDetails(<?= $room['room_id'] ?>)">ดูรายละเอียดการรีวิว</button>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>ยังไม่มีการรีวิว</p>
    <?php endif; ?>
</div>
<!-- Hidden container for reviews -->
<div id="reviews-container" style="display: none;">
    <?php foreach ($room_reviews as $review): ?>
        <div>
            <strong>คะแนนรีวิว:</strong> <?= $review['rating'] ?>/5<br>
            <p><?= htmlspecialchars($review['comment']) ?></p>
            <small>วันที่: <?= $review['review_date'] ?></small>
            <br>
            <!-- ปุ่มลบรีวิว -->
            <form action="delete_review.php" method="POST" style="display:inline;">
            <input type="hidden" name="review_id" value="<?= isset($review['id']) ? htmlspecialchars($review['id']) : '' ?>">
                <button type="submit" class="btn delete-btn" onclick="return confirm('คุณต้องการลบรีวิวนี้หรือไม่?');">
                    ลบรีวิว
                </button>
            </form>
            <hr>
        </div>
    <?php endforeach; ?>
</div>



    <!-- Overlay -->
    <div class="overlay" onclick="closeModal()"></div>

    <!-- Modal -->
    <div class="modal">
        <div class="modal-header">
            <span>รายละเอียดรีวิวห้อง</span>
            <span class="modal-close" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body"></div>
    </div>
    <!-- รวม SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(reviewId) {
        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: 'คุณต้องการลบรีวิวนี้หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                // สร้างฟอร์มและส่งค่าไป delete_review.php
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = 'delete_review.php';

                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'review_id';
                input.value = reviewId;

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
</body>
</html>