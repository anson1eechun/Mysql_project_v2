<?php
require_once 'config.php';

// 處理登入表單提交
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // 使用 prepared statement 防止 SQL injection
    $stmt = $conn->prepare("SELECT * FROM administrator WHERE username = ? AND password_hash = SHA2(?, 256)");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // 登入成功
    if ($result->num_rows === 1) {
        $_SESSION["username"] = $username;
        header("Location: dashboard.php"); // 導向教授列表頁面
        exit;
    } else {
        $error = "帳號或密碼錯誤，請重新輸入。";
    }
    $stmt->close();
}
?>

<!-- HTML 登入表單 -->
<h2>登入系統</h2>

<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

<form method="POST">
    帳號：<input type="text" name="username" required><br><br>
    密碼：<input type="password" name="password" required><br><br>
    <button type="submit">登入</button>
</form>

<p>還沒有帳號？<a href="register.php">點我註冊</a></p>