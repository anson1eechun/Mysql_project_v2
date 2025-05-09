<?php
// 引入資料庫連接文件
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    // 根據提交的表單類型執行不同的操作
    if (isset($_POST['update_basic'])) {
        // 更新基本資料
        $id = $_POST['teacher_id'];
        $name = $_POST['name'];
        $title = $_POST['title'];
        $intro = $_POST['intro'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];

        $sql = "UPDATE teacher_info SET
                name = '$name',
                title = '$title',
                intro = '$intro',
                email = '$email',
                phone = '$phone'
                WHERE id = $id";

        if ($conn->query($sql) === TRUE) {
            $message = "基本資料更新成功！";
        } else {
            $error = "錯誤: " . $conn->error;
        }
    }<?php
     // 引入資料庫連接文件
     require_once 'db_connection.php';

     if ($_SERVER["REQUEST_METHOD"] == "POST") {
         // 根據提交的表單類型執行不同的操作
         if (isset($_POST['update_basic'])) {
             // 更新基本資料
             $id = $_POST['teacher_id'];
             $name = $_POST['name'];
             $title = $_POST['title'];
             $intro = $_POST['intro'];
             $email = $_POST['email'];
             $phone = $_POST['phone'];

             $sql = "UPDATE teacher_info SET
                     name = '$name',
                     title = '$title',
                     intro = '$intro',
                     email = '$email',
                     phone = '$phone'
                     WHERE id = $id";

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
                     $id = $_POST['teacher_id'];
                     $sql = "UPDATE teacher_info SET photo_path = '$target_file' WHERE id = $id";
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
             $teacher_id = $_POST['teacher_id'];
             $degree = $_POST['degree'];
             $school = $_POST['school'];
             $department = $_POST['department'];

             $sql = "INSERT INTO education (teacher_id, degree, school, department)
                     VALUES ($teacher_id, '$degree', '$school', '$department')";

             if ($conn->query($sql) === TRUE) {
                 $message = "學歷新增成功！";
             } else {
                 $error = "錯誤: " . $conn->error;
             }
         }

         // 處理新增專長
         if (isset($_POST['add_specialty'])) {
             $teacher_id = $_POST['teacher_id'];
             $specialty = $_POST['specialty'];
             $specialty_en = $_POST['specialty_en'];

             $sql = "INSERT INTO specialties (teacher_id, specialty, specialty_en)
                     VALUES ($teacher_id, '$specialty', '$specialty_en')";

             if ($conn->query($sql) === TRUE) {
                 $message = "專長新增成功！";
             } else {
                 $error = "錯誤: " . $conn->error;
             }
         }

         // 處理新增論文
         if (isset($_POST['add_paper'])) {
             $teacher_id = $_POST['teacher_id'];
             $title = $_POST['paper_title'];
             $journal = $_POST['journal'];
             $date = $_POST['publish_date'];
             $type = $_POST['paper_type'];
             $paper_category = $_POST['paper_category'];

             if ($paper_category == 'journal') {
                 $sql = "INSERT INTO journal_papers (teacher_id, title, journal, publish_date, type)
                         VALUES ($teacher_id, '$title', '$journal', '$date', '$type')";
             } else {
                 $sql = "INSERT INTO conference_papers (teacher_id, title, conference, publish_date)
                         VALUES ($teacher_id, '$title', '$journal', '$date')";
             }

             if ($conn->query($sql) === TRUE) {
                 $message = "論文新增成功！";
             } else {
                 $error = "錯誤: " . $conn->error;
             }
         }

         // 處理新增經歷
         if (isset($_POST['add_experience'])) {
             $teacher_id = $_POST['teacher_id'];
             $position = $_POST['position'];
             $organization = $_POST['organization'];
             $is_internal = $_POST['is_internal'];

             $sql = "INSERT INTO experiences (teacher_id, position, organization, is_internal)
                     VALUES ($teacher_id, '$position', '$organization', $is_internal)";

             if ($conn->query($sql) === TRUE) {
                 $message = "經歷新增成功！";
             } else {
                 $error = "錯誤: " . $conn->error;
             }
         }


         // 處理刪除資料
         if (isset($_POST['delete_data'])) {
             $table = $_POST['table'];
             $id = $_POST['record_id'];

             $sql = "DELETE FROM $table WHERE id = $id";

             if ($conn->query($sql) === TRUE) {
                 $message = "資料刪除成功！";
             } else {
                 $error = "錯誤: " . $conn->error;
             }
         }
     }

     // 查詢教師基本資料
     $teacherId = isset($_GET['id']) ? $_GET['id'] : 1; // 預設顯示ID為1的教師
     $sql_teacher = "SELECT * FROM teacher_info WHERE id = $teacherId";
     $result_teacher = $conn->query($sql_teacher);
     $teacher = $result_teacher->fetch_assoc();

     // 查詢學歷
     $sql_education = "SELECT * FROM education WHERE teacher_id = $teacherId ORDER BY id";
     $result_education = $conn->query($sql_education);

     // 查詢專長
     $sql_specialties = "SELECT * FROM specialties WHERE teacher_id = $teacherId ORDER BY id";
     $result_specialties = $conn->query($sql_specialties);

     // 查詢期刊論文
     $sql_journals = "SELECT * FROM journal_papers WHERE teacher_id = $teacherId ORDER BY publish_date DESC";
     $result_journals = $conn->query($sql_journals);

     // 查詢會議論文
     $sql_conferences = "SELECT * FROM conference_papers WHERE teacher_id = $teacherId ORDER BY publish_date DESC";
     $result_conferences = $conn->query($sql_conferences);

     // 查詢校內經歷
     $sql_internal_exp = "SELECT * FROM experiences WHERE teacher_id = $teacherId AND is_internal = TRUE";
     $result_internal_exp = $conn->query($sql_internal_exp);

     // 查詢校外經歷
     $sql_external_exp = "SELECT * FROM experiences WHERE teacher_id = $teacherId AND is_internal = FALSE";
     $result_external_exp = $conn->query($sql_external_exp);
     ?>

     <!DOCTYPE html>
     <html lang="zh-Hant-TW">
     <head>
         <meta charset="UTF-8">
         <title>教師資料管理系統</title>
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
                 margin-top: 15px;
             }
             label {
                 display: block;
                 margin-bottom: 5px;
                 font-weight: bold;
             }
             input[type="text"], input[type="email"], textarea, select {
                 width: 100%;
                 padding: 8px;
                 margin-bottom: 10px;
                 border: 1px solid #ddd;
                 border-radius: 4px;
             }
             textarea {
                 height: 100px;
             }
             button {
                 background-color: #4CAF50;
                 color: white;
                 padding: 10px 15px;
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
                 margin-top: 10px;
             }
             table, th, td {
                 border: 1px solid #ddd;
             }
             th, td {
                 padding: 10px;
                 text-align: left;
             }
             th {
                 background-color: #f2f2f2;
             }
             .delete-btn {
                 background-color: #f44336;
             }
             .delete-btn:hover {
                 background-color: #d32f2f;
             }
         </style>
     </head>
     <body>
         <div class="container">
             <h1>教師資料管理系統</h1>

             <?php if(isset($message)): ?>
                 <div class="message"><?php echo $message; ?></div>
             <?php endif; ?>

             <?php if(isset($error)): ?>
                 <div class="error"><?php echo $error; ?></div>
             <?php endif; ?>

             <div class="section">
                 <h2>基本資料</h2>
                 <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                     <input type="hidden" name="teacher_id" value="<?php echo $teacher['id']; ?>">

                     <label for="name">姓名：</label>
                     <input type="text" id="name" name="name" value="<?php echo $teacher['name']; ?>" required>

                     <label for="title">職稱：</label>
                     <input type="text" id="title" name="title" value="<?php echo $teacher['title']; ?>" required>

                     <label for="intro">簡介：</label>
                     <textarea id="intro" name="intro" required><?php echo $teacher['intro']; ?></textarea>

                     <label for="email">Email：</label>
                     <input type="email" id="email" name="email" value="<?php echo $teacher['email']; ?>" required>

                     <label for="phone">電話：</label>
                     <input type="text" id="phone" name="phone" value="<?php echo $teacher['phone']; ?>" required>

                     <button type="submit" name="update_basic">更新基本資料</button>
                 </form>

                 <h3>更新照片</h3>
                 <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                     <input type="hidden" name="teacher_id" value="<?php echo $teacher['id']; ?>">

                     <label for="photo">選擇照片：</label>
                     <input type="file" id="photo" name="photo" accept="image/*" required>

                     <button type="submit" name="update_photo">上傳照片</button>
                 </form>
             </div>

             <div class="section">
                 <h2>學歷管理</h2>
                 <table>
                     <tr>
                         <th>學位</th>
                         <th>學校</th>
                         <th>科系</th>
                         <th>操作</th>
                     </tr>
                     <?php while($edu = $result_education->fetch_assoc()): ?>
                     <tr>
                         <td><?php echo $edu['degree']; ?></td>
                         <td><?php echo $edu['school']; ?></td>
                         <td><?php echo $edu['department']; ?></td>
                         <td>
                             <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                 <input type="hidden" name="table" value="education">
                                 <input type="hidden" name="record_id" value="<?php echo $edu['id']; ?>">
                                 <button type="submit" class="delete-btn" name="delete_data">刪除</button>
                             </form>
                         </td>
                     </tr>
                     <?php endwhile; ?>
                 </table>

                 <h3>新增學歷</h3>
                 <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                     <input type="hidden" name="teacher_id" value="<?php echo $teacher['id']; ?>">

                     <label for="degree">學位：</label>
                     <input type="text" id="degree" name="degree" required>

                     <label for="school">學校：</label>
                     <input type="text" id="school" name="school" required>

                     <label for="department">科系：</label>
                     <input type="text" id="department" name="department" required>

                     <button type="submit" name="add_education">新增學歷</button>
                 </form>
             </div>

             <div class="section">
                 <h2>專長管理</h2>
                 <table>
                     <tr>
                         <th>專長名稱</th>
                         <th>英文名稱</th>
                         <th>操作</th>
                     </tr>
                     <?php while($spec = $result_specialties->fetch_assoc()): ?>
                     <tr>
                         <td><?php echo $spec['specialty']; ?></td>
                         <td><?php echo $spec['specialty_en']; ?></td>
                         <td>
                             <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                 <input type="hidden" name="table" value="specialties">
                                 <input type="hidden" name="record_id" value="<?php echo $spec['id']; ?>">
                                 <button type="submit" class="delete-btn" name="delete_data">刪除</button>
                             </form>
                         </td>
                     </tr>
                     <?php endwhile; ?>
                 </table>

                 <h3>新增專長</h3>
                 <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                     <input type="hidden" name="teacher_id" value="<?php echo $teacher['id']; ?>">

                     <label for="specialty">專長名稱：</label>
                     <input type="text" id="specialty" name="specialty" required>

                     <label for="specialty_en">英文名稱：</label>
                     <input type="text" id="specialty_en" name="specialty_en" required>

                     <button type="submit" name="add_specialty">新增專長</button>
                 </form>
             </div>

             <div class="section">
                 <h2>期刊論文管理</h2>
                 <table>
                     <tr>
                         <th>標題</th>
                         <th>期刊</th>
                         <th>發布日期</th>
                         <th>類型</th>
                         <th>操作</th>
                     </tr>
                     <?php while($journal = $result_journals->fetch_assoc()): ?>
                     <tr>
                         <td><?php echo $journal['title']; ?></td>
                         <td><?php echo $journal['journal']; ?></td>
                         <td><?php echo $journal['publish_date']; ?></td>
                         <td><?php echo $journal['type']; ?></td>
                         <td>
                             <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                 <input type="hidden" name="table" value="journal_papers">
                                 <input type="hidden" name="record_id" value="<?php echo $journal['id']; ?>">
                                 <button type="submit" class="delete-btn" name="delete_data">刪除</button>
                             </form>
                         </td>
                     </tr>
                     <?php endwhile; ?>
                 </table>

                 <h2>會議論文管理</h2>
                 <table>
                     <tr>
                         <th>標題</th>
                         <th>會議</th>
                         <th>發布日期</th>
                         <th>操作</th>
                     </tr>
                     <?php while($conf = $result_conferences->fetch_assoc()): ?>
                     <tr>
                         <td><?php echo $conf['title']; ?></td>
                         <td><?php echo $conf['conference']; ?></td>
                         <td><?php echo $conf['publish_date']; ?></td>
                         <td>
                             <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                 <input type="hidden" name="table" value="conference_papers">
                                 <input type="hidden" name="record_id" value="<?php echo $conf['id']; ?>">
                                 <button type="submit" class="delete-btn" name="delete_data">刪除</button>
                             </form>
                         </td>
                     </tr>
                     <?php endwhile; ?>
                 </table>

                 <h3>新增論文</h3>
                 <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                     <input type="hidden" name="teacher_id" value="<?php echo $teacher['id']; ?>">

                     <label for="paper_category">論文類型：</label>
                     <select id="paper_category" name="paper_category" required>
                         <option value="journal">期刊論文</option>
                         <option value="conference">會議論文</option>
                     </select>

                     <label for="paper_title">論文標題：</label>
                     <input type="text" id="paper_title" name="paper_title" required>

                     <label for="journal">期刊/會議名稱：</label>
                     <input type="text" id="journal" name="journal" required>

                     <label for="publish_date">發布日期 (YYYY-MM)：</label>
                     <input type="text" id="publish_date" name="publish_date" placeholder="例如：2023-05" required>

                     <div id="journal_type_field">
                         <label for="paper_type">期刊類型 (例如：SCIE)：</label>
                         <input type="text" id="paper_type" name="paper_type">
                     </div>

                     <button type="submit" name="add_paper">新增論文</button>
                 </form>
             </div>

             <div class="section">
                 <h2>經歷管理</h2>
                 <h3>校內經歷</h3>
                 <table>
                     <tr>
                         <th>職稱</th>
                         <th>單位</th>
                         <th>操作</th>
                     </tr>
                     <?php while($exp = $result_internal_exp->fetch_assoc()): ?>
                     <tr>
                         <td><?php echo $exp['position']; ?></td>
                         <td><?php echo $exp['organization']; ?></td>
                         <td>
                             <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                 <input type="hidden" name="table" value="experiences">
                                 <input type="hidden" name="record_id" value="<?php echo $exp['id']; ?>">
                                 <button type="submit" class="delete-btn" name="delete_data">刪除</button>
                             </form>
                         </td>
                     </tr>
                     <?php endwhile; ?>
                 </table>

                 <h3>校外經歷</h3>
                 <table>
                     <tr>
                         <th>職稱</th>
                         <th>單位</th>
                         <th>操作</th>
                     </tr>
                     <?php while($exp = $result_external_exp->fetch_assoc()): ?>
                     <tr>
                         <td><?php echo $exp['position']; ?></td>
                         <td><?php echo $exp['organization']; ?></td>
                         <td>
                             <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                 <input type="hidden" name="table" value="experiences">
                                 <input type="hidden" name="record_id" value="<?php echo $exp['id']; ?>">
                                 <button type="submit" class="delete-btn" name="delete_data">刪除</button>
                             </form>
                         </td>
                     </tr>
                     <?php endwhile; ?>
                 </table>

                 <h3>新增經歷</h3>
                 <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                     <input type="hidden" name="teacher_id" value="<?php echo $teacher['id']; ?>">

                     <label for="position">職稱：</label>
                     <input type="text" id="position" name="position" required>

                     <label for="organization">單位：</label>
                     <input type="text" id="organization" name="organization" required>

                     <label for="is_internal">性質：</label>
                     <select id="is_internal" name="is_internal" required>
                         <option value="1">校內經歷</option>
                         <option value="0">校外經歷</option>
                     </select>

                     <button type="submit" name="add_experience">新增經歷</button>
                 </form>
             </div>

             <div class="section">
                 <a href="index.php" target="_blank">查看前台頁面</a>
             </div>
         </div>

         <script>
             // 當論文類型改變時，顯示或隱藏期刊類型欄位
             document.getElementById('paper_category').addEventListener('change', function() {
                 var journalTypeField = document.getElementById('journal_type_field');
                 if (this.value === 'journal') {
                     journalTypeField.style.display = 'block';
                 } else {
                     journalTypeField.style.display = 'none';
                 }
             });
         </script>
     </body>
     </html>

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
                $id = $_POST['teacher_id'];
                $sql = "UPDATE teacher_info SET photo_path = '$target_file' WHERE id = $id";
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
        $teacher_id = $_POST['teacher_id'];
        $degree = $_POST['degree'];
        $school = $_POST['school'];
        $department = $_POST['department'];

        $sql = "INSERT INTO education (teacher_id, degree, school, department)
                VALUES ($teacher_id, '$degree', '$school', '$department')";

        if ($conn->query($sql) === TRUE) {
            $message = "學歷新增成功！";
        } else {
            $error = "錯誤: " . $conn->error;
        }
    }

    // 處理新增專長
    if (isset($_POST['add_specialty'])) {
        $teacher_id = $_POST['teacher_id'];
        $specialty = $_POST['specialty'];
        $specialty_en = $_POST['specialty_en'];

        $sql = "INSERT INTO specialties (teacher_id, specialty, specialty_en)
                VALUES ($teacher_id, '$specialty', '$specialty_en')";

        if ($conn->query($sql) === TRUE) {
            $message = "專長新增成功！";
        } else {
            $error = "錯誤: " . $conn->error;
        }
    }

    // 處理新增論文
    if (isset($_POST['add_paper'])) {
        $teacher_id = $_POST['teacher_id'];
        $title = $_POST['paper_title'];
        $journal = $_POST['journal'];
        $date = $_POST['publish_date'];
        $type = $_POST['paper_type'];
        $paper_category = $_POST['paper_category'];

        if ($paper_category == 'journal') {
            $sql = "INSERT INTO journal_papers (teacher_id, title, journal, publish_date, type)
                    VALUES ($teacher_id, '$title', '$journal', '$date', '$type')";
        } else {
            $sql = "INSERT INTO conference_papers (teacher_id, title, conference, publish_date)
                    VALUES ($teacher_id, '$title', '$journal', '$date')";
        }

        if ($conn->query($sql) === TRUE) {
            $message = "論文新增成功！";
        } else {
            $error = "錯誤: " . $conn->error;
        }
    }

    // 處理新增經歷
    if (isset($_POST['add_experience'])) {
        $teacher_id = $_POST['teacher_id'];
        $position = $_POST['position'];
        $organization = $_POST['organization'];
        $is_internal = $_POST['is_internal'];

        $sql = "INSERT INTO experiences (teacher_id, position, organization, is_internal)
                VALUES ($teacher_id, '$position', '$organization', $is_internal)";

        if ($conn->query($sql) === TRUE) {
            $message = "經歷新增成功！";
        } else {
            $error = "錯誤: " . $conn->error;
        }
    }

    // 處理刪除資料
    if (isset($_POST['delete_data'])) {
        $table = $_POST['table'];
        $id = $_POST['record_id'];

        $sql = "DELETE FROM $table WHERE id = $id";

        if ($conn->query($sql) === TRUE) {
            $message = "資料刪除成功！";
        } else {
            $error = "錯誤: " . $conn->error;
        }
    }
}

// 查詢教師基本資料
$teacherId = isset($_GET['id']) ? $_GET['id'] : 1; // 預設顯示ID為1的教師
$sql_teacher = "SELECT * FROM teacher_info WHERE id = $teacherId";
$result_teacher = $conn->query($sql_teacher);
$teacher = $result_teacher->fetch_assoc();

// 查詢學歷
$sql_education = "SELECT * FROM education WHERE teacher_id = $teacherId ORDER BY id";
$result_education = $conn->query($sql_education);

// 查詢專長
$sql_specialties = "SELECT * FROM specialties WHERE teacher_id = $teacherId ORDER BY id";
$result_specialties = $conn->query($sql_specialties);

// 查詢期刊論文
$sql_journals = "SELECT * FROM journal_papers WHERE teacher_id = $teacherId ORDER BY publish_date DESC";
$result_journals = $conn->query($sql_journals);

// 查詢會議論文
$sql_conferences = "SELECT * FROM conference_papers WHERE teacher_id = $teacherId ORDER BY publish_date DESC";
$result_conferences = $conn->query($sql_conferences);

// 查詢校內經歷
$sql_internal_exp = "SELECT * FROM experiences WHERE teacher_id = $teacherId AND is_internal = TRUE";
$result_internal_exp = $conn->query($sql_internal_exp);

// 查詢校外經歷
$sql_external_exp = "SELECT * FROM experiences WHERE teacher_id = $teacherId AND is_internal = FALSE";
$result_external_exp = $conn->query($sql_external_exp);
?>

<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8">
    <title>教師資料管理系統</title>
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
            margin-top: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], input[type="email"], textarea, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        textarea {
            height: 100px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
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
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .delete-btn {
            background-color: #f44336;
        }
        .delete-btn:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>教師資料管理系統</h1>

        <?php if(isset($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if(isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="section">
            <h2>基本資料</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                <input type="hidden" name="teacher_id" value="<?php echo $teacher['id']; ?>">

                <label for="name">姓名：</label>
                <input type="text" id="name" name="name" value="<?php echo $teacher['name']; ?>" required>

                <label for="title">職稱：</label>
                <input type="text" id="title" name="title" value="<?php echo $teacher['title']; ?>" required>

                <label for="intro">簡介：</label>
                <textarea id="intro" name="intro" required><?php echo $teacher['intro']; ?></textarea>

                <label for="email">Email：</label>
                <input type="email" id="email" name="email" value="<?php echo $teacher['email']; ?>" required>

                <label for="phone">電話：</label>
                <input type="text" id="phone" name="phone" value="<?php echo $teacher['phone']; ?>" required>

                <button type="submit" name="update_basic">更新基本資料</button>
            </form>

            <h3>更新照片</h3>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                <input type="hidden" name="teacher_id" value="<?php echo $teacher['id']; ?>">

                <label for="photo">選擇照片：</label>
                <input type="file" id="photo" name="photo" accept="image/*" required>

                <button type="submit" name="update_photo">上傳照片</button>
            </form>
        </div>

        <div class="section">
            <h2>學歷管理</h2>
            <table>
                <tr>
                    <th>學位</th>
                    <th>學校</th>
                    <th>科系</th>
                    <th>操作</th>
                </tr>
                <?php while($edu = $result_education->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $edu['degree']; ?></td>
                    <td><?php echo $edu['school']; ?></td>
                    <td><?php echo $edu['department']; ?></td>
                    <td>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="table" value="education">
                            <input type="hidden" name="record_id" value="<?php echo $edu['id']; ?>">
                            <button type="submit" class="delete-btn" name="delete_data">刪除</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>

            <h3>新增學歷</h3>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" name="teacher_id" value="<?php echo $teacher['id']; ?>">

                <label for="degree">學位：</label>
                <input type="text" id="degree" name="degree" required>

                <label for="school">學校：</label>
                <input type="text" id="school" name="school" required>

                <label for="department">科系：</label>
                <input type="text" id="department" name="department" required>

                <button type="submit" name="add_education">新增學歷</button>
            </form>
        </div>

        <div class="section">
            <h2>專長管理</h2>
            <table>
                <tr>
                    <th>專長名稱</th>
                    <th>英文名稱</th>
                    <th>操作</th>
                </tr>
                <?php while($spec = $result_specialties->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $spec['specialty']; ?></td>
                    <td><?php echo $spec['specialty_en']; ?></td>
                    <td>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="table" value="specialties">
                            <input type="hidden" name="record_id" value="<?php echo $spec['id']; ?>">
                            <button type="submit" class="delete-btn" name="delete_data">刪除</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>

            <h3>新增專長</h3>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" name="teacher_id" value="<?php echo $teacher['id']; ?>">

                <label for="specialty">專長名稱：</label>
                <input type="text" id="specialty" name="specialty" required>

                <label for="specialty_en">英文名稱：</label>
                <input type="text" id="specialty_en" name="specialty_en" required>

                <button type="submit" name="add_specialty">新增專長</button>
            </form>
        </div>

        <div class="section">
            <h2>期刊論文管理</h2>
            <table>
                <tr>
                    <th>標題</th>
                    <th>期刊</th>
                    <th>發布日期</th>
                    <th>類型</th>
                    <th>操作</th>
                </tr>
                <?php while($journal = $result_journals->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $journal['title']; ?></td>
                    <td><?php echo $journal['journal']; ?></td>
                    <td><?php echo $journal['publish_date']; ?></td>
                    <td><?php echo $journal['type']; ?></td>
                    <td>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="table" value="journal_papers">
                            <input type="hidden" name="record_id" value="<?php echo $journal['id']; ?>">
                            <button type="submit" class="delete-btn" name="delete_data">刪除</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>

            <h2>會議論文管理</h2>
            <table>
                <tr>
                    <th>標題</th>
                    <th>會議</th>
                    <th>發布日期</th>
                    <th>操作</th>
                </tr>
                <?php while($conf = $result_conferences->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $conf['title']; ?></td>
                    <td><?php echo $conf['conference']; ?></td>
                    <td><?php echo $conf['publish_date']; ?></td>
                    <td>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="table" value="conference_papers">
                            <input type="hidden" name="record_id" value="<?php echo $conf['id']; ?>">
                            <button type="submit" class="delete-btn" name="delete_data">刪除</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>

            <h3>新增論文</h3>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" name="teacher_id" value="<?php echo $teacher['id']; ?>">

                <label for="paper_category">論文類型：</label>
                <select id="paper_category" name="paper_category" required>
                    <option value="journal">期刊論文</option>
                    <option value="conference">會議論文</option>
                </select>

                <label for="paper_title">論文標題：</label>
                <input type="text" id="paper_title" name="paper_title" required>

                <label for="journal">期刊/會議名稱：</label>
                <input type="text" id="journal" name="journal" required>

                <label for="publish_date">發布日期 (YYYY-MM)：</label>
                <input type="text" id="publish_date" name="publish_date" placeholder="例如：2023-05" required>

                <div id="journal_type_field">
                    <label for="paper_type">期刊類型 (例如：SCIE)：</label>
                    <input type="text" id="paper_type" name="paper_type">
                </div>

                <button type="submit" name="add_paper">新增論文</button>
            </form>
        </div>

        <div class="section">
            <h2>經歷管理</h2>
            <h3>校內經歷</h3>
            <table>
                <tr>
                    <th>職稱</th>
                    <th>單位</th>
                    <th>操作</th>
                </tr>
                <?php while($exp = $result_internal_exp->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $exp['position']; ?></td>
                    <td><?php echo $exp['organization']; ?></td>
                    <td>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="table" value="experiences">
                            <input type="hidden" name="record_id" value="<?php echo $exp['id']; ?>">
                            <button type="submit" class="delete-btn" name="delete_data">刪除</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>

            <h3>校外經歷</h3>
            <table>
                <tr>
                    <th>職稱</th>
                    <th>單位</th>
                    <th>操作</th>
                </tr>
                <?php while($exp = $result_external_exp->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $exp['position']; ?></td>
                    <td><?php echo $exp['organization']; ?></td>
                    <td>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="table" value="experiences">
                            <input type="hidden" name="record_id" value="<?php echo $exp['id']; ?>">
                            <button type="submit" class="delete-btn" name="delete_data">刪除</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>

            <h3>新增經歷</h3>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" name="teacher_id" value="<?php echo $teacher['id']; ?>">

                <label for="position">職稱：</label>
                <input type="text" id="position" name="position" required>

                <label for="organization">單位：</label>
                <input type="text" id="organization" name="organization" required>

                <label for="is_internal">性質：</label>
                <select id="is_internal" name="is_internal" required>
                    <option value="1">校內經歷</option>
                    <option value="0">校外經歷</option>
                </select>

                <button type="submit" name="add_experience">新增經歷</button>
            </form>
        </div>

        <div class="section">
            <a href="index.php" target="_blank">查看前台頁面</a>
        </div>
    </div>

    <script>
        // 當論文類型改變時，顯示或隱藏期刊類型欄位
        document.getElementById('paper_category').addEventListener('change', function() {
            var journalTypeField = document.getElementById('journal_type_field');
            if (this.value === 'journal') {
                journalTypeField.style.display = 'block';
            } else {
                journalTypeField.style.display = 'none';
            }
        });
    </script>
</body>
</html>