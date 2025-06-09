<?php
// 引入必要檔案
require_once 'config.php';
//require_once 'auth.php';

// 檢查登入權限
requirePermission('admin');

// 獲取當前用戶資訊
$current_user = getCurrentUser();
$pro_ID = $current_user['role'] === 'teacher' ? $current_user['pro_ID'] : ($_GET['id'] ?? 'A001');

// 驗證 CSRF Tokenß
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    checkCSRFToken();
}


$message = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // 根據提交的表單類型執行不同的操作
        if (isset($_POST['update_basic'])) {
            // 更新基本資料
            $name = sanitizeInput($_POST['name']);
            $name_en = sanitizeInput($_POST['name_en']);
            $position = sanitizeInput($_POST['position']);
            $department = sanitizeInput($_POST['department']);
            $email = sanitizeInput($_POST['email'], 'email');
            $phone = sanitizeInput($_POST['phone']);
            $extension = sanitizeInput($_POST['extension']);
            $office_location = sanitizeInput($_POST['office_location']);
            $website = sanitizeInput($_POST['website']);
            $introduction = sanitizeInput($_POST['introduction']);

            // 檢查權限（教師只能修改自己的資料）
            if ($current_user['role'] === 'teacher' && $current_user['pro_ID'] !== $pro_ID) {
                throw new Exception("您只能修改自己的資料");
            }

            // 獲取原始資料用於日誌
            $old_data_stmt = $conn->prepare("SELECT * FROM professor WHERE pro_ID = ?");
            $old_data_stmt->bind_param("s", $pro_ID);
            $old_data_stmt->execute();
            $old_data = $old_data_stmt->get_result()->fetch_assoc();
            $old_data_stmt->close();

            $stmt = $conn->prepare("UPDATE professor SET name = ?, name_en = ?, position = ?, department = ?, email = ?, phone = ?, extension = ?, office_location = ?, website = ?, introduction = ?, updated_at = NOW() WHERE pro_ID = ?");
            $stmt->bind_param("sssssssssss", $name, $name_en, $position, $department, $email, $phone, $extension, $office_location, $website, $introduction, $pro_ID);

            if ($stmt->execute()) {
                // 記錄操作日誌
                $new_data = compact('name', 'name_en', 'position', 'department', 'email', 'phone', 'extension', 'office_location', 'website', 'introduction');
                logUserAction('update_professor', 'professor', $pro_ID, $old_data, $new_data);
                
                $message = "基本資料更新成功！";
            } else {
                throw new Exception("更新基本資料失敗：" . $stmt->error);
            }
            $stmt->close();
        }

        // 處理照片上傳
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            // 檢查權限
            if ($current_user['role'] === 'teacher' && $current_user['pro_ID'] !== $pro_ID) {
                throw new Exception("您只能修改自己的照片");
            }

            $target_dir = "uploads/photos/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            
            // 生成唯一檔名
            $file_extension = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));
            $new_filename = $pro_ID . '_' . time() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;

            // 檢查是否為真實圖片
            $check = getimagesize($_FILES["photo"]["tmp_name"]);
            if($check === false) {
                throw new Exception("檔案不是圖片。");
            }

            // 檢查檔案大小（限制為 5MB）
            if ($_FILES["photo"]["size"] > 5000000) {
                throw new Exception("檔案太大，請選擇小於 5MB 的圖片。");
            }

            // 允許的檔案格式
            if(!in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                throw new Exception("只允許 JPG, JPEG, PNG, GIF 檔案。");
            }

            // 嘗試上傳文件
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                // 刪除舊照片
                $old_photo_stmt = $conn->prepare("SELECT photo FROM professor WHERE pro_ID = ?");
                $old_photo_stmt->bind_param("s", $pro_ID);
                $old_photo_stmt->execute();
                $old_photo_result = $old_photo_stmt->get_result();
                if ($old_photo_data = $old_photo_result->fetch_assoc()) {
                    $old_photo = $old_photo_data['photo'];
                    if ($old_photo && file_exists($old_photo) && strpos($old_photo, 'uploads/') === 0) {
                        unlink($old_photo);
                    }
                }
                $old_photo_stmt->close();

                // 更新資料庫中的照片路徑
                $stmt = $conn->prepare("UPDATE professor SET photo = ?, updated_at = NOW() WHERE pro_ID = ?");
                $stmt->bind_param("ss", $target_file, $pro_ID);
                
                if ($stmt->execute()) {
                    logUserAction('update_photo', 'professor', $pro_ID, ['photo' => $old_photo], ['photo' => $target_file]);
                    $message = "照片上傳成功！";
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
            if ($current_user['role'] === 'teacher' && $current_user['pro_ID'] !== $pro_ID) {
                throw new Exception("您只能管理自己的學歷");
            }

            $department = sanitizeInput($_POST['department']);
            $school = sanitizeInput($_POST['school']);
            $degree = sanitizeInput($_POST['degree']);
            $graduation_year = sanitizeInput($_POST['graduation_year'], 'int');
            $sort_order = sanitizeInput($_POST['sort_order'], 'int');

            // 生成學歷ID
            $edu_ID = 'EDU_' . $pro_ID . '_' . time();

            $stmt = $conn->prepare("INSERT INTO education (edu_ID, pro_ID, department, school, degree, graduation_year, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssii", $edu_ID, $pro_ID, $department, $school, $degree, $graduation_year, $sort_order);

            if ($stmt->execute()) {
                $new_data = compact('department', 'school', 'degree', 'graduation_year', 'sort_order');
                logUserAction('add_education', 'education', $edu_ID, null, $new_data);
                $message = "學歷新增成功！";
            } else {
                throw new Exception("新增學歷失敗：" . $stmt->error);
            }
            $stmt->close();
        }

        // 處理新增專長
        if (isset($_POST['add_expertise'])) {
            if ($current_user['role'] === 'teacher' && $current_user['pro_ID'] !== $pro_ID) {
                throw new Exception("您只能管理自己的專長");
            }

            $category = sanitizeInput($_POST['category']);
            $item = sanitizeInput($_POST['item']);
            $item_en = sanitizeInput($_POST['item_en']);
            $sort_order = sanitizeInput($_POST['sort_order'], 'int');

            $expertise_ID = 'EXP_' . $pro_ID . '_' . time();

            $stmt = $conn->prepare("INSERT INTO expertise (expertise_ID, pro_ID, category, item, item_en, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssi", $expertise_ID, $pro_ID, $category, $item, $item_en, $sort_order);

            if ($stmt->execute()) {
                $new_data = compact('category', 'item', 'item_en', 'sort_order');
                logUserAction('add_expertise', 'expertise', $expertise_ID, null, $new_data);
                $message = "專長新增成功！";
            } else {
                throw new Exception("新增專長失敗：" . $stmt->error);
            }
            $stmt->close();
        }

        // 處理新增期刊論文
        if (isset($_POST['add_journal'])) {
            if ($current_user['role'] === 'teacher' && $current_user['pro_ID'] !== $pro_ID) {
                throw new Exception("您只能管理自己的論文");
            }

            $jour_character = sanitizeInput($_POST['jour_character']);
            $title = sanitizeInput($_POST['title']);
            $name = sanitizeInput($_POST['name']);
            $volume = sanitizeInput($_POST['volume']);
            $issue = sanitizeInput($_POST['issue']);
            $pages = sanitizeInput($_POST['pages']);
            $publication_year = sanitizeInput($_POST['publication_year'], 'int');
            $publication_month = sanitizeInput($_POST['publication_month'], 'int');
            $doi = sanitizeInput($_POST['doi']);
            $impact_factor = sanitizeInput($_POST['impact_factor'], 'float');
            $category = sanitizeInput($_POST['category']);
            $is_corresponding = isset($_POST['is_corresponding']) ? 1 : 0;

            $jour_ID = 'JOU_' . $pro_ID . '_' . time();

            $stmt = $conn->prepare("INSERT INTO journal (jour_ID, pro_ID, jour_character, title, name, volume, issue, pages, publication_year, publication_month, doi, impact_factor, category, is_corresponding) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssiisdssi", $jour_ID, $pro_ID, $jour_character, $title, $name, $volume, $issue, $pages, $publication_year, $publication_month, $doi, $impact_factor, $category, $is_corresponding);

            if ($stmt->execute()) {
                $new_data = compact('jour_character', 'title', 'name', 'volume', 'issue', 'pages', 'publication_year', 'publication_month', 'doi', 'impact_factor', 'category', 'is_corresponding');
                logUserAction('add_journal', 'journal', $jour_ID, null, $new_data);
                $message = "期刊論文新增成功！";
            } else {
                throw new Exception("新增期刊論文失敗：" . $stmt->error);
            }
            $stmt->close();
        }

        // 處理新增會議論文
        if (isset($_POST['add_conference'])) {
            if ($current_user['role'] === 'teacher' && $current_user['pro_ID'] !== $pro_ID) {
                throw new Exception("您只能管理自己的論文");
            }

            $conf_character = sanitizeInput($_POST['conf_character']);
            $title = sanitizeInput($_POST['title']);
            $conference_name = sanitizeInput($_POST['conference_name']);
            $conference_abbr = sanitizeInput($_POST['conference_abbr']);
            $pages = sanitizeInput($_POST['pages']);
            $publication_year = sanitizeInput($_POST['publication_year'], 'int');
            $publication_month = sanitizeInput($_POST['publication_month'], 'int');
            $location = sanitizeInput($_POST['location']);
            $country = sanitizeInput($_POST['country']);
            $conference_type = sanitizeInput($_POST['conference_type']);
            $is_corresponding = isset($_POST['is_corresponding']) ? 1 : 0;

            $conf_ID = 'CON_' . $pro_ID . '_' . time();

            $stmt = $conn->prepare("INSERT INTO conference (conf_ID, pro_ID, conf_character, title, conference_name, conference_abbr, pages, publication_year, publication_month, location, country, conference_type, is_corresponding) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssiifssi", $conf_ID, $pro_ID, $conf_character, $title, $conference_name, $conference_abbr, $pages, $publication_year, $publication_month, $location, $country, $conference_type, $is_corresponding);

            if ($stmt->execute()) {
                $new_data = compact('conf_character', 'title', 'conference_name', 'conference_abbr', 'pages', 'publication_year', 'publication_month', 'location', 'country', 'conference_type', 'is_corresponding');
                logUserAction('add_conference', 'conference', $conf_ID, null, $new_data);
                $message = "會議論文新增成功！";
            } else {
                throw new Exception("新增會議論文失敗：" . $stmt->error);
            }
            $stmt->close();
        }

        // 處理新增經歷
        if (isset($_POST['add_experience'])) {
            if ($current_user['role'] === 'teacher' && $current_user['pro_ID'] !== $pro_ID) {
                throw new Exception("您只能管理自己的經歷");
            }

            $category = sanitizeInput($_POST['category']);
            $organization = sanitizeInput($_POST['organization']);
            $department = sanitizeInput($_POST['department']);
            $position = sanitizeInput($_POST['position']);
            $start_date = sanitizeInput($_POST['start_date']);
            $end_date = sanitizeInput($_POST['end_date']);
            $is_current = isset($_POST['is_current']) ? 1 : 0;
            $description = sanitizeInput($_POST['description']);
            $sort_order = sanitizeInput($_POST['sort_order'], 'int');

            $experience_ID = 'EXP_' . $pro_ID . '_' . time();

            $stmt = $conn->prepare("INSERT INTO experience (experience_ID, pro_ID, category, organization, department, position, start_date, end_date, is_current, description, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssissi", $experience_ID, $pro_ID, $category, $organization, $department, $position, $start_date, $end_date, $is_current, $description, $sort_order);

            if ($stmt->execute()) {
                $new_data = compact('category', 'organization', 'department', 'position', 'start_date', 'end_date', 'is_current', 'description', 'sort_order');
                logUserAction('add_experience', 'experience', $experience_ID, null, $new_data);
                $message = "經歷新增成功！";
            } else {
                throw new Exception("新增經歷失敗：" . $stmt->error);
            }
            $stmt->close();
        }

        // 處理刪除資料
        if (isset($_POST['delete_data'])) {
            $table = sanitizeInput($_POST['table']);
            $id_field = sanitizeInput($_POST['id_field']);
            $id_value = sanitizeInput($_POST['id_value']);

            // 驗證表名和欄位名，防止 SQL 注入
            $allowed_operations = [
                'education' => 'edu_ID',
                'expertise' => 'expertise_ID',
                'journal' => 'jour_ID',
                'conference' => 'conf_ID',
                'experience' => 'experience_ID'
            ];

            if (!array_key_exists($table, $allowed_operations) || $allowed_operations[$table] !== $id_field) {
                throw new Exception("無效的操作");
            }

            // 檢查權限
            if ($current_user['role'] === 'teacher') {
                $check_stmt = $conn->prepare("SELECT pro_ID FROM $table WHERE $id_field = ?");
                $check_stmt->bind_param("s", $id_value);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
                if ($check_data = $check_result->fetch_assoc()) {
                    if ($check_data['pro_ID'] !== $current_user['pro_ID']) {
                        throw new Exception("您只能刪除自己的資料");
                    }
                }
                $check_stmt->close();
            }

            // 獲取要刪除的資料用於日誌
            $old_data_stmt = $conn->prepare("SELECT * FROM $table WHERE $id_field = ?");
            $old_data_stmt->bind_param("s", $id_value);
            $old_data_stmt->execute();
            $old_data = $old_data_stmt->get_result()->fetch_assoc();
            $old_data_stmt->close();

            $stmt = $conn->prepare("DELETE FROM $table WHERE $id_field = ?");
            $stmt->bind_param("s", $id_value);

            if ($stmt->execute()) {
                logUserAction('delete_' . $table, $table, $id_value, $old_data, null);
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
$stmt = $conn->prepare("SELECT * FROM professor WHERE pro_ID = ?");
$stmt->bind_param("s", $pro_ID);
$stmt->execute();
$result_professor = $stmt->get_result();
$professor = $result_professor->fetch_assoc();
$stmt->close();

if (!$professor) {
    $error = "找不到指定的教授資料";
}

// 查詢各類資料
$education_data = [];
$expertise_data = [];
$journal_data = [];
$conference_data = [];
$experience_data = [];

if ($professor) {
    // 查詢學歷
    $stmt = $conn->prepare("SELECT * FROM education WHERE pro_ID = ? ORDER BY sort_order, graduation_year DESC");
    $stmt->bind_param("s", $pro_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $education_data[] = $row;
    }
    $stmt->close();

    // 查詢專長
    $stmt = $conn->prepare("SELECT * FROM expertise WHERE pro_ID = ? ORDER BY sort_order, expertise_ID");
    $stmt->bind_param("s", $pro_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $expertise_data[] = $row;
    }
    $stmt->close();

    // 查詢期刊論文
    $stmt = $conn->prepare("SELECT * FROM journal WHERE pro_ID = ? ORDER BY publication_year DESC, publication_month DESC");
    $stmt->bind_param("s", $pro_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $journal_data[] = $row;
    }
    $stmt->close();

    // 查詢會議論文
    $stmt = $conn->prepare("SELECT * FROM conference WHERE pro_ID = ? ORDER BY publication_year DESC, publication_month DESC");
    $stmt->bind_param("s", $pro_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $conference_data[] = $row;
    }
    $stmt->close();

    // 查詢經歷
    $stmt = $conn->prepare("SELECT * FROM experience WHERE pro_ID = ? ORDER BY sort_order, start_date DESC");
    $stmt->bind_param("s", $pro_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $experience_data[] = $row;
    }
    $stmt->close();
}

// 如果是管理員，獲取所有教授列表
$professors_list = [];
if ($current_user['role'] !== 'teacher') {
    $prof_result = $conn->query("SELECT pro_ID, name FROM professor WHERE is_active = 1 ORDER BY name");
    if ($prof_result) {
        while ($row = $prof_result->fetch_assoc()) {
            $professors_list[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>教授資料管理系統</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Noto Sans TC', sans-serif;
            line-height: 1.6;
            background: #f8f9fa;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 1.8rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logout-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 8px 16px;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s;
        }

        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .professor-selector {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .professor-selector select {
            width: 100%;
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 1rem;
        }

        .message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 12px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 12px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .section {
            background: white;
            margin-bottom: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .section-header {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-header h2 {
            color: #333;
            margin: 0;
        }

        .toggle-btn {
            background: #6c757d;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 0.8rem;
        }

        .section-content {
            padding: 20px;
        }

        .section-content.collapsed {
            display: none;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 0.95rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
        }

        button {
            background: #667eea;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 500;
            transition: background 0.3s;
        }

        button:hover {
            background: #5a6fd8;
        }

        .btn-danger {
            background: #dc3545;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .btn-small {
            padding: 5px 10px;
            font-size: 0.8rem;
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
            vertical-align: top;
        }

        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        tr:hover {
            background-color: #f8f9fa;
        }

        .photo-preview {
            max-width: 200px;
            max-height: 200px;
            object-fit: cover;
            border-radius: 5px;
            margin-top: 10px;
        }

        .corresponding-badge {
            background: #28a745;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.7rem;
        }

        .category-badge {
            background: #17a2b8;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.7rem;
        }

        .current-badge {
            background: #ffc107;
            color: #333;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.7rem;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 10px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 0.8rem;
            }

            th, td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>教授資料管理系統</h1>
            <div class="user-info">
                <span>歡迎，<?php echo htmlspecialchars($current_user['username']); ?> (<?php echo htmlspecialchars($current_user['role']); ?>)</span>
                <a href="?logout=1" class="logout-btn">登出</a>
            </div>
        </div>
    </div>

    <div class="container">
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($current_user['role'] !== 'teacher' && !empty($professors_list)): ?>
        <div class="professor-selector">
            <label for="professor-select"><strong>選擇要管理的教授：</strong></label>
            <select id="professor-select" onchange="changeProfessor()">
                <?php foreach ($professors_list as $prof): ?>
                    <option value="<?php echo htmlspecialchars($prof['pro_ID']); ?>" 
                            <?php echo $prof['pro_ID'] === $pro_ID ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($prof['pro_ID'] . ' - ' . $prof['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>

        <?php if ($professor): ?>
        
        <!-- 基本資料區塊 -->
        <div class="section">
            <div class="section-header">
                <h2>基本資料</h2>
                <button type="button" class="toggle-btn" onclick="toggleSection(this)">收合</button>
            </div>
            <div class="section-content">
                <form method="post" enctype="multipart/form-data">
                    <?php echo csrfTokenField(); ?>
                    <input type="hidden" name="pro_ID" value="<?php echo htmlspecialchars($professor['pro_ID']); ?>">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">中文姓名 *</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($professor['name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="name_en">英文姓名</label>
                            <input type="text" id="name_en" name="name_en" value="<?php echo htmlspecialchars($professor['name_en']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="position">職位 *</label>
                            <input type="text" id="position" name="position" value="<?php echo htmlspecialchars($professor['position']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="department">所屬系所</label>
                            <input type="text" id="department" name="department" value="<?php echo htmlspecialchars($professor['department']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">電子信箱</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($professor['email']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">電話</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($professor['phone']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="extension">分機</label>
                            <input type="text" id="extension" name="extension" value="<?php echo htmlspecialchars($professor['extension']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="office_location">辦公室位置</label>
                            <input type="text" id="office_location" name="office_location" value="<?php echo htmlspecialchars($professor['office_location']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="website">個人網站</label>
                            <input type="url" id="website" name="website" value="<?php echo htmlspecialchars($professor['website']); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="introduction">自我介紹</label>
                        <textarea id="introduction" name="introduction" rows="6"><?php echo htmlspecialchars($professor['introduction']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="photo">照片</label>
                        <input type="file" id="photo" name="photo" accept="image/*">
                        <?php if ($professor['photo']): ?>
                            <img src="<?php echo htmlspecialchars($professor['photo']); ?>" alt="教授照片" class="photo-preview">
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" name="update_basic">更新基本資料</button>
                </form>
            </div>
        </div>

        <!-- 學歷區塊 -->
        <div class="section">
            <div class="section-header">
                <h2>學歷</h2>
                <button type="button" class="toggle-btn" onclick="toggleSection(this)">收合</button>
            </div>
            <div class="section-content">
                <form method="post">
                    <?php echo csrfTokenField(); ?>
                    <input type="hidden" name="pro_ID" value="<?php echo htmlspecialchars($professor['pro_ID']); ?>">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="school">學校名稱 *</label>
                            <input type="text" id="school" name="school" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="department">系所 *</label>
                            <input type="text" id="department" name="department" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="degree">學位 *</label>
                            <select id="degree" name="degree" required>
                                <option value="">請選擇學位</option>
                                <option value="博士">博士</option>
                                <option value="碩士">碩士</option>
                                <option value="學士">學士</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="graduation_year">畢業年份</label>
                            <input type="number" id="graduation_year" name="graduation_year" min="1950" max="2030">
                        </div>
                        
                        <div class="form-group">
                            <label for="sort_order">排序</label>
                            <input type="number" id="sort_order" name="sort_order" value="0" min="0">
                        </div>
                    </div>
                    
                    <button type="submit" name="add_education">新增學歷</button>
                </form>

                <?php if (!empty($education_data)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>學校</th>
                            <th>系所</th>
                            <th>學位</th>
                            <th>畢業年份</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($education_data as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['school']); ?></td>
                            <td><?php echo htmlspecialchars($row['department']); ?></td>
                            <td><?php echo htmlspecialchars($row['degree']); ?></td>
                            <td><?php echo htmlspecialchars($row['graduation_year']); ?></td>
                            <td>
                                <form method="post" style="display: inline;" onsubmit="return confirm('確定要刪除這筆資料嗎？')">
                                    <?php echo csrfTokenField(); ?>
                                    <input type="hidden" name="table" value="education">
                                    <input type="hidden" name="id_field" value="edu_ID">
                                    <input type="hidden" name="id_value" value="<?php echo htmlspecialchars($row['edu_ID']); ?>">
                                    <button type="submit" name="delete_data" class="btn-danger btn-small">刪除</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- 專長區塊 -->
        <div class="section">
            <div class="section-header">
                <h2>專長</h2>
                <button type="button" class="toggle-btn" onclick="toggleSection(this)">收合</button>
            </div>
            <div class="section-content">
                <form method="post">
                    <?php echo csrfTokenField(); ?>
                    <input type="hidden" name="pro_ID" value="<?php echo htmlspecialchars($professor['pro_ID']); ?>">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="category">專長類別</label>
                            <select id="category" name="category">
                                <option value="research">研究領域</option>
                                <option value="teaching">教學領域</option>
                                <option value="technical">技術專長</option>
                                <option value="other">其他</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="item">專長項目 *</label>
                            <input type="text" id="item" name="item" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="item_en">英文項目</label>
                            <input type="text" id="item_en" name="item_en">
                        </div>
                        
                        <div class="form-group">
                            <label for="sort_order">排序</label>
                            <input type="number" id="sort_order" name="sort_order" value="0" min="0">
                        </div>
                    </div>
                    
                    <button type="submit" name="add_expertise">新增專長</button>
                </form>

                <?php if (!empty($expertise_data)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>類別</th>
                            <th>專長項目</th>
                            <th>英文項目</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($expertise_data as $row): ?>
                        <tr>
                            <td><span class="category-badge"><?php echo htmlspecialchars($row['category']); ?></span></td>
                            <td><?php echo nl2br(htmlspecialchars($row['item'])); ?></td>
                            <td><?php echo htmlspecialchars($row['item_en']); ?></td>
                            <td>
                                <form method="post" style="display: inline;" onsubmit="return confirm('確定要刪除這筆資料嗎？')">
                                    <?php echo csrfTokenField(); ?>
                                    <input type="hidden" name="table" value="expertise">
                                    <input type="hidden" name="id_field" value="expertise_ID">
                                    <input type="hidden" name="id_value" value="<?php echo htmlspecialchars($row['expertise_ID']); ?>">
                                    <button type="submit" name="delete_data" class="btn-danger btn-small">刪除</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- 期刊論文區塊 -->
        <div class="section">
            <div class="section-header">
                <h2>期刊論文</h2>
                <button type="button" class="toggle-btn" onclick="toggleSection(this)">收合</button>
            </div>
            <div class="section-content">
                <form method="post">
                    <?php echo csrfTokenField(); ?>
                    <input type="hidden" name="pro_ID" value="<?php echo htmlspecialchars($professor['pro_ID']); ?>">
                    
                    <div class="form-group">
                        <label for="jour_character">作者 *</label>
                        <textarea id="jour_character" name="jour_character" rows="2" required placeholder="請按照論文順序填寫所有作者姓名"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="title">論文標題 *</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">期刊名稱 *</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="volume">卷</label>
                            <input type="text" id="volume" name="volume">
                        </div>
                        
                        <div class="form-group">
                            <label for="issue">期</label>
                            <input type="text" id="issue" name="issue">
                        </div>
                        
                        <div class="form-group">
                            <label for="pages">頁數</label>
                            <input type="text" id="pages" name="pages" placeholder="如: pp. 1-10">
                        </div>
                        
                        <div class="form-group">
                            <label for="publication_year">發表年份 *</label>
                            <input type="number" id="publication_year" name="publication_year" min="1950" max="2030" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="publication_month">發表月份</label>
                            <select id="publication_month" name="publication_month">
                                <option value="">請選擇月份</option>
                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?>月</option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="doi">DOI</label>
                            <input type="text" id="doi" name="doi" placeholder="如: 10.1000/182">
                        </div>
                        
                        <div class="form-group">
                            <label for="impact_factor">影響因子</label>
                            <input type="number" id="impact_factor" name="impact_factor" step="0.001" min="0">
                        </div>
                        
                        <div class="form-group">
                            <label for="category">期刊類別</label>
                            <select id="category" name="category">
                                <option value="Other">其他</option>
                                <option value="SCI">SCI</option>
                                <option value="SSCI">SSCI</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_corresponding" name="is_corresponding" value="1">
                        <label for="is_corresponding">通訊作者</label>
                    </div>
                    
                    <button type="submit" name="add_journal">新增期刊論文</button>
                </form>

                <?php if (!empty($journal_data)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>作者</th>
                            <th>論文標題</th>
                            <th>期刊名稱</th>
                            <th>卷期</th>
                            <th>年份</th>
                            <th>類別</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($journal_data as $row): ?>
                        <tr>
                            <td>
                                <?php echo htmlspecialchars($row['jour_character']); ?>
                                <?php if ($row['is_corresponding']): ?>
                                    <br><span class="corresponding-badge">通訊作者</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td>
                                <?php echo htmlspecialchars($row['name']); ?>
                                <?php if ($row['impact_factor']): ?>
                                    <br><small>IF: <?php echo htmlspecialchars($row['impact_factor']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['volume'] || $row['issue']): ?>
                                    Vol. <?php echo htmlspecialchars($row['volume']); ?>, 
                                    Iss. <?php echo htmlspecialchars($row['issue']); ?>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['publication_year']); ?>-<?php echo htmlspecialchars($row['publication_month']); ?></td>
                            <td><span class="category-badge"><?php echo htmlspecialchars($row['category']); ?></span></td>
                            <td>
                                <form method="post" style="display: inline;" onsubmit="return confirm('確定要刪除這筆資料嗎？')">
                                    <?php echo csrfTokenField(); ?>
                                    <input type="hidden" name="table" value="journal">
                                    <input type="hidden" name="id_field" value="jour_ID">
                                    <input type="hidden" name="id_value" value="<?php echo htmlspecialchars($row['jour_ID']); ?>">
                                    <button type="submit" name="delete_data" class="btn-danger btn-small">刪除</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- 會議論文區塊 -->
        <div class="section">
            <div class="section-header">
                <h2>會議論文</h2>
                <button type="button" class="toggle-btn" onclick="toggleSection(this)">收合</button>
            </div>
            <div class="section-content">
                <form method="post">
                    <?php echo csrfTokenField(); ?>
                    <input type="hidden" name="pro_ID" value="<?php echo htmlspecialchars($professor['pro_ID']); ?>">
                    
                    <div class="form-group">
                        <label for="conf_character">作者 *</label>
                        <textarea id="conf_character" name="conf_character" rows="2" required placeholder="請按照論文順序填寫所有作者姓名"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="title">論文標題 *</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="conference_name">會議名稱 *</label>
                            <input type="text" id="conference_name" name="conference_name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="conference_abbr">會議簡稱</label>
                            <input type="text" id="conference_abbr" name="conference_abbr" placeholder="如: ICML">
                        </div>
                        
                        <div class="form-group">
                            <label for="pages">頁數</label>
                            <input type="text" id="pages" name="pages" placeholder="如: pp. 1-10">
                        </div>
                        
                        <div class="form-group">
                            <label for="publication_year">發表年份 *</label>
                            <input type="number" id="publication_year" name="publication_year" min="1950" max="2030" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="publication_month">發表月份</label>
                            <select id="publication_month" name="publication_month">
                                <option value="">請選擇月份</option>
                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?>月</option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="location">會議地點 *</label>
                            <input type="text" id="location" name="location" required placeholder="城市名稱">
                        </div>
                        
                        <div class="form-group">
                            <label for="country">國家</label>
                            <input type="text" id="country" name="country" placeholder="如: Taiwan, USA">
                        </div>
                        
                        <div class="form-group">
                            <label for="conference_type">會議類型</label>
                            <select id="conference_type" name="conference_type">
                                <option value="International">國際會議</option>
                                <option value="Domestic">國內會議</option>
                                <option value="Workshop">研討會</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_corresponding" name="is_corresponding" value="1">
                        <label for="is_corresponding">通訊作者</label>
                    </div>
                    
                    <button type="submit" name="add_conference">新增會議論文</button>
                </form>

                <?php if (!empty($conference_data)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>作者</th>
                            <th>論文標題</th>
                            <th>會議名稱</th>
                            <th>地點</th>
                            <th>年份</th>
                            <th>類型</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($conference_data as $row): ?>
                        <tr>
                            <td>
                                <?php echo htmlspecialchars($row['conf_character']); ?>
                                <?php if ($row['is_corresponding']): ?>
                                    <br><span class="corresponding-badge">通訊作者</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td>
                                <?php echo htmlspecialchars($row['conference_name']); ?>
                                <?php if ($row['conference_abbr']): ?>
                                    <br><small>(<?php echo htmlspecialchars($row['conference_abbr']); ?>)</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($row['location']); ?>
                                <?php if ($row['country']): ?>
                                    <br><small><?php echo htmlspecialchars($row['country']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['publication_year']); ?>-<?php echo htmlspecialchars($row['publication_month']); ?></td>
                            <td><span class="category-badge"><?php echo htmlspecialchars($row['conference_type']); ?></span></td>
                            <td>
                                <form method="post" style="display: inline;" onsubmit="return confirm('確定要刪除這筆資料嗎？')">
                                    <?php echo csrfTokenField(); ?>
                                    <input type="hidden" name="table" value="conference">
                                    <input type="hidden" name="id_field" value="conf_ID">
                                    <input type="hidden" name="id_value" value="<?php echo htmlspecialchars($row['conf_ID']); ?>">
                                    <button type="submit" name="delete_data" class="btn-danger btn-small">刪除</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- 經歷區塊 -->
        <div class="section">
            <div class="section-header">
                <h2>經歷</h2>
                <button type="button" class="toggle-btn" onclick="toggleSection(this)">收合</button>
            </div>
            <div class="section-content">
                <form method="post">
                    <?php echo csrfTokenField(); ?>
                    <input type="hidden" name="pro_ID" value="<?php echo htmlspecialchars($professor['pro_ID']); ?>">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="category">類別</label>
                            <select id="category" name="category" required>
                                <option value="">請選擇類別</option>
                                <option value="校內">校內</option>
                                <option value="校外">校外</option>
                                <option value="產業">產業</option>
                                <option value="政府">政府</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="organization">機構名稱 *</label>
                            <input type="text" id="organization" name="organization" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="department">部門</label>
                            <input type="text" id="department" name="department">
                        </div>
                        
                        <div class="form-group">
                            <label for="position">職位 *</label>
                            <input type="text" id="position" name="position" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="start_date">開始日期</label>
                            <input type="date" id="start_date" name="start_date">
                        </div>
                        
                        <div class="form-group">
                            <label for="end_date">結束日期</label>
                            <input type="date" id="end_date" name="end_date">
                        </div>
                        
                        <div class="form-group">
                            <label for="sort_order">排序</label>
                            <input type="number" id="sort_order" name="sort_order" value="0" min="0">
                        </div>
                    </div>
                    
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_current" name="is_current" value="1">
                        <label for="is_current">目前任職</label>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">工作描述</label>
                        <textarea id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <button type="submit" name="add_experience">新增經歷</button>
                </form>

                <?php if (!empty($experience_data)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>類別</th>
                            <th>機構</th>
                            <th>部門</th>
                            <th>職位</th>
                            <th>任職期間</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($experience_data as $row): ?>
                        <tr>
                            <td><span class="category-badge"><?php echo htmlspecialchars($row['category']); ?></span></td>
                            <td><?php echo htmlspecialchars($row['organization']); ?></td>
                            <td><?php echo htmlspecialchars($row['department']); ?></td>
                            <td>
                                <?php echo htmlspecialchars($row['position']); ?>
                                <?php if ($row['is_current']): ?>
                                    <br><span class="current-badge">現職</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($row['start_date']); ?>
                                <?php if ($row['end_date'] && !$row['is_current']): ?>
                                    ~ <?php echo htmlspecialchars($row['end_date']); ?>
                                <?php elseif ($row['is_current']): ?>
                                    ~ 至今
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="post" style="display: inline;" onsubmit="return confirm('確定要刪除這筆資料嗎？')">
                                    <?php echo csrfTokenField(); ?>
                                    <input type="hidden" name="table" value="experience">
                                    <input type="hidden" name="id_field" value="experience_ID">
                                    <input type="hidden" name="id_value" value="<?php echo htmlspecialchars($row['experience_ID']); ?>">
                                    <button type="submit" name="delete_data" class="btn-danger btn-small">刪除</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>

        <?php else: ?>
            <div class="error">找不到教授資料或您沒有權限訪問。</div>
        <?php endif; ?>
    </div>

    <script>
        function changeProfessor() {
            const select = document.getElementById('professor-select');
            const selectedValue = select.value;
            if (selectedValue) {
                window.location.href = '?id=' + selectedValue;
            }
        }

        function toggleSection(button) {
            const content = button.parentNode.nextElementSibling;
            if (content.classList.contains('collapsed')) {
                content.classList.remove('collapsed');
                button.textContent = '收合';
            } else {
                content.classList.add('collapsed');
                button.textContent = '展開';
            }
        }

        // 自動調整日期欄位
        document.addEventListener('DOMContentLoaded', function() {
            const isCurrentCheckbox = document.getElementById('is_current');
            const endDateField = document.getElementById('end_date');
            
            if (isCurrentCheckbox && endDateField) {
                isCurrentCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        endDateField.value = '';
                        endDateField.disabled = true;
                    } else {
                        endDateField.disabled = false;
                    }
                });
            }
        });

        // 表單驗證
        function validateForm(formType) {
            switch(formType) {
                case 'journal':
                    const year = document.querySelector('input[name="publication_year"]').value;
                    const currentYear = new Date().getFullYear();
                    if (year > currentYear + 1) {
                        alert('發表年份不能超過明年');
                        return false;
                    }
                    break;
                case 'conference':
                    const confYear = document.querySelector('input[name="publication_year"]').value;
                    const currentConfYear = new Date().getFullYear();
                    if (confYear > currentConfYear + 1) {
                        alert('發表年份不能超過明年');
                        return false;
                    }
                    break;
                case 'experience':
                    const startDate = document.getElementById('start_date').value;
                    const endDate = document.getElementById('end_date').value;
                    const isCurrent = document.getElementById('is_current').checked;
                    
                    if (startDate && endDate && !isCurrent && new Date(startDate) > new Date(endDate)) {
                        alert('開始日期不能晚於結束日期');
                        return false;
                    }
                    break;
            }
            return true;
        }

        // 為表單添加驗證事件
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (this.querySelector('input[name="add_journal"]')) {
                    if (!validateForm('journal')) {
                        e.preventDefault();
                    }
                } else if (this.querySelector('input[name="add_conference"]')) {
                    if (!validateForm('conference')) {
                        e.preventDefault();
                    }
                } else if (this.querySelector('input[name="add_experience"]')) {
                    if (!validateForm('experience')) {
                        e.preventDefault();
                    }
                }
            });
        });

        // 自動保存草稿功能（可選）
        function autoSave() {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                const inputs = form.querySelectorAll('input, textarea, select');
                inputs.forEach(input => {
                    input.addEventListener('change', function() {
                        const key = 'draft_' + this.name;
                        const value = this.value;
                        // 這裡可以實作本地存儲功能
                        // localStorage.setItem(key, value);
                    });
                });
            });
        }

        // 快速導航
        function scrollToSection(sectionName) {
            const section = document.querySelector('.section h2');
            if (section && section.textContent.includes(sectionName)) {
                section.scrollIntoView({ behavior: 'smooth' });
            }
        }
    </script>
</body>
</html>