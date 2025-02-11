<?php
session_start();
include('db_connection.php');

$records_per_page = 5; // Records per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($page - 1) * $records_per_page;



// Query with pagination
$sql = "SELECT b.*, m.room_name 
        FROM bookings b
        JOIN meeting_room m ON b.room_id = m.room_id 
        ORDER BY b.booking_date DESC, b.time DESC 
        LIMIT $start_from, $records_per_page";
$result = $conn->query($sql);

// Fetch total records for pagination calculation
$total_sql = "SELECT COUNT(*) FROM bookings";
$total_result = $conn->query($total_sql);
$total_records = $total_result->fetch_row()[0];
$total_pages = ceil($total_records / $records_per_page);

$bookings = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}

$conn->close();
$bookings_json = json_encode($bookings);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจองห้องประชุม PEANE3</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .container {
            margin-top: 50px;
        }

        .table th, .table td {
            text-align: center;
            vertical-align: middle;
            padding: 12px;
        }

        .table-bordered {
            border: 1px solid #ddd;
        }

        .table th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        .table td {
            background-color: #ffffff;
        }

        .table-hover tbody tr:hover {
            background-color: #e9ecef;
        }

        .btn-custom {
            background-color: #28a745;
            color: white;
            border-radius: 5px;
        }

        .btn-custom:hover {
            background-color: #218838;
            color: white;
        }

        .btn-cancel {
            background-color: #dc3545;
            color: white;
            border-radius: 5px;
        }

        .btn-cancel:hover {
            background-color: #c82333;
            color: white;
        }

        .header-title {
            font-size: 36px;
            color: #007bff;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .header-logo {
            width: 50px;
            margin-right: 10px;
        }

        .status-pending {
            color: orange;
        }

        .status-confirmed {
            color: green;
        }

        .status-cancelled {
            color: red;
        }

        .status-label {
            font-weight: bold;
        }

        .table-container {
            overflow-x: auto;
        }

        /* Make the table responsive for small screens */
        @media (max-width: 767px) {
            .table th, .table td {
                padding: 8px;
            }

            .header-title {
                font-size: 28px;
            }
        }
        .search-container {
            margin-bottom: 20px;
        }
        .search-input {
            width: 250px;
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

 
    <a href="Booking_history.php" class="menu-item active">
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
    <!-- Main Content -->
    <div class="container">
        <h1 class="header-title">ประวัติการจองห้องประชุม</h1>

        <!-- Search Bar -->
        <div class="search-container">
            <input type="text" id="searchInput" class="search-input form-control" placeholder="ค้นหาห้องประชุม..." onkeyup="searchBookings()">
        </div>

        <!-- Table -->
        <div class="table-container">
        <table class="table table-bordered table-hover">
        <thead>
    <tr>
        <th><i class="fas fa-sort"></i> ลำดับที่</th>
        <th><i class="fas fa-door-open"></i> ห้องประชุม</th>
        <th><i class="fas fa-calendar-check"></i> การจอง</th>
        <th><i class="fas fa-clock"></i> วันที่เวลา</th>
        <th><i class="fas fa-info-circle"></i> สถานะการจอง</th>
    </tr>
</thead>
<tbody id="historyTable">
                    <!-- Data injected here -->
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <nav aria-label="Pagination">
            <ul class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
</div>

<!-- JavaScript -->
<!-- Remove this section -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteLabel">ยืนยันการลบ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                คุณต้องการลบการจองนี้ใช่หรือไม่?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">ลบ</button>
            </div>
        </div>
    </div>
</div>




<script>
    const bookings = <?php echo $bookings_json; ?>;
    const historyTable = document.getElementById("historyTable");

    // Display bookings in table
    function displayHistory(history) {
        historyTable.innerHTML = '';
        history.forEach((entry, index) => {
            const row = document.createElement("tr");
            row.innerHTML = `
    <td>${index + 1 + (<?php echo $start_from; ?>)}</td>
    <td>${entry.room_name}</td>
    <td>${entry.purpose || 'N/A'}</td>
    <td>${entry.booking_date} ${entry.time}</td>
    <td class="status-${entry.status.toLowerCase().replace(/\s+/g, '-')}" >
        <span class="status-label">${entry.status}</span>
    </td>

`;
            historyTable.appendChild(row);
        });
    }

    

    // Search functionality
    function searchBookings() {
        const searchTerm = document.getElementById("searchInput").value.toLowerCase();
        const filteredBookings = bookings.filter(entry =>
            entry.room_name.toLowerCase().includes(searchTerm) ||
            entry.room_id.toString().includes(searchTerm)
        );
        displayHistory(filteredBookings);
    }

    // Initial display of bookings
    displayHistory(bookings);
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>