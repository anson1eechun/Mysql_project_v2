<?php
// 引入資料庫連接文件
require_once 'config.php';

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


<!-- 頂部：Logo + 導覽列 + English 按鈕 -->


<body>
    <header class="header-top">
    <div class="container header-inner">
        <div class="logo">
            <img src="title.png" alt="網站標誌" />
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="#">招生/ADMISSION</a></li>
                <li><a href="#">關於本系</a></li>
                <li><a href="#">課程介紹</a></li>
                <li><a href="#">系所成員</a></li>
                <li><a href="#">訊息與公告</a></li>
                <li><a href="#">問卷回饋</a></li>
            </ul>
        </nav>
        <a href="login.php" class="btn-english">English</a>


        </div>
    </div>
</header>
    <div class="container">
        
        <div class="professor-header">
            <div class="black-bar"></div>
            <h1><?php echo htmlspecialchars($professor['name'] ?? '-'); ?></h1>
            <p class="position"><?php echo htmlspecialchars($professor['position'] ?? '-'); ?></p>
        </div>

        <div class="section">
            <div class="intro-wrapper">
                <div class="intro-content">
                    <p><?php echo htmlspecialchars($professor['introduction'] ?? '-'); ?></p>
                </div>
                <div class="intro-photo">
<<<<<<< HEAD
                    <img src=<?php echo $professor['photo']; ?>" alt="<?php echo $professor['name']; ?>">
=======
                    <img src="<?php echo htmlspecialchars($professor['photo'] ?? 'uploads/teacher_photo.jpg'); ?>" alt="<?php echo htmlspecialchars($professor['name'] ?? '無名教授'); ?>">
>>>>>>> 30f2dd4 (feat: 完成所有管理區塊導覽列與圖片上傳、資料展示等功能)
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
                    echo "<li>" . htmlspecialchars($row['department'] ?? '-') . " " . htmlspecialchars($row['degree'] ?? '-') . "</li>";
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
                    echo "<li>" . htmlspecialchars($row['item'] ?? '-') . "</li>";
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
                        echo "<li>" . htmlspecialchars($row['title'] ?? '-') . " (" . htmlspecialchars($row['date'] ?? '-') . ")</li>";
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
                        echo "<li>" . htmlspecialchars($row['title'] ?? '-') . " (" . htmlspecialchars($row['date'] ?? '-') . ")</li>";
                    }
                    ?>
                </ul>
            </details>
        </div>
        
        <div class="section">
            <h2>經歷</h2>
            <div class="experience-container">
                <div class="experience-section">
                    <h3>校內經歷</h3>
                    <ul>
                        <?php
                        $sql = "SELECT * FROM experience WHERE pro_ID = '$pro_ID' AND category = '校內'";
                        $result = $conn->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            echo "<li>" . htmlspecialchars($row['department'] ?? '-') . " " . htmlspecialchars($row['position'] ?? '-') . "</li>";
                        }
                        ?>
                    </ul>
                </div>
                <div class="experience-section">
                    <h3>校外經歷</h3>
                    <ul>
                        <?php
                        $sql = "SELECT * FROM experience WHERE pro_ID = '$pro_ID' AND category = '校外'";
                        $result = $conn->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            echo "<li>" . htmlspecialchars($row['department'] ?? '-') . " " . htmlspecialchars($row['position'] ?? '-') . "</li>";
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="section">
            <h2>獎項</h2>
            <details>
                <summary>獲獎紀錄</summary>
                <ul>
                    <?php
                    $sql = "SELECT * FROM award WHERE pro_ID = '$pro_ID'";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()) {
                        echo "<li>" . htmlspecialchars($row['title'] ?? '-') . " (" . htmlspecialchars($row['date'] ?? '-') . ")<br>";
                        echo "主辦單位：" . htmlspecialchars($row['organizer'] ?? '-') . "<br>";
                        echo "參賽主題：" . htmlspecialchars($row['topic'] ?? '-') . "<br>";
                        echo "指導學生：" . htmlspecialchars($row['student_list'] ?? '-') . "</li>";
                    }
                    ?>
                </ul>
            </details>
        </div>

        <div class="section">
            <h2>演講</h2>
            <details>
                <summary>演講紀錄</summary>
                <ul>
                    <?php
                    $sql = "SELECT * FROM lecture WHERE pro_ID = '$pro_ID'";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()) {
                        echo "<li>" . htmlspecialchars($row['title'] ?? '-') . "<br>";
                        echo "地點：" . htmlspecialchars($row['location'] ?? '-') . "<br>";
                        echo "日期：" . htmlspecialchars($row['date'] ?? '-') . "</li>";
                    }
                    ?>
                </ul>
            </details>
        </div>

        <div class="section">
            <h2>專案</h2>
            <details>
                <summary>研究計畫</summary>
                <ul>
                    <?php
                    $sql = "SELECT * FROM project WHERE pro_ID = '$pro_ID'";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()) {
                        echo "<li>" . htmlspecialchars($row['name'] ?? '-') . "<br>";
                        echo "類別：" . htmlspecialchars($row['category'] ?? '-') . "<br>";
                        echo "日期：" . htmlspecialchars($row['date'] ?? '-') . "<br>";
                        echo "計畫編號：" . htmlspecialchars($row['number'] ?? '-') . "<br>";
                        echo "計畫角色：" . htmlspecialchars($row['role'] ?? '-') . "</li>";
                    }
                    ?>
                </ul>
            </details>
        </div>
        
        <div class="section">
            <h2>課程資訊</h2>
            <div class="course-table-wrapper">
                <a href="schedule.php" class="schedule-link">查看完整課表</a>
                <table class="schedule-table">
                    <thead>
                        <tr>
                            <th>時段</th>
                            <th>星期一</th>
                            <th>星期二</th>
                            <th>星期三</th>
                            <th>星期四</th>
                            <th>星期五</th>
                            <th>星期六</th>
                            <th>星期日</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // 定義時段對應
                        $time_slots = [
                            '08:10 - 09:00',
                            '09:10 - 10:00',
                            '10:10 - 11:00',
                            '11:10 - 12:00',
                            '13:10 - 14:00',
                            '14:10 - 15:00',
                            '15:10 - 16:00',
                            '16:10 - 17:00',
                            '17:10 - 18:00',
                            '18:10 - 19:00',
                            '19:10 - 20:00',
                            '20:10 - 21:00',
                            '21:10 - 22:00'
                        ];

                        // 初始化課表陣列
                        $schedule = array_fill(1, 7, array_fill(1, 13, []));

                        // 獲取課程資料
                        $sql = "SELECT * FROM courses WHERE pro_ID = '$pro_ID'";
                        $result = $conn->query($sql);
                        
                        // 處理課程時間
                        while ($row = $result->fetch_assoc()) {
                            $times = explode(',', $row['time'] ?? '');
                            foreach ($times as $time) {
                                if (strlen($time) >= 2) {
                                    $day = intval($time[0]); // 星期幾
                                    $period = intval(substr($time, 1)); // 第幾節
                                    if ($day >= 1 && $day <= 7 && $period >= 1 && $period <= 13) {
                                        // 將課程資料加入陣列，而不是覆蓋
                                        $schedule[$day][$period][] = htmlspecialchars($row['name'] ?? '-') . '<br>(' . htmlspecialchars($row['class'] ?? '-') . ')';
                                    }
                                } else if ($time === '-') {
                                    // 如果時間是 '-'，則直接顯示 '-'。這通常不應該發生，因為我們期望 time 有值。
                                    // 但為了確保穩健性，我們將其保留。
                                    $schedule[$day][$period][] = '-';
                                }
                            }
                        }

                        // 生成課表
                        foreach ($time_slots as $index => $time_slot) {
                            $period = $index + 1;
                            echo "<tr>";
                            echo "<td>$time_slot</td>";
                            for ($day = 1; $day <= 7; $day++) {
                                echo "<td>";
                                // 顯示該時段的所有課程
                                if (!empty($schedule[$day][$period])) {
                                    foreach ($schedule[$day][$period] as $course) {
                                        echo "<div class='course-item'>$course</div>";
                                    }
                                } else {
                                    echo '-'; // 如果沒有課程，顯示 '-' 
                                }
                                echo "</td>";
                            }
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <style>
            .schedule-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            .schedule-table th,
            .schedule-table td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: center;
            }

            .schedule-table th {
                background-color: #f5f5f5;
                font-weight: bold;
            }

            .schedule-table td {
                height: 60px;
                vertical-align: middle;
            }

            .course-item {
                padding: 4px;
                margin: 2px 0;
                background-color: #f8f9fa;
                border-radius: 3px;
                font-size: 0.9em;
            }

            .course-item:hover {
                background-color: #e9ecef;
            }

            .schedule-link {
                display: inline-block;
                margin-bottom: 10px;
                color: #007bff;
                text-decoration: none;
            }

            .schedule-link:hover {
                text-decoration: underline;
            }

            .admin-button {
                text-align: center;
                margin-top: 20px;
            }

            .admin-link {
                display: inline-block;
                padding: 10px 20px;
                background-color: #4CAF50;
                color: white;
                text-decoration: none;
                border-radius: 5px;
                font-weight: bold;
                transition: background-color 0.3s;
            }

            .admin-link:hover {
                background-color: #45a049;
            }

            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                background-color: white;
            }
            .container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 20px;
            }
            .professor-header {
                text-align: center;
                margin-bottom: 30px;
                position: relative;
            }
            .black-bar {
                height: 4px;
                background-color: black;
                width: 100%;
                margin-bottom: 20px;
            }
            h1 {
                margin: 0;
                font-size: 2.5em;
                color: #333;
            }
            .position {
                font-size: 1.2em;
                color: #666;
                margin-top: 10px;
            }
            .section {
                margin-bottom: 40px;
            }
            h2 {
                color: #333;
                border-bottom: 2px solid #333;
                padding-bottom: 10px;
            }
            h3 {
                color: #444;
                margin-top: 0;
                margin-bottom: 15px;
            }
            .intro-wrapper {
                display: flex;
                align-items: center;
                gap: 30px;
            }
            .intro-content {
                flex: 1;
            }
            .intro-photo {
                flex: 0 0 300px;
            }
            .intro-photo img {
                width: 100%;
                height: auto;
                border-radius: 5px;
            }
            .experience-container {
                display: flex;
                gap: 40px;
            }
            .experience-section {
                flex: 1;
            }
            .course-table-wrapper {
                overflow-x: auto;
            }
            .schedule-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            .schedule-table th,
            .schedule-table td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: center;
            }
            .schedule-table th {
                background-color: #f5f5f5;
            }
            .course-item {
                margin: 5px 0;
                padding: 5px;
                background-color: #f9f9f9;
                border-radius: 3px;
            }
            .schedule-link {
                display: inline-block;
                margin-bottom: 10px;
                color: #0066cc;
                text-decoration: none;
            }
            .schedule-link:hover {
                text-decoration: underline;
            }
            .contact-info {
                list-style: none;
                padding: 0;
            }
            .contact-info li {
                margin-bottom: 10px;
            }
            details {
                margin: 10px 0;
            }
            summary {
                cursor: pointer;
                color: #0066cc;
                font-weight: bold;
                padding: 5px 0;
            }
            summary:hover {
                text-decoration: underline;
            }
            ul {
                list-style-type: disc;
                padding-left: 20px;
            }
            li {
                margin-bottom: 15px;
                line-height: 1.5;
            }
        </style>

        <div class="section">
            <h2>聯絡資訊</h2>
            <ul class="contact-info">
                <li>信箱：<?php echo htmlspecialchars($professor['email'] ?? '-'); ?></li>
                <li>分機：<?php echo htmlspecialchars($professor['phone'] ?? '-'); ?></li>
                <li>辦公室：<?php echo htmlspecialchars($professor['office'] ?? '-'); ?></li>
            </ul>
        </div>
        
        
    </div>
</body>
<script>
    // 按鍵序列檢測
    let keySequence = [];
    const secretCode = ['ArrowLeft', 'ArrowLeft', 'ArrowRight', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'ArrowUp', 'ArrowDown'];
    
    document.addEventListener('keydown', (e) => {
        // 將按下的鍵加入序列
        keySequence.push(e.key);
        
        // 保持序列長度不超過密碼長度
        if (keySequence.length > secretCode.length) {
            keySequence.shift();
        }
        
        // 檢查是否匹配密碼
        if (keySequence.join(',') === secretCode.join(',')) {
            // 導向到 2d.html
            window.location.href = '2d.html';
        }
    });
</script>
</html>
<?php
} else {
    echo "<p>找不到教授資料</p>";
}
?>