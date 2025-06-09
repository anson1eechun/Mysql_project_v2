<?php
// teacher_map.php – 教授列表與連結頁面
require_once 'config.php';
// 設定連線與字元編碼
$conn->set_charset('utf8mb4');

// 取得所有教授資料
$sql    = "SELECT pro_ID, name, position, photo FROM professor ORDER BY name";
$result = $conn->query($sql);
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
        .card { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center; padding: 16px; transition: transform .2s; }
        .card:hover { transform: translateY(-4px); }
        .card img { width: 120px; height: 120px; object-fit: cover; border-radius: 50%; display: block; margin: 0 auto 12px; }
        .card a { text-decoration: none; color: #003366; }
        .card-name { font-size: 16px; font-weight: 500; margin: 4px 0; transition: color .2s; }
        .card-position { font-size: 14px; color: #555; }
        .card:hover .card-name { color: #005bac; }
        h1 { color: #003366; text-align: center; margin-bottom: 24px; }
    </style>
</head>

<body>
    <div class="container">
        <h1>系所成員</h1>

        <div class="grid">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): 
                    $id       = htmlspecialchars($row['pro_ID'], ENT_QUOTES, 'UTF-8');
                    $name     = htmlspecialchars($row['name'],    ENT_QUOTES, 'UTF-8');
                    $position = htmlspecialchars($row['position'],ENT_QUOTES, 'UTF-8');
                    $photo    = !empty($row['photo'])
                                ? htmlspecialchars($row['photo'], ENT_QUOTES, 'UTF-8')
                                : 'uploads/default.jpg';
                ?>
                    <div class="card">
                        <a href="index.php?id=<?php echo $id; ?>">
                            <img src="<?php echo $photo; ?>" alt="<?php echo $name; ?>">
                            <div class="card-name"><?php echo $name; ?></div>
                            <?php if ($position): ?>
                                <div class="card-position"><?php echo $position; ?></div>
                            <?php endif; ?>
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align:center; color:#666;">目前尚無教授資料。</p>
            <?php endif; ?>

            <?php if ($result): $result->free(); endif; ?>
        </div>
    </div>
</body>
</html>
