<<<<<<< HEAD
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>登錄介面</title>
    <style>
        body {
            margin: 100px auto 0 auto; 
            width: 300px;
            padding: 50px;
            border-radius: 10px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            width: 107%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #5cb85c;
            color: white;
            font-size: 18px;
        }
        .error {
            color: red;
            font-size: 14px;
=======
<?php
// 引入資料庫連接
require_once 'config.php';

// 檢查是否已經登入
if (isset($_SESSION['user_id'])) {
    header("Location: main.php");
    exit();
}

$error_message = '';
$success_message = '';

// 處理登入表單提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if (isset($_POST['login'])) {
            // 登入處理
            $username = trim($_POST['username']);
            $password = $_POST['password'];
            
            if (empty($username) || empty($password)) {
                throw new Exception("請輸入用戶名和密碼");
            }
            
            // 查詢用戶
            $stmt = $conn->prepare("SELECT * FROM professor WHERE pro_ID = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                // 驗證密碼（這裡假設密碼是 pro_ID）
                if ($password === $user['pro_ID']) {
                    // 登入成功，設定 session
                    $_SESSION['user_id'] = $user['pro_ID'];
                    $_SESSION['username'] = $user['name'];
                    $_SESSION['role'] = 'teacher';
                    $_SESSION['pro_ID'] = $user['pro_ID'];
                    $_SESSION['login_time'] = time();
                    
                    // 重導向到管理頁面
                    header("Location: main.php");
                    exit();
                } else {
                    throw new Exception("用戶名或密碼錯誤");
                }
            } else {
                throw new Exception("用戶名或密碼錯誤");
            }
            $stmt->close();
        }
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// 獲取所有教授資料（用於登入時選擇）
$professors = [];
$prof_result = $conn->query("SELECT pro_ID, name FROM professor ORDER BY name");
if ($prof_result) {
    while ($row = $prof_result->fetch_assoc()) {
        $professors[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>教授資料管理系統 - 登入</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Noto Sans TC', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
            position: relative;
        }

        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .login-header h1 {
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        .login-header p {
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .form-container {
            padding: 30px 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .message {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }

        .success {
            background: #efe;
            color: #363;
            border: 1px solid #cfc;
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 10px;
            }
            
            .form-container {
                padding: 20px;
            }
>>>>>>> 30f2dd4 (feat: 完成所有管理區塊導覽列與圖片上傳、資料展示等功能)
        }
    </style>
</head>
<body>
<<<<<<< HEAD
    <h2>後台登錄</h2>
    <form action=" check.php" method="post">
        <label for="username">帳號:</label>
        <input type="text" id="username" name="username" required>
        <label for="password">密碼:</label>
        <input type="password" id="password" name="password" required>
        <input type="submit" value="登陸">
    </form>
    <?php
    if (isset($_GET['error'])) {
        echo '<div class="error">帳號或密碼錯誤</div>';
    }
    ?>
</body>
=======
    <div class="login-container">
        <div class="login-header">
            <h1>系統登入</h1>
            <p>教授資料管理系統</p>
        </div>
        
        <div class="form-container">
            <?php if ($error_message): ?>
                <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>

            <!-- 登入表單 -->
            <form method="post">
                <div class="form-group">
                    <label for="username">教授ID</label>
                    <select id="username" name="username" required>
                        <option value="">請選擇教授</option>
                        <?php foreach ($professors as $prof): ?>
                            <option value="<?php echo htmlspecialchars($prof['pro_ID']); ?>">
                                <?php echo htmlspecialchars($prof['pro_ID'] . ' - ' . $prof['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="password">密碼</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" name="login" class="submit-btn">登入系統</button>
            </form>
        </div>
    </div>
</body>
</html>
>>>>>>> 30f2dd4 (feat: 完成所有管理區塊導覽列與圖片上傳、資料展示等功能)
