<?php
// index_v3.php – 前台教授詳細頁，結合 teacher_map_v3 版型與分類
require_once 'config.php';
$conn->set_charset('utf8mb4');

// 分類對應
$categories = [
    '系主任' => '系主任',
    '榮譽特聘講座' => '榮譽特聘講座',
    '講座教授' => '講座教授',
    '特約講座' => '特約講座',
    '特聘教授' => '特聘教授',
    '專任教師' => '專任教師',
    '兼任教師' => '兼任教師',
    '行政人員' => '行政人員',
    '退休教師' => '退休教師',
];
$selected = isset($_GET['cat']) ? $_GET['cat'] : '';
$pro_ID = isset($_GET['id']) ? $_GET['id'] : '';

// 取得教授資料
$professor = null;
if ($pro_ID) {
    $sql = "SELECT * FROM professor WHERE pro_ID = '" . $conn->real_escape_string($pro_ID) . "'";
    $result = $conn->query($sql);
    $professor = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>系所成員<?php if($professor) echo ' - '.htmlspecialchars($professor['name']); ?></title>
    <link rel="stylesheet" href="styles.css" />
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans TC', sans-serif; background: #f9f9f9; margin: 0; }
        .header-top { background: #fff; border-bottom: 1px solid #e5e5e5; }
        .header-inner {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            max-width: 1200px;
            margin: 0 auto;
            padding: 4px 8px;
        }
        .logo {
            margin-right: auto;
        }
        .logo img {
            height: 120px;
            width: auto;
            margin-right: 0;
            display: block;
        }
        .main-nav {
            position: relative;
        }
        .main-nav ul {
            display: flex;
            gap: 4px;
            list-style: none;
            margin: 0;
            padding: 0 4px;
            align-items: center;
            /* background: #f5f7fa; */
            background: none;
            border-radius: 8px;
            padding: 2px 6px;
        }
        .main-nav a {
            color: #003366;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.92em;
            white-space: nowrap;
            padding: 2px 8px;
            border-radius: 5px;
            background: transparent;
            transition: background .2s, color .2s;
        }
        .main-nav a.active, .main-nav a:hover {
            color: #005bac;
            background: none;
            border-bottom: 2px solid #005bac;
        }
        .btn-english {
            border: 1px solid #003366;
            background: #fff;
            color: #003366;
            border-radius: 12px;
            padding: 2px 10px;
            font-weight: 500;
            margin-left: 6px;
            font-size: 0.92em;
            text-decoration: none;
            transition: background .2s;
            height: 22px;
            display: flex;
            align-items: center;
        }
        .btn-english:hover {
            background: #003366;
            color: #fff;
        }
        .main-content {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            padding: 32px 24px;
            margin-top: 32px;
        }
        .sidebar {
            background: #f5f7fa;
            border-radius: 12px;
            box-shadow: 0 1px 6px rgba(0,0,0,0.04);
            padding: 24px 12px 24px 18px;
            margin-right: 32px;
            min-width: 180px;
        }
        .sidebar-title {
            font-size: 1.25em;
            font-weight: 700;
            color: #003366;
            margin-bottom: 18px;
            border-bottom: 3px solid #003366;
            padding-bottom: 6px;
            letter-spacing: 1px;
        }
        .member-title-area {
            border-bottom: 4px solid #003366;
            margin-bottom: 24px;
            padding-bottom: 8px;
        }
        .member-title {
            font-size: 2em;
            font-weight: 600;
            color: #003366;
            margin: 0;
            background: none;
            border: none;
            padding: 0;
        }
        .main-content { display: flex; max-width: 1200px; margin: 40px auto; padding: 0 20px; gap: 40px; }
        .sidebar {
            width: 170px;
            min-width: 140px;
            max-width: 200px;
            background: transparent;
            border-radius: 0;
            box-shadow: none;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            height: auto;
            min-height: unset;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 24px 0 0 0;
            width: 100%;
        }
        .sidebar li {
            padding: 8px 0 8px 18px;
            margin-bottom: 8px;
            background: #e3eaf3;
            color: #1a3557;
            font-size: 0.98em;
            border-left: 4px solid #b0c4de;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: none;
            transition: background .2s, color .2s, border .2s;
        }
        .sidebar li.active, .sidebar li:hover {
            background: #c7d6ea;
            color: #005bac;
            border-left: 4px solid #005bac;
        }
        .member-area { flex: 1; }
        .member-title { font-size: 2em; font-weight: 600; color: #003366; margin-bottom: 24px; border-bottom: 4px solid #003366; padding-bottom: 8px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 32px; }
        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 24px rgba(0,91,172,0.18), 0 1.5px 6px rgba(0,0,0,0.08);
            display: flex;
            gap: 20px;
            align-items: flex-start;
            padding: 24px 20px 20px 20px;
            transition: box-shadow .2s, transform .2s;
            border: 1.5px solid #e0e8f3;
            margin-top: 0;
        }
        .card:hover {
            box-shadow: 0 10px 32px rgba(0,91,172,0.22), 0 2px 8px rgba(0,0,0,0.10);
            transform: translateY(-2px) scale(1.025);
            border-color: #b0c4de;
        }
        .card img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            background: #eee;
            box-shadow: 0 2px 8px rgba(0,91,172,0.10);
        }
        .main-nav .dropdown {
            position: relative;
        }
        .main-nav .dropdown-menu {
            display: none;
            position: absolute;
            left: 0;
            top: 100%;
            min-width: 160px;
            background: #fff;
            box-shadow: 0 4px 16px rgba(0,0,0,0.10);
            border-radius: 8px;
            z-index: 100;
            padding: 8px 0;
            margin: 0;
            list-style: none;
        }
        .main-nav .dropdown-menu li {
            padding: 8px 20px 8px 16px;
            color: #003366;
            font-size: 0.92em;
            cursor: pointer;
            transition: background .18s, color .18s;
            border: none;
            background: none;
        }
        .main-nav .dropdown-menu li.active,
        .main-nav .dropdown-menu li:hover {
            background: #e3eaf3;
            color: #005bac;
        }
        .main-nav .dropdown:hover .dropdown-menu,
        .main-nav .dropdown.open .dropdown-menu {
            display: block;
        }
        .main-nav .dropdown > a {
            cursor: pointer;
        }
        @media (max-width: 900px) {
            .main-content { flex-direction: column; padding: 12px 4px; }
            .sidebar { margin-right: 0; margin-bottom: 18px; }
            .main-nav .dropdown-menu {
                left: auto;
                right: 0;
            }
        }
        .contact-btn-group { margin-bottom: 10px; }
        .btn-row { display: flex; gap: 10px; margin-bottom: 10px; }
        .btn-row:last-child { margin-bottom: 0; }
        .info-btn {
            display: flex;
            align-items: center;
            border-radius: 18px;
            font-size: 0.98em;
            padding: 3px 12px 3px 10px;
            border-width: 1.5px;
            border-style: solid;
            min-width: unset;
            width: auto;
            font-weight: 500;
            transition: background .18s, color .18s, border .18s;
            cursor: pointer;
            justify-content: center;
            line-height: 1.6;
            box-sizing: border-box;
        }
        .info-btn-blue {
            border-color: #2196f3;
            color: #2196f3;
            background: #fff;
        }
        .info-btn-blue:hover {
            background: #e3f1fd;
            color: #1565c0;
        }
        .info-btn-outline {
            border-color: #222;
            color: #222;
            background: #fff;
        }
        .info-btn-outline:hover {
            background: #f5f5f5;
            color: #005bac;
            border-color: #005bac;
        }
        .icon-mail::before { content: '\2709'; margin-right: 6px; font-size: 1em; }
        .icon-phone::before { content: '\1F4DE'; margin-right: 6px; font-size: 1em; }
        .icon-calendar::before { content: '\1F4C5'; margin-right: 6px; font-size: 1em; }
        .icon-office::before { content: '\1F3E2'; margin-right: 6px; font-size: 1em; }
        @media (max-width: 700px) {
            .btn-row { flex-direction: column; gap: 8px; }
            .info-btn { width: 100%; justify-content: flex-start; }
        }
    </style>
</head>
<body>
    <header class="header-top">
        <div class="header-inner">
            <div class="logo">
                <img src="title.png" alt="ECS Logo" />
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="#">招生/ADMISSION</a></li>
                    <li><a href="#">關於本系</a></li>
                    <li><a href="#">課程介紹</a></li>
                    <li class="dropdown">
                        <a href="index_v3.php" class="active" id="memberDropdown">系所成員</a>
                        <ul class="dropdown-menu" id="dropdownMenu">
                            <?php foreach($categories as $catName => $catValue): ?>
                                <?php if($catValue !== ''): ?>
                                    <li data-cat="<?php echo $catValue; ?>" onclick="window.location.href='index_v3.php?cat=<?php echo urlencode($catValue); ?>'"<?php if($selected===$catValue)echo' class="active"';?>><?php echo $catName; ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <li><a href="#">訊息與公告</a></li>
                    <li><a href="#">問卷回饋</a></li>
                </ul>
            </nav>
            <a href="login.php" class="btn-english">English</a>
        </div>
    </header>
    <div class="main-content">
        <aside class="sidebar">
            <div class="sidebar-title">系所成員</div>
            <ul id="category-list">
                <?php foreach($categories as $catName => $catValue): ?>
                    <?php if($catValue !== ''): ?>
                        <li data-cat="<?php echo $catValue; ?>"<?php if($selected===$catValue)echo' class="active"';?>><?php echo $catName; ?></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </aside>
        <section class="member-area">
            <?php if ($professor): ?>
                <div class="professor-detail">
                    <div class="professor-header" style="margin-top:36px;margin-bottom:18px;display:flex;align-items:center;gap:32px;">
                        <div style="flex:1;">
                            <h1 style="font-size:2em;margin:0 0 6px 0;line-height:1.2;letter-spacing:1px;"><?php echo htmlspecialchars($professor['name'] ?? '-'); ?></h1>
                            <p class="position" style="font-size:1.1em;color:#337ab7;margin:0 0 0 2px;"> <?php echo htmlspecialchars($professor['position'] ?? '-'); ?></p>
                        </div>
                        <div class="intro-photo" style="flex:0 0 220px;max-width:220px;">
                            <img src="<?php echo htmlspecialchars($professor['photo'] ?? 'uploads/teacher_photo.jpg'); ?>" alt="<?php echo htmlspecialchars($professor['name'] ?? '無名教授'); ?>" style="width:100%;border-radius:8px;border:1px solid #eee;">
                        </div>
                    </div>
                    <hr style="border:0;border-top:1.5px solid #e3eaf3;margin:0 0 28px 0;">
                    <div class="intro-wrapper" style="display:flex;align-items:flex-start;gap:40px;margin-bottom:32px;">
                        <div class="intro-content" style="flex:1;min-width:220px;">
                            <div style="font-size:1.08em;line-height:1.9;white-space:pre-line;margin-bottom:12px;">
                                <?php echo nl2br(htmlspecialchars($professor['introduction'] ?? '-')); ?>
                            </div>
                            <hr style="border:0;border-top:1.5px solid #e3eaf3;margin:18px 0 24px 0;">
                            <div class="contact-btn-group">
                                <div class="btn-row">
                                    <a class="info-btn info-btn-blue" href="https://mail.google.com/mail/?view=cm&to=<?php echo urlencode($professor['email'] ?? ''); ?>" target="_blank">
                                        <span class="icon-mail"></span> 信箱:<?php echo htmlspecialchars($professor['email'] ?? '-'); ?>
                                    </a>
                                    <?php
                                        $ext = $professor['phone'] ?? '';
                                        $ext_num = preg_replace('/^.*#/', '', $ext); // 只取#後的分機碼
                                    ?>
                                    <a class="info-btn info-btn-blue" href="tel:<?php echo htmlspecialchars($ext_num); ?>">
                                        <span class="icon-phone"></span> 分機:<?php echo htmlspecialchars($ext_num ?: '-'); ?>
                                    </a>
                                </div>
                                <div class="btn-row">
                                    <a class="info-btn info-btn-outline" href="course.php?id=<?php echo urlencode($professor['pro_ID']); ?>">
                                        <span class="icon-calendar"></span> 課表時間
                                    </a>
                                    <button class="info-btn info-btn-outline" id="showMapBtn" type="button">
                                        <span class="icon-office"></span> 辦公室:<?php echo htmlspecialchars($professor['office'] ?? '-'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="section">
                        <h2>學歷</h2>
                        <ul>
                            <?php
                            $sql = "SELECT * FROM education WHERE pro_ID = '" . $conn->real_escape_string($pro_ID) . "' ORDER BY edu_ID";
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
                            $sql = "SELECT * FROM expertise WHERE pro_ID = '" . $conn->real_escape_string($pro_ID) . "' ORDER BY expertise_ID";
                            $result = $conn->query($sql);
                            while ($row = $result->fetch_assoc()) {
                                echo "<li>" . htmlspecialchars($row['item'] ?? '-') . "</li>";
                            }
                            ?>
                        </ul>
                    </div>
                    <div class="section">
                        <h2>經歷</h2>
                        <div class="experience-container" style="display:flex;gap:32px;">
                            <div class="experience-section" style="flex:1;">
                                <h3>校內經歷</h3>
                                <ul>
                                    <?php
                                    $sql = "SELECT * FROM experience WHERE pro_ID = '" . $conn->real_escape_string($pro_ID) . "' AND category = '校內' ORDER BY experience_ID";
                                    $result = $conn->query($sql);
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<li>" . htmlspecialchars($row['department'] ?? '-') . " " . htmlspecialchars($row['position'] ?? '-') . "</li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                            <div class="experience-section" style="flex:1;">
                                <h3>校外經歷</h3>
                                <ul>
                                    <?php
                                    $sql = "SELECT * FROM experience WHERE pro_ID = '" . $conn->real_escape_string($pro_ID) . "' AND category = '校外' ORDER BY experience_ID";
                                    $result = $conn->query($sql);
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<li>" . htmlspecialchars($row['department'] ?? '-') . " " . htmlspecialchars($row['position'] ?? '-') . "</li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="section" id="scheduleSection" style="display:none;">
                        <h2>課程資訊</h2>
                        <div class="course-table-wrapper">
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
                                    $schedule = array_fill(1, 7, array_fill(1, 13, []));
                                    $sql = "SELECT * FROM courses WHERE pro_ID = '" . $conn->real_escape_string($pro_ID) . "'";
                                    $result = $conn->query($sql);
                                    while ($row = $result->fetch_assoc()) {
                                        $times = explode(',', $row['time'] ?? '');
                                        foreach ($times as $time) {
                                            if (strlen($time) >= 2) {
                                                $day = intval($time[0]);
                                                $period = intval(substr($time, 1));
                                                if ($day >= 1 && $day <= 7 && $period >= 1 && $period <= 13) {
                                                    $schedule[$day][$period][] = htmlspecialchars($row['name'] ?? '-') . '<br>(' . htmlspecialchars($row['class'] ?? '-') . ')';
                                                }
                                            } else if ($time === '-') {
                                                $schedule[$day][$period][] = '-';
                                            }
                                        }
                                    }
                                    foreach ($time_slots as $index => $time_slot) {
                                        $period = $index + 1;
                                        echo "<tr>";
                                        echo "<td>$time_slot</td>";
                                        for ($day = 1; $day <= 7; $day++) {
                                            echo "<td>";
                                            if (!empty($schedule[$day][$period])) {
                                                foreach ($schedule[$day][$period] as $course) {
                                                    echo "<div class='course-item'>$course</div>";
                                                }
                                            } else {
                                                echo '-';
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
                    <div class="section">
                        <h2>期刊論文</h2>
                        <details>
                            <summary>發表期刊論文</summary>
                            <ul>
                                <?php
                                $sql = "SELECT * FROM journal WHERE pro_ID = '" . $conn->real_escape_string($pro_ID) . "' ORDER BY date DESC";
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
                                $sql = "SELECT * FROM conference WHERE pro_ID = '" . $conn->real_escape_string($pro_ID) . "' ORDER BY date DESC";
                                $result = $conn->query($sql);
                                while ($row = $result->fetch_assoc()) {
                                    echo "<li>" . htmlspecialchars($row['title'] ?? '-') . " (" . htmlspecialchars($row['date'] ?? '-') . ")</li>";
                                }
                                ?>
                            </ul>
                        </details>
                    </div>
                    <div class="section">
                        <h2>獎項</h2>
                        <details>
                            <summary>獲獎紀錄</summary>
                            <ul>
                                <?php
                                $sql = "SELECT * FROM award WHERE pro_ID = '" . $conn->real_escape_string($pro_ID) . "' ORDER BY award_ID";
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
                                $sql = "SELECT * FROM lecture WHERE pro_ID = '" . $conn->real_escape_string($pro_ID) . "' ORDER BY lecture_ID";
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
                                $sql = "SELECT * FROM project WHERE pro_ID = '" . $conn->real_escape_string($pro_ID) . "' ORDER BY project_ID";
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
                </div>
            <?php else: ?>
                <div class="grid">
                    <?php
                    $where = $selected ? "WHERE role = '" . $conn->real_escape_string($selected) . "'" : '';
                    $sql = "SELECT pro_ID, name, position, photo, phone, email FROM professor $where ORDER BY name";
                    $result = $conn->query($sql);
                    if ($result && $result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):
                            $id   = htmlspecialchars($row['pro_ID'], ENT_QUOTES, 'UTF-8');
                            $name = htmlspecialchars($row['name'],    ENT_QUOTES, 'UTF-8');
                            $position = htmlspecialchars($row['position'],ENT_QUOTES, 'UTF-8');
                            $photo = !empty($row['photo']) ? htmlspecialchars($row['photo'], ENT_QUOTES, 'UTF-8') : 'uploads/none.jpg';
                            $phone = htmlspecialchars($row['phone'] ?? '-', ENT_QUOTES, 'UTF-8');
                            $email = htmlspecialchars($row['email'] ?? '-', ENT_QUOTES, 'UTF-8');
                            $exp_sql = "SELECT item FROM expertise WHERE pro_ID = '" . $conn->real_escape_string($row['pro_ID']) . "'";
                            $exp_result = $conn->query($exp_sql);
                            $expertise_arr = [];
                            if ($exp_result && $exp_result->num_rows > 0) {
                                while ($exp_row = $exp_result->fetch_assoc()) {
                                    $zh = preg_replace('/[^\x{4e00}-\x{9fa5}]/u', '', $exp_row['item']);
                                    if ($zh !== '') $expertise_arr[] = $zh;
                                }
                            }
                            $expertise = $expertise_arr ? implode(' ', $expertise_arr) : '-';
                    ?>
                    <div class="card">
                        <a href="index_v3.php?id=<?php echo $id; ?><?php if($selected) echo '&cat=' . urlencode($selected); ?>">
                            <img src="<?php echo $photo; ?>" alt="<?php echo $name; ?>">
                        </a>
                        <div class="card-info">
                            <div class="card-name"><a href="index_v3.php?id=<?php echo $id; ?><?php if($selected) echo '&cat=' . urlencode($selected); ?>"><?php echo $name; ?></a></div>
                            <div class="card-position"><?php echo $position; ?></div>
                            <div class="card-contact">分機：<?php echo $phone; ?></div>
                            <div class="card-contact">信箱：<?php echo $email; ?></div>
                            <div class="card-expertise">專長：<?php echo $expertise; ?></div>
                        </div>
                    </div>
                    <?php endwhile; else: ?>
                        <p style="text-align:center; color:#666;">目前尚無教授資料。</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
    <div id="mapModal" class="map-modal" style="display:none;position:fixed;z-index:9999;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.45);align-items:center;justify-content:center;">
        <div style="background:#fff;padding:18px 18px 8px 18px;border-radius:10px;max-width:90vw;max-height:90vh;box-shadow:0 2px 16px rgba(0,0,0,0.18);position:relative;">
            <img src="uploads/iecs_map.jpg" alt="辦公室地圖" style="max-width:80vw;max-height:70vh;display:block;">
            <button id="closeMapBtn" style="position:absolute;top:8px;right:8px;background:#fff;border:1px solid #bbb;border-radius:50%;width:32px;height:32px;font-size:1.2em;cursor:pointer;">&times;</button>
        </div>
    </div>
    <script>
    // 下拉選單互動
    const dropdown = document.querySelector('.main-nav .dropdown');
    const dropdownMenu = document.getElementById('dropdownMenu');
    const dropdownBtn = document.getElementById('memberDropdown');
    if(dropdown && dropdownMenu && dropdownBtn) {
        dropdownBtn.removeEventListener && dropdownBtn.removeEventListener('click', function(){});
        dropdown.classList.remove('open');
    }
    // 左側快捷鍵功能
    document.querySelectorAll('#category-list li').forEach(function(li) {
        li.addEventListener('click', function() {
            var cat = this.getAttribute('data-cat');
            window.location.href = 'index_v3.php?cat=' + encodeURIComponent(cat);
        });
    });
    // 取消上方「系所成員」點擊展開，改為直接顯示全部
    const navLinks = document.querySelectorAll('.main-nav a');
    navLinks.forEach(function(link) {
        if(link.textContent.includes('系所成員')) {
            link.addEventListener('click', function(e) {
                window.location.href = 'index_v3.php';
            });
        }
    });
    // 下拉選單點選分類直接跳轉
    if(dropdownMenu) {
        dropdownMenu.querySelectorAll('li').forEach(function(li) {
            li.addEventListener('click', function(e) {
                // 已由 onclick 實現跳轉
            });
        });
    }
    document.getElementById('showMapBtn').onclick = function() {
        document.getElementById('mapModal').style.display = 'flex';
    };
    document.getElementById('closeMapBtn').onclick = function() {
        document.getElementById('mapModal').style.display = 'none';
    };
    </script>
</body>
</html>
