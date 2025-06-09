<?php
/**
 * 身份驗證和權限控制中介層
 * 用於保護需要登入的頁面和功能
 */

// 防止直接訪問此檔案
if (!defined('AUTH_INCLUDED')) {
    define('AUTH_INCLUDED', true);
}

/**
 * 檢查用戶是否已登入
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && 
           isset($_SESSION['username']) && 
           isset($_SESSION['login_time']);
}

/**
 * 檢查 session 是否過期
 * @param int $timeout 超時時間（秒），預設 8 小時
 * @return bool
 */
function isSessionExpired($timeout = 28800) {
    if (!isset($_SESSION['login_time'])) {
        return true;
    }
    
    return (time() - $_SESSION['login_time']) > $timeout;
}

/**
 * 獲取當前用戶資訊
 * @return array|null
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'user_id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'role' => $_SESSION['role'],
        'pro_ID' => $_SESSION['pro_ID'] ?? null,
        'login_time' => $_SESSION['login_time']
    ];
}

/**
 * 檢查用戶是否有指定權限
 * @param string $required_role 需要的角色 (super_admin, admin, teacher)
 * @param string $pro_ID 教授ID（當需要檢查特定教授權限時）
 * @return bool
 */
function hasPermission($required_role = 'admin', $pro_ID = null) {
    $user = getCurrentUser();
    if (!$user) {
        return false;
    }
    
    // 角色權限層級：super_admin > admin > teacher
    $role_levels = [
        'super_admin' => 3,
        'admin' => 2,
        'teacher' => 1
    ];
    
    $user_level = $role_levels[$user['role']] ?? 0;
    $required_level = $role_levels[$required_role] ?? 0;
    
    // 檢查角色權限
    if ($user_level < $required_level) {
        return false;
    }
    
    // 如果是教師角色且指定了 pro_ID，檢查是否為同一教授
    if ($user['role'] === 'teacher' && $pro_ID && $user['pro_ID'] !== $pro_ID) {
        return false;
    }
    
    return true;
}

/**
 * 要求用戶登入，如果未登入則重導向到登入頁面
 * @param string $redirect_to 登入後要重導向的頁面
 */
function requireLogin($redirect_to = null) {
    if (!isLoggedIn() || isSessionExpired()) {
        // 清除 session
        session_destroy();
        session_start();
        
        // 設定重導向URL
        if ($redirect_to) {
            $_SESSION['redirect_after_login'] = $redirect_to;
        }
        
        // 重導向到登入頁面
        header("Location: login.php");
        exit();
    }
}

/**
 * 要求特定權限，如果權限不足則顯示錯誤頁面
 * @param string $required_role 需要的角色
 * @param string $pro_ID 教授ID
 */
function requirePermission($required_role = 'admin', $pro_ID = null) {
    requireLogin();
    
    if (!hasPermission($required_role, $pro_ID)) {
        showAccessDeniedPage();
        exit();
    }
}

/**
 * 顯示權限不足頁面
 */
function showAccessDeniedPage() {
    http_response_code(403);
    ?>
    <!DOCTYPE html>
    <html lang="zh-Hant-TW">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>存取被拒絕</title>
        <style>
            body {
                font-family: 'Noto Sans TC', sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                margin: 0;
                background: #f5f5f5;
            }
            .container {
                text-align: center;
                background: white;
                padding: 40px;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                max-width: 500px;
            }
            .icon {
                font-size: 4rem;
                color: #e74c3c;
                margin-bottom: 20px;
            }
            h1 {
                color: #333;
                margin-bottom: 15px;
            }
            p {
                color: #666;
                margin-bottom: 25px;
                line-height: 1.6;
            }
            .btn {
                display: inline-block;
                padding: 10px 20px;
                background: #3498db;
                color: white;
                text-decoration: none;
                border-radius: 5px;
                margin: 0 5px;
            }
            .btn:hover {
                background: #2980b9;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="icon">🚫</div>
            <h1>存取被拒絕</h1>
            <p>您沒有權限訪問此頁面。<br>請聯絡系統管理員或使用有權限的帳號登入。</p>
            <a href="login.php" class="btn">重新登入</a>
            <a href="index.php" class="btn">回到首頁</a>
        </div>
    </body>
    </html>
    <?php
}

/**
 * 安全登出
 */
function logout() {
    global $conn;
    
    // 記錄登出日誌
    if (isLoggedIn() && isset($conn)) {
        $stmt = $conn->prepare("INSERT INTO system_logs (user_id, action, ip_address, user_agent) VALUES (?, 'logout', ?, ?)");
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $stmt->bind_param("iss", $_SESSION['user_id'], $ip_address, $user_agent);
        $stmt->execute();
        $stmt->close();
    }
    
    // 清除 session
    session_destroy();
    session_start();
    
    // 重導向到登入頁面
    header("Location: login.php");
    exit();
}

/**
 * 記錄用戶操作日誌
 * @param string $action 操作動作
 * @param string $table_name 操作的表名
 * @param string $record_id 記錄ID
 * @param array $old_values 原始值
 * @param array $new_values 新值
 */
function logUserAction($action, $table_name = null, $record_id = null, $old_values = null, $new_values = null) {
    global $conn;
    
    if (!isLoggedIn() || !isset($conn)) {
        return;
    }
    
    $user = getCurrentUser();
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
    $old_json = $old_values ? json_encode($old_values, JSON_UNESCAPED_UNICODE) : null;
    $new_json = $new_values ? json_encode($new_values, JSON_UNESCAPED_UNICODE) : null;
    
    $stmt = $conn->prepare("INSERT INTO system_logs (user_id, action, table_name, record_id, old_values, new_values, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssss", $user['user_id'], $action, $table_name, $record_id, $old_json, $new_json, $ip_address, $user_agent);
    $stmt->execute();
    $stmt->close();
}

/**
 * 生成 CSRF Token
 * @return string
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * 驗證 CSRF Token
 * @param string $token
 * @return bool
 */
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && 
           hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * CSRF Token 表單欄位
 * @return string
 */
function csrfTokenField() {
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

/**
 * 檢查並驗證 CSRF Token（用於 POST 請求）
 */
function checkCSRFToken() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!validateCSRFToken($token)) {
            http_response_code(403);
            die('CSRF token 驗證失敗');
        }
    }
}

/**
 * 清理和驗證輸入資料
 * @param string $data 原始資料
 * @param string $type 資料類型 (string, email, int, float, html)
 * @return mixed
 */
function sanitizeInput($data, $type = 'string') {
    $data = trim($data);
    
    switch ($type) {
        case 'email':
            return filter_var($data, FILTER_SANITIZE_EMAIL);
        case 'int':
            return filter_var($data, FILTER_SANITIZE_NUMBER_INT);
        case 'float':
            return filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        case 'html':
            return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        case 'string':
        default:
            return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * 處理登出請求
 */
if (isset($_GET['logout'])) {
    logout();
}

// 自動檢查 session 是否過期
if (isLoggedIn() && isSessionExpired()) {
    logout();
}
?>