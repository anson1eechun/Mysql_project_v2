<?php
// 引入資料庫連接文件
require_once 'config.php';

// 取得教授基本資料
$pro_ID = 'A001';

// 獲取教授基本資料
$sql = "SELECT * FROM professor WHERE pro_ID = '$pro_ID'";
$result = $conn->query($sql);
$professor = $result->fetch_assoc();

// 獲取課表資料
$sql = "SELECT name, time, location 
        FROM courses 
        WHERE pro_ID = '$pro_ID' 
        ORDER BY FIELD(
            SUBSTRING_INDEX(time, ' ', 1),
            '星期一','星期二','星期三','星期四','星期五','星期六','星期日'
        ), time";
$result = $conn->query($sql);

// 初始化課表陣列
$schedule = array(
    '星期一' => array(),
    '星期二' => array(),
    '星期三' => array(),
    '星期四' => array(),
    '星期五' => array(),
    '星期六' => array(),
    '星期日' => array()
);

// 整理課表資料
while ($row = $result->fetch_assoc()) {
    $day = explode(' ', $row['time'])[0];
    $schedule[$day][] = $row;
}
?>

<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $professor['name']; ?>教授課表</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="schedule.css">
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="title2.png" alt="Logo">
        </div>
        
        <h1><?php echo $professor['name']; ?>教授課表</h1>
        
        <div class="schedule-container">
            <?php foreach ($schedule as $day => $classes): ?>
            <div class="day-schedule">
                <h2><?php echo $day; ?></h2>
                <?php if (empty($classes)): ?>
                    <p class="no-class">無課程安排</p>
                <?php else: ?>
                    <div class="class-list">
                        <?php foreach ($classes as $class): ?>
                            <div class="class-item">
                                <div class="class-time"><?php echo $class['time']; ?></div>
                                <div class="class-info">
                                    <div class="class-name"><?php echo $class['name'] ?: '未定'; ?></div>
                                    <div class="class-location"><?php echo $class['location'] ?: '未定'; ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="back-link">
            <a href="index.php">返回教授介紹</a>
        </div>
    </div>
</body>
</html> 