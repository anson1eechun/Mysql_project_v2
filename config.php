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
    // 記錄錯誤
    error_log("資料庫連接錯誤：" . $e->getMessage());
    // 顯示友好的錯誤訊息
    die("系統暫時無法連接到資料庫，請稍後再試。");
}

// 設定時區
date_default_timezone_set('Asia/Taipei');

// 設定會話
session_start();

// 設定安全標頭
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
?> 