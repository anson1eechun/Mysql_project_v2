<?php
// 引入資料庫連接文件
require_once 'db_connection.php';

// 取得教授基本資料
$pro_ID = 'A001';

// 獲取教授基本資料
$sql = "SELECT * FROM professor WHERE pro_ID = '$pro_ID'";
$result = $conn->query($sql);
$professor = $result->fetch_assoc();

if ($professor) {
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $professor['name']; ?>教授介紹</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="title2.png" alt="Logo">
        </div>
        
        <h1><?php echo $professor['name']; ?>教授介紹</h1>
        
        <div class="section">
            <div class="intro-wrapper">
                <div class="intro-content">
                    <p><?php echo $professor['introduction']; ?></p>
                </div>
                <div class="intro-photo">
                    <img src="uploads/<?php echo $professor['photo']; ?>" alt="<?php echo $professor['name']; ?>">
                </div>
            </div>
        </div>
        
        <div class="section">
            <h2>學歷</h2>
            <ul>
                <?php
                $sql = "SELECT * FROM education WHERE pro_ID = '$pro_ID'";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    echo "<li>{$row['department']} {$row['degree']}</li>";
                }
                ?>
            </ul>
        </div>
        
        <div class="section">
            <h2>專長</h2>
            <ul>
                <?php
                $sql = "SELECT * FROM expertise WHERE pro_ID = '$pro_ID'";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    echo "<li>{$row['item']}</li>";
                }
                ?>
            </ul>
        </div>
        
        <div class="section">
            <h2>期刊論文</h2>
            <details>
                <summary>發表期刊論文</summary>
                <ul>
                    <?php
                    $sql = "SELECT * FROM journal WHERE pro_ID = '$pro_ID'";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()) {
                        echo "<li>{$row['title']} ({$row['date']})</li>";
                    }
                    ?>
                </ul>
            </details>
        </div>
        
        <div class="section">
            <h2>會議論文</h2>
            <details>
                <summary>發表會議論文</summary>
                <ul>
                    <?php
                    $sql = "SELECT * FROM conference WHERE pro_ID = '$pro_ID'";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()) {
                        echo "<li>{$row['title']} ({$row['date']})</li>";
                    }
                    ?>
                </ul>
            </details>
        </div>
        
        <div class="section">
            <h2>經歷</h2>
            <ul>
                <?php
                $sql = "SELECT * FROM experience WHERE pro_ID = '$pro_ID'";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    echo "<li>{$row['department']} {$row['position']}</li>";
                }
                ?>
            </ul>
        </div>
        
        <div class="section">
            <h2>課程資訊</h2>
            <div class="course-table-wrapper">
                <a href="schedule.php" class="schedule-link">查看完整課表</a>
                <table class="course-table">
                    <thead>
                        <tr>
                            <th>課程名稱</th>
                            <th>上課時間</th>
                            <th>上課地點</th>
                            <th>授課班級</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM courses WHERE pro_ID = '$pro_ID'";
                        $result = $conn->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$row['name']}</td>";
                            echo "<td>{$row['time']}</td>";
                            echo "<td>{$row['location']}</td>";
                            echo "<td>{$row['class_name']}</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="section">
            <h2>聯絡資訊</h2>
            <ul class="contact-info">
                <li>信箱：<?php echo $professor['email'] ?? '未提供'; ?></li>
                <li>分機：<?php echo $professor['extension'] ?? '未提供'; ?></li>
            </ul>
        </div>
        
        <div class="image-gallery">
            <img src="title.png" alt="Footer Image">
        </div>
    </div>
</body>
</html>
<?php
} else {
    echo "<p>找不到教授資料</p>";
}
?>