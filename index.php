<?php
// 引入資料庫連接文件
require_once 'db_connection.php';

// 取得教師基本資料
$sql_teacher = "SELECT * FROM teacher_info WHERE id = 1";
$result_teacher = $conn->query($sql_teacher);
$teacher = $result_teacher->fetch_assoc();

// 取得學歷
$sql_education = "SELECT * FROM education WHERE teacher_id = 1 ORDER BY id";
$result_education = $conn->query($sql_education);

// 取得專長
$sql_specialties = "SELECT * FROM specialties WHERE teacher_id = 1 ORDER BY id";
$result_specialties = $conn->query($sql_specialties);

// 取得期刊論文
$sql_journals = "SELECT * FROM journal_papers WHERE teacher_id = 1 ORDER BY publish_date DESC";
$result_journals = $conn->query($sql_journals);

// 取得會議論文
$sql_conferences = "SELECT * FROM conference_papers WHERE teacher_id = 1 ORDER BY publish_date DESC";
$result_conferences = $conn->query($sql_conferences);

// 取得校內經歷
$sql_internal_exp = "SELECT * FROM experiences WHERE teacher_id = 1 AND is_internal = TRUE";
$result_internal_exp = $conn->query($sql_internal_exp);

// 取得校外經歷
$sql_external_exp = "SELECT * FROM experiences WHERE teacher_id = 1 AND is_internal = FALSE";
$result_external_exp = $conn->query($sql_external_exp);

// 關閉資料庫連接 
$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8">
    <title><?php echo $teacher['name']; ?>老師介紹</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<!-- 左上角 Logo -->
<div class="logo">
    <img src="title.png" alt="網站標誌">
</div>

<!-- 頁面標題與職稱 -->
<h1><?php echo $teacher['name']; ?></h1>
<p><strong><?php echo $teacher['title']; ?></strong></p>

<!-- 教師簡介區塊（含個人照片） -->
<div class="section intro-wrapper">
    <div class="intro-content">
        <p><strong>簡介：</strong></p>
        <p><?php echo $teacher['intro']; ?></p>
    </div>
    <div class="intro-photo">
        <img src="<?php echo $teacher['photo_path']; ?>" alt="<?php echo $teacher['name']; ?>照片">
    </div>
</div>

<!-- 學歷區塊 -->
<div class="section">
    <p><strong>學歷：</strong></p>
    <ul>
        <?php while($education = $result_education->fetch_assoc()): ?>
        <li><?php echo $education['school'] . ' ' . $education['department'] . ' ' . $education['degree']; ?></li>
        <?php endwhile; ?>
    </ul>
</div>

<!-- 專長區塊 -->
<div class="section">
    <p><strong>專長：</strong></p>
    <ul>
        <?php while($specialty = $result_specialties->fetch_assoc()): ?>
        <li><?php echo $specialty['specialty'] . '（' . $specialty['specialty_en'] . '）'; ?></li>
        <?php endwhile; ?>
    </ul>
</div>

<!-- 聯絡資訊區塊 -->
<div class="section">
    <p><strong>聯絡資訊：</strong></p>
    <ul class="contact-info">
        <li><?php echo $teacher['email']; ?></li>
        <li><?php echo $teacher['phone']; ?></li>
    </ul>
</div>

<!-- 發表期刊論文（ 可收合區塊） -->
<div class="section">
    <details open>
        <summary class="category-title">發表期刊論文（<?php echo $result_journals->num_rows; ?>）</summary>
        <ol>
            <?php while($paper = $result_journals->fetch_assoc()): ?>
            <li>
                <?php
                echo $paper['title'] . ' <i>' . $paper['journal'] . '</i>, ' . $paper['publish_date'];
                if(!empty($paper['type'])) echo '. (' . $paper['type'] . ')';
                ?>
            </li>
            <?php endwhile; ?>
        </ol>
    </details>
</div>

<!-- 會議論文(可收合區塊） -->
<div class="section">
    <details open>
        <summary class="category-title">會議論文（<?php echo $result_conferences->num_rows; ?>）</summary>
        <ol>
            <?php while($paper = $result_conferences->fetch_assoc()): ?>
            <li>
                <?php echo $paper['title'] . ' <i>' . $paper['conference'] . '</i>, ' . $paper['publish_date']; ?>
            </li>
            <?php endwhile; ?>
        </ol>
    </details>
</div>

<!-- 校內與校外經歷 -->
<div class="section">
    <p><strong>校內經歷：</strong></p>
    <ul>
        <?php while($exp = $result_internal_exp->fetch_assoc()): ?>
        <li><?php echo $exp['organization'] . ' ' . $exp['position']; ?></li>
        <?php endwhile; ?>
    </ul>
    <p><strong>校外經歷：</strong></p>
    <ul>
        <?php while($exp = $result_external_exp->fetch_assoc()): ?>
        <li><?php echo $exp['organization'] . ' ' . $exp['position']; ?></li>
        <?php endwhile; ?>
    </ul>
</div>

<!-- 最下方新增圖片 -->
<div class="image-gallery">
    <img src="title2.png" alt="底部圖片">
</div>
</body>
</html>