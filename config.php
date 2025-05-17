<?php
// 顯示錯誤訊息
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 資料庫連接設定
define('DB_SERVER', '140.134.53.57');     // 資料庫伺服器
define('DB_PORT', 3306);                  // MySQL 端口
define('DB_USERNAME', 'D1371716');        // 資料庫使用者名稱
define('DB_PASSWORD', '$Fwfqgr9P');       // 資料庫密碼
define('DB_NAME', 'D1371716');            // 資料庫名稱

// 設定連接超時時間（秒）
ini_set('default_socket_timeout', 30);
ini_set('mysql.connect_timeout', 30);
ini_set('max_execution_time', 30);

// 診斷資訊函數
function getDiagnosticInfo() {
    $info = "<div style='font-family: Arial, sans-serif; margin: 20px; padding: 20px; border: 1px solid #ccc; border-radius: 5px;'>";
    $info .= "<h3>系統診斷資訊</h3>";
    
    // 檢查 VPN 連接
    $vpn_test = @fsockopen(DB_SERVER, DB_PORT, $errno, $errstr, 5);
    $info .= "<p>VPN 連接狀態: " . ($vpn_test ? "已連接" : "未連接") . "</p>";
    if (!$vpn_test) {
        $info .= "<p>錯誤代碼: $errno</p>";
        $info .= "<p>錯誤訊息: $errstr</p>";
    }
    
    // 顯示網路資訊
    $info .= "<p>本地 IP: " . $_SERVER['SERVER_ADDR'] . "</p>";
    $info .= "<p>遠端 IP: " . $_SERVER['REMOTE_ADDR'] . "</p>";
    
    // 顯示 PHP 資訊
    $info .= "<p>PHP 版本: " . phpversion() . "</p>";
    $info .= "<p>當前時間: " . date('Y-m-d H:i:s') . "</p>";
    
    // 顯示 MySQL 擴展資訊
    $info .= "<p>MySQL 擴展狀態: " . (extension_loaded('mysqli') ? "已載入" : "未載入") . "</p>";
    
    // 顯示網路連接測試
    $info .= "<h4>網路連接測試</h4>";
    
    // 測試 ping
    $ping_result = shell_exec("ping -c 1 " . DB_SERVER);
    $info .= "<p>Ping 測試結果：<br><pre>" . htmlspecialchars($ping_result) . "</pre></p>";
    
    // 測試 telnet
    $telnet_test = @fsockopen(DB_SERVER, DB_PORT, $errno, $errstr, 5);
    $info .= "<p>Telnet 測試 (" . DB_PORT . "端口): " . ($telnet_test ? "成功" : "失敗") . "</p>";
    if (!$telnet_test) {
        $info .= "<p>Telnet 錯誤代碼: $errno</p>";
        $info .= "<p>Telnet 錯誤訊息: $errstr</p>";
    }
    
    // 顯示路由資訊
    $traceroute = shell_exec("traceroute -m 5 " . DB_SERVER);
    $info .= "<p>路由追蹤結果：<br><pre>" . htmlspecialchars($traceroute) . "</pre></p>";
    
    // 顯示 DNS 解析
    $ip = gethostbyname(DB_SERVER);
    $info .= "<p>DNS 解析結果：" . $ip . "</p>";
    
    $info .= "</div>";
    return $info;
}

// 建立資料庫連接
try {
    // 先測試是否可以 ping 通伺服器
    if (!@fsockopen(DB_SERVER, DB_PORT, $errno, $errstr, 5)) {
        echo getDiagnosticInfo();
        throw new Exception("無法連接到資料庫伺服器，請確認：\n1. 是否已連接 VPN\n2. 伺服器是否在運行\n3. 防火牆設定是否正確");
    }

    // 使用 PDO 連接
    $dsn = "mysql:host=" . DB_SERVER . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 30,
        PDO::ATTR_PERSISTENT => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    );
    
    $conn = new PDO($dsn, DB_USERNAME, DB_PASSWORD, $options);
    
    // 設定編碼為UTF-8
    $conn->exec("SET NAMES utf8mb4");
    
} catch (PDOException $e) {
    echo getDiagnosticInfo();
    die("<div style='color: red; font-family: Arial, sans-serif; margin: 20px; padding: 20px; border: 1px solid red; border-radius: 5px;'>" .
        "<h3>資料庫連接錯誤</h3>" .
        "<p>" . $e->getMessage() . "</p>" .
        "<p>當前時間：" . date('Y-m-d H:i:s') . "</p>" .
        "<p>伺服器：" . DB_SERVER . ":" . DB_PORT . "</p>" .
        "</div>");
}
?> 