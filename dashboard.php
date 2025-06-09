<?php
require_once 'config.php';

if (!isset($_SESSION["username"])) {
    header("Location: login_v2.php");
    exit;
}

$result = $conn->query("SELECT pro_ID, name, photo FROM professor");

// CSS 直接寫在這裡，或可移到 styles.css
?>
<style>
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 40px;
    max-width: 1000px;
    margin: 40px auto;
}
.dashboard-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    padding: 24px 12px 12px 12px;
    transition: box-shadow 0.2s;
    cursor: pointer;
    text-align: center;
}
.dashboard-card:hover {
    box-shadow: 0 6px 16px rgba(0,0,0,0.15);
}
.dashboard-card img {
    width: 180px;
    height: 180px;
    object-fit: cover;
    border-radius: 12px;
    margin-bottom: 16px;
    background: #f0f0f0;
}
.dashboard-card .prof-name {
    font-size: 1.4rem;
    font-weight: 500;
    margin-top: 0;
    margin-bottom: 0;
    color: #222;
}
@media (max-width: 600px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
        gap: 24px;
    }
    .dashboard-card img {
        width: 120px;
        height: 120px;
    }
}
</style>

<h2 style="text-align:center; margin-top:32px;">系上教授列表</h2>
<div class="dashboard-grid">
<?php
while ($row = $result->fetch_assoc()) {
    $photo = !empty($row["photo"]) ? htmlspecialchars($row["photo"]) : "uploads/teacher_photo.jpg";
    $name = htmlspecialchars($row["name"]);
    $pro_ID = urlencode($row["pro_ID"]);
    echo "<div class='dashboard-card' onclick=\"window.location.href='main.php?id=$pro_ID'\">";
    echo "<img src='$photo' alt='$name'>";
    echo "<div class='prof-name'>$name</div>";
    echo "</div>";
}
?>
</div>