<?php
// 錯誤報告設定
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 資料庫連接設定
define('DB_SERVER', '140.134.53.57');
define('DB_USERNAME', 'D1371716');
define('DB_PASSWORD', '$Fwfqgr9P');
define('DB_NAME', 'D1371716');

// 建立資料庫連接
try {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    // 設定編碼為UTF-8
    $conn->set_charset("utf8mb4");
    
    // 檢查連接
    if ($conn->connect_error) {
        throw new Exception("連接失敗: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("資料庫連接錯誤：" . $e->getMessage());
}

// 設定時區
date_default_timezone_set('Asia/Taipei');
?> 