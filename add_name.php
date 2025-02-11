<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ระบบจองห้องประชุม</title>
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

    .content {
      flex-grow: 1;
      padding: 20px;
    }

    .form-container {
      background-color: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    }

    .btn-upload {
      background-color: #0d6efd;
      color: white;
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
        <a href="admin.php" class="menu-item "><i class="fas fa-search"></i> เช็คห้องประชุม</a>
        
        <a href="Modify_booking_status.php" class="menu-item"><i class="fas fa-edit"></i> ปรับเปลี่ยนสถานะการจอง</a>
        <a href="Booking_history.php" class="menu-item"><i class="fas fa-history"></i> ประวัติการจอง</a>
        <a href="#" class="menu-item"><i class="fas fa-thumbs-up"></i> สถิติการจอง</a>
        <a href="name.php" class="menu-item active"><i class="fas fa-user"></i> ข้อมูลผู้ใช้งาน</a>
        <a href="#" class="menu-item"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
    </div>
  </div>

  <div class="content">
    <div class="form-container">
      <h2 class="mb-4">เพิ่มผู้ใช้งาน</h2>
      <form>
        <div class="mb-3 text-center">
          <button type="button" class="btn btn-upload">
            <i class="bi bi-cloud-upload"></i> อัปโหลด
          </button>
        </div>
        <div class="row g-3">
          <div class="col-md-6">
            <label for="emp-id" class="form-label">รหัสพนักงาน</label>
            <input type="text" id="emp-id" class="form-control" placeholder="รหัสพนักงาน">
          </div>
          <div class="col-md-6">
            <label for="fullname" class="form-label">ชื่อ-นามสกุล</label>
            <input type="text" id="fullname" class="form-control" placeholder="ชื่อ-นามสกุล">
          </div>
          <div class="col-md-6">
            <label for="department" class="form-label">แผนก</label>
            <select id="department" class="form-select">
              <option selected>เลือกแผนก</option>
              <option>การตลาด</option>
              <option>พัฒนาซอฟต์แวร์</option>
            </select>
          </div>
          <div class="col-md-6">
            <label for="position" class="form-label">ตำแหน่ง</label>
            <select id="position" class="form-select">
              <option selected>เลือกตำแหน่ง</option>
              <option>ผู้จัดการ</option>
              <option>พนักงาน</option>
            </select>
          </div>
          <div class="col-md-6">
            <label for="phone" class="form-label">เบอร์โทร</label>
            <input type="text" id="phone" class="form-control" placeholder="เบอร์โทร">
          </div>
        </div>
        <div class="text-center mt-4">
          <button type="submit" class="btn btn-primary">บันทึก</button>
          <button type="reset" class="btn btn-secondary">ยกเลิก</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>