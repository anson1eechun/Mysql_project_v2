<?php
// profile.php - 個別教授介紹頁面
require_once 'config.php';

// 取得 URL 參數 id 並過濾
$pro_ID = isset($_GET['id']) ? $conn->real_escape_string($_GET['id']) : '';
if (empty($pro_ID)) {
    echo '<p>無效的教授 ID</p>';
    exit;
}

// 查詢教授資料
$stmt = $conn->prepare("SELECT * FROM professor WHERE pro_ID = ?");
$stmt->bind_param("s", $pro_ID);
$stmt->execute();
$result = $stmt->get_result();
$prof = $result->fetch_assoc();
$stmt->close();

if (!$prof) {
    echo '<p>找不到此教授</p>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($prof['name']); ?> - 教授介紹</title>
    <link rel="stylesheet" href="styles.css" />
    <style>
        body { font-family: 'Noto Sans TC', sans-serif; background: #f9f9f9; margin: 0; }
        .container { max-width: 900px; margin: 40px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .header { display: flex; align-items: center; gap: 20px; }
        .header img { width: 150px; height: 150px; object-fit: cover; border-radius: 50%; }
        .header .info h1 { margin: 0; font-size: 32px; color: #003366; }
        .header .info p { margin: 4px 0; font-size: 18px; color: #666; }
        .section { margin-top: 30px; }
        .section h2 { font-size: 24px; color: #005bac; border-bottom: 2px solid #005bac; padding-bottom: 6px; }
        .section ul { margin: 10px 0; padding-left: 20px; }
        .schedule-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .schedule-table th, .schedule-table td { border: 1px solid #ccc; padding: 6px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="uploads/<?php echo htmlspecialchars($prof['photo']); ?>" alt="<?php echo htmlspecialchars($prof['name']); ?> 照片" />
            <div class="info">
                <h1><?php echo htmlspecialchars($prof['name']); ?></h1>
                <p><?php echo htmlspecialchars($prof['position']); ?></p>
            </div>
        </div>

        <div class="section">
            <h2>簡介</h2>
            <p><?php echo nl2br(htmlspecialchars($prof['introduction'])); ?></p>
        </div>

        <div class="section">
            <h2>學歷</h2>
            <ul>
            <?php
            $edu_sql = $conn->prepare("SELECT department, degree FROM education WHERE pro_ID = ? ORDER BY edu_ID");
            $edu_sql->bind_param("s", $pro_ID);
            $edu_sql->execute();
            $edu_res = $edu_sql->get_result();
            while ($row = $edu_res->fetch_assoc()) {
                echo '<li>'.htmlspecialchars($row['department'].' '.$row['degree']).'</li>';
            }
            $edu_sql->close();
            ?>
            </ul>
        </div>

        <div class="section">
            <h2>專長</h2>
            <ul>
            <?php
            $exp_sql = $conn->prepare("SELECT item FROM expertise WHERE pro_ID = ? ORDER BY expertise_ID");
            $exp_sql->bind_param("s", $pro_ID);
            $exp_sql->execute();
            $exp_res = $exp_sql->get_result();
            while ($row = $exp_res->fetch_assoc()) {
                echo '<li>'.htmlspecialchars($row['item']).'</li>';
            }
            $exp_sql->close();
            ?>
            </ul>
        </div>

        <div class="section">
            <h2>聯絡資訊</h2>
            <ul>
                <li>信箱：<?php echo htmlspecialchars($prof['email'] ?? '未提供'); ?></li>
                <li>分機：<?php echo htmlspecialchars($prof['extension'] ?? '未提供'); ?></li>
            </ul>
        </div>

        <!-- 其他區塊例如 課表 / 論文 / 經歷 可依需求依樣建置 -->
    </div>
</body>
</html>
