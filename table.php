<?php

require_once 'config.php';
// 检查连接
if ($db_link->connect_error) {
    die("Connection failed: " . $db_link->connect_error);
}

// 获取课程表数据
$sql = "SELECT 課程名稱, 開課時間, 開課班級 FROM 課程 WHERE 開課教授 = '劉明機'";
$result = $db_link->query($sql);

$schedule = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $course_name = $row["課程名稱"];
        $course_class = $row["開課班級"];
        $times = explode(",", $row["開課時間"]); // 使用逗号分隔多个时间段
        foreach ($times as $time) {
            if (strpos($time, '-') !== false) {
                list($day, $start, $end) = explode('-', $time);
                for ($i = $start; $i <= $end; $i++) {
                    $schedule["$day$i"][] = [
                        "course_name" => $course_name,
                        "course_class" => $course_class
                    ];
                }
            } else {
                $day = substr($time, 0, 1);
                $period = substr($time, 1);
                $schedule["$day$period"][] = [
                    "course_name" => $course_name,
                    "course_class" => $course_class
                ];
            }
        }
    }
}

// 关闭数据库连接
$db_link->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>課表</title>
    <style>
        body {
            margin: auto;
            width: 1100px;
            position: relative;
        }
        table {
            border-collapse: collapse;
            width: 1100px; 
        }
        th, td {
            padding: 15px; 
            text-align: left; 
            vertical-align: top; 
            width: 120px;
        }
        th:first-child, td:first-child {
            border-left: 1px solid transparent; 
        }
        th:last-child, td:last-child {
            border-right: 1px solid transparent; 
        }
        td {
            border-top: 1px solid transparent; 
        }
        .highlighted {
            background-color: #F0F0F0; 
        }
        .home {
            position: absolute;
            top: 0px;
            left: 1000px;
            padding: 5px;
            border: 3px solid ;
            border-color: black;
            display: inline-block;
            font-size: 24px; 
            border-radius:24px;
            color: black;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <h1>劉明機 老師課表及請益時間</h1>
    <a class="home" href="index.php">回主頁</a>
    <table>
        <tr>
            <th></th>
            <th>星期一</th>
            <th>星期二</th>
            <th>星期三</th>
            <th>星期四</th>
            <th>星期五</th>
            <th>星期六</th>
            <th>星期日</th>
        </tr>
        <?php
            $time_slots = [
                "第一節<br>08:10~09:00",
                "第二節<br>09:10~10:00",
                "第三節<br>10:10~11:00",
                "第四節<br>11:10~12:00",
                "第五節<br>12:10~13:00",
                "第六節<br>13:10~14:00",
                "第七節<br>14:10~15:00",
                "第八節<br>15:10~16:00",
                "第九節<br>16:10~17:00",
                "第十節<br>17:10~18:00",
                "第十一節<br>18:10~19:00",
                "第十二節<br>19:10~20:00",
                "第十三節<br>20:10~21:00",
                "第十四節<br>21:10~22:00"
            ];

            for ($i = 1; $i <= 14; $i++) {
                $highlighted = $i % 2 == 0 ? "" : "highlighted";
                echo "<tr class='$highlighted'>";
                echo "<td>{$time_slots[$i-1]}</td>";

                for ($j = 1; $j <= 7; $j++) {
                    $schedule_key = "$j$i";
                    echo "<td>";
                    if (isset($schedule[$schedule_key])) {
                        foreach ($schedule[$schedule_key] as $entry) {
                            echo htmlspecialchars($entry["course_name"]) . "<br>" . htmlspecialchars($entry["course_class"]) . "<br>";
                        }
                    } else {
                        echo "-";
                    }
                    echo "</td>";
                }

                echo "</tr>";
            }
        ?>
    </table>
</body>
</html>




