<?php
session_start();
include('db_connection.php'); 

// รายชื่อคำหยาบที่ต้องการกรอง
$bad_words = ['คำหยาบ1', 'คำหยาบ2', 'คำหยาบ3']; // ใส่คำหยาบที่ต้องการกรอง

// Fetch meeting rooms from the database
$sql_rooms = "SELECT * FROM meeting_room";
$result_rooms = mysqli_query($conn, $sql_rooms);

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!$result_rooms) {
    die("Database query failed: " . mysqli_error($conn));
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ตรวจสอบการตั้งค่าคะแนน (rating)
    $rating = isset($_POST['rating']) ? $_POST['rating'] : null;

    // Get comment และทำความสะอาดข้อมูล
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);

    // ตรวจสอบคำหยาบในความคิดเห็น
    foreach ($bad_words as $word) {
        if (stripos($comment, $word) !== false) {
            // ถ้ามีคำหยาบพบ ให้แสดงคำเตือนและไม่ส่งรีวิว
            echo "<script>
                Swal.fire({
                    title: 'คำเตือน!',
                    text: 'พบคำหยาบในข้อความของคุณ กรุณาหลีกเลี่ยงคำหยาบเพื่อส่งรีวิว',
                    icon: 'warning',
                    confirmButtonText: 'ตกลง'
                }).then(() => {
                    // รีเซ็ต textarea ให้มีข้อความเดิม
                    document.getElementById('review-comment').value = '$comment';
                });
            </script>";
            exit; // หยุดการทำงานของสคริปต์
        }
    }

    $user_id = $_SESSION['user_id']; 
    $room_id = $_POST['room_id']; 
    $review_date = date('Y-m-d H:i:s'); 

    // ใช้ prepared statements เพื่อป้องกัน SQL injection
    $stmt = $conn->prepare("INSERT INTO reviews (rating, comment, user_id, room_id, review_date) 
                            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isiss", $rating, $comment, $user_id, $room_id, $review_date);

    if ($stmt->execute()) {
        // SweetAlert2 pop-up on success with form confirmation
        echo "<script>
            Swal.fire({
                title: 'สำเร็จ!',
                text: 'รีวิวถูกส่งเรียบร้อย',
                icon: 'success',
                confirmButtonText: 'ตกลง'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'Review_Meeting_Room.php'; // Redirect to review page
                }
            });
        </script>";
    } else {
        // SweetAlert2 pop-up on error
        echo "<script>
            Swal.fire({
                title: 'เกิดข้อผิดพลาด!',
                text: 'เกิดข้อผิดพลาดในการส่งรีวิว',
                icon: 'error',
                confirmButtonText: 'ตกลง'
            });
        </script>";
    }
    $stmt->close();
}
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>



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
    flex: 1;
    padding: 40px 30px;
    background-color: rgb(255, 255, 255);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    margin: 50px auto;
    overflow-y: auto;
    width: 100%;
    max-width: 800px;
    height: 800px;
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

.star-rating {
    display: flex;
    justify-content: center;
    margin-top: 30px;
}

.star-rating i {
    font-size: 2.6em;
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s ease;
}

.star-rating i:hover, .star-rating i.selected {
    color: #ffcc00;
    
}

.form-group-container {
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    margin-top: 50px;
}

button.btn {
    margin-top: -5px;
}

footer {
    text-align: center;
    font-size: 0.9em;
    color: #777;
    margin-top: 50px;
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

.form-group {
    margin-bottom: 15px;
}

.form-control {
    width: 100%;
    padding: 10px;
    font-size: 16px;
}

.title {
    background-color: #ADD8E6;
    color: white;
    text-align: center;
    padding: 15px 50px;
    margin: 0 auto;
    border-radius: 15px;
    font-size: 24px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

textarea.form-control {
    width: 100%;
    margin-top: 10px;
}

button.btn {
    margin-top: 20px;

}button.btn {
    font-size: 1.0em;  
    padding: 8px 15px;  
    margin: 0 auto;  
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

<a href="user.php" class="menu-item">
  <img src="images/seo.png" alt="Search Icon" style="width: 33px; height: 33px;"> จองห้องประชุม
</a>

<a href="all_booking_information.php" class="menu-item">
  <img src="images/time-management.png" alt="Calendar Check Icon" style="width: 33px; height: 33px;"> ประวัติการจองของฉัน
</a>

<a href="Review_Meeting_Room.php" class="menu-item active">
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

<div class="main-content">
    <div class="header">
        <h2 class="title">รีวิวการจองห้องประชุม</h2>
    </div>
    <form action="Review_Meeting_Room.php" method="POST">
        <div class="form-group">
            <label for="room_id">เลือกห้องประชุม</label>
            <select id="room_id" name="room_id" class="form-control">
                <?php while ($room = mysqli_fetch_assoc($result_rooms)): ?>
                    <option value="<?= $room['room_id']; ?>"><?= $room['room_name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="star-rating">
            <i class="fas fa-star" data-value="1"></i>
            <i class="fas fa-star" data-value="2"></i>
            <i class="fas fa-star" data-value="3"></i>
            <i class="fas fa-star" data-value="4"></i>
            <i class="fas fa-star" data-value="5"></i>
        </div>
        
        <div class="form-group-container">
            <div class="form-group">
                <label for="review-comment">
                    <i class="fas fa-comment" style="color: purple; font-size: 24px; margin-right: 10px;"></i>
                    แสดงความคิดเห็นและให้คะแนน
                </label>
                <textarea id="review-comment" name="comment" class="form-control" rows="4"></textarea>
            </div>

            <input type="hidden" name="rating" value="" id="rating">
            <button type="submit" class="btn btn-primary">ส่งรีวิว</button>
        </div>
    </form>
</div>

<script>
    // Handle star rating
    const stars = document.querySelectorAll('.star-rating i');
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = this.getAttribute('data-value');
            document.getElementById('rating').value = rating; // Store rating value
            stars.forEach(star => {
                // Reset all stars to default
                star.classList.remove('selected');
                // Highlight stars based on rating
                if (star.getAttribute('data-value') <= rating) {
                    star.classList.add('selected');
                }
            });
        });
    });

    document.querySelector('button').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent form submission and page reload

        const selectedStars = document.querySelectorAll('.star-rating i.selected').length;
        const comment = document.getElementById('review-comment').value;

        if (!comment.trim()) {
            // If no comment, show SweetAlert2 alert
            Swal.fire({
                title: 'กรุณากรอกความคิดเห็น',
                text: 'คุณต้องกรอกความคิดเห็นก่อนส่งรีวิว',
                icon: 'warning',
                confirmButtonText: 'ตกลง'
            });
            return; // Prevent form submission
        }

        // ตรวจสอบคำหยาบจากฟังก์ชัน JS
        const badWords = ['ไอ้', 'ควาย', 'งี่เง่า', 'หมา', 'ชิบหาย', 'เลว', 'สถุน', 'สวะ', 'ถุย', 'เฮงซวย','กู','มึง',
            'บ้า', 'สัส', 'ห่า','ปากหมา', 'ดอก', 'สารเลว', 'เหี้ย', 'แม่ง','ฉิบ', 'เสือก', 'ซวย', 'ไอ้สัตว์', 'บัก', 
            'แดก', 'หมาแก่', 'เวร', 'เหี้ย','ระยำ', 'ชัง', 'ตีน', 'อี','ควย', 'ปากหมา', 'จังไร','สันดาน', 'ขี้โกง',
            'ส้นตีน', 'โง่','damn', 'hell', 'stupid', 'idiot', 'moron', 'jerk', 'asshole', 'bastard', 'fuck',
            'shit', 'bitch','cunt', 'dick', 'piss', 'slut', 'whore', 'fag', 'nigger', 'motherfucker', 'cock', 
            'twat', 'douchebag','prick', 'wanker', 'wank', 'fistfuck', 'cocksucker', 'pussy', 'shithead', 'ass',
            'butt', 'dumbass','cocksucker', 'dumbass', 'shitface', 'clit', 'piss off', 'twat', 'fisting', 'tits',
            'nipple', 'cum','douche','smegma', 'bukkake', 'fist', 'wank', 'blowjob', 'orgy', 'rapist', 'tard', 'retard' ]; // เพิ่มคำหยาบที่ต้องการตรวจสอบ
        const containsBadWords = badWords.some(word => comment.toLowerCase().includes(word));

        if (containsBadWords) {
            // แสดง alert ถ้ามีคำหยาบ
            Swal.fire({
                title: 'คำเตือน!',
                text: 'ข้อความของคุณมีคำหยาบ กรุณาแก้ไขข้อความก่อนส่งรีวิว',
                icon: 'error',
                confirmButtonText: 'ตกลง'
            });
            return; // ป้องกันการส่งฟอร์ม
        }

        // Show SweetAlert2 preview for review
        Swal.fire({
            title: 'รีวิวของคุณ',
            html: `
                <strong>คุณให้คะแนน:</strong> ${selectedStars} ดาว<br>
                <strong>คอมเมนต์:</strong> ${comment}
            `,
            icon: 'success',
            confirmButtonText: 'ตกลง'
        }).then((result) => {
            if (result.isConfirmed) {
                // If confirmed, submit the form and save to database
                document.querySelector('form').submit();
            }
        });
    });
</script>

</body>
</html>