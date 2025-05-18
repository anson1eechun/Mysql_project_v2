<?php
// 引入資料庫連接文件
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // 根據提交的表單類型執行不同的操作
        if (isset($_POST['update_basic'])) {
            // 更新基本資料
            $pro_ID = $_POST['pro_ID'];
            $name = $_POST['name'];
            $position = $_POST['position'];
            $introduction = $_POST['introduction'];

            $stmt = $conn->prepare("UPDATE professor SET name = ?, position = ?, introduction = ? WHERE pro_ID = ?");
            $stmt->bind_param("ssss", $name, $position, $introduction, $pro_ID);

            if ($stmt->execute()) {
                $message = "基本資料更新成功！";
            } else {
                throw new Exception("更新基本資料失敗：" . $stmt->error);
            }
            $stmt->close();
        }

        // 處理照片上傳
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $target_dir = "uploads/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $target_file = $target_dir . basename($_FILES["photo"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // 檢查是否為真實圖片
            $check = getimagesize($_FILES["photo"]["tmp_name"]);
            if($check === false) {
                throw new Exception("檔案不是圖片。");
            }

            // 檢查檔案大小（限制為 5MB）
            if ($_FILES["photo"]["size"] > 5000000) {
                throw new Exception("檔案太大。");
            }

            // 允許的檔案格式
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                throw new Exception("只允許 JPG, JPEG, PNG 檔案。");
            }

            // 嘗試上傳文件
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                // 更新資料庫中的照片路徑
                $pro_ID = $_POST['pro_ID'];
                $stmt = $conn->prepare("UPDATE professor SET photo = ? WHERE pro_ID = ?");
                $stmt->bind_param("ss", $target_file, $pro_ID);
                
                if ($stmt->execute()) {
                    $message = "照片上傳成功並更新資料庫！";
                } else {
                    throw new Exception("更新資料庫時出錯：" . $stmt->error);
                }
                $stmt->close();
            } else {
                throw new Exception("上傳照片時出錯。");
            }
        }

        // 處理新增學歷
        if (isset($_POST['add_education'])) {
            $pro_ID = $_POST['pro_ID'];
            $department = $_POST['department'];
            $degree = $_POST['degree'];

            $stmt = $conn->prepare("INSERT INTO education (pro_ID, department, degree) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $pro_ID, $department, $degree);

            if ($stmt->execute()) {
                $message = "學歷新增成功！";
            } else {
                throw new Exception("新增學歷失敗：" . $stmt->error);
            }
            $stmt->close();
        }

        // 處理新增專長
        if (isset($_POST['add_expertise'])) {
            $pro_ID = $_POST['pro_ID'];
            $item = $_POST['item'];

            $stmt = $conn->prepare("INSERT INTO expertise (pro_ID, item) VALUES (?, ?)");
            $stmt->bind_param("ss", $pro_ID, $item);

            if ($stmt->execute()) {
                $message = "專長新增成功！";
            } else {
                throw new Exception("新增專長失敗：" . $stmt->error);
            }
            $stmt->close();
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

            $stmt = $conn->prepare("INSERT INTO journal (pro_ID, jour_character, title, name, issue, date, pages) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $pro_ID, $jour_character, $title, $name, $issue, $date, $pages);

            if ($stmt->execute()) {
                $message = "期刊論文新增成功！";
            } else {
                throw new Exception("新增期刊論文失敗：" . $stmt->error);
            }
            $stmt->close();
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

            $stmt = $conn->prepare("INSERT INTO conference (pro_ID, conf_character, title, name, pages, date, location) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $pro_ID, $conf_character, $title, $name, $pages, $date, $location);

            if ($stmt->execute()) {
                $message = "會議論文新增成功！";
            } else {
                throw new Exception("新增會議論文失敗：" . $stmt->error);
            }
            $stmt->close();
        }

        // 處理新增經歷
        if (isset($_POST['add_experience'])) {
            $pro_ID = $_POST['pro_ID'];
            $category = $_POST['category'];
            $department = $_POST['department'];
            $position = $_POST['position'];

            $stmt = $conn->prepare("INSERT INTO experience (pro_ID, category, department, position) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $pro_ID, $category, $department, $position);

            if ($stmt->execute()) {
                $message = "經歷新增成功！";
            } else {
                throw new Exception("新增經歷失敗：" . $stmt->error);
            }
            $stmt->close();
        }

        // 處理刪除資料
        if (isset($_POST['delete_data'])) {
            $table = $_POST['table'];
            $id_field = $_POST['id_field'];
            $id_value = $_POST['id_value'];

            // 驗證表名和欄位名，防止 SQL 注入
            $allowed_tables = ['education', 'expertise', 'journal', 'conference', 'experience'];
            $allowed_fields = ['edu_ID', 'expertise_ID', 'jour_ID', 'conf_ID', 'experience_ID'];

            if (!in_array($table, $allowed_tables) || !in_array($id_field, $allowed_fields)) {
                throw new Exception("無效的表名或欄位名");
            }

            $stmt = $conn->prepare("DELETE FROM $table WHERE $id_field = ?");
            $stmt->bind_param("s", $id_value);

            if ($stmt->execute()) {
                $message = "資料刪除成功！";
            } else {
                throw new Exception("刪除資料失敗：" . $stmt->error);
            }
            $stmt->close();
        }

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// 查詢教授基本資料
$pro_ID = isset($_GET['id']) ? $_GET['id'] : 'A001'; // 預設顯示ID為A001的教授

$stmt = $conn->prepare("SELECT * FROM professor WHERE pro_ID = ?");
$stmt->bind_param("s", $pro_ID);
$stmt->execute();
$result_professor = $stmt->get_result();
$professor = $result_professor->fetch_assoc();
$stmt->close();

// 查詢學歷
$stmt = $conn->prepare("SELECT * FROM education WHERE pro_ID = ? ORDER BY edu_ID");
$stmt->bind_param("s", $pro_ID);
$stmt->execute();
$result_education = $stmt->get_result();
$stmt->close();

// 查詢專長
$stmt = $conn->prepare("SELECT * FROM expertise WHERE pro_ID = ? ORDER BY expertise_ID");
$stmt->bind_param("s", $pro_ID);
$stmt->execute();
$result_expertise = $stmt->get_result();
$stmt->close();

// 查詢期刊論文
$stmt = $conn->prepare("SELECT * FROM journal WHERE pro_ID = ? ORDER BY date DESC");
$stmt->bind_param("s", $pro_ID);
$stmt->execute();
$result_journal = $stmt->get_result();
$stmt->close();

// 查詢會議論文
$stmt = $conn->prepare("SELECT * FROM conference WHERE pro_ID = ? ORDER BY date DESC");
$stmt->bind_param("s", $pro_ID);
$stmt->execute();
$result_conference = $stmt->get_result();
$stmt->close();

// 查詢經歷
$stmt = $conn->prepare("SELECT * FROM experience WHERE pro_ID = ? ORDER BY experience_ID");
$stmt->bind_param("s", $pro_ID);
$stmt->execute();
$result_experience = $stmt->get_result();
$stmt->close();
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
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
        }
        .delete-btn {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <h1>教授資料管理系統</h1>
        
        <!-- 基本資料區塊 -->
        <div class="section">
            <h2>基本資料</h2>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="pro_ID" value="<?php echo $professor['pro_ID']; ?>">
                
                <div>
                    <label>姓名：</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($professor['name']); ?>" required>
                </div>
                
                <div>
                    <label>職位：</label>
                    <input type="text" name="position" value="<?php echo htmlspecialchars($professor['position']); ?>" required>
                </div>
                
                <div>
                    <label>自介：</label>
                    <textarea name="introduction" rows="4"><?php echo htmlspecialchars($professor['introduction']); ?></textarea>
                </div>
                
                <div>
                    <label>照片：</label>
                    <input type="file" name="photo" accept="image/*">
                    <?php if ($professor['photo']): ?>
                        <img src="<?php echo htmlspecialchars($professor['photo']); ?>" alt="教授照片" style="max-width: 200px;">
                    <?php endif; ?>
                </div>
                
                <button type="submit" name="update_basic">更新基本資料</button>
            </form>
        </div>

        <!-- 學歷區塊 -->
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
                <?php while ($row = $result_education->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['department']); ?></td>
                    <td><?php echo htmlspecialchars($row['degree']); ?></td>
                    <td>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="table" value="education">
                            <input type="hidden" name="id_field" value="edu_ID">
                            <input type="hidden" name="id_value" value="<?php echo $row['edu_ID']; ?>">
                            <button type="submit" name="delete_data" class="delete-btn" onclick="return confirm('確定要刪除這筆資料嗎？')">刪除</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <!-- 專長區塊 -->
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
                <?php while ($row = $result_expertise->fetch_assoc()): ?>
                <tr>
                    <td><?php echo nl2br(htmlspecialchars($row['item'])); ?></td>
                    <td>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="table" value="expertise">
                            <input type="hidden" name="id_field" value="expertise_ID">
                            <input type="hidden" name="id_value" value="<?php echo $row['expertise_ID']; ?>">
                            <button type="submit" name="delete_data" class="delete-btn" onclick="return confirm('確定要刪除這筆資料嗎？')">刪除</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <!-- 期刊論文區塊 -->
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
                    <input type="text" name="pages">
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
                <?php while ($row = $result_journal->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['jour_character']); ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['issue']); ?></td>
                    <td><?php echo htmlspecialchars($row['date']); ?></td>
                    <td><?php echo htmlspecialchars($row['pages']); ?></td>
                    <td>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="table" value="journal">
                            <input type="hidden" name="id_field" value="jour_ID">
                            <input type="hidden" name="id_value" value="<?php echo $row['jour_ID']; ?>">
                            <button type="submit" name="delete_data" class="delete-btn" onclick="return confirm('確定要刪除這筆資料嗎？')">刪除</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <!-- 會議論文區塊 -->
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
                    <input type="text" name="pages">
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
                <?php while ($row = $result_conference->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['conf_character']); ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['pages']); ?></td>
                    <td><?php echo htmlspecialchars($row['date']); ?></td>
                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                    <td>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="table" value="conference">
                            <input type="hidden" name="id_field" value="conf_ID">
                            <input type="hidden" name="id_value" value="<?php echo $row['conf_ID']; ?>">
                            <button type="submit" name="delete_data" class="delete-btn" onclick="return confirm('確定要刪除這筆資料嗎？')">刪除</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <!-- 經歷區塊 -->
        <div class="section">
            <h2>經歷</h2>
            <form method="post">
                <input type="hidden" name="pro_ID" value="<?php echo $professor['pro_ID']; ?>">
                
                <div>
                    <label>類別：</label>
                    <select name="category" required>
                        <option value="校內">校內</option>
                        <option value="校外">校外</option>
                    </select>
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
                    <th>類別</th>
                    <th>單位</th>
                    <th>職位</th>
                    <th>操作</th>
                </tr>
                <?php while ($row = $result_experience->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                    <td><?php echo htmlspecialchars($row['department']); ?></td>
                    <td><?php echo htmlspecialchars($row['position']); ?></td>
                    <td>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="table" value="experience">
                            <input type="hidden" name="id_field" value="experience_ID">
                            <input type="hidden" name="id_value" value="<?php echo $row['experience_ID']; ?>">
                            <button type="submit" name="delete_data" class="delete-btn" onclick="return confirm('確定要刪除這筆資料嗎？')">刪除</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>