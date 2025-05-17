<?php
// 引入資料庫設定檔
require_once "config.php";

// 測試查詢
$sql = "SELECT * FROM professor LIMIT 1";
$result = mysqli_query($conn, $sql);

if ($result) {
    echo "資料庫連接成功！<br>";
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo "成功讀取教授資料：<br>";
        echo "教授ID: " . $row['pro_ID'] . "<br>";
        echo "姓名: " . $row['name'] . "<br>";
        echo "系所: " . $row['department'] . "<br>";
    } else {
        echo "資料表中沒有資料。";
    }
} else {
    echo "查詢失敗：" . mysqli_error($conn);
}

// 關閉資料庫連接
mysqli_close($conn);
?> 
