<?php
session_start();
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                $_SESSION['login_status'] = 'success';
                header("Location: login.php");
                exit();
            } else {
                $_SESSION['login_status'] = 'error';
                header("Location: login.php");
                exit();
            }
        } else {
            $_SESSION['login_status'] = 'error';
            header("Location: login.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>เข้าสู่ระบบ</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    body {
        background: linear-gradient(to bottom, #6a0dad, #d8b4fe);
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0;
        font-family: 'Arial', sans-serif;
    }
    .login-card {
        width: 100%;
        max-width: 450px;
        border-radius: 20px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        background-color: #ffffff;
        padding: 2.5rem;
        border-top: 6px solid #9b59b6;
    }
    .login-card h3 {
        color: #6a0dad;
        margin-bottom: 1.5rem;
        text-align: center;
    }
    .form-control {
        border-radius: 30px;
        border: 1px solid #cccccc;
        padding: 0.75rem 1.25rem;
    }
    .form-control:focus {
        border-color: #9b59b6;
        box-shadow: 0 0 5px rgba(155, 89, 182, 0.5);
    }
    .btn-primary {
        background-color: #9b59b6;
        border: none;
        border-radius: 30px;
        padding: 0.75rem;
    }
    .btn-primary:hover {
        background-color: #884ea0;
    }
</style>
</head>
<body>

<div class="container-fluid d-flex justify-content-center align-items-center">
    <div class="card login-card">
        <h3>เข้าสู่ระบบ</h3>
        <form method="POST" action="" id="loginForm">
            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="กรุณากรอกชื่อผู้ใช้..." required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="กรุณากรอกรหัสผ่าน..." required>
            </div>
            <button type="submit" class="btn btn-primary w-100">เข้าสู่ระบบ</button>
        </form>
  <div class="mt-3 text-center">
            <small class="text-muted">ยังไม่มีบัญชี? <a href="register.php">สมัครสมาชิก</a></small>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (isset($_SESSION['login_status'])): ?>
            Swal.fire({
                icon: '<?php echo $_SESSION['login_status'] == 'success' ? 'success' : 'error'; ?>',
                title: '<?php echo $_SESSION['login_status'] == 'success' ? 'เข้าสู่ระบบสำเร็จ' : 'เข้าสู่ระบบล้มเหลว'; ?>',
                text: '<?php echo $_SESSION['login_status'] == 'success' ? 'ยินดีต้อนรับ!' : 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง'; ?>',
                confirmButtonText: 'ตกลง'
            }).then(() => {
                <?php if ($_SESSION['login_status'] == 'success'): ?>
                    window.location.href = '<?php echo $_SESSION['role'] == 'admin' ? 'admin.php' : 'user.php'; ?>';
                <?php endif; ?>
            });
            <?php unset($_SESSION['login_status']); ?>
        <?php endif; ?>
    });
</script>
</body>
</html>
