<?php
// 引入資料庫連接文件
require_once 'db_connection.php';

// 取得教授基本資料
$sql_professor = "SELECT * FROM professor WHERE pro_ID = '1'";
$result_professor = $conn->query($sql_professor);
$professor = $result_professor->fetch_assoc();

// 取得學歷
$sql_education = "SELECT * FROM education WHERE pro_ID = '1' ORDER BY edu_ID";
$result_education = $conn->query($sql_education);

// 取得專長
$sql_expertise = "SELECT * FROM expertise WHERE pro_ID = '1' ORDER BY expertise_ID";
$result_expertise = $conn->query($sql_expertise);

// 取得期刊論文
$sql_journal = "SELECT * FROM journal WHERE pro_ID = '1' ORDER BY date DESC";
$result_journal = $conn->query($sql_journal);

// 取得會議論文
$sql_conference = "SELECT * FROM conference WHERE pro_ID = '1' ORDER BY date DESC";
$result_conference = $conn->query($sql_conference);

// 取得經歷
$sql_experience = "SELECT * FROM experience WHERE pro_ID = '1' ORDER BY experience_ID";
$result_experience = $conn->query($sql_experience);

// 關閉資料庫連接 
$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8">
    <title><?php echo $professor['name']; ?>教授介紹</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<!-- 左上角 Logo -->
<div class="logo">
    <img src="title.png" alt="網站標誌">
</div>

<!-- 頁面標題與職稱 -->
<h1><?php echo $professor['name']; ?></h1>
<p><strong><?php echo $professor['department']; ?></strong></p>

<!-- 教師簡介區塊（含個人照片） -->
<div class="section intro-wrapper">
    <div class="intro-content">
        <p><strong>簡介：</strong></p>
        <p><?php echo $professor['introduction']; ?></p>
    </div>
    <div class="intro-photo">
        <img src="<?php echo $professor['photo']; ?>" alt="<?php echo $professor['name']; ?>照片">
    </div>
</div>

<!-- 學歷區塊 -->
<div class="section">
    <p><strong>學歷：</strong></p>
    <ul>
        <?php while($education = $result_education->fetch_assoc()): ?>
        <li><?php echo $education['department'] . ' ' . $education['degree']; ?></li>
        <?php endwhile; ?>
    </ul>
</div>

<!-- 專長區塊 -->
<div class="section">
    <p><strong>專長：</strong></p>
    <ul>
        <?php while($expertise = $result_expertise->fetch_assoc()): ?>
        <li><?php echo $expertise['item']; ?></li>
        <?php endwhile; ?>
    </ul>
</div>

<!-- 發表期刊論文（可收合區塊） -->
<div class="section">
    <details open>
        <summary class="category-title">發表期刊論文（<?php echo $result_journal->num_rows; ?>）</summary>
        <ol>
            <?php while($paper = $result_journal->fetch_assoc()): ?>
            <li>
                <?php
                echo $paper['title'] . ' <i>' . $paper['name'] . '</i>, ' . $paper['date'];
                if(!empty($paper['issue'])) echo '. (' . $paper['issue'] . ')';
                ?>
            </li>
            <?php endwhile; ?>
        </ol>
    </details>
</div>

<!-- 會議論文(可收合區塊） -->
<div class="section">
    <details open>
        <summary class="category-title">會議論文（<?php echo $result_conference->num_rows; ?>）</summary>
        <ol>
            <?php while($paper = $result_conference->fetch_assoc()): ?>
            <li>
                <?php echo $paper['title'] . ' <i>' . $paper['name'] . '</i>, ' . $paper['date']; ?>
            </li>
            <?php endwhile; ?>
        </ol>
    </details>
</div>

<!-- 經歷 -->
<div class="section">
    <p><strong>經歷：</strong></p>
    <ul>
        <?php while($exp = $result_experience->fetch_assoc()): ?>
        <li><?php echo $exp['department'] . ' ' . $exp['position']; ?></li>
        <?php endwhile; ?>
    </ul>
</div>

<!-- 最下方新增圖片 -->
<div class="image-gallery">
    <img src="title2.png" alt="底部圖片">
</div>
</body>
</html>