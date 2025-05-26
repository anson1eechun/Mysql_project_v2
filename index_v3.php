<?php
require_once 'config.php';

if ($db_link->connect_error) {
    die("Connection failed: " . $db_link->connect_error);
}

$professor_name = "劉明機";

$sql = "SELECT  分機, 職稱 ,自我介紹 FROM 教授 WHERE 姓名 = ?";
$stmt = $db_link->prepare($sql);

if ($stmt === false) {
    die("Prepare failed: " . $db_link->error);
}

$stmt->bind_param("s", $professor_name);

if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}
$stmt->bind_result($phone, $title1, $my);
$stmt->fetch();
$stmt->close();
$sql = "SELECT 信箱 FROM 教授信箱 WHERE 教授 = ?";
$stmt = $db_link->prepare($sql);
$stmt->bind_param("s", $professor_name);
$stmt->execute();
$result = $stmt->get_result();

$emails = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $people_email = $row["信箱"];
        $emails[] = "$people_email";
    }
}
$stmt->close();
$sql = "SELECT 經歷, 類型 FROM 教授經歷 WHERE 教授 = ?";
$stmt = $db_link->prepare($sql);
$stmt->bind_param("s", $professor_name);
$stmt->execute();
$result = $stmt->get_result();

$experiences = [];
while ($row = $result->fetch_assoc()) {
    $experiences[] = [
        '經歷' => $row["經歷"],
        '類型' => $row["類型"]
    ];
}
$stmt->close();



$sql = "SELECT 參與者, 名稱, 發表處, 時間, 地點, 類型 FROM 論文 WHERE 類型 != ''";
$result = $db_link->query($sql);

$meetings = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $meeting_people = $row["參與者"];
        $meeting_name = $row["名稱"];
        $meeting_place = $row["發表處"];
        $meeting_time = date("Y-m", strtotime($row["時間"]));
        $meeting_location = $row["地點"];
        $meeting_type = $row["類型"];
        $meetings[] = "$meeting_people \"$meeting_name\" $meeting_place, $meeting_location $meeting_time.$meeting_type";
    }
}

$sql = "SELECT 參與者, 名稱, 發表處, 時間, 地點 FROM 論文 WHERE 類型 = ' ' ";
$result = $db_link->query($sql);

$conferences = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $conference_people = $row["參與者"];
        $conference_name = $row["名稱"];
        $conference_place = $row["發表處"];
        $conference_time = date("Y-m", strtotime($row["時間"]));
        $conference_location = $row["地點"];
        $conferences[] = "$conference_people \"$conference_name\" $conference_place, $conference_time,$conference_location";
    }
}
//獲獎
$sql_external = "SELECT 獎項名稱, 作品名稱, 指導學生, 主辦單位, 時間 FROM 獎項 WHERE 教授 = '劉明機' AND 主辦單位 != '逢甲大學'";
$result_external = $db_link->query($sql_external);

$sql_internal = "SELECT 獎項名稱, 作品名稱, 指導學生, 主辦單位, 時間 FROM 獎項 WHERE 教授 = '劉明機' AND 主辦單位 = '逢甲大學'";
$result_internal = $db_link->query($sql_internal);
$external_awards = [];
$internal_awards = [];

if ($result_external->num_rows > 0) {
    while ($row = $result_external->fetch_assoc()) {
        $external_gname = $row["獎項名稱"];
        $external_name = $row["作品名稱"];
        $external_stu = $row["指導學生"];
        $external_organizer = $row["主辦單位"];
        $external_time = date("Y-m-d", strtotime($row["時間"]));
        $year_tw = (int) date("Y", strtotime($row["時間"])) - 1911;
        $external_time_tw = str_replace(date("Y", strtotime($row["時間"])), $year_tw, $external_time);

        $external_awards[] = "$year_tw / $external_gname / $external_organizer / $external_time /指導學生 $external_stu /$external_name";
    }
}

if ($result_internal->num_rows > 0) {
    while ($row = $result_internal->fetch_assoc()) {
        $award_gname = $row["獎項名稱"];
        $award_organizer = $row["主辦單位"];
        $award_time = date("Y-m-d", strtotime($row["時間"]));
        $year_tw = (int) date("Y", strtotime($row["時間"])) - 1911;
        $award_time_tw = str_replace(date("Y", strtotime($row["時間"])), $year_tw, $award_time);

        $internal_award_info = "$year_tw / $award_gname / $award_organizer / $award_time";

        if (!empty($row["指導學生"])) {
            $internal_award_info .= " / 指導學生 " . $row["指導學生"];
        }

        if (!empty($row["作品名稱"])) {
            $internal_award_info .= " / $row[作品名稱]";
        }

        $internal_awards[] = $internal_award_info;
    }
}

$sql_nstc = "SELECT 名稱, 開始時間, 結束時間, 計畫編號_國科會, 身分 FROM 國科會和產學合作計畫 WHERE 計畫編號_國科會 != ''";

$result_nstc = $db_link->query($sql_nstc);

$sql_industry = "SELECT 名稱, 開始時間, 結束時間, 身分 FROM 國科會和產學合作計畫 WHERE 計畫編號_國科會 = ''";
$result_industry = $db_link->query($sql_industry);

// 國科會
$nstc_projects = [];
if ($result_nstc && $result_nstc->num_rows > 0) {
    while ($row = $result_nstc->fetch_assoc()) {
        $name = $row["名稱"];
        $start_date = date("Y-m", strtotime($row["開始時間"]));
        $end_date = date("Y-m", strtotime($row["結束時間"]));
        $role = $row["身分"]; // 获取身分信息

        $nstc_projects[] = "$name / $start_date~$end_date / {$row['計畫編號_國科會']} / $role";
    }
}

// 產學
$industry_projects = [];
if ($result_industry && $result_industry->num_rows > 0) {
    while ($row = $result_industry->fetch_assoc()) {
        $name = $row["名稱"];
        $start_date = date("Y-m", strtotime($row["開始時間"]));
        $end_date = date("Y-m", strtotime($row["結束時間"]));
        $role = $row["身分"]; // 获取身分信息

        $industry_projects[] = "$name / $start_date~$end_date / $role";
    }
}
//學歷
$result_industry = $db_link->query($sql_industry);
$stmt_education = $db_link->prepare("SELECT 學歷 FROM 教授學歷 WHERE 教授 = ?");
$stmt_education->bind_param("s", $professor_name);
$stmt_education->execute();
$stmt_education->bind_result($education);
$educations = [];
while ($stmt_education->fetch()) {
    $educations[] = $education;
}
$stmt_education->close();
//專長
$stmt_specialty = $db_link->prepare("SELECT 專長 FROM 教授專長 WHERE 教授 = ?");
$stmt_specialty->bind_param("s", $professor_name);
$stmt_specialty->execute();
$stmt_specialty->bind_result($specialty);
$specialties = [];
while ($stmt_specialty->fetch()) {
    $specialties[] = $specialty;
}
$stmt_specialty->close();
//其他研究
$sql = "SELECT 名稱, 開始時間, 結束時間 FROM 其他研究 WHERE 教授 = '劉明機'";
$result = $db_link->query($sql);

$research = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $name = $row["名稱"];
        $start_date = date("F j, Y", strtotime($row["開始時間"]));
        $end_date = date("F j, Y", strtotime($row["結束時間"]));
        $research[] = "{$name} /{$start_date} / {$end_date} /";
    }
}
//演講
$sql = "SELECT 名稱, 主辦單位, 時間 FROM 演講 WHERE 教授 = '劉明機'";
$result = $db_link->query($sql);

$speeches = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $speech_name = $row["名稱"];
        $organizer = $row["主辦單位"];
        $speech_time = date("Y-m", strtotime($row["時間"]));
        $speeches[] = "$speech_name /$organizer /$speech_time";
    }
}
//專書
$sql = "SELECT 參與者, 名稱, 出版社, 時間, 章節 FROM 專書論文 WHERE 教授 = '劉明機'";
$result = $db_link->query($sql);

$papers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $participants = $row["參與者"];
        $title = $row["名稱"];
        $publisher = $row["出版社"];
        $papers_time = date("Y-m-d", strtotime($row["時間"]));
        $chapter = $row["章節"];
        $papers[] = "$participants / $title /$publisher /$papers_time /$chapter";
    }
}
$db_link->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>教授網頁</title>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const headers = document.querySelectorAll('h3');

            headers.forEach(header => {
                header.addEventListener('click', function () {
                    const content = this.nextElementSibling;
                    if (content.style.display === 'none') {
                        8888888888
                        content.style.display = 'block';
                    } else {
                        content.style.display = 'none';
                    }
                    this.classList.toggle('expanded');
                });
            });
        });
    </script>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: auto;
            max-width: 1100px;
            padding: 20px;
            font-family: Arial, sans-serif;
        }

        .t {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            position: relative;
        }

        .t img {
            width: 300px;
            height: auto;
            order: 2;
            margin-top: 80px;
            box-sizing: border-box;
        }

        .professor-info {
            flex: 1;
            order: 1;
            padding-right: 20px;
        }

        .professor-name {
            font-size: 40px;
            margin-bottom: 10px;
        }

        .title {
            font-size: 18px;
            color: #2894FF;
            margin-top: 10px;
        }

        .email,
        .phone,
        .cla {
            padding: 5px;
            border: 3px solid;
            border-radius: 20px;
            font-size: 16px;
            margin-top: 10px;
            display: inline-block;
        }

        .email {
            border-color: #66B3FF;
            color: #2894FF;
        }

        .phone {
            border-color: #66B3FF;
            color: #2894FF;
        }

        .cla {
            border-color: black;
            color: black;
            text-decoration: none;
        }

        .login {
            position: absolute;
            right: 20px;
            top: 20px;
            padding: 5px;
            border: 3px solid black;
            border-radius: 20px;
            font-size: 20px;
            color: black;
            text-decoration: none;
        }

        .my {
            width: 700px;
            word-wrap: break-word;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }

        h3 {
            margin-top: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        h3::after {
            content: '▼';
            margin-left: 10px;
            font-size: 12px;
            transition: transform 0.3s;
        }

        h3.expanded::after {
            transform: rotate(180deg);
        }

        .content {
            display: block;
            padding-left: 20px;
        }

        ol {
            padding-left: 20px;
        }

        li {
            margin-bottom: 10px;
        }

        .flex-container {
            display: flex;
            flex-wrap: nowrap;
            background-color: #F0F0F0;
            padding: 10px;
        }

        .flex-container2 {
            display: flex;
            flex-wrap: nowrap;
            padding: 10px;
        }

        .column>p.w {
            width: 500px;
            text-align: left;
            line-height: 25px;
            font-size: 30px;
            padding-left: 10px;
        }

        .column>p.in {
            line-height: 1.8;
            font-size: 18px;
            padding-left: 30px;
        }

        .section-2 {
            background-color: #F0F0F0;
            padding: 20px;
            margin-top: 20px;
        }

        .po {
            text-align: center;
            font-size: 28px;
        }

        @media (max-width: 1100px) {
            body {
                padding: 10px;
            }

            .t {
                flex-direction: column;
                align-items: flex-start;
            }

            .t img {
                width: 100%;
                max-width: 300px;
                margin-top: 20px;
            }

            .login {
                position: static;
                align-self: flex-end;
                margin-top: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="t">
        <div class="professor-info">
            <h1 class="professor-name">劉明機</h1>
            <div class="title"><?php echo htmlspecialchars($title1); ?></div>
            <div class="my"><?php echo htmlspecialchars($my); ?></div>
            <div class="email">信箱: <?php foreach ($emails as $email): ?>
                    <?php echo htmlspecialchars($email); ?>
                <?php endforeach; ?>
            </div>
            <div class="phone">分機: <?php echo htmlspecialchars($phone); ?></div>
            <br>
            <a class="cla" href="table.php">課表時間</a>
        </div>
        <img src="img/pic_02726.jpg" alt="教授照片">
        <a class="login" href="login.php" target="_blank">資料維護</a>
    </div>
    <br>
    <div class="d">
        <div class="flex-container">
            <div class="column">
                <p class="w">學歷</p>
                <?php foreach ($educations as $education): ?>
                    <p class="in"><?php echo nl2br(htmlspecialchars($education)); ?></p>
                <?php endforeach; ?>
            </div>
            <div class="column">
                <p class="w">專長</p>
                <?php foreach ($specialties as $specialty): ?>
                    <p class="in"><?php echo nl2br(htmlspecialchars($specialty)); ?></p>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="flex-container2">
            <div class="column">
                <p class="w">校內經歷</p>
                <?php foreach ($experiences as $experience): ?>
                    <?php if ($experience['類型'] == '校內'): ?>
                        <p class="in"><?= htmlspecialchars($experience['經歷']); ?></pㄣ>
                        <?php endif; ?>
                    <?php endforeach; ?>
            </div>

            <div class="column">
                <p class="w">校外經歷</p>
                <?php foreach ($experiences as $experience): ?>
                    <?php if ($experience['類型'] == '校外'): ?>
                        <p class="in"><?= htmlspecialchars($experience['經歷']); ?></pㄣ>
                        <?php endif; ?>
                    <?php endforeach; ?>
            </div>
        </div>
        <div class="section-2">
            <p class="po">論文與參與計畫</p>
            <h3>發表期刊論文</h3>
            <ol>
                <?php foreach ($meetings as $meeting): ?>
                    <li><?php echo $meeting; ?></li>
                <?php endforeach; ?>
            </ol>
            <h3>會議論文</h3>
            <ol>
                <?php foreach ($conferences as $conference): ?>
                    <li><?php echo $conference; ?></li>
                <?php endforeach; ?>
            </ol>
            <h3>國科會計畫</h3>
            <ol>
                <?php foreach ($nstc_projects as $project): ?>
                    <li><?php echo $project; ?></li>
                <?php endforeach; ?>
            </ol>
            <h3>產學合作計畫</h3>
            <ol>
                <?php foreach ($industry_projects as $project): ?>
                    <li><?php echo $project; ?></li>
                <?php endforeach; ?>
            </ol>
            <h3>校外獎勵及指導學生獲獎</h3>
            <ol>
                <?php foreach ($external_awards as $award): ?>
                    <li><?php echo $award; ?></li>
                <?php endforeach; ?>
            </ol>
            <h3>校內獎勵及指導學生獲獎</h3>
            <ol>
                <?php foreach ($internal_awards as $award): ?>
                    <li><?php echo $award; ?></li>
                <?php endforeach; ?>
            </ol>
            <h3>校內外演講</h3>
            <ol>
                <?php foreach ($speeches as $speech): ?>
                    <li><?php echo $speech; ?></li>
                <?php endforeach; ?>
            </ol>
            <h3>專書論文</h3>
            <ol>
                <?php foreach ($papers as $paper): ?>
                    <li><?php echo $paper; ?></li>
                <?php endforeach; ?>
            </ol>
            <h3>其他相關研究</h3>
            <ol>
                <?php foreach ($research as $item): ?>
                    <li><?php echo $item; ?></li>
                <?php endforeach; ?>
            </ol>
        </div>
    </div>
</body>



</html>