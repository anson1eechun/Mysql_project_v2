<?php
// 載入資料庫連線設定
require_once 'config.php';

// 取得 URL 參數 pro_ID，並做基本過濾
$pro_ID = isset($_GET['pro_ID']) ? $conn->real_escape_string($_GET['pro_ID']) : '';

if (empty($pro_ID)) {
    echo '<p>無效的教授 ID</p>';
    exit;
}

// 查詢指定教授的基本資料
$sql = "SELECT * FROM professor WHERE pro_ID = '$pro_ID'";
$result = $conn->query($sql);
$professor = $result ? $result->fetch_assoc() : null;

if (!$professor) {
    echo '<p>找不到對應的教授資料</p>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($professor['name'] ?? ''); ?> 教授介紹</title>
    <!-- Google 字體 -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- 共用樣式表 (與總覽頁相同風格) -->
    <link rel="stylesheet" href="styles.css">
    <style>
        /* 若 styles.css 無法覆蓋，可在此補充額外樣式 */
        .container { max-width: 800px; margin: 0 auto; padding: 20px; font-family: 'Noto Sans TC', sans-serif; }
        .intro-wrapper { display: flex; flex-wrap: wrap; gap: 20px; }
        .intro-content { flex: 1; }
        .intro-photo img { width: 200px; height: 200px; object-fit: cover; border-radius: 50%; }
        .section { margin-top: 40px; }
        .section h2 { font-size: 24px; color: #005bac; margin-bottom: 10px; }
        .contact-info li { margin-bottom: 5px; }
        .course-table { width: 100%; border-collapse: collapse; }
        .course-table th, .course-table td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <div class="container">
        <!-- 標題與照片 -->
        <h1><?php echo htmlspecialchars($professor['name'] ?? ''); ?> 教授介紹</h1>

        <!-- 個人簡介與照片 -->
        <div class="section intro-wrapper">
            <div class="intro-content">
                <p><?php echo nl2br(htmlspecialchars($professor['introduction'] ?? '')); ?></p>
            </div>
            <div class="intro-photo">
                <img src="uploads/<?php echo htmlspecialchars($professor['photo'] ?? ''); ?>" alt="<?php echo htmlspecialchars($professor['name'] ?? ''); ?>">
            </div>
        </div>

        <!-- 學歷 -->
        <div class="section">
            <h2>學歷</h2>
            <ul>
                <?php
                $edu_sql = "SELECT * FROM education WHERE pro_ID = '$pro_ID'";
                $edu_res = $conn->query($edu_sql);
                while ($row = $edu_res->fetch_assoc()) {
                    echo '<li>' . htmlspecialchars($row['department'] . ' ' . $row['degree']) . '</li>';
                }
                ?>
            </ul>
        </div>

        <!-- 專長 -->
        <div class="section">
            <h2>專長</h2>
            <ul>
                <?php
                $exp_sql = "SELECT * FROM expertise WHERE pro_ID = '$pro_ID'";
                $exp_res = $conn->query($exp_sql);
                while ($row = $exp_res->fetch_assoc()) {
                    echo '<li>' . htmlspecialchars($row['item']) . '</li>';
                }
                ?>
            </ul>
        </div>

        <!-- 期刊論文 -->
        <div class="section">
            <h2>期刊論文</h2>
            <details>
                <summary>展開查看</summary>
                <ul>
                    <?php
                    $jnl_sql = "SELECT * FROM journal WHERE pro_ID = '$pro_ID'";
                    $jnl_res = $conn->query($jnl_sql);
                    while ($row = $jnl_res->fetch_assoc()) {
                        echo '<li>' . htmlspecialchars($row['title'] . ' (' . $row['date'] . ')') . '</li>';
                    }
                    ?>
                </ul>
            </details>
        </div>

        <!-- 會議論文 -->
        <div class="section">
            <h2>會議論文</h2>
            <details>
                <summary>展開查看</summary>
                <ul>
                    <?php
                    $conf_sql = "SELECT * FROM conference WHERE pro_ID = '$pro_ID'";
                    $conf_res = $conn->query($conf_sql);
                    while ($row = $conf_res->fetch_assoc()) {
                        echo '<li>' . htmlspecialchars($row['title'] . ' (' . $row['date'] . ')') . '</li>';
                    }
                    ?>
                </ul>
            </details>
        </div>

        <!-- 經歷 -->
        <div class="section">
            <h2>經歷</h2>
            <ul>
                <?php
                $exp2_sql = "SELECT * FROM experience WHERE pro_ID = '$pro_ID'";
                $exp2_res = $conn->query($exp2_sql);
                while ($row = $exp2_res->fetch_assoc()) {
                    echo '<li>' . htmlspecialchars($row['department'] . ' - ' . $row['position']) . '</li>';
                }
                ?>
            </ul>
        </div>

        <!-- 課程資訊 -->
        <div class="section">
            <h2>課程資訊</h2>
            <table class="course-table">
                <thead>
                    <tr>
                        <th>課程名稱</th><th>上課時間</th><th>地點</th><th>授課班級</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $crs_sql = "SELECT * FROM courses WHERE pro_ID = '$pro_ID'";
                    $crs_res = $conn->query($crs_sql);
                    while ($row = $crs_res->fetch_assoc()) {
                        echo '<tr>' .
                             '<td>' . htmlspecialchars($row['name']) . '</td>' .
                             '<td>' . htmlspecialchars($row['time']) . '</td>' .
                             '<td>' . htmlspecialchars($row['location']) . '</td>' .
                             '<td>' . htmlspecialchars($row['class_name']) . '</td>' .
                             '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- 聯絡資訊 -->
        <div class="section">
            <h2>聯絡資訊</h2>
            <ul class="contact-info">
                <li>信箱：<?php echo htmlspecialchars($professor['email'] ?? '未提供'); ?></li>
                <li>分機：<?php echo htmlspecialchars($professor['extension'] ?? '未提供'); ?></li>
            </ul>
        </div>

        <!-- 頁尾圖示 -->
        <div class="section image-gallery">
            <img src="title.png" alt="Footer Image">
        </div>
    </div>
</body>
</html>
