<?php
// course.php – 顯示教授課表（星期為列，節次為行）
require_once 'config.php';
$conn->set_charset('utf8mb4');

$pro_ID = isset($_GET['id']) ? $_GET['id'] : '';
if (!$pro_ID) {
    echo '<p style="text-align:center;color:#c00;">未指定教授</p>';
    exit;
}
// 查詢教授基本資料
$sql_prof = "SELECT name FROM professor WHERE pro_ID = '".$conn->real_escape_string($pro_ID)."'";
$res_prof = $conn->query($sql_prof);
$prof = $res_prof->fetch_assoc();
$prof_name = $prof ? $prof['name'] : '';

// 查詢所有課程
$sql = "SELECT * FROM courses WHERE pro_ID = '".$conn->real_escape_string($pro_ID)."'";
$result = $conn->query($sql);

// 節次與時間對照
$periods = [
    1 => '08:10~09:00',
    2 => '09:10~10:00',
    3 => '10:10~11:00',
    4 => '11:10~12:00',
    5 => '12:10~13:00',
    6 => '13:10~14:00',
    7 => '14:10~15:00',
    8 => '15:10~16:00',
    9 => '16:10~17:00',
    10 => '17:10~18:00',
    11 => '18:10~19:00',
    12 => '19:10~20:00',
    13 => '20:10~21:00',
    14 => '21:10~22:00',
];
$weekdays = [1=>'星期一',2=>'星期二',3=>'星期三',4=>'星期四',5=>'星期五',6=>'星期六',7=>'星期日'];

// 建立空課表陣列 [period][weekday]
$schedule = [];
foreach ($periods as $p => $t) {
    foreach ($weekdays as $w => $wd) {
        $schedule[$p][$w] = '';
    }
}
// 將課程填入課表
while ($row = $result->fetch_assoc()) {
    $times = explode(',', $row['time']);
    foreach ($times as $code) {
        $code = trim($code);
        if (preg_match('/^([1-7])([0-9]{1,2})$/', $code, $m)) {
            $w = intval($m[1]);
            $p = intval($m[2]);
        } else if (strlen($code) == 2) {
            $w = intval(substr($code,0,1));
            $p = intval(substr($code,1,1));
        } else if (strlen($code) == 3) {
            $w = intval(substr($code,0,1));
            $p = intval(substr($code,1,2));
        } else {
            continue;
        }
        if (isset($schedule[$p][$w])) {
            $schedule[$p][$w] .= ($schedule[$p][$w] ? '<br>' : '') . htmlspecialchars($row['name']) . ' (' . htmlspecialchars($row['class']) . ')';
        }
    }
}
?><!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($prof_name); ?> - 課表</title>
    <link rel="stylesheet" href="styles.css" />
    <style>
        body { font-family: 'Noto Sans TC', sans-serif; background: #f9f9f9; margin: 0; }
        .schedule-table { width: 100%; border-collapse: collapse; margin: 40px auto; max-width: 1100px; background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
        .schedule-table th, .schedule-table td { border: none; padding: 16px 8px; text-align: center; font-size: 1em; }
        .schedule-table th { background: #f5f7fa; color: #003366; font-weight: 700; }
        .schedule-table tr:nth-child(even) td { background: #f7f7f7; }
        .schedule-table tr:nth-child(odd) td { background: #fff; }
        .schedule-table td { min-width: 120px; }
        .period-label { font-weight: 600; color: #005bac; }
        .time-label { font-size: 0.95em; color: #888; }
        .back-btn { display: inline-block; margin: 24px 0 0 40px; padding: 8px 18px; background: #005bac; color: #fff; border-radius: 8px; text-decoration: none; font-weight: 500; }
        .back-btn:hover { background: #003366; }
        @media (max-width: 900px) {
            .schedule-table { font-size: 0.92em; }
            .schedule-table td, .schedule-table th { padding: 8px 2px; }
        }
    </style>
</head>
<body>
    <a href="javascript:history.back()" class="back-btn">← 返回</a>
    <h2 style="text-align:center;color:#003366;margin-top:24px;">
    <?php echo htmlspecialchars($prof_name); ?> - 課表
    </h2>
    <table class="schedule-table">
        <tr>
            <th></th>
            <?php foreach ($weekdays as $w => $wd): ?>
                <th><?php echo $wd; ?></th>
            <?php endforeach; ?>
        </tr>
        <?php foreach ($periods as $p => $t): ?>
        <tr>
            <td style="text-align:left;">
                <span class="period-label">第<?php echo $p; ?>節</span><br>
                <span class="time-label"><?php echo $t; ?></span>
            </td>
            <?php foreach ($weekdays as $w => $wd): ?>
                <td><?php echo $schedule[$p][$w] ? $schedule[$p][$w] : '-'; ?></td>
            <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
