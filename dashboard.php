<?php
require_once 'config.php';

if (!isset($_SESSION["username"])) {
    header("Location: login_v2.php");
    exit;
}

// 新增教授功能
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_professor'])) {
    $pro_ID = trim($_POST['pro_ID']);
    $role = isset($_POST['role']) ? trim($_POST['role']) : '';
    $name = trim($_POST['name']);
    $position = trim($_POST['position']);
    $introduction = trim($_POST['introduction']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $office = trim($_POST['office']);
    $photo = '';
    // 處理圖片上傳
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo = 'uploads/' . uniqid('prof_', true) . '.' . $ext;
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
    } else {
        $photo = 'uploads/none.jpg'; // 預設頭像
    }
    $stmt = $conn->prepare("INSERT INTO professor (pro_ID, role, name, position, introduction, email, phone, office, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $pro_ID, $role, $name, $position, $introduction, $email, $phone, $office, $photo);
    $stmt->execute();
    $stmt->close();
}

// 處理刪除教授及其所有資料
if (isset($_POST['delete_professor']) && isset($_POST['pro_ID'])) {
    $pro_ID = $_POST['pro_ID'];
    try {
        // 依序刪除所有相關資料
        $tables = [
            'education', 'expertise', 'journal', 'conference', 'experience', 'award', 'lecture', 'project', 'courses'
        ];
        foreach ($tables as $table) {
            $stmt = $conn->prepare("DELETE FROM $table WHERE pro_ID = ?");
            $stmt->bind_param("s", $pro_ID);
            $stmt->execute();
            $stmt->close();
        }
        // 最後刪除教授本身
        $stmt = $conn->prepare("DELETE FROM professor WHERE pro_ID = ?");
        $stmt->bind_param("s", $pro_ID);
        $stmt->execute();
        $stmt->close();
        $message = "教授及其所有相關資料已刪除。";
    } catch (Exception $e) {
        $error = "刪除失敗：" . $e->getMessage();
    }
}

// 搜尋功能
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$showAll = isset($_GET['showall']);
$role = isset($_GET['role']) ? trim($_GET['role']) : '';
if ($showAll) {
    $result = $conn->query("SELECT pro_ID, name, photo FROM professor");
} elseif ($role !== '') {
    $stmt = $conn->prepare("SELECT pro_ID, name, photo FROM professor WHERE role = ?");
    $stmt->bind_param("s", $role);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} elseif ($search !== '') {
    $stmt = $conn->prepare("SELECT pro_ID, name, photo FROM professor WHERE name LIKE CONCAT('%', ?, '%') OR pro_ID LIKE CONCAT('%', ?, '%')");
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    $result = false; // 預設不顯示任何教授
}
?>
<style>
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); /* 更小格子 */
    gap: 18px; /* 更小間距 */
    max-width: 600px; /* 更小最大寬度 */
    margin: 20px auto;
}
.dashboard-card {
    padding: 8px 4px 6px 4px;
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    align-items: center;
    background: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: box-shadow 0.2s;
    cursor: pointer;
    text-align: center;
}
.dashboard-card:hover {
    box-shadow: 0 6px 16px rgba(0,0,0,0.15);
}
.dashboard-card img {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 10px;
    margin-bottom: 8px;
    background: #f0f0f0;
}
.dashboard-card .prof-name {
    font-size: 0.95rem;
    font-weight: 500;
    margin-top: 0;
    margin-bottom: 0;
    color: #222;
}
.delete-btn {
    background: #e74c3c;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 8px 12px;
    cursor: pointer;
    font-size: 0.9rem;
    margin-top: 8px;
    transition: background 0.3s;
}
.delete-btn:hover {
    background: #c0392b;
}
.sidebar-nav {
    position: fixed;
    top: 0;
    left: 0;
    width: 180px;
    height: 100vh;
    background: #fff;
    box-shadow: 2px 0 8px rgba(0,0,0,0.08);
    z-index: 100;
    padding: 32px 0 0 0;
}
.sidebar-nav ul {
    list-style: none;
    padding: 0 24px;
}
.sidebar-nav a {
    display: block;
    padding: 8px 0;
    color: #333;
    text-decoration: none;
}
.sidebar-nav a:hover {
    color: #667eea;
}
.main-content {
    margin-left: 200px;
    padding: 0 24px;
}
.sidebar-link.active {
    color: #667eea;
    font-weight: bold;
    background: #f0f4ff;
    border-radius: 6px;
}
@media (max-width: 600px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
        gap: 8px;
    }
    .dashboard-card img {
        width: 48px;
        height: 48px;
    }
    .sidebar-nav {
        position: static !important;
        width: 100% !important;
        height: auto !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin-bottom: 16px;
    }
    .sidebar-nav ul {
        display: flex;
        flex-wrap: wrap;
        padding: 0 8px !important;
    }
    .sidebar-nav li {
        flex: 1 1 40%;
        min-width: 100px;
        text-align: center;
    }
    .sidebar-nav a {
        padding: 6px 0 !important;
    }
    .main-content {
        margin-left: 0 !important;
    }
}
</style>

<div class="sidebar-nav">
    <ul>
        <li><a href="dashboard.php?role=系主任" class="sidebar-link<?php if(isset($_GET['role']) && $_GET['role']==='系主任')echo ' active'; ?>">系主任</a></li>
        <li><a href="dashboard.php?role=榮譽特聘講座" class="sidebar-link<?php if(isset($_GET['role']) && $_GET['role']==='榮譽特聘講座')echo ' active'; ?>">榮譽特聘講座</a></li>
        <li><a href="dashboard.php?role=講座教授" class="sidebar-link<?php if(isset($_GET['role']) && $_GET['role']==='講座教授')echo ' active'; ?>">講座教授</a></li>
        <li><a href="dashboard.php?role=特約講座" class="sidebar-link<?php if(isset($_GET['role']) && $_GET['role']==='特約講座')echo ' active'; ?>">特約講座</a></li>
        <li><a href="dashboard.php?role=特聘教授" class="sidebar-link<?php if(isset($_GET['role']) && $_GET['role']==='特聘教授')echo ' active'; ?>">特聘教授</a></li>
        <li><a href="dashboard.php?role=專任教師" class="sidebar-link<?php if(isset($_GET['role']) && $_GET['role']==='專任教師')echo ' active'; ?>">專任教師</a></li>
        <li><a href="dashboard.php?role=兼任教師" class="sidebar-link<?php if(isset($_GET['role']) && $_GET['role']==='兼任教師')echo ' active'; ?>">兼任教師</a></li>
        <li><a href="dashboard.php?role=行政人員" class="sidebar-link<?php if(isset($_GET['role']) && $_GET['role']==='行政人員')echo ' active'; ?>">行政人員</a></li>
        <li><a href="dashboard.php?role=退休教師" class="sidebar-link<?php if(isset($_GET['role']) && $_GET['role']==='退休教師')echo ' active'; ?>">退休教師</a></li>
    </ul>
</div>

<div class="main-content" style="margin-left:200px;">
<h2 style="text-align:center; margin-top:32px;">系所成員</h2>
<div style="max-width:1000px;margin:0 auto 24px auto;display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;">
    <form method="get" style="flex:1;min-width:220px;">
        <input type="text" name="search" placeholder="搜尋教授姓名或ID (A開頭)" value="<?php echo isset($_GET['search']) ? htmlspecialchars($search, ENT_QUOTES, 'UTF-8') : ''; ?>" style="padding:8px 12px;width:70%;max-width:260px;">
        <button type="submit" style="padding:8px 18px;">搜尋</button>
        <a href="dashboard.php?showall=1" style="margin-left:8px;">顯示全部</a>
    </form>
    <div style="display:flex;gap:10px;">
        <button onclick="document.getElementById('addProfessorModal').style.display='block'" style="padding:8px 18px;background:#667eea;color:#fff;border:none;border-radius:6px;cursor:pointer;">+ 新增教授</button>
        <button id="deleteModeBtn" style="padding:8px 18px;background:#e74c3c;color:#fff;border:none;border-radius:6px;cursor:pointer;">刪除教授</button>
    </div>
</div>

<div class="dashboard-grid">
<?php
if ($result && $result instanceof mysqli_result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $photo = !empty($row["photo"]) ? htmlspecialchars($row["photo"]) : "uploads/teacher_photo.jpg";
        $name = htmlspecialchars($row["name"]);
        $pro_ID = urlencode($row["pro_ID"]);
        echo "<div class='dashboard-card' onclick=\"if(!window.deleteMode){window.location.href='main_v3.php?id=$pro_ID'}\">";
        echo "<img src='$photo' alt='$name'>";
        echo "<div class='prof-name'>$name</div>";
        echo "<form method='post' class='delete-prof-form' style='display:none;' onsubmit=\"return confirm('確定要刪除此教授及其所有資料嗎？')\">";
        echo "<input type='hidden' name='pro_ID' value='" . htmlspecialchars($row['pro_ID']) . "'>";
        echo "<button type='submit' name='delete_professor' class='delete-btn'>刪除</button>";
        echo "</form>";
        echo "</div>";
    }
} elseif ($result && $result instanceof mysqli_result && $result->num_rows === 0) {
    echo '<div style="grid-column: 1 / -1; text-align:center; color:#888; font-size:1.1rem;">查無教授資料</div>';
}
?>
</div>

<!-- 新增教授 Modal -->
<div id="addProfessorModal" style="display:none;position:fixed;z-index:1000;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.3);">
  <div style="background:#fff;max-width:400px;margin:60px auto;padding:32px 24px;border-radius:12px;position:relative;">
    <span onclick="document.getElementById('addProfessorModal').style.display='none'" style="position:absolute;right:18px;top:12px;font-size:1.5rem;cursor:pointer;">&times;</span>
    <h3 style="margin-bottom:18px;">新增教授</h3>
    <form method="post" enctype="multipart/form-data">
      <div style="margin-bottom:10px;">
        <label>教授ID：</label><input type="text" name="pro_ID" required style="width:80%;padding:6px;">
      </div>
      <div style="margin-bottom:10px;">
        <label>身分：</label>
        <select name="role" required style="width:82%;padding:6px;">
          <option value="系主任">系主任</option>
          <option value="榮譽特聘講座">榮譽特聘講座</option>
          <option value="講座教授">講座教授</option>
          <option value="特約講座">特約講座</option>
          <option value="特聘教授">特聘教授</option>
          <option value="專任教師">專任教師</option>
          <option value="兼任教師">兼任教師</option>
          <option value="行政人員">行政人員</option>
          <option value="退休教師">退休教師</option>
        </select>
      </div>
      <div style="margin-bottom:10px;">
        <label>姓名：</label><input type="text" name="name" required style="width:80%;padding:6px;">
      </div>
      <div style="margin-bottom:10px;">
        <label>職位：</label><input type="text" name="position" required style="width:80%;padding:6px;">
      </div>
      <div style="margin-bottom:10px;">
        <label>自介：</label><textarea name="introduction" style="width:80%;padding:6px;" rows="2"></textarea>
      </div>
      <div style="margin-bottom:10px;">
        <label>Email：</label><input type="email" name="email" style="width:80%;padding:6px;">
      </div>
      <div style="margin-bottom:10px;">
        <label>辦公室電話：</label><input type="text" name="phone" style="width:80%;padding:6px;">
      </div>
      <div style="margin-bottom:10px;">
        <label>辦公室位置：</label><input type="text" name="office" style="width:80%;padding:6px;">
      </div>
      <div style="margin-bottom:18px;">
        <label>照片：</label><input type="file" name="photo" accept="image/*">
      </div>
      <button type="submit" name="add_professor" style="padding:8px 18px;background:#667eea;color:#fff;border:none;border-radius:6px;">新增</button>
    </form>
  </div>
</div>

<script>
let deleteMode = false;
document.getElementById('deleteModeBtn').onclick = function() {
    deleteMode = !deleteMode;
    window.deleteMode = deleteMode;
    document.querySelectorAll('.delete-prof-form').forEach(f => f.style.display = deleteMode ? 'block' : 'none');
    document.querySelectorAll('.dashboard-card').forEach(card => {
        card.style.cursor = deleteMode ? 'default' : 'pointer';
    });
    this.textContent = deleteMode ? '取消刪除' : '刪除教授';
};
</script>
</div>