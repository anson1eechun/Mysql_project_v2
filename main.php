<?php
// 引入資料庫連接文件
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 根據提交的表單類型執行不同的操作
    if (isset($_POST['update_basic'])) {
        // 更新基本資料
        $pro_ID = $_POST['pro_ID'];
        $name = $_POST['name'];
        $department = $_POST['department'];
        $introduction = $_POST['introduction'];

        $sql = "UPDATE professor SET
                name = '$name',
                department = '$department',
                introduction = '$introduction'
                WHERE pro_ID = '$pro_ID'";

        if ($conn->query($sql) === TRUE) {
            $message = "基本資料更新成功！";
        } else {
            $error = "錯誤: " . $conn->error;
        }
    }

    // 處理照片上傳
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["photo"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // 檢查是否為真實圖片
        $check = getimagesize($_FILES["photo"]["tmp_name"]);
        if($check !== false) {
            // 嘗試上傳文件
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                // 更新資料庫中的照片路徑
                $pro_ID = $_POST['pro_ID'];
                $sql = "UPDATE professor SET photo = '$target_file' WHERE pro_ID = '$pro_ID'";
                if ($conn->query($sql) === TRUE) {
                    $message = "照片上傳成功並更新資料庫！";
                } else {
                    $error = "更新資料庫時出錯: " . $conn->error;
                }
            } else {
                $error = "上傳照片時出錯。";
            }
        } else {
            $error = "檔案不是圖片。";
        }
    }

    // 處理新增學歷
    if (isset($_POST['add_education'])) {
        $pro_ID = $_POST['pro_ID'];
        $department = $_POST['department'];
        $degree = $_POST['degree'];

        $sql = "INSERT INTO education (pro_ID, department, degree)
                VALUES ('$pro_ID', '$department', '$degree')";

        if ($conn->query($sql) === TRUE) {
            $message = "學歷新增成功！";
        } else {
            $error = "錯誤: " . $conn->error;
        }
    }

    // 處理新增專長
    if (isset($_POST['add_expertise'])) {
        $pro_ID = $_POST['pro_ID'];
        $item = $_POST['item'];

        $sql = "INSERT INTO expertise (pro_ID, item)
                VALUES ('$pro_ID', '$item')";

        if ($conn->query($sql) === TRUE) {
            $message = "專長新增成功！";
        } else {
            $error = "錯誤: " . $conn->error;
        }
    }

    // 處理新增期刊論文
    if (isset($_POST['add_journal'])) {
        $pro_ID = $_POST['pro_ID'];
        $jour_character = $_POST['jour_character'];
        $title = $_POST['title'];
        $name = $_POST['name'];
        $issue = $_POST['issue'];
        $date = $_POST['date'];
        $pages = $_POST['pages'];

        $sql = "INSERT INTO journal (pro_ID, jour_character, title, name, issue, date, pages)
                VALUES ('$pro_ID', '$jour_character', '$title', '$name', '$issue', '$date', $pages)";

        if ($conn->query($sql) === TRUE) {
            $message = "期刊論文新增成功！";
        } else {
            $error = "錯誤: " . $conn->error;
        }
    }

    // 處理新增會議論文
    if (isset($_POST['add_conference'])) {
        $pro_ID = $_POST['pro_ID'];
        $conf_character = $_POST['conf_character'];
        $title = $_POST['title'];
        $name = $_POST['name'];
        $pages = $_POST['pages'];
        $date = $_POST['date'];
        $location = $_POST['location'];

        $sql = "INSERT INTO conference (pro_ID, conf_character, title, name, pages, date, location)
                VALUES ('$pro_ID', '$conf_character', '$title', '$name', $pages, '$date', '$location')";

        if ($conn->query($sql) === TRUE) {
            $message = "會議論文新增成功！";
        } else {
            $error = "錯誤: " . $conn->error;
        }
    }

    // 處理新增經歷
    if (isset($_POST['add_experience'])) {
        $pro_ID = $_POST['pro_ID'];
        $sort = $_POST['sort'];
        $department = $_POST['department'];
        $position = $_POST['position'];

        $sql = "INSERT INTO experience (pro_ID, sort, department, position)
                VALUES ('$pro_ID', '$sort', '$department', '$position')";

        if ($conn->query($sql) === TRUE) {
            $message = "經歷新增成功！";
        } else {
            $error = "錯誤: " . $conn->error;
        }
    }

    // 處理刪除資料
    if (isset($_POST['delete_data'])) {
        $table = $_POST['table'];
        $id_field = $_POST['id_field'];
        $id_value = $_POST['id_value'];

        $sql = "DELETE FROM $table WHERE $id_field = '$id_value'";

        if ($conn->query($sql) === TRUE) {
            $message = "資料刪除成功！";
        } else {
            $error = "錯誤: " . $conn->error;
        }
    }
}

// 查詢教授基本資料
$pro_ID = isset($_GET['id']) ? $_GET['id'] : '1'; // 預設顯示ID為1的教授
$sql_professor = "SELECT * FROM professor WHERE pro_ID = '$pro_ID'";
$result_professor = $conn->query($sql_professor);
$professor = $result_professor->fetch_assoc();

// 查詢學歷
$sql_education = "SELECT * FROM education WHERE pro_ID = '$pro_ID' ORDER BY edu_ID";
$result_education = $conn->query($sql_education);

// 查詢專長
$sql_expertise = "SELECT * FROM expertise WHERE pro_ID = '$pro_ID' ORDER BY expertise_ID";
$result_expertise = $conn->query($sql_expertise);

// 查詢期刊論文
$sql_journal = "SELECT * FROM journal WHERE pro_ID = '$pro_ID' ORDER BY date DESC";
$result_journal = $conn->query($sql_journal);

// 查詢會議論文
$sql_conference = "SELECT * FROM conference WHERE pro_ID = '$pro_ID' ORDER BY date DESC";
$result_conference = $conn->query($sql_conference);

// 查詢經歷
$sql_experience = "SELECT * FROM experience WHERE pro_ID = '$pro_ID' ORDER BY experience_ID";
$result_experience = $conn->query($sql_experience);
?>

<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8">
    <title>教授資料管理系統</title>
    <style>
        body {
            font-family: 'Noto Sans TC', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 5px;
        }
        h1, h2, h3 {
            color: #333;
        }
        .message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 10px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 10px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .section {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        form {
            margin-bottom: 20px;
        }
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        textarea {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .delete-btn {
            background-color: #f44336;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .delete-btn:hover {
            background-color: #da190b;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>教授資料管理系統</h1>

    <?php if(isset($message)): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php if(isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- 基本資料表單 -->
    <div class="section">
        <h2>基本資料</h2>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="pro_ID" value="<?php echo $professor['pro_ID']; ?>">
            <div>
                <label>姓名：</label>
                <input type="text" name="name" value="<?php echo $professor['name']; ?>" required>
            </div>
            <div>
                <label>系所：</label>
                <input type="text" name="department" value="<?php echo $professor['department']; ?>" required>
            </div>
            <div>
                <label>簡介：</label>
                <textarea name="introduction" rows="4"><?php echo $professor['introduction']; ?></textarea>
            </div>
            <div>
                <label>照片：</label>
                <input type="file" name="photo" accept="image/*">
            </div>
            <button type="submit" name="update_basic">更新基本資料</button>
        </form>
    </div>

    <!-- 學歷表單 -->
    <div class="section">
        <h2>學歷</h2>
        <form method="post">
            <input type="hidden" name="pro_ID" value="<?php echo $professor['pro_ID']; ?>">
            <div>
                <label>系所：</label>
                <input type="text" name="department" required>
            </div>
            <div>
                <label>學位：</label>
                <input type="text" name="degree" required>
            </div>
            <button type="submit" name="add_education">新增學歷</button>
        </form>

        <table>
            <tr>
                <th>系所</th>
                <th>學位</th>
                <th>操作</th>
            </tr>
            <?php while($education = $result_education->fetch_assoc()): ?>
            <tr>
                <td><?php echo $education['department']; ?></td>
                <td><?php echo $education['degree']; ?></td>
                <td>
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="table" value="education">
                        <input type="hidden" name="id_field" value="edu_ID">
                        <input type="hidden" name="id_value" value="<?php echo $education['edu_ID']; ?>">
                        <button type="submit" name="delete_data" class="delete-btn">刪除</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <!-- 專長表單 -->
    <div class="section">
        <h2>專長</h2>
        <form method="post">
            <input type="hidden" name="pro_ID" value="<?php echo $professor['pro_ID']; ?>">
            <div>
                <label>專長項目：</label>
                <input type="text" name="item" required>
            </div>
            <button type="submit" name="add_expertise">新增專長</button>
        </form>

        <table>
            <tr>
                <th>專長項目</th>
                <th>操作</th>
            </tr>
            <?php while($expertise = $result_expertise->fetch_assoc()): ?>
            <tr>
                <td><?php echo $expertise['item']; ?></td>
                <td>
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="table" value="expertise">
                        <input type="hidden" name="id_field" value="expertise_ID">
                        <input type="hidden" name="id_value" value="<?php echo $expertise['expertise_ID']; ?>">
                        <button type="submit" name="delete_data" class="delete-btn">刪除</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <!-- 期刊論文表單 -->
    <div class="section">
        <h2>期刊論文</h2>
        <form method="post">
            <input type="hidden" name="pro_ID" value="<?php echo $professor['pro_ID']; ?>">
            <div>
                <label>作者：</label>
                <input type="text" name="jour_character" required>
            </div>
            <div>
                <label>論文標題：</label>
                <input type="text" name="title" required>
            </div>
            <div>
                <label>期刊名稱：</label>
                <input type="text" name="name" required>
            </div>
            <div>
                <label>卷期：</label>
                <input type="text" name="issue">
            </div>
            <div>
                <label>日期：</label>
                <input type="text" name="date" required>
            </div>
            <div>
                <label>頁數：</label>
                <input type="number" name="pages" required>
            </div>
            <button type="submit" name="add_journal">新增期刊論文</button>
        </form>

        <table>
            <tr>
                <th>作者</th>
                <th>論文標題</th>
                <th>期刊名稱</th>
                <th>卷期</th>
                <th>日期</th>
                <th>頁數</th>
                <th>操作</th>
            </tr>
            <?php while($journal = $result_journal->fetch_assoc()): ?>
            <tr>
                <td><?php echo $journal['jour_character']; ?></td>
                <td><?php echo $journal['title']; ?></td>
                <td><?php echo $journal['name']; ?></td>
                <td><?php echo $journal['issue']; ?></td>
                <td><?php echo $journal['date']; ?></td>
                <td><?php echo $journal['pages']; ?></td>
                <td>
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="table" value="journal">
                        <input type="hidden" name="id_field" value="jour_ID">
                        <input type="hidden" name="id_value" value="<?php echo $journal['jour_ID']; ?>">
                        <button type="submit" name="delete_data" class="delete-btn">刪除</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <!-- 會議論文表單 -->
    <div class="section">
        <h2>會議論文</h2>
        <form method="post">
            <input type="hidden" name="pro_ID" value="<?php echo $professor['pro_ID']; ?>">
            <div>
                <label>作者：</label>
                <input type="text" name="conf_character" required>
            </div>
            <div>
                <label>論文標題：</label>
                <input type="text" name="title" required>
            </div>
            <div>
                <label>會議名稱：</label>
                <input type="text" name="name" required>
            </div>
            <div>
                <label>頁數：</label>
                <input type="number" name="pages" required>
            </div>
            <div>
                <label>日期：</label>
                <input type="text" name="date" required>
            </div>
            <div>
                <label>地點：</label>
                <input type="text" name="location" required>
            </div>
            <button type="submit" name="add_conference">新增會議論文</button>
        </form>

        <table>
            <tr>
                <th>作者</th>
                <th>論文標題</th>
                <th>會議名稱</th>
                <th>頁數</th>
                <th>日期</th>
                <th>地點</th>
                <th>操作</th>
            </tr>
            <?php while($conference = $result_conference->fetch_assoc()): ?>
            <tr>
                <td><?php echo $conference['conf_character']; ?></td>
                <td><?php echo $conference['title']; ?></td>
                <td><?php echo $conference['name']; ?></td>
                <td><?php echo $conference['pages']; ?></td>
                <td><?php echo $conference['date']; ?></td>
                <td><?php echo $conference['location']; ?></td>
                <td>
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="table" value="conference">
                        <input type="hidden" name="id_field" value="conf_ID">
                        <input type="hidden" name="id_value" value="<?php echo $conference['conf_ID']; ?>">
                        <button type="submit" name="delete_data" class="delete-btn">刪除</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <!-- 經歷表單 -->
    <div class="section">
        <h2>經歷</h2>
        <form method="post">
            <input type="hidden" name="pro_ID" value="<?php echo $professor['pro_ID']; ?>">
            <div>
                <label>經歷類別：</label>
                <input type="text" name="sort" required>
            </div>
            <div>
                <label>單位：</label>
                <input type="text" name="department" required>
            </div>
            <div>
                <label>職位：</label>
                <input type="text" name="position" required>
            </div>
            <button type="submit" name="add_experience">新增經歷</button>
        </form>

        <table>
            <tr>
                <th>經歷類別</th>
                <th>單位</th>
                <th>職位</th>
                <th>操作</th>
            </tr>
            <?php while($experience = $result_experience->fetch_assoc()): ?>
            <tr>
                <td><?php echo $experience['sort']; ?></td>
                <td><?php echo $experience['department']; ?></td>
                <td><?php echo $experience['position']; ?></td>
                <td>
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="table" value="experience">
                        <input type="hidden" name="id_field" value="experience_ID">
                        <input type="hidden" name="id_value" value="<?php echo $experience['experience_ID']; ?>">
                        <button type="submit" name="delete_data" class="delete-btn">刪除</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>
</body>
</html>