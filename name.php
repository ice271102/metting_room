<?php
session_start();
include('db_connection.php');

// จำนวนการจองที่จะแสดงต่อหน้า
$records_per_page = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$start_from = ($page - 1) * $records_per_page;

// รับค่าการค้นหาจากฟอร์ม
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
// แก้ไขข้อมูลผู้ใช้งาน
if (isset($_POST['updateUser'])) {
  $user_id = $_POST['user_id'];
  $username = $_POST['username'];
  $first_name = $_POST['first_name'];
  $last_name = $_POST['last_name'];
  $email = $_POST['email'];
  $department = $_POST['department'];
  $position = $_POST['position'];
  $phone = $_POST['phone'];
  $role = $_POST['role'];

  $update_sql = "UPDATE users SET username = ?, first_name = ?, last_name = ?, email = ?, department = ?, position = ?, phone = ?, role = ? WHERE user_id = ?";
  $stmt = $conn->prepare($update_sql);
  $stmt->bind_param("ssssssssi", $username, $first_name, $last_name, $email, $department, $position, $phone, $role, $user_id);

  if ($stmt->execute()) {
      echo "<script>
          document.addEventListener('DOMContentLoaded', function() {
              Swal.fire({
                  title: 'อัปเดตข้อมูลผู้ใช้งานสำเร็จ',
                  icon: 'success',
                  confirmButtonText: 'ตกลง'
              }).then(() => {
                  window.location.href = 'name.php';
              });
          });
      </script>";
  } else {
      echo "<script>
          Swal.fire({
              title: 'เกิดข้อผิดพลาด',
              text: '" . addslashes($stmt->error) . "',
              icon: 'error',
              confirmButtonText: 'ตกลง'
          });
      </script>";
  }
}
// ลบข้อมูลผู้ใช้งาน
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    if ($delete_id > 0) {
        $sql = "DELETE FROM users WHERE user_id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $delete_id);
            if ($stmt->execute()) {
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'ลบข้อมูลสำเร็จ',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => { window.location.href = 'name.php'; });
                </script>";
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถลบข้อมูลได้'
                    });
                </script>";
            }
            $stmt->close();
        }
    }
}

// ค้นหาข้อมูลผู้ใช้งาน
$sql = "SELECT user_id, username, first_name, last_name, email, department, position, phone, role 
        FROM users 
        WHERE username LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR email LIKE ? 
        LIMIT ?, ?";
if ($stmt = $conn->prepare($sql)) {
    $search_term = "%" . $search . "%";
    $stmt->bind_param("ssssii", $search_term, $search_term, $search_term, $search_term, $start_from, $records_per_page);
    $stmt->execute();
    $result = $stmt->get_result();
}

// คำนวณจำนวนผู้ใช้ทั้งหมด
$total_records_sql = "SELECT COUNT(*) AS total FROM users WHERE username LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR email LIKE ?";
if ($stmt = $conn->prepare($total_records_sql)) {
    $stmt->bind_param("ssss", $search_term, $search_term, $search_term, $search_term);
    $stmt->execute();
    $total_records_result = $stmt->get_result();
    $total_records = $total_records_result->fetch_assoc()['total'];
    $total_pages = ceil($total_records / $records_per_page);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ระบบจองห้องประชุม PEANE3</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
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
      flex-direction: column;
      min-height: 100vh;
    }
    .main-container {
      display: flex;
      flex-grow: 1;
      width: 100%;
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

    .menu-item:hover, .menu-item.active {
      background-color: #0066cc;
    }
    .header {
      background-color: #6f42c1;
      color: #fff;
      padding: 15px;
      border-bottom: 2px solid #ddd;
    }
    .btn-add {
      background-color: #007bff;
      color: #fff;
      border: none;
      margin-bottom: 15px;
    }
    .table-container {
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
      padding: 20px;
    }
    th, td {
  text-align: center; /* จัดข้อความในแนวนอนให้ตรงกลาง */
  vertical-align: middle; /* จัดข้อความในแนวตั้งให้ตรงกลาง */
}
.table th, .table td {
    border: 1px solidrgb(227, 211, 245); /* กรอบสีม่วงอ่อน */
  }

  /* สีพื้นหลังของหัวตาราง */
  .table th {
    background-color:rgb(187, 228, 239);
  }
  .modal-header {
    background-color: #6f42c1; /* พื้นหลังสีม่วง */
    color: white; /* สีข้อความเป็นสีขาว */
  }
  .modal-footer .btn-secondary {
    background-color: #dc3545; /* สีพื้นหลังเป็นสีแดง */
  }
    .status {
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 12px;
    }
    .active {
      background-color: #28a745;
      color: #fff;
    }
    .inactive {
      background-color: #ffc107;
      color: #fff;
    }
  </style>
</head>
<body>
<div class="main-container">
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

    <a href="Booking_statistics.php" class="menu-item">
 <img src="images/analytics.png" alt="Booking Statistics Icon" width="33" height="33">สถิติการจอง</a>

 <a href="name.php" class="menu-item active">
    <img src="images/teamwork.png" alt="User Info Icon" width="33" height="33"> ข้อมูลผู้ใช้งาน</a>

    <a href="admin_room_reviews.php" class="menu-item">
    <img src="images/chat-box.png" alt="Review Icon" width="33" height="33">ผลการรีวิวห้องประชุมของผู้ใช้</a>

    <a href="Logout_u.php" class="menu-item">
  <img src="uploads/check-out.png" alt="Sign Out Icon" style="width: 33px; height: 33px;"> ออกจากระบบ</a>
    </div>
  </div>
  <div class="col-md-10">
      <div class="header">
        <h2>ข้อมูลผู้ใช้งาน</h2>
      </div>
      <div class="container mt-4">
      <form action="name.php" method="GET">
  <input type="text" id="searchInput" class="form-control w-25" name="search"
         value="<?php echo htmlspecialchars($search); ?>" 
         placeholder="ค้นหา..." 
         onkeyup="searchUser()">
  <button type="submit" class="btn btn-primary mt-3">ค้นหา</button>
</form>
</div>
<div class="table-container mt-4">
  <table class="table table-bordered">
    <thead>
    <tr>
    <th style="background-color: #007bff; color: white; text-align: center; vertical-align: middle;">
        <i class="fas fa-sort" style="color: white;"></i> ลำดับที่
    </th>
    <th style="background-color: #007bff; color: white; text-align: center; vertical-align: middle;">
        <i class="fas fa-user" style="color: white;"></i> ชื่อผู้ใช้
    </th>
    <th style="background-color: #007bff; color: white; text-align: center; vertical-align: middle;">
        <i class="fas fa-user-alt" style="color: white;"></i> ชื่อ
    </th>
    <th style="background-color: #007bff; color: white; text-align: center; vertical-align: middle;">
        <i class="fas fa-user-alt" style="color: white;"></i> นามสกุล
    </th>
    <th style="background-color: #007bff; color: white; text-align: center; vertical-align: middle;">
        <i class="fas fa-envelope" style="color: white;"></i> อีเมล
    </th>
    <th style="background-color: #007bff; color: white; text-align: center; vertical-align: middle;">
        <i class="fas fa-building" style="color: white;"></i> แผนก
    </th>
    <th style="background-color: #007bff; color: white; text-align: center; vertical-align: middle;">
        <i class="fas fa-briefcase" style="color: white;"></i> ตำแหน่ง
    </th>
    <th style="background-color: #007bff; color: white; text-align: center; vertical-align: middle;">
        <i class="fas fa-phone" style="color: white;"></i> โทรศัพท์
    </th>
<th style="background-color: #007bff; color: white; text-align: center; vertical-align: middle;">
        <i class="fas fa-user-shield" style="color: white;"></i> บทบาท
    </th>
    <th style="background-color: #007bff; color: white; text-align: center; vertical-align: middle; width: 185px;">
    <i class="fas fa-cogs" style="color: white;"></i> การจัดการ
</th>


</tr>

    </thead>
    <tbody>
    <?php
// ลูปแสดงข้อมูล
if ($result->num_rows > 0) {
    // Start the counter from $start_from + 1 to continue numbering across pages
    $count = $start_from + 1;
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $count++ . "</td>"; // Counter starts from $start_from + 1
        echo "<td>" . $row['username'] . "</td>";
        echo "<td>" . $row['first_name'] . "</td>";
        echo "<td>" . $row['last_name'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['department'] . "</td>";
        echo "<td>" . $row['position'] . "</td>";
        echo "<td>" . $row['phone'] . "</td>";
        echo "<td>" . $row['role'] . "</td>";
        echo "<td>
 <button class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#viewUserModal" . $row['user_id'] . "'>แสดง</button>
            <button class='btn btn-success btn-sm' data-bs-toggle='modal' data-bs-target='#editUserModal" . $row['user_id'] . "'>แก้ไข</button>
            <!-- ปุ่มลบที่เรียก confirmDelete() -->
            <button type='button' class='btn btn-danger btn-sm' onclick='confirmDelete(" . $row['user_id'] . ")'>ลบ</button>
        </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='10' class='text-center'>ไม่มีข้อมูลผู้ใช้งาน</td></tr>";
}
?>
          </tbody>
        </table>
        <nav>
          <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
              <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                <a class="page-link" href="name.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
              </li>
            <?php } ?>
          </ul>
        </nav>
      </div>
    </div>
</div>
  <?php
  if ($result->num_rows > 0) {
      $result->data_seek(0); // reset pointer
      while ($row = $result->fetch_assoc()) {
  ?>
  <div class="modal fade" id="viewUserModal<?php echo $row['user_id']; ?>" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewUserModalLabel">รายละเอียดผู้ใช้งาน</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p><strong>ชื่อผู้ใช้:</strong> <?php echo $row['username']; ?></p>
          <p><strong>ชื่อ:</strong> <?php echo $row['first_name']; ?></p>
          <p><strong>นามสกุล:</strong> <?php echo $row['last_name']; ?></p>
          <p><strong>อีเมล:</strong> <?php echo $row['email']; ?></p>
          <p><strong>แผนก:</strong> <?php echo $row['department']; ?></p>
          <p><strong>ตำแหน่ง:</strong> <?php echo $row['position']; ?></p>
          <p><strong>โทรศัพท์:</strong> <?php echo $row['phone']; ?></p>
          <p><strong>บทบาท:</strong> <?php echo $row['role']; ?></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
        </div>
      </div>
    </div>
  </div>
  <?php } } ?>
  <!-- Modal for Edit -->
  <?php
  if ($result->num_rows > 0) {
      $result->data_seek(0); // reset pointer
      while ($row = $result->fetch_assoc()) {
  ?>
  <div class="modal fade" id="editUserModal<?php echo $row['user_id']; ?>" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="name.php" method="POST">
          <div class="modal-header">
            <h5 class="modal-title" id="editUserModalLabel">แก้ไขข้อมูลผู้ใช้งาน</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
            <div class="mb-3">
              <label for="username" class="form-label">ชื่อผู้ใช้</label>
              <input type="text" class="form-control" name="username" value="<?php echo $row['username']; ?>" required>
            </div>
            <div class="mb-3">
              <label for="first_name" class="form-label">ชื่อ</label>
              <input type="text" class="form-control" name="first_name" value="<?php echo $row['first_name']; ?>" required>
            </div>
            <div class="mb-3">
              <label for="last_name" class="form-label">นามสกุล</label>
              <input type="text" class="form-control" name="last_name" value="<?php echo $row['last_name']; ?>" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">อีเมล</label>
              <input type="email" class="form-control" name="email" value="<?php echo $row['email']; ?>" required>
            </div>
            <div class="mb-3">
              <label for="department" class="form-label">แผนก</label>
              <input type="text" class="form-control" name="department" value="<?php echo $row['department']; ?>">
            </div>
            <div class="mb-3">
              <label for="position" class="form-label">ตำแหน่ง</label>
              <input type="text" class="form-control" name="position" value="<?php echo $row['position']; ?>">
            </div>
            <div class="mb-3">
              <label for="phone" class="form-label">โทรศัพท์</label>
              <input type="text" class="form-control" name="phone" value="<?php echo $row['phone']; ?>">
            </div>
            <div class="mb-3">
              <label for="role" class="form-label">บทบาท</label>
              <select class="form-control" name="role">
                <option value="Admin" <?php echo ($row['role'] == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                <option value="User" <?php echo ($row['role'] == 'User') ? 'selected' : ''; ?>>User</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
          <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
    <button type="submit" class="btn btn-primary" name="updateUser">อัพเดต</button>
</div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <?php } } ?>
  <script>
function searchUser() {
    let input = document.getElementById("searchInput").value;
    let resultsDiv = document.getElementById("searchResults");

    if (input.length === 0) {
        resultsDiv.innerHTML = "";
        return;
    }

    fetch(`name.php?search=${input}`)
        .then(response => response.text())
        .then(data => {
            resultsDiv.innerHTML = data;
        })
        .catch(error => console.error("Error:", error));
}
</script>
<script>
function confirmDelete(userId) {
  Swal.fire({
    title: 'คุณต้องการลบข้อมูลนี้หรือไม่?',
    text: "ข้อมูลจะถูกลบถาวร!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'ใช่, ลบ!',
    cancelButtonText: 'ยกเลิก',
    customClass: {
      cancelButton: 'cancel-button'  // กำหนดคลาสสำหรับปุ่มยกเลิก
    }
  }).then((result) => {
    if (result.isConfirmed) {
      // ถ้าผู้ใช้กดยืนยัน, ส่งลิงค์ลบข้อมูลไปที่ server
      window.location.href = 'name.php?delete_id=' + userId;
    }
  });
}
function confirmUpdate(event, formId) {
  event.preventDefault(); // หยุดการส่งฟอร์มทันที
  Swal.fire({
    title: 'คุณต้องการอัปเดตข้อมูลนี้หรือไม่?',
    text: "ข้อมูลจะถูกอัปเดต!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'ใช่, อัปเดต!',
    cancelButtonText: 'ยกเลิก',
    customClass: {
      cancelButton: 'cancel-button'
    }
  }).then((result) => {
    if (result.isConfirmed) {
      document.getElementById(formId).submit(); // ส่งฟอร์มเมื่อผู้ใช้ยืนยัน
    }
  });
}
</script>

<style>
.cancel-button {
  background-color:  #d33f49;  /* สีพื้นหลังเป็นสีแดง */
  color: white;           /* สีข้อความเป็นสีขาว */
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>