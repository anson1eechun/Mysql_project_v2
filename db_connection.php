<?php
// 資料庫連接設定
$servername = "140.134.53.57"; // 改為你的MySQL伺服器地址
$username = "D1378388"; // 改為你的資料庫用戶名
$password = "$3Jqmsrhn"; // 改為你的資料庫密碼
$dbname = "D1378388"; // 改為你的資料庫名稱

$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連接
if ($conn->connect_error) {
    die("連接失敗: " . $conn->connect_error);
}

// 設定編碼為UTF-8
$conn->set_charset("utf8mb4");
?>


