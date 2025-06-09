<?php
die('LOADED teacher_map.php');
require_once 'config.php';
…

<?php
// 載入資料庫連線設定
require_once 'config.php';

// 查詢所有教授資料
$sql    = "SELECT * FROM professor";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>教授介紹總覽</title>
    <!-- 載入 Google 字體 -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* 全域樣式設定 */
        body {
            font-family: 'Noto Sans TC', sans-serif;
            background: linear-gradient(to bottom right, #f0f4f8, #ffffff);
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        /* 主標題 */
        h1 {
            text-align: center;
            font-size: 36px;
            color: #003366;
            margin-bottom: 40px;
        }
        /* Grid 版面配置 */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        /* 卡片樣式（使用 <a> 使整張卡片可點擊） */
        .card {
            display: block;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 20px;
            text-align: center;
            transition: transform 0.2s;
            text-decoration: none;
            color: inherit;
            cursor: pointer;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 15px;
        }
        .card h3 {
            font-size: 20px;
            color: #005bac;
            margin: 10px 0;
        }
        .card p {
            font-size: 14px;
            color: #555;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1>教授介紹總覽</h1>
    <div class="grid">
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // 安全取值並避免 null
                $pro_ID   = htmlspecialchars($row['pro_ID']       ?? '');
                $name     = htmlspecialchars($row['name']         ?? '');
                $position = htmlspecialchars($row['position']     ?? '');
                $intro    = mb_substr(strip_tags($row['introduction'] ?? ''), 0, 50, 'UTF-8') . '...';
                $photo    = htmlspecialchars($row['photo']        ?? '');

                // 整張卡片可點擊，導向 main.php?id=...
                echo "<a class='card' href='main.php?id={$pro_ID}'>";
                  echo "<img src='uploads/{$photo}' alt='{$name}'>";
                  echo "<h3>{$name}</h3>";
                  echo "<p>{$position}</p>";
                  echo "<p>{$intro}</p>";
                echo "</a>";
            }
        } else {
            echo '<p>目前尚無教授資料。</p>';
        }
        ?>
    </div>
</body>
</html>
