<?php
// 引入資料庫設定檔
require_once "config.php";

// 顯示系統資訊
echo "<div style='font-family: Arial, sans-serif; margin: 20px;'>";
echo "<h2>系統資訊</h2>";
echo "<p>PHP 版本: " . phpversion() . "</p>";
echo "<p>伺服器軟體: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>當前時間: " . date('Y-m-d H:i:s') . "</p>";

// 顯示資料庫資訊
echo "<h2>資料庫資訊</h2>";
echo "<p>資料庫名稱：" . DB_NAME . "</p>";
echo "<p>資料庫主機：" . DB_SERVER . "</p>";

// 測試資料庫查詢
$sql = "SHOW TABLES";
$result = $conn->query($sql);

if ($result) {
    echo "<h2>資料表列表</h2>";
    echo "<ul>";
    while ($row = $result->fetch_array()) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: red;'>無法獲取資料表列表：" . $conn->error . "</p>";
}

// 關閉資料庫連接
$conn->close();
echo "</div>";
?> 