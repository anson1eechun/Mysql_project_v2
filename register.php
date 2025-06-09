<?php
require_once 'config.php';

// handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // hash password using SHA2
    $stmt = $conn->prepare("INSERT INTO administrator (username, password_hash) VALUES (?, SHA2(?, 256))");
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
        echo "註冊成功！<a href='login_v2.php'>點我登入</a>";
    } else {
        echo "註冊失敗：" . $stmt->error;
    }
    $stmt->close();
}
?>

<!-- HTML 表單 -->
<h2>註冊</h2>
<form method="POST">
    帳號：<input type="text" name="username" required><br>
    密碼：<input type="password" name="password" required><br>
    <button type="submit">註冊</button>
</form>