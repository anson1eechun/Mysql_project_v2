<?php
// teacher_map.php - 教授列表與連結頁面
require_once 'config.php';

// 設定字元編碼
$conn->set_charset('utf8');
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>系所成員列表</title>
    <link rel="stylesheet" href="styles.css" />
    <style>
        body { font-family: 'Noto Sans TC', sans-serif; background: #f9f9f9; margin: 0; }
        .container { max-width: 1000px; margin: 40px auto; padding: 20px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 24px; }
        .card { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center; padding: 16px; }
        .card img { width: 120px; height: 120px; object-fit: cover; border-radius: 50%; display: block; margin: 0 auto 12px; }
        .card a { text-decoration: none; color: #003366; font-size: 16px; font-weight: 500; }
        .card a:hover { color: #005bac; }
        h1 { color: #003366; text-align: center; margin-bottom: 24px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>系所成員</h1>
        <div class="grid">
        <?php
        // 取得所有教授資料
        $sql = "SELECT pro_ID, name, photo FROM professor ORDER BY name";
        if ($result = $conn->query($sql)) {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $id    = htmlspecialchars($row['pro_ID']);
                    $name  = htmlspecialchars($row['name']);
                    // 若 photo 欄位存放路徑，直接使用；否則預設頭像
                    $photo = !empty($row['photo']) ? $row['photo'] : 'uploads/default.jpg';
                    // 建立卡片
                    echo "<div class='card'>";
                    echo "<a href='teacher.php?id={$id}'>";
                    echo "<img src='" . htmlspecialchars($photo) . "' alt='" . \$name . "'>";
                    echo "<div>" . \$name . "</div>";
                    echo "</a>";
                    echo "</div>";
                }
            } else {
                echo "<p>目前尚無教授資料。</p>";
            }
            $result->free();
        } else {
            echo "<p>資料庫查詢失敗：" . htmlspecialchars($conn->error) . "</p>";
        }
        ?>
        </div>
    </div>
</body>
</html>
