<?php

require_once 'config.php';


if ($db_link->connect_error) {
    die("Connection failed: " . $db_link->connect_error);
}

$professor_id = 'A001'; // 假設教授ID為A001
$sql = "SELECT * FROM professor WHERE pro_ID = ?";
$stmt = $db_link->prepare($sql);
$stmt->bind_param("s", $professor_id);
$stmt->execute();
$result = $stmt->get_result();
$professor = $result->fetch_assoc();

$sql_awards = "SELECT * FROM award WHERE pro_ID = ?";
$stmt_awards = $db_link->prepare($sql_awards);
$stmt_awards->bind_param("s", $professor_id);
$stmt_awards->execute();
$result_awards = $stmt_awards->get_result();

$sql_experience = "SELECT * FROM experience WHERE pro_ID = ?";
$stmt_experience = $db_link->prepare($sql_experience);
$stmt_experience->bind_param("s", $professor_id);
$stmt_experience->execute();
$result_experience = $stmt_experience->get_result();

$db_link->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($professor['name']); ?>的網頁</title>
    <style>
        /* 這裡可以添加與index.php相似的CSS樣式 */
    </style>
</head>

<body>
    <div class="professor-info">
        <h1 class="professor-name"><?php echo htmlspecialchars($professor['name']); ?></h1>
        <div class="title"><?php echo htmlspecialchars($professor['position']); ?></div>
        <div class="my"><?php echo nl2br(htmlspecialchars($professor['introduction'])); ?></div>
    </div>

    <div class="awards">
        <h3>獎項</h3>
        <ul>
            <?php while ($award = $result_awards->fetch_assoc()): ?>
                <li><?php echo htmlspecialchars($award['title']) . " - " . htmlspecialchars($award['organizer']) . " (" . htmlspecialchars($award['date']) . ")"; ?></li>
            <?php endwhile; ?>
        </ul>
    </div>

    <div class="experience">
        <h3>經歷</h3>
        <ul>
            <?php while ($experience = $result_experience->fetch_assoc()): ?>
                <li><?php echo htmlspecialchars($experience['department']) . " - " . htmlspecialchars($experience['position']); ?></li>
            <?php endwhile; ?>
        </ul>
    </div>
</body>

</html>