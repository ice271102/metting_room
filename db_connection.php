<?php
$servername = "localhos:4306";
$username = "root"; // ชื่อผู้ใช้งาน MySQL
$password = "";     // รหัสผ่าน MySQL
$dbname = "meetingroom"; // ชื่อฐานข้อมูลของคุณ

// Create connection

$conn = new mysqli('localhost:4306', 'root', 'password', 'meetingroom');

// Check connection
if ($conn->connect_error) {
    die("การเชื่อมต่อล้มเหลว" . $conn->connect_error);
}
?>