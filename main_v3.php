<?php
// 引入資料庫連接文件
require_once 'config.php';

$pro_ID = isset($_GET['id']) ? $_GET['id'] : null;
if (!$pro_ID) {
    echo '<p style="text-align:center;color:#c00;">請從教授列表選擇要編輯的教授</p>';
    exit;
}

$stmt = $conn->prepare("SELECT * FROM professor WHERE pro_ID = ?");
$stmt->bind_param("s", $pro_ID);
$stmt->execute();
$result_professor = $stmt->get_result();
$professor = $result_professor->fetch_assoc();
$stmt->close();

if (!$professor) {
    echo '<p style="text-align:center;color:#c00;">找不到該教授資料</p>';
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // 根據提交的表單類型執行不同的操作
        if (isset($_POST['update_basic'])) {
            // 更新基本資料
            $pro_ID = $_POST['pro_ID'];
            $name = $_POST['name'];
            $position = $_POST['position'];
            $introduction = $_POST['introduction'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $office = $_POST['office'];

            $stmt = $conn->prepare("UPDATE professor SET name = ?, position = ?, introduction = ?, email = ?, phone = ?, office = ? WHERE pro_ID = ?");
            $stmt->bind_param("sssssss", $name, $position, $introduction, $email, $phone, $office, $pro_ID);

            if ($stmt->execute()) {
                $message = "基本資料更新成功！";
            } else {
                throw new Exception("更新基本資料失敗：" . $stmt->error);
            }
            $stmt->close();
        }

        // 處理照片上傳
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            $filename = basename($_FILES['photo']['name']);
            $uploadFile = $uploadDir . $filename;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)) {
                // 更新資料庫中的照片路徑
                $pro_ID = $_POST['pro_ID'];
                $stmt = $conn->prepare("UPDATE professor SET photo = ? WHERE pro_ID = ?");
                $stmt->bind_param("ss", $uploadFile, $pro_ID);
                if ($stmt->execute()) {
                    $message = "照片上傳成功並更新資料庫！";
                } else {
                    echo json_encode([
                        "status" => "error",
                        "message" => "更新資料庫時出錯：" . $stmt->error
                    ]);
                    exit;
                }
                $stmt->close();
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "圖片搬移失敗",
                    "debug_tmp_name" => $_FILES['photo']['tmp_name'],
                    "debug_target" => $uploadFile
                ]);
                exit;
            }
        } elseif (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
            echo json_encode([
                "status" => "error",
                "message" => "圖片上傳錯誤，代碼：" . $_FILES['photo']['error']
            ]);
            exit;
        }

        // 處理新增學歷
        if (isset($_POST['add_education'])) {
            $pro_ID = $_POST['pro_ID'];
            $department = $_POST['department'];
            $degree = $_POST['degree'];

            // 生成新的 edu_ID
            $stmt = $conn->prepare("SELECT MAX(CAST(SUBSTRING(edu_ID, 2) AS UNSIGNED)) as max_id FROM education WHERE edu_ID LIKE 'B%'");
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $next_id = ($row['max_id'] ?? 0) + 1;
            $edu_ID = 'B' . str_pad($next_id, 3, '0', STR_PAD_LEFT);

            $stmt = $conn->prepare("INSERT INTO education (edu_ID, pro_ID, department, degree) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $edu_ID, $pro_ID, $department, $degree);

            if ($stmt->execute()) {
                $message = "學歷新增成功！";
            } else {
                throw new Exception("新增學歷失敗：" . $stmt->error);
            }
            $stmt->close();
        }

        // 處理更新學歷
        if (isset($_POST['update_education'])) {
            $edu_ID = $_POST['edu_ID'];
            $department = $_POST['department'];
            $degree = $_POST['degree'];

            $stmt = $conn->prepare("UPDATE education SET department = ?, degree = ? WHERE edu_ID = ?");
            $stmt->bind_param("sss", $department, $degree, $edu_ID);

            if ($stmt->execute()) {
                $message = "學歷更新成功！";
            } else {
                throw new Exception("更新學歷失敗：" . $stmt->error);
            }
            $stmt->close();
        }

        // 處理新增專長
        if (isset($_POST['add_expertise'])) {
            $pro_ID = $_POST['pro_ID'];
            $item = $_POST['item'];

            // 生成新的 expertise_ID
            $stmt = $conn->prepare("SELECT MAX(CAST(SUBSTRING(expertise_ID, 2) AS UNSIGNED)) as max_id FROM expertise WHERE expertise_ID LIKE 'C%'");
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $next_id = ($row['max_id'] ?? 0) + 1;
            $expertise_ID = 'C' . str_pad($next_id, 3, '0', STR_PAD_LEFT);

            $stmt = $conn->prepare("INSERT INTO expertise (expertise_ID, pro_ID, item) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $expertise_ID, $pro_ID, $item);

            if ($stmt->execute()) {
                $message = "專長新增成功！";
            } else {
                throw new Exception("新增專長失敗：" . $stmt->error);
            }
            $stmt->close();
        }

        // 處理更新專長
        if (isset($_POST['update_expertise'])) {
            $expertise_ID = $_POST['expertise_ID'];
            $item = $_POST['item'];

            $stmt = $conn->prepare("UPDATE expertise SET item = ? WHERE expertise_ID = ?");
            $stmt->bind_param("ss", $item, $expertise_ID);

            if ($stmt->execute()) {
                $message = "專長更新成功！";
            } else {
                throw new Exception("更新專長失敗：" . $stmt->error);
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

            // 生成新的 jour_ID
            $stmt = $conn->prepare("SELECT MAX(CAST(SUBSTRING(jour_ID, 2) AS UNSIGNED)) as max_id FROM journal WHERE jour_ID LIKE 'F%'");
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $next_id = ($row['max_id'] ?? 0) + 1;
            $jour_ID = 'F' . str_pad($next_id, 3, '0', STR_PAD_LEFT);

            $stmt = $conn->prepare("INSERT INTO journal (jour_ID, pro_ID, jour_character, title, name, issue, date, pages) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $jour_ID, $pro_ID, $jour_character, $title, $name, $issue, $date, $pages);

            if ($stmt->execute()) {
                $message = "期刊論文新增成功！";
            } else {
                throw new Exception("新增期刊論文失敗：" . $stmt->error);
            }
            $stmt->close();
        }

        // 處理更新期刊論文
        if (isset($_POST['update_journal'])) {
            $jour_ID = $_POST['jour_ID'];
            $jour_character = $_POST['jour_character'];
            $title = $_POST['title'];
            $name = $_POST['name'];
            $issue = $_POST['issue'];
            $date = $_POST['date'];
            $pages = $_POST['pages'];

            $stmt = $conn->prepare("UPDATE journal SET jour_character = ?, title = ?, name = ?, issue = ?, date = ?, pages = ? WHERE jour_ID = ?");
            $stmt->bind_param("sssssss", $jour_character, $title, $name, $issue, $date, $pages, $jour_ID);

            if ($stmt->execute()) {
                $message = "期刊論文更新成功！";
            } else {
                throw new Exception("更新期刊論文失敗：" . $stmt->error);
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

            // 生成新的 conf_ID
            $stmt = $conn->prepare("SELECT MAX(CAST(SUBSTRING(conf_ID, 2) AS UNSIGNED)) as max_id FROM conference WHERE conf_ID LIKE 'G%'");
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $next_id = ($row['max_id'] ?? 0) + 1;
            $conf_ID = 'G' . str_pad($next_id, 3, '0', STR_PAD_LEFT);

            $stmt = $conn->prepare("INSERT INTO conference (conf_ID, pro_ID, conf_character, title, name, pages, date, location) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $conf_ID, $pro_ID, $conf_character, $title, $name, $pages, $date, $location);

            if ($stmt->execute()) {
                $message = "會議論文新增成功！";
            } else {
                throw new Exception("新增會議論文失敗：" . $stmt->error);
            }
            $stmt->close();
        }

        // 處理更新會議論文
        if (isset($_POST['update_conference'])) {
            $conf_ID = $_POST['conf_ID'];
            $conf_character = $_POST['conf_character'];
            $title = $_POST['title'];
            $name = $_POST['name'];
            $pages = $_POST['pages'];
            $date = $_POST['date'];
            $location = $_POST['location'];

            $stmt = $conn->prepare("UPDATE conference SET conf_character = ?, title = ?, name = ?, pages = ?, date = ?, location = ? WHERE conf_ID = ?");
            $stmt->bind_param("sssssss", $conf_character, $title, $name, $pages, $date, $location, $conf_ID);

            if ($stmt->execute()) {
                $message = "會議論文更新成功！";
            } else {
                throw new Exception("更新會議論文失敗：" . $stmt->error);
            }
            $stmt->close();
        }

        // 處理新增經歷
        if (isset($_POST['add_experience'])) {
            $pro_ID = $_POST['pro_ID'];
            $category = $_POST['category'];
            $department = $_POST['department'];
            $position = $_POST['position'];

            // 生成新的 experience_ID
            $stmt = $conn->prepare("SELECT MAX(CAST(SUBSTRING(experience_ID, 2) AS UNSIGNED)) as max_id FROM experience WHERE experience_ID LIKE 'D%'");
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $next_id = ($row['max_id'] ?? 0) + 1;
            $experience_ID = 'D' . str_pad($next_id, 3, '0', STR_PAD_LEFT);

            $stmt = $conn->prepare("INSERT INTO experience (experience_ID, pro_ID, category, department, position) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $experience_ID, $pro_ID, $category, $department, $position);

            if ($stmt->execute()) {
                $message = "經歷新增成功！";
            } else {
                throw new Exception("新增經歷失敗：" . $stmt->error);
            }
            $stmt->close();
        }

        // 處理更新經歷
        if (isset($_POST['update_experience'])) {
            $experience_ID = $_POST['experience_ID'];
            $category = $_POST['category'];
            $department = $_POST['department'];
            $position = $_POST['position'];

            $stmt = $conn->prepare("UPDATE experience SET category = ?, department = ?, position = ? WHERE experience_ID = ?");
            $stmt->bind_param("ssss", $category, $department, $position, $experience_ID);

            if ($stmt->execute()) {
                $message = "經歷更新成功！";
            } else {
                throw new Exception("更新經歷失敗：" . $stmt->error);
            }
            $stmt->close();
        }

        // 處理刪除資料
        if (isset($_POST['delete_data'])) {
            $table = $_POST['table'];
            $id_field = $_POST['id_field'];
            $id_value = $_POST['id_value'];

            // 驗證表名和欄位名，防止 SQL 注入
            $allowed_tables = ['education', 'expertise', 'journal', 'conference', 'experience', 'award', 'lecture', 'project', 'courses'];
            $allowed_fields = ['edu_ID', 'expertise_ID', 'jour_ID', 'conf_ID', 'experience_ID', 'award_ID', 'lecture_ID', 'project_ID', 'courses_ID'];

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

        // 處理課程相關操作
        if (isset($_POST['add_course'])) {
            $pro_ID = $_POST['pro_ID'];
            $name = $_POST['name'];
            $class = $_POST['class'];
            $time = $_POST['time'];

            // 生成新的 courses_ID
            $stmt = $conn->prepare("SELECT MAX(CAST(SUBSTRING(courses_ID, 2) AS UNSIGNED)) as max_id FROM courses WHERE courses_ID LIKE 'E%'");
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $next_id = ($row['max_id'] ?? 0) + 1;
            $courses_ID = 'E' . str_pad($next_id, 3, '0', STR_PAD_LEFT);

            $stmt = $conn->prepare("INSERT INTO courses (courses_ID, pro_ID, name, class, time) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $courses_ID, $pro_ID, $name, $class, $time);

            if ($stmt->execute()) {
                $message = "課程新增成功！";
            } else {
                throw new Exception("新增課程失敗：" . $stmt->error);
            }
            $stmt->close();
        }

        // 處理更新課程
        if (isset($_POST['update_course'])) {
            $courses_ID = $_POST['courses_ID'];
            $name = $_POST['name'];
            $class = $_POST['class'];
            $time = $_POST['time'];

            $stmt = $conn->prepare("UPDATE courses SET name = ?, class = ?, time = ? WHERE courses_ID = ?");
            $stmt->bind_param("ssss", $name, $class, $time, $courses_ID);

            if ($stmt->execute()) {
                $message = "課程更新成功！";
            } else {
                throw new Exception("更新課程失敗：" . $stmt->error);
            }
            $stmt->close();
        }

        // 處理刪除課程
        if (isset($_POST['delete_course'])) {
            $courses_ID = $_POST['courses_ID'];

            $stmt = $conn->prepare("DELETE FROM courses WHERE courses_ID = ?");
            $stmt->bind_param("s", $courses_ID);

            if ($stmt->execute()) {
                $message = "課程刪除成功！";
            } else {
                throw new Exception("刪除課程失敗：" . $stmt->error);
            }
            $stmt->close();
        }

        // 處理新增獎項
        if (isset($_POST['add_award'])) {
            $pro_ID = $_POST['pro_ID'];
            $type = $_POST['type'];
            $title = $_POST['title'];
            $organizer = $_POST['organizer'];
            $date = $_POST['date'];
            $topic = $_POST['topic'];
            $student_list = $_POST['student_list'];

            // 生成新的 award_ID
            $stmt = $conn->prepare("SELECT MAX(CAST(SUBSTRING(award_ID, 2) AS UNSIGNED)) as max_id FROM award WHERE award_ID LIKE 'H%'");
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $next_id = ($row['max_id'] ?? 0) + 1;
            $award_ID = 'I' . str_pad($next_id, 3, '0', STR_PAD_LEFT);

            $stmt = $conn->prepare("INSERT INTO award (award_ID, pro_ID, type, title, organizer, date, topic, student_list) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $award_ID, $pro_ID, $type, $title, $organizer, $date, $topic, $student_list);

            if ($stmt->execute()) {
                $message = "獎項新增成功！";
            } else {
                throw new Exception("新增獎項失敗：" . $stmt->error);
            }
            $stmt->close();
        }

        // 處理更新獎項
        if (isset($_POST['update_award'])) {
            $award_ID = $_POST['award_ID'];
            $type = $_POST['type'];
            $title = $_POST['title'];
            $organizer = $_POST['organizer'];
            $date = $_POST['date'];
            $topic = $_POST['topic'];
            $student_list = $_POST['student_list'];

            $stmt = $conn->prepare("UPDATE award SET type = ?, title = ?, organizer = ?, date = ?, topic = ?, student_list = ? WHERE award_ID = ?");
            $stmt->bind_param("sssssss", $type, $title, $organizer, $date, $topic, $student_list, $award_ID);

            if ($stmt->execute()) {
                $message = "獎項更新成功！";
            } else {
                throw new Exception("更新獎項失敗：" . $stmt->error);
            }
            $stmt->close();
        }

        // 處理新增演講
        if (isset($_POST['add_lecture'])) {
            $pro_ID = $_POST['pro_ID'];
            $title = $_POST['title'];
            $location = $_POST['location'];
            $date = $_POST['date'];

            // 生成新的 lecture_ID
            $stmt = $conn->prepare("SELECT MAX(CAST(SUBSTRING(lecture_ID, 2) AS UNSIGNED)) as max_id FROM lecture WHERE lecture_ID LIKE 'J%'");
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $next_id = ($row['max_id'] ?? 0) + 1;
            $lecture_ID = 'J' . str_pad($next_id, 3, '0', STR_PAD_LEFT);

            $stmt = $conn->prepare("INSERT INTO lecture (lecture_ID, pro_ID, title, location, date) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $lecture_ID, $pro_ID, $title, $location, $date);

            if ($stmt->execute()) {
                $message = "演講新增成功！";
            } else {
                throw new Exception("新增演講失敗：" . $stmt->error);
            }
            $stmt->close();
        }

        // 處理更新演講
        if (isset($_POST['update_lecture'])) {
            $lecture_ID = $_POST['lecture_ID'];
            $title = $_POST['title'];
            $location = $_POST['location'];
            $date = $_POST['date'];

            $stmt = $conn->prepare("UPDATE lecture SET title = ?, location = ?, date = ? WHERE lecture_ID = ?");
            $stmt->bind_param("ssss", $title, $location, $date, $lecture_ID);

            if ($stmt->execute()) {
                $message = "演講更新成功！";
            } else {
                throw new Exception("更新演講失敗：" . $stmt->error);
            }
            $stmt->close();
        }

        // 處理新增專案
        if (isset($_POST['add_project'])) {
            $pro_ID = $_POST['pro_ID'];
            $title = $_POST['title'];
            $description = $_POST['description'];
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];

            // 生成新的 project_ID
            $stmt = $conn->prepare("SELECT MAX(CAST(SUBSTRING(project_ID, 2) AS UNSIGNED)) as max_id FROM project WHERE project_ID LIKE 'K%'");
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $next_id = ($row['max_id'] ?? 0) + 1;
            $project_ID = 'K' . str_pad($next_id, 3, '0', STR_PAD_LEFT);

            $stmt = $conn->prepare("INSERT INTO project (project_ID, pro_ID, title, description, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $project_ID, $pro_ID, $title, $description, $start_date, $end_date);

            if ($stmt->execute()) {
                $message = "專案新增成功！";
            } else {
                throw new Exception("新增專案失敗：" . $stmt->error);
            }
            $stmt->close();
        }

        // 處理更新專案
        if (isset($_POST['update_project'])) {
            $project_ID = $_POST['project_ID'];
            $category = $_POST['category'];
            $name = $_POST['name'];
            $date = $_POST['date'];
            $number = $_POST['number'];
            $role = $_POST['role'];

            $stmt = $conn->prepare("UPDATE project SET category = ?, name = ?, date = ?, number = ?, role = ? WHERE project_ID = ?");
            $stmt->bind_param("ssssss", $category, $name, $date, $number, $role, $project_ID);

            if ($stmt->execute()) {
                $message = "專案更新成功！";
            } else {
                throw new Exception("更新專案失敗：" . $stmt->error);
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

// 查詢學歷 (包含搜尋功能)
$search_edu_id = isset($_GET['search_edu_id']) ? $_GET['search_edu_id'] : '';
$where_clause_edu = $search_edu_id ? "AND edu_ID LIKE ?" : "";
$sql_education = "SELECT * FROM education WHERE pro_ID = ? $where_clause_edu ORDER BY edu_ID";
$stmt = $conn->prepare($sql_education);
if ($search_edu_id) {
    $search_pattern_edu = "%$search_edu_id%";
    $stmt->bind_param("ss", $pro_ID, $search_pattern_edu);
} else {
    $stmt->bind_param("s", $pro_ID);
}
$stmt->execute();
$result_education = $stmt->get_result();
$stmt->close();

// 查詢專長 (包含搜尋功能)
$search_expertise_id = isset($_GET['search_expertise_id']) ? $_GET['search_expertise_id'] : '';
$where_clause_expertise = $search_expertise_id ? "AND expertise_ID LIKE ?" : "";
$sql_expertise = "SELECT * FROM expertise WHERE pro_ID = ? $where_clause_expertise ORDER BY expertise_ID";
$stmt = $conn->prepare($sql_expertise);
if ($search_expertise_id) {
    $search_pattern_expertise = "%$search_expertise_id%";
    $stmt->bind_param("ss", $pro_ID, $search_pattern_expertise);
} else {
    $stmt->bind_param("s", $pro_ID);
}
$stmt->execute();
$result_expertise = $stmt->get_result();
$stmt->close();

// 查詢期刊論文 (包含搜尋功能)
$search_jour_id = isset($_GET['search_jour_id']) ? $_GET['search_jour_id'] : '';
$where_clause_jour = $search_jour_id ? "AND jour_ID LIKE ?" : "";
$sql_journal = "SELECT * FROM journal WHERE pro_ID = ? $where_clause_jour ORDER BY date DESC";
$stmt = $conn->prepare($sql_journal);
if ($search_jour_id) {
    $search_pattern_jour = "%$search_jour_id%";
    $stmt->bind_param("ss", $pro_ID, $search_pattern_jour);
} else {
    $stmt->bind_param("s", $pro_ID);
}
$stmt->execute();
$result_journal = $stmt->get_result();
$stmt->close();

// 查詢會議論文 (包含搜尋功能)
$search_conf_id = isset($_GET['search_conf_id']) ? $_GET['search_conf_id'] : '';
$where_clause_conf = $search_conf_id ? "AND conf_ID LIKE ?" : "";
$sql_conference = "SELECT * FROM conference WHERE pro_ID = ? $where_clause_conf ORDER BY date DESC";
$stmt = $conn->prepare($sql_conference);
if ($search_conf_id) {
    $search_pattern_conf = "%$search_conf_id%";
    $stmt->bind_param("ss", $pro_ID, $search_pattern_conf);
} else {
    $stmt->bind_param("s", $pro_ID);
}
$stmt->execute();
$result_conference = $stmt->get_result();
$stmt->close();

// 查詢經歷 (包含搜尋功能)
$search_experience_id = isset($_GET['search_experience_id']) ? $_GET['search_experience_id'] : '';
$where_clause_experience = $search_experience_id ? "AND experience_ID LIKE ?" : "";
$sql_experience = "SELECT * FROM experience WHERE pro_ID = ? $where_clause_experience ORDER BY experience_ID";
$stmt = $conn->prepare($sql_experience);
if ($search_experience_id) {
    $search_pattern_experience = "%$search_experience_id%";
    $stmt->bind_param("ss", $pro_ID, $search_pattern_experience);
} else {
    $stmt->bind_param("s", $pro_ID);
}
$stmt->execute();
$result_experience = $stmt->get_result();
$stmt->close();

// 處理搜尋
$search_id = isset($_GET['search_id']) ? $_GET['search_id'] : '';
$where_clause = $search_id ? "WHERE courses_ID LIKE ?" : "";

// 查詢課程資料
$sql = "SELECT * FROM courses $where_clause ORDER BY courses_ID";
$stmt = $conn->prepare($sql);
if ($search_id) {
    $search_pattern = "%$search_id%";
    $stmt->bind_param("s", $search_pattern);
}
$stmt->execute();
$result_courses = $stmt->get_result();
$stmt->close();

// 查詢獎項 (包含搜尋功能)
$search_award_id = isset($_GET['search_award_id']) ? $_GET['search_award_id'] : '';
$where_clause_award = $search_award_id ? "AND award_ID LIKE ?" : "";
$sql_award = "SELECT * FROM award WHERE pro_ID = ? $where_clause_award ORDER BY award_ID";
$stmt = $conn->prepare($sql_award);
if ($search_award_id) {
    $search_pattern_award = "%$search_award_id%";
    $stmt->bind_param("ss", $pro_ID, $search_pattern_award);
} else {
    $stmt->bind_param("s", $pro_ID);
}
$stmt->execute();
$result_award = $stmt->get_result();
$stmt->close();

// 查詢演講 (包含搜尋功能)
$search_lecture_id = isset($_GET['search_lecture_id']) ? $_GET['search_lecture_id'] : '';
$where_clause_lecture = $search_lecture_id ? "AND lecture_ID LIKE ?" : "";
$sql_lecture = "SELECT * FROM lecture WHERE pro_ID = ? $where_clause_lecture ORDER BY lecture_ID";
$stmt = $conn->prepare($sql_lecture);
if ($search_lecture_id) {
    $search_pattern_lecture = "%$search_lecture_id%";
    $stmt->bind_param("ss", $pro_ID, $search_pattern_lecture);
} else {
    $stmt->bind_param("s", $pro_ID);
}
$stmt->execute();
$result_lecture = $stmt->get_result();
$stmt->close();

// 查詢專案 (包含搜尋功能)
$search_project_id = isset($_GET['search_project_id']) ? $_GET['search_project_id'] : '';
$where_clause_project = $search_project_id ? "AND project_ID LIKE ?" : "";
$sql_project = "SELECT * FROM project WHERE pro_ID = ? $where_clause_project ORDER BY project_ID";
$stmt = $conn->prepare($sql_project);
if ($search_project_id) {
    $search_pattern_project = "%$search_project_id%";
    $stmt->bind_param("ss", $pro_ID, $search_pattern_project);
} else {
    $stmt->bind_param("s", $pro_ID);
}
$stmt->execute();
$result_project = $stmt->get_result();
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
            padding: 20px 0 20px 220px; /* 預留左側導覽列空間 */
            background-color: #f5f5f5;
        }
        .sidebar-nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 200px;
            height: 100vh;
            background: #222;
            color: #fff;
            padding-top: 40px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .sidebar-nav h2 {
            color: #fff;
            font-size: 1.2em;
            margin: 0 0 20px 20px;
        }
        .sidebar-nav ul {
            list-style: none;
            padding: 0 0 0 20px;
            margin: 0;
            width: 100%;
        }
        .sidebar-nav li {
            margin-bottom: 18px;
        }
        .sidebar-nav a {
            color: #fff;
            text-decoration: none;
            font-size: 1em;
            transition: color 0.2s;
        }
        .sidebar-nav a:hover {
            color: #4CAF50;
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
        .search-form {
            margin-bottom: 20px;
        }
        .search-form input[type="text"] {
            padding: 8px;
            margin-right: 10px;
        }
        .clear-search {
            margin-left: 10px;
            color: #666;
            text-decoration: none;
        }
        .add-form {
            margin-bottom: 20px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .form-group {
            margin-bottom: 10px;
        }
        .form-group label {
            display: inline-block;
            width: 100px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .data-table th,
        .data-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .data-table th {
            background-color: #f5f5f5;
        }
        .edit-btn {
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            margin-right: 5px;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 5px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: black;
        }
        .course-item:hover {
            background-color: #e9ecef;
        }
        .schedule-link {
            display: inline-block;
            margin-bottom: 10px;
            color: #007bff;
            text-decoration: none;
        }
        .schedule-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- 漢堡選單按鈕 -->
    <div id="hamburger" style="position:fixed;top:18px;left:18px;z-index:2000;cursor:pointer;">
        <div style="width:32px;height:4px;background:#333;margin:6px 0;border-radius:2px;"></div>
        <div style="width:32px;height:4px;background:#333;margin:6px 0;border-radius:2px;"></div>
        <div style="width:32px;height:4px;background:#333;margin:6px 0;border-radius:2px;"></div>
    </div>
    <!-- 側邊導覽選單 -->
    <div id="sideNav" style="display:none;position:fixed;top:0;left:0;width:220px;height:100vh;background:#fff;box-shadow:2px 0 8px rgba(0,0,0,0.08);z-index:2100;padding:32px 0 0 0;">
        <ul style="list-style:none;padding:0 24px;">
            <li style="margin-bottom:18px;"><a href="#basic-info">基本資料</a></li>
            <li style="margin-bottom:18px;"><a href="#education">學歷</a></li>
            <li style="margin-bottom:18px;"><a href="#expertise">專長</a></li>
            <li style="margin-bottom:18px;"><a href="#journal">期刊論文</a></li>
            <li style="margin-bottom:18px;"><a href="#conference">會議論文</a></li>
            <li style="margin-bottom:18px;"><a href="#experience">經歷</a></li>
            <li style="margin-bottom:18px;"><a href="#courses">課程管理</a></li>
            <li style="margin-bottom:18px;"><a href="#award">獎項</a></li>
            <li style="margin-bottom:18px;"><a href="#lecture">演講</a></li>
            <li style="margin-bottom:18px;"><a href="#project">專案</a></li>
        </ul>
    </div>
    <!-- 遮罩 -->
    <div id="sideNavMask" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.15);z-index:2099;"></div>
    <!-- 返回 dashboard 按鈕 -->
    <a href="dashboard.php" style="position:fixed;top:18px;right:18px;z-index:2000;padding:8px 18px;background:#667eea;color:#fff;border:none;border-radius:6px;text-decoration:none;">返回</a>
    <div class="container">
        <?php if (isset($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <h1>教授資料管理系統</h1>
        
        <!-- 基本資料區塊 -->
        <div class="section" id="basic-info">
            <h2>基本資料 (含聯絡資訊)</h2>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="pro_ID" value="<?php echo $professor['pro_ID']; ?>">
                
                <div>
                    <label>姓名：</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($professor['name'] ?? '-'); ?>" required>
                </div>
                
                <div>
                    <label>職位：</label>
                    <input type="text" name="position" value="<?php echo htmlspecialchars($professor['position'] ?? '-'); ?>" required>
                </div>
                
                <div>
                    <label>自介：</label>
                    <textarea name="introduction" rows="4"><?php echo htmlspecialchars($professor['introduction'] ?? '-'); ?></textarea>
                </div>

                <div>
                    <label>信箱：</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($professor['email'] ?? '-'); ?>">
                </div>
                
                <div>
                    <label>辦公室電話：</label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($professor['phone'] ?? '-'); ?>">
                </div>

                <div>
                    <label>辦公室位置：</label>
                    <input type="text" name="office" value="<?php echo htmlspecialchars($professor['office'] ?? '-'); ?>">
                </div>
                
                <div>
                    <label>照片：</label>
                    <input type="file" name="photo" accept="image/*">
                    <?php if ($professor['photo']): ?>
                        <img src="<?php echo htmlspecialchars($professor['photo']); ?>" alt="教授照片" style="max-width: 200px;">
                    <?php else: ?>
                        <span>無照片</span>
                    <?php endif; ?>
                </div>
                
                <button type="submit" name="update_basic">更新基本資料</button>
            </form>
        </div>

        <!-- 學歷區塊 -->
        <div class="section" id="education">
            <h2>學歷</h2>
            <!-- 搜尋功能 -->
            <form method="get" class="search-form">
                <input type="hidden" name="id" value="<?php echo $pro_ID; ?>">
                <input type="text" name="search_edu_id" placeholder="搜尋學歷ID" value="<?php echo htmlspecialchars($search_edu_id ?? ''); ?>">
                <button type="submit">搜尋</button>
                <?php if (!empty($search_edu_id)): ?>
                    <a href="?id=<?php echo $pro_ID; ?>" class="clear-search">清除搜尋</a>
                <?php endif; ?>
            </form>
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
            <details>
                <summary>展開學歷列表</summary>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>學歷ID</th>
                        <th>系所</th>
                        <th>學位</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $result_education->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['edu_ID'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['department'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['degree'] ?? '-'); ?></td>
                    <td>
                        <button onclick="editEducation('<?php echo htmlspecialchars($row['edu_ID'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['department'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['degree'] ?? '-'); ?>')" class="edit-btn">編輯</button>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="table" value="education">
                            <input type="hidden" name="id_field" value="edu_ID">
                            <input type="hidden" name="id_value" value="<?php echo $row['edu_ID']; ?>">
                            <button type="submit" name="delete_data" class="delete-btn" onclick="return confirm('確定要刪除這筆資料嗎？')">刪除</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            </details>
        </div>

        <!-- 編輯學歷的彈出視窗 -->
        <div id="editEducationModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('editEducationModal')">&times;</span>
                <h2>編輯學歷</h2>
                <form method="post">
                    <input type="hidden" name="edu_ID" id="edit_edu_ID">
                    <div class="form-group">
                        <label>系所：</label>
                        <input type="text" name="department" id="edit_edu_department" required>
                    </div>
                    <div class="form-group">
                        <label>學位：</label>
                        <input type="text" name="degree" id="edit_edu_degree" required>
                    </div>
                    <button type="submit" name="update_education">更新學歷</button>
                </form>
            </div>
        </div>

        <!-- 專長區塊 -->
        <div class="section" id="expertise">
            <h2>專長</h2>
            <!-- 搜尋功能 -->
            <form method="get" class="search-form">
                <input type="hidden" name="id" value="<?php echo $pro_ID; ?>">
                <input type="text" name="search_expertise_id" placeholder="搜尋專長ID" value="<?php echo htmlspecialchars($search_expertise_id ?? ''); ?>">
                <button type="submit">搜尋</button>
                <?php if (!empty($search_expertise_id)): ?>
                    <a href="?id=<?php echo $pro_ID; ?>" class="clear-search">清除搜尋</a>
                <?php endif; ?>
            </form>
            <form method="post">
                <input type="hidden" name="pro_ID" value="<?php echo $professor['pro_ID']; ?>">
                <div>
                    <label>專長項目：</label>
                    <input type="text" name="item" required>
                </div>
                <button type="submit" name="add_expertise">新增專長</button>
            </form>
            <details>
                <summary>展開專長列表</summary>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>專長ID</th>
                        <th>專長項目</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $result_expertise->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['expertise_ID'] ?? '-'); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($row['item'] ?? '-')); ?></td>
                    <td>
                        <button onclick="editExpertise('<?php echo htmlspecialchars($row['expertise_ID'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['item'] ?? '-'); ?>')" class="edit-btn">編輯</button>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="table" value="expertise">
                            <input type="hidden" name="id_field" value="expertise_ID">
                            <input type="hidden" name="id_value" value="<?php echo $row['expertise_ID']; ?>">
                            <button type="submit" name="delete_data" class="delete-btn" onclick="return confirm('確定要刪除這筆資料嗎？')">刪除</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            </details>
        </div>

        <!-- 編輯專長的彈出視窗 -->
        <div id="editExpertiseModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('editExpertiseModal')">&times;</span>
                <h2>編輯專長</h2>
                <form method="post">
                    <input type="hidden" name="expertise_ID" id="edit_expertise_ID">
                    <div class="form-group">
                        <label>專長項目：</label>
                        <input type="text" name="item" id="edit_expertise_item" required>
                    </div>
                    <button type="submit" name="update_expertise">更新專長</button>
                </form>
            </div>
        </div>

        <!-- 期刊論文區塊 -->
        <div class="section" id="journal">
            <h2>期刊論文</h2>
            <!-- 搜尋功能 -->
            <form method="get" class="search-form">
                <input type="hidden" name="id" value="<?php echo $pro_ID; ?>">
                <input type="text" name="search_jour_id" placeholder="搜尋期刊ID" value="<?php echo htmlspecialchars($search_jour_id ?? ''); ?>">
                <button type="submit">搜尋</button>
                <?php if (!empty($search_jour_id)): ?>
                    <a href="?id=<?php echo $pro_ID; ?>" class="clear-search">清除搜尋</a>
                <?php endif; ?>
            </form>
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
            <details>
                <summary>展開期刊論文列表</summary>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>期刊ID</th>
                        <th>作者</th>
                        <th>論文標題</th>
                        <th>期刊名稱</th>
                        <th>卷期</th>
                        <th>日期</th>
                        <th>頁數</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $result_journal->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['jour_ID'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['jour_character'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['title'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['name'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['issue'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['date'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['pages'] ?? '-'); ?></td>
                    <td>
                        <button onclick="editJournal('<?php echo htmlspecialchars($row['jour_ID'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['jour_character'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['title'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['name'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['issue'] ?? '-'); ?>', '<?php htmlspecialchars($row['date'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['pages'] ?? '-'); ?>')" class="edit-btn">編輯</button>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="table" value="journal">
                            <input type="hidden" name="id_field" value="jour_ID">
                            <input type="hidden" name="id_value" value="<?php echo $row['jour_ID']; ?>">
                            <button type="submit" name="delete_data" class="delete-btn" onclick="return confirm('確定要刪除這筆資料嗎？')">刪除</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            </details>
        </div>

        <!-- 編輯期刊論文的彈出視窗 -->
        <div id="editJournalModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('editJournalModal')">&times;</span>
                <h2>編輯期刊論文</h2>
                <form method="post">
                    <input type="hidden" name="jour_ID" id="edit_jour_ID">
                    <div class="form-group">
                        <label>作者：</label>
                        <input type="text" name="jour_character" id="edit_jour_character" required>
                    </div>
                    <div class="form-group">
                        <label>論文標題：</label>
                        <input type="text" name="title" id="edit_jour_title" required>
                    </div>
                    <div class="form-group">
                        <label>期刊名稱：</label>
                        <input type="text" name="name" id="edit_jour_name" required>
                    </div>
                    <div class="form-group">
                        <label>卷期：</label>
                        <input type="text" name="issue" id="edit_jour_issue">
                    </div>
                    <div class="form-group">
                        <label>日期：</label>
                        <input type="text" name="date" id="edit_jour_date" required>
                    </div>
                    <div class="form-group">
                        <label>頁數：</label>
                        <input type="text" name="pages" id="edit_jour_pages">
                    </div>
                    <button type="submit" name="update_journal">更新期刊論文</button>
                </form>
            </div>
        </div>

        <!-- 會議論文區塊 -->
        <div class="section" id="conference">
            <h2>會議論文</h2>
            <!-- 搜尋功能 -->
            <form method="get" class="search-form">
                <input type="hidden" name="id" value="<?php echo $pro_ID; ?>">
                <input type="text" name="search_conf_id" placeholder="搜尋會議ID" value="<?php echo htmlspecialchars($search_conf_id ?? ''); ?>">
                <button type="submit">搜尋</button>
                <?php if (!empty($search_conf_id)): ?>
                    <a href="?id=<?php echo $pro_ID; ?>" class="clear-search">清除搜尋</a>
                <?php endif; ?>
            </form>
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
            <details>
                <summary>展開會議論文列表</summary>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>會議ID</th>
                        <th>作者</th>
                        <th>論文標題</th>
                        <th>會議名稱</th>
                        <th>頁數</th>
                        <th>日期</th>
                        <th>地點</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $result_conference->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['conf_ID'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['conf_character'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['title'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['name'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['pages'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['date'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['location'] ?? '-'); ?></td>
                    <td>
                        <button onclick="editConference('<?php echo htmlspecialchars($row['conf_ID'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['conf_character'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['title'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['name'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['pages'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['date'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['location'] ?? '-'); ?>')" class="edit-btn">編輯</button>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="table" value="conference">
                            <input type="hidden" name="id_field" value="conf_ID">
                            <input type="hidden" name="id_value" value="<?php echo $row['conf_ID']; ?>">
                            <button type="submit" name="delete_data" class="delete-btn" onclick="return confirm('確定要刪除這筆資料嗎？')">刪除</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            </details>
        </div>

        <!-- 編輯會議論文的彈出視窗 -->
        <div id="editConferenceModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('editConferenceModal')">&times;</span>
                <h2>編輯會議論文</h2>
                <form method="post">
                    <input type="hidden" name="conf_ID" id="edit_conf_ID">
                    <div class="form-group">
                        <label>作者：</label>
                        <input type="text" name="conf_character" id="edit_conf_character" required>
                    </div>
                    <div class="form-group">
                        <label>論文標題：</label>
                        <input type="text" name="title" id="edit_conf_title" required>
                    </div>
                    <div class="form-group">
                        <label>會議名稱：</label>
                        <input type="text" name="name" id="edit_conf_name" required>
                    </div>
                    <div class="form-group">
                        <label>頁數：</label>
                        <input type="text" name="pages" id="edit_conf_pages">
                    </div>
                    <div class="form-group">
                        <label>日期：</label>
                        <input type="text" name="date" id="edit_conf_date" required>
                    </div>
                    <div class="form-group">
                        <label>地點：</label>
                        <input type="text" name="location" id="edit_conf_location" required>
                    </div>
                    <button type="submit" name="update_conference">更新會議論文</button>
                </form>
            </div>
        </div>

        <!-- 經歷區塊 -->
        <div class="section" id="experience">
            <h2>經歷</h2>
            <!-- 搜尋功能 -->
            <form method="get" class="search-form">
                <input type="hidden" name="id" value="<?php echo $pro_ID; ?>">
                <input type="text" name="search_experience_id" placeholder="搜尋經歷ID" value="<?php echo htmlspecialchars($search_experience_id ?? ''); ?>">
                <button type="submit">搜尋</button>
                <?php if (!empty($search_experience_id)): ?>
                    <a href="?id=<?php echo $pro_ID; ?>" class="clear-search">清除搜尋</a>
                <?php endif; ?>
            </form>
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
            <details>
                <summary>展開經歷列表</summary>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>經歷ID</th>
                        <th>類別</th>
                        <th>單位</th>
                        <th>職位</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $result_experience->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['experience_ID'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['category'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['department'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['position'] ?? '-'); ?></td>
                    <td>
                        <button onclick="editExperience('<?php echo htmlspecialchars($row['experience_ID'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['category'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['department'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['position'] ?? '-'); ?>')" class="edit-btn">編輯</button>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="table" value="experience">
                            <input type="hidden" name="id_field" value="experience_ID">
                            <input type="hidden" name="id_value" value="<?php echo $row['experience_ID']; ?>">
                            <button type="submit" name="delete_data" class="delete-btn" onclick="return confirm('確定要刪除這筆資料嗎？')">刪除</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            </details>
        </div>

        <!-- 編輯經歷的彈出視窗 -->
        <div id="editExperienceModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('editExperienceModal')">&times;</span>
                <h2>編輯經歷</h2>
                <form method="post">
                    <input type="hidden" name="experience_ID" id="edit_experience_ID">
                    <div class="form-group">
                        <label>類別：</label>
                        <select name="category" id="edit_experience_category" required>
                            <option value="校內">校內</option>
                            <option value="校外">校外</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>單位：</label>
                        <input type="text" name="department" id="edit_experience_department" required>
                    </div>
                    <div class="form-group">
                        <label>職位：</label>
                        <input type="text" name="position" id="edit_experience_position" required>
                    </div>
                    <button type="submit" name="update_experience">更新經歷</button>
                </form>
            </div>
        </div>

        <!-- 課程管理區塊 -->
        <div class="section" id="courses">
            <h2>課程管理</h2>
            <!-- 搜尋功能 -->
            <form method="get" class="search-form">
                <input type="hidden" name="id" value="<?php echo $pro_ID; ?>">
                <input type="text" name="search_id" placeholder="搜尋課程ID" value="<?php echo htmlspecialchars($search_id); ?>">
                <button type="submit">搜尋</button>
                <?php if ($search_id): ?>
                    <a href="?id=<?php echo $pro_ID; ?>" class="clear-search">清除搜尋</a>
                <?php endif; ?>
            </form>
            <!-- 新增課程表單 -->
            <form method="post" class="add-form">
                <input type="hidden" name="pro_ID" value="<?php echo $pro_ID; ?>">
                <div class="form-group">
                    <label>課程名稱：</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>開課班級：</label>
                    <input type="text" name="class" required>
                </div>
                <div class="form-group">
                    <label>上課時間：</label>
                    <select name="day" required>
                        <option value="">選擇星期</option>
                        <option value="1">星期一</option>
                        <option value="2">星期二</option>
                        <option value="3">星期三</option>
                        <option value="4">星期四</option>
                        <option value="5">星期五</option>
                        <option value="6">星期六</option>
                        <option value="7">星期日</option>
                    </select>
                    <select name="period" required>
                        <option value="">選擇節次</option>
                        <?php
                        $periods = [
                            '1' => '08:10 - 09:00',
                            '2' => '09:10 - 10:00',
                            '3' => '10:10 - 11:00',
                            '4' => '11:10 - 12:00',
                            '5' => '13:10 - 14:00',
                            '6' => '14:10 - 15:00',
                            '7' => '15:10 - 16:00',
                            '8' => '16:10 - 17:00',
                            '9' => '17:10 - 18:00',
                            '10' => '18:10 - 19:00',
                            '11' => '19:10 - 20:00',
                            '12' => '20:10 - 21:00',
                            '13' => '21:10 - 22:00'
                        ];
                        foreach ($periods as $num => $time) {
                            echo "<option value='$num'>$time</option>";
                        }
                        ?>
                    </select>
                    <input type="hidden" name="time" id="course_time">
                </div>
                <button type="submit" name="add_course">新增課程</button>
            </form>
            <details>
                <summary>展開課程列表</summary>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>課程ID</th>
                        <th>課程名稱</th>
                        <th>開課班級</th>
                        <th>上課時間</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result_courses->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['courses_ID'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($row['name'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($row['class'] ?? '-'); ?></td>
                        <td>
                            <?php
                            $times = explode(',', $row['time'] ?? '');
                            if (empty($times) || ($times[0] === '-' && count($times) === 1)) {
                                echo '-';
                            } else {
                                foreach ($times as $time) {
                                    if (strlen($time) >= 2) {
                                        $day = intval($time[0]); // 星期幾
                                        $period = intval(substr($time, 1)); // 第幾節
                                        $day_names = ['', '一', '二', '三', '四', '五', '六', '日'];
                                        echo "星期{$day_names[$day]} 第{$period}節<br>";
                                    } else if ($time === '-') {
                                        echo '-';
                                    }
                                }
                            }
                            ?>
                        </td>
                        <td>
                            <button onclick="editCourse('<?php echo htmlspecialchars($row['courses_ID'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['name'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['class'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['time'] ?? '-'); ?>')" class="edit-btn">編輯</button>
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="courses_ID" value="<?php echo $row['courses_ID']; ?>">
                                <button type="submit" name="delete_course" class="delete-btn" onclick="return confirm('確定要刪除這門課程嗎？')">刪除</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            </details>
        </div>

        <!-- 編輯課程的彈出視窗 -->
        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>編輯課程</h2>
                <form method="post">
                    <input type="hidden" name="courses_ID" id="edit_courses_ID">
                    <div class="form-group">
                        <label>課程名稱：</label>
                        <input type="text" name="name" id="edit_name" required>
                    </div>
                    <div class="form-group">
                        <label>開課班級：</label>
                        <input type="text" name="class" id="edit_class" required>
                    </div>
                    <div class="form-group">
                        <label>上課時間：</label>
                        <input type="text" name="time" id="edit_time" required>
                        <small>格式：星期(1-7)節次(1-13)，例如：211 表示星期二第11節</small>
                    </div>
                    <button type="submit" name="update_course">更新課程</button>
                </form>
            </div>
        </div>

        <!-- 獎項區塊 -->
        <div class="section" id="award">
            <h2>獎項</h2>
            <!-- 搜尋功能 -->
            <form method="get" class="search-form">
                <input type="hidden" name="id" value="<?php echo $pro_ID; ?>">
                <input type="text" name="search_award_id" placeholder="搜尋獎項ID" value="<?php echo htmlspecialchars($search_award_id ?? ''); ?>">
                <button type="submit">搜尋</button>
                <?php if (!empty($search_award_id)): ?>
                    <a href="?id=<?php echo $pro_ID; ?>" class="clear-search">清除搜尋</a>
                <?php endif; ?>
            </form>
            <form method="post">
                <input type="hidden" name="pro_ID" value="<?php echo $professor['pro_ID']; ?>">
                <div>
                    <label>獎項類別：</label>
                    <input type="text" name="type">
                </div>
                <div>
                    <label>獎項名稱：</label>
                    <input type="text" name="title" required>
                </div>
                <div>
                    <label>主辦單位：</label>
                    <input type="text" name="organizer">
                </div>
                <div>
                    <label>日期：</label>
                    <input type="text" name="date">
                </div>
                <div>
                    <label>參賽主題：</label>
                    <input type="text" name="topic">
                </div>
                <div>
                    <label>指導學生名單：</label>
                    <input type="text" name="student_list">
                </div>
                <button type="submit" name="add_award">新增獎項</button>
            </form>
            <details>
                <summary>展開獎項列表</summary>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>獎項ID</th>
                        <th>獎項類別</th>
                        <th>獎項名稱</th>
                        <th>主辦單位</th>
                        <th>日期</th>
                        <th>參賽主題</th>
                        <th>指導學生名單</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $result_award->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['award_ID'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['type'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['title'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['organizer'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['date'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['topic'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['student_list'] ?? '-'); ?></td>
                    <td>
                        <button onclick="editAward('<?php echo htmlspecialchars($row['award_ID'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['type'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['title'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['organizer'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['date'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['topic'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['student_list'] ?? '-'); ?>')" class="edit-btn">編輯</button>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="table" value="award">
                            <input type="hidden" name="id_field" value="award_ID">
                            <input type="hidden" name="id_value" value="<?php echo $row['award_ID']; ?>">
                            <button type="submit" name="delete_data" class="delete-btn" onclick="return confirm('確定要刪除這筆資料嗎？')">刪除</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            </details>
        </div>

        <!-- 編輯獎項的彈出視窗 -->
        <div id="editAwardModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('editAwardModal')">&times;</span>
                <h2>編輯獎項</h2>
                <form method="post">
                    <input type="hidden" name="award_ID" id="edit_award_ID">
                    <div class="form-group">
                        <label>獎項類別：</label>
                        <input type="text" name="type" id="edit_award_type">
                    </div>
                    <div class="form-group">
                        <label>獎項名稱：</label>
                        <input type="text" name="title" id="edit_award_title" required>
                    </div>
                    <div class="form-group">
                        <label>主辦單位：</label>
                        <input type="text" name="organizer" id="edit_award_organizer">
                    </div>
                    <div class="form-group">
                        <label>日期：</label>
                        <input type="text" name="date" id="edit_award_date">
                    </div>
                    <div class="form-group">
                        <label>參賽主題：</label>
                        <input type="text" name="topic" id="edit_award_topic">
                    </div>
                    <div class="form-group">
                        <label>指導學生名單：</label>
                        <input type="text" name="student_list" id="edit_award_student_list">
                    </div>
                    <button type="submit" name="update_award">更新獎項</button>
                </form>
            </div>
        </div>

        <!-- 演講區塊 -->
        <div class="section" id="lecture">
            <h2>演講</h2>
            <!-- 搜尋功能 -->
            <form method="get" class="search-form">
                <input type="hidden" name="id" value="<?php echo $pro_ID; ?>">
                <input type="text" name="search_lecture_id" placeholder="搜尋演講ID" value="<?php echo htmlspecialchars($search_lecture_id ?? ''); ?>">
                <button type="submit">搜尋</button>
                <?php if (!empty($search_lecture_id)): ?>
                    <a href="?id=<?php echo $pro_ID; ?>" class="clear-search">清除搜尋</a>
                <?php endif; ?>
            </form>
            <form method="post">
                <input type="hidden" name="pro_ID" value="<?php echo $professor['pro_ID']; ?>">
                <div>
                    <label>演講題目：</label>
                    <input type="text" name="title" required>
                </div>
                <div>
                    <label>地點：</label>
                    <input type="text" name="location" required>
                </div>
                <div>
                    <label>日期：</label>
                    <input type="text" name="date" required>
                </div>
                <button type="submit" name="add_lecture">新增演講</button>
            </form>
            <details>
                <summary>展開演講列表</summary>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>演講ID</th>
                        <th>演講題目</th>
                        <th>地點</th>
                        <th>日期</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $result_lecture->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['lecture_ID'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['title'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['location'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['date'] ?? '-'); ?></td>
                    <td>
                        <button onclick="editLecture('<?php echo htmlspecialchars($row['lecture_ID'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['title'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['location'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['date'] ?? '-'); ?>')" class="edit-btn">編輯</button>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="table" value="lecture">
                            <input type="hidden" name="id_field" value="lecture_ID">
                            <input type="hidden" name="id_value" value="<?php echo $row['lecture_ID']; ?>">
                            <button type="submit" name="delete_data" class="delete-btn" onclick="return confirm('確定要刪除這筆資料嗎？')">刪除</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            </details>
        </div>

        <!-- 編輯演講的彈出視窗 -->
        <div id="editLectureModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('editLectureModal')">&times;</span>
                <h2>編輯演講</h2>
                <form method="post">
                    <input type="hidden" name="lecture_ID" id="edit_lecture_ID">
                    <div class="form-group">
                        <label>演講題目：</label>
                        <input type="text" name="title" id="edit_lecture_title" required>
                    </div>
                    <div class="form-group">
                        <label>地點：</label>
                        <input type="text" name="location" id="edit_lecture_location" required>
                    </div>
                    <div class="form-group">
                        <label>日期：</label>
                        <input type="text" name="date" id="edit_lecture_date" required>
                    </div>
                    <button type="submit" name="update_lecture">更新演講</button>
                </form>
            </div>
        </div>

        <!-- 專案區塊 -->
        <div class="section" id="project">
            <h2>專案</h2>
            <!-- 搜尋功能 -->
            <form method="get" class="search-form">
                <input type="hidden" name="id" value="<?php echo $pro_ID; ?>">
                <input type="text" name="search_project_id" placeholder="搜尋專案ID" value="<?php echo htmlspecialchars($search_project_id ?? ''); ?>">
                <button type="submit">搜尋</button>
                <?php if (!empty($search_project_id)): ?>
                    <a href="?id=<?php echo $pro_ID; ?>" class="clear-search">清除搜尋</a>
                <?php endif; ?>
            </form>
            <form method="post">
                <input type="hidden" name="pro_ID" value="<?php echo $professor['pro_ID']; ?>">
                <div>
                    <label>計畫類別：</label>
                    <input type="text" name="category">
                </div>
                <div>
                    <label>計畫名稱：</label>
                    <input type="text" name="name" required>
                </div>
                <div>
                    <label>日期：</label>
                    <input type="text" name="date">
                </div>
                <div>
                    <label>計畫編號：</label>
                    <input type="text" name="number">
                </div>
                <div>
                    <label>計畫角色：</label>
                    <input type="text" name="role">
                </div>
                <button type="submit" name="add_project">新增專案</button>
            </form>
            <details>
                <summary>展開專案列表</summary>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>專案ID</th>
                        <th>計畫類別</th>
                        <th>計畫名稱</th>
                        <th>日期</th>
                        <th>計畫編號</th>
                        <th>計畫角色</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $result_project->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['project_ID'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['category'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['name'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['date'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['number'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['role'] ?? '-'); ?></td>
                    <td>
                        <button onclick="editProject('<?php echo htmlspecialchars($row['project_ID'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['category'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['name'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['date'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['number'] ?? '-'); ?>', '<?php echo htmlspecialchars($row['role'] ?? '-'); ?>')" class="edit-btn">編輯</button>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="table" value="project">
                            <input type="hidden" name="id_field" value="project_ID">
                            <input type="hidden" name="id_value" value="<?php echo $row['project_ID']; ?>">
                            <button type="submit" name="delete_data" class="delete-btn" onclick="return confirm('確定要刪除這筆資料嗎？')">刪除</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            </details>
        </div>

        <!-- 編輯專案的彈出視窗 -->
        <div id="editProjectModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('editProjectModal')">&times;</span>
                <h2>編輯專案</h2>
                <form method="post">
                    <input type="hidden" name="project_ID" id="edit_project_ID">
                    <div class="form-group">
                        <label>計畫類別：</label>
                        <input type="text" name="category" id="edit_project_category">
                    </div>
                    <div class="form-group">
                        <label>計畫名稱：</label>
                        <input type="text" name="name" id="edit_project_name" required>
                    </div>
                    <div class="form-group">
                        <label>日期：</label>
                        <input type="text" name="date" id="edit_project_date">
                    </div>
                    <div class="form-group">
                        <label>計畫編號：</label>
                        <input type="text" name="number" id="edit_project_number">
                    </div>
                    <div class="form-group">
                        <label>計畫角色：</label>
                        <input type="text" name="role" id="edit_project_role">
                    </div>
                    <button type="submit" name="update_project">更新專案</button>
                </form>
            </div>
        </div>

        <script>
            // 處理新增課程的時間組合
            document.querySelector('.add-form').addEventListener('submit', function(e) {
                const day = document.querySelector('select[name="day"]').value;
                const period = document.querySelector('select[name="period"]').value;
                document.getElementById('course_time').value = day + period;
            });

            // 處理編輯課程的彈出視窗
            function editCourse(id, name, class_name, time) {
                document.getElementById('editModal').style.display = 'block';
                document.getElementById('edit_courses_ID').value = id;
                document.getElementById('edit_name').value = (name === '-') ? '' : name;
                document.getElementById('edit_class').value = (class_name === '-') ? '' : class_name;
                document.getElementById('edit_time').value = (time === '-') ? '' : time;
            }

            // 關閉彈出視窗 (通用函數)
            function closeModal(modalId) {
                document.getElementById(modalId).style.display = 'none';
            }

            // 處理編輯學歷的彈出視窗
            function editEducation(id, department, degree) {
                document.getElementById('editEducationModal').style.display = 'block';
                document.getElementById('edit_edu_ID').value = id;
                document.getElementById('edit_edu_department').value = (department === '-') ? '' : department;
                document.getElementById('edit_edu_degree').value = (degree === '-') ? '' : degree;
            }

            // 處理編輯專長的彈出視窗
            function editExpertise(id, item) {
                document.getElementById('editExpertiseModal').style.display = 'block';
                document.getElementById('edit_expertise_ID').value = id;
                document.getElementById('edit_expertise_item').value = (item === '-') ? '' : item;
            }

            // 處理編輯期刊論文的彈出視窗
            function editJournal(id, character, title, name, issue, date, pages) {
                document.getElementById('editJournalModal').style.display = 'block';
                document.getElementById('edit_jour_ID').value = id;
                document.getElementById('edit_jour_character').value = (character === '-') ? '' : character;
                document.getElementById('edit_jour_title').value = (title === '-') ? '' : title;
                document.getElementById('edit_jour_name').value = (name === '-') ? '' : name;
                document.getElementById('edit_jour_issue').value = (issue === '-') ? '' : issue;
                document.getElementById('edit_jour_date').value = (date === '-') ? '' : date;
                document.getElementById('edit_jour_pages').value = (pages === '-') ? '' : pages;
            }

            // 處理編輯會議論文的彈出視窗
            function editConference(id, character, title, name, pages, date, location) {
                document.getElementById('editConferenceModal').style.display = 'block';
                document.getElementById('edit_conf_ID').value = id;
                document.getElementById('edit_conf_character').value = (character === '-') ? '' : character;
                document.getElementById('edit_conf_title').value = (title === '-') ? '' : title;
                document.getElementById('edit_conf_name').value = (name === '-') ? '' : name;
                document.getElementById('edit_conf_pages').value = (pages === '-') ? '' : pages;
                document.getElementById('edit_conf_date').value = (date === '-') ? '' : date;
                document.getElementById('edit_conf_location').value = (location === '-') ? '' : location;
            }

            // 處理編輯經歷的彈出視窗
            function editExperience(id, category, department, position) {
                document.getElementById('editExperienceModal').style.display = 'block';
                document.getElementById('edit_experience_ID').value = id;
                document.getElementById('edit_experience_category').value = (category === '-') ? '' : category;
                document.getElementById('edit_experience_department').value = (department === '-') ? '' : department;
                document.getElementById('edit_experience_position').value = (position === '-') ? '' : position;
            }

            // 處理編輯獎項的彈出視窗
            function editAward(id, type, title, organizer, date, topic, student_list) {
                document.getElementById('editAwardModal').style.display = 'block';
                document.getElementById('edit_award_ID').value = id;
                document.getElementById('edit_award_type').value = (type === '-') ? '' : type;
                document.getElementById('edit_award_title').value = (title === '-') ? '' : title;
                document.getElementById('edit_award_organizer').value = (organizer === '-') ? '' : organizer;
                document.getElementById('edit_award_date').value = (date === '-') ? '' : date;
                document.getElementById('edit_award_topic').value = (topic === '-') ? '' : topic;
                document.getElementById('edit_award_student_list').value = (student_list === '-') ? '' : student_list;
            }

            // 漢堡選單開關
            document.getElementById('hamburger').onclick = function() {
                document.getElementById('sideNav').style.display = 'block';
                document.getElementById('sideNavMask').style.display = 'block';
            };
            document.getElementById('sideNavMask').onclick = function() {
                document.getElementById('sideNav').style.display = 'none';
                document.getElementById('sideNavMask').style.display = 'none';
            };
            // 點擊側邊選單連結自動關閉側邊欄並平滑跳轉
            Array.from(document.querySelectorAll('#sideNav a')).forEach(function(link){
                link.onclick = function(e) {
                    var href = this.getAttribute('href');
                    if (href && href.startsWith('#')) {
                        e.preventDefault();
                        document.getElementById('sideNav').style.display = 'none';
                        document.getElementById('sideNavMask').style.display = 'none';
                        var target = document.querySelector(href);
                        if (target) {
                            target.scrollIntoView({behavior: 'smooth'});
                        }
                    } else {
                        document.getElementById('sideNav').style.display = 'none';
                        document.getElementById('sideNavMask').style.display = 'none';
                    }
                };
            });
        </script>
    </div>
</body>
</html>