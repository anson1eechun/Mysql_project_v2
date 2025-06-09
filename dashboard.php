<?php
require_once 'config.php';

if (!isset($_SESSION["username"])) {
    header("Location: login_v2.php");
    exit;
}

// 新增教授功能
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_professor'])) {
    $pro_ID = trim($_POST['pro_ID']);
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
    }
    $stmt = $conn->prepare("INSERT INTO professor (pro_ID, name, position, introduction, email, phone, office, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $pro_ID, $name, $position, $introduction, $email, $phone, $office, $photo);
    $stmt->execute();
    $stmt->close();
}

// 搜尋功能
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$showAll = isset($_GET['showall']);
if ($showAll) {
    $result = $conn->query("SELECT pro_ID, name, photo FROM professor");
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
@media (max-width: 600px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
        gap: 8px;
    }
    .dashboard-card img {
        width: 48px;
        height: 48px;
    }
}
</style>

<h2 style="text-align:center; margin-top:32px;">系所成員</h2>
<div style="max-width:1000px;margin:0 auto 24px auto;display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;">
    <form method="get" style="flex:1;min-width:220px;">
        <input type="text" name="search" placeholder="搜尋教授姓名或ID" value="<?php echo isset($_GET['search']) ? htmlspecialchars($search, ENT_QUOTES, 'UTF-8') : ''; ?>" style="padding:8px 12px;width:70%;max-width:260px;">
        <button type="submit" style="padding:8px 18px;">搜尋</button>
        <a href="dashboard.php?showall=1" style="margin-left:8px;">顯示全部</a>
    </form>
    <button onclick="document.getElementById('addProfessorModal').style.display='block'" style="padding:8px 18px;background:#667eea;color:#fff;border:none;border-radius:6px;cursor:pointer;">+ 新增教授</button>
</div>

<div class="dashboard-grid">
<?php
if ($result && $result instanceof mysqli_result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $photo = !empty($row["photo"]) ? htmlspecialchars($row["photo"]) : "uploads/teacher_photo.jpg";
        $name = htmlspecialchars($row["name"]);
        $pro_ID = urlencode($row["pro_ID"]);
        echo "<div class='dashboard-card' onclick=\"window.location.href='main_v3.php?id=$pro_ID'\">";
        echo "<img src='$photo' alt='$name'>";
        echo "<div class='prof-name'>$name</div>";
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