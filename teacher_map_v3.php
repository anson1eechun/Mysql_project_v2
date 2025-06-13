<?php
// teacher_map_v3.php – 教授列表與連結頁面
require_once 'config.php';
// 設定連線與字元編碼
$conn->set_charset('utf8mb4');

// 分類對應
$categories = [
    '系主任' => '系主任',
    '榮譽特聘講座' => '榮譽特聘講座',
    '講座教授' => '講座教授',
    '特約講座' => '特約講座',
    '特聘教授' => '特聘教授',
    '專任教師' => '專任教師',
    '兼任教師' => '兼任教師',
    '行政人員' => '行政人員',
    '退休教師' => '退休教師',
];

// 取得目前分類
$selected = isset($_GET['cat']) ? $_GET['cat'] : '';

// 取得所有教授資料
if ($selected !== '') {
    // 參考 dashboard.php，改用 role 欄位過濾
    $where = "WHERE role = '" . $conn->real_escape_string($selected) . "'";
} else {
    $where = '';
}
$sql = "SELECT pro_ID, name, position, photo, phone, email FROM professor $where ORDER BY name";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>系所成員</title>
    <link rel="stylesheet" href="styles.css" />
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans TC', sans-serif; background: #f9f9f9; margin: 0; }
        .header-top { background: #fff; border-bottom: 1px solid #e5e5e5; }
        .header-inner {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            max-width: 1200px;
            margin: 0 auto;
            padding: 4px 8px;
        }
        .logo {
            margin-right: auto;
        }
        .logo img {
            height: 120px;
            width: auto;
            margin-right: 0;
            display: block;
        }
        .main-nav {
            position: relative;
        }
        .main-nav ul {
            display: flex;
            gap: 4px;
            list-style: none;
            margin: 0;
            padding: 0 4px;
            align-items: center;
            /* background: #f5f7fa; */
            background: none;
            border-radius: 8px;
            padding: 2px 6px;
        }
        .main-nav a {
            color: #003366;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.92em;
            white-space: nowrap;
            padding: 2px 8px;
            border-radius: 5px;
            background: transparent;
            transition: background .2s, color .2s;
        }
        .main-nav a.active, .main-nav a:hover {
            color: #005bac;
            background: none;
            border-bottom: 2px solid #005bac;
        }
        .btn-english {
            border: 1px solid #003366;
            background: #fff;
            color: #003366;
            border-radius: 12px;
            padding: 2px 10px;
            font-weight: 500;
            margin-left: 6px;
            font-size: 0.92em;
            text-decoration: none;
            transition: background .2s;
            height: 22px;
            display: flex;
            align-items: center;
        }
        .btn-english:hover {
            background: #003366;
            color: #fff;
        }
        .main-content {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            padding: 32px 24px;
            margin-top: 32px;
        }
        .sidebar {
            background: #f5f7fa;
            border-radius: 12px;
            box-shadow: 0 1px 6px rgba(0,0,0,0.04);
            padding: 24px 12px 24px 18px;
            margin-right: 32px;
            min-width: 180px;
        }
        .sidebar-title {
            font-size: 1.25em;
            font-weight: 700;
            color: #003366;
            margin-bottom: 18px;
            border-bottom: 3px solid #003366;
            padding-bottom: 6px;
            letter-spacing: 1px;
        }
        .member-title-area {
            border-bottom: 4px solid #003366;
            margin-bottom: 24px;
            padding-bottom: 8px;
        }
        .member-title {
            font-size: 2em;
            font-weight: 600;
            color: #003366;
            margin: 0;
            background: none;
            border: none;
            padding: 0;
        }
        .main-content { display: flex; max-width: 1200px; margin: 40px auto; padding: 0 20px; gap: 40px; }
        .sidebar {
            width: 170px;
            min-width: 140px;
            max-width: 200px;
            background: transparent;
            border-radius: 0;
            box-shadow: none;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            height: auto;
            min-height: unset;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 24px 0 0 0;
            width: 100%;
        }
        .sidebar li {
            padding: 8px 0 8px 18px;
            margin-bottom: 8px;
            background: #e3eaf3;
            color: #1a3557;
            font-size: 0.98em;
            border-left: 4px solid #b0c4de;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: none;
            transition: background .2s, color .2s, border .2s;
        }
        .sidebar li.active, .sidebar li:hover {
            background: #c7d6ea;
            color: #005bac;
            border-left: 4px solid #005bac;
        }
        .member-area { flex: 1; }
        .member-title { font-size: 2em; font-weight: 600; color: #003366; margin-bottom: 24px; border-bottom: 4px solid #003366; padding-bottom: 8px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 32px; }
        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 24px rgba(0,91,172,0.18), 0 1.5px 6px rgba(0,0,0,0.08);
            display: flex;
            gap: 20px;
            align-items: flex-start;
            padding: 24px 20px 20px 20px;
            transition: box-shadow .2s, transform .2s;
            border: 1.5px solid #e0e8f3;
            margin-top: 0;
        }
        .card:hover {
            box-shadow: 0 10px 32px rgba(0,91,172,0.22), 0 2px 8px rgba(0,0,0,0.10);
            transform: translateY(-2px) scale(1.025);
            border-color: #b0c4de;
        }
        .card img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            background: #eee;
            box-shadow: 0 2px 8px rgba(0,91,172,0.10);
        }
        .main-nav .dropdown {
            position: relative;
        }
        .main-nav .dropdown-menu {
            display: none;
            position: absolute;
            left: 0;
            top: 100%;
            min-width: 160px;
            background: #fff;
            box-shadow: 0 4px 16px rgba(0,0,0,0.10);
            border-radius: 8px;
            z-index: 100;
            padding: 8px 0;
            margin: 0;
            list-style: none;
        }
        .main-nav .dropdown-menu li {
            padding: 8px 20px 8px 16px;
            color: #003366;
            font-size: 0.92em;
            cursor: pointer;
            transition: background .18s, color .18s;
            border: none;
            background: none;
        }
        .main-nav .dropdown-menu li.active,
        .main-nav .dropdown-menu li:hover {
            background: #e3eaf3;
            color: #005bac;
        }
        .main-nav .dropdown:hover .dropdown-menu,
        .main-nav .dropdown.open .dropdown-menu {
            display: block;
        }
        .main-nav .dropdown > a {
            cursor: pointer;
        }
        @media (max-width: 900px) {
            .main-content { flex-direction: column; padding: 12px 4px; }
            .sidebar { margin-right: 0; margin-bottom: 18px; }
            .main-nav .dropdown-menu {
                left: auto;
                right: 0;
            }
        }
    </style>
</head>
<body>
    <header class="header-top">
        <div class="header-inner">
            <div class="logo">
                <img src="title.png" alt="ECS Logo" />
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="#">招生/ADMISSION</a></li>
                    <li><a href="#">關於本系</a></li>
                    <li><a href="#">課程介紹</a></li>
                    <li class="dropdown">
                        <a href="teacher_map_v3.php" class="active" id="memberDropdown">系所成員</a>
                        <ul class="dropdown-menu" id="dropdownMenu">
                            <?php foreach($categories as $catName => $catValue): ?>
                                <?php if($catValue !== ''): ?>
                                    <li data-cat="<?php echo $catValue; ?>" onclick="window.location.href='teacher_map_v3.php?cat=<?php echo urlencode($catValue); ?>'"<?php if($selected===$catValue)echo' class="active"';?>><?php echo $catName; ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <li><a href="#">訊息與公告</a></li>
                    <li><a href="#">問卷回饋</a></li>
                </ul>
            </nav>
            <a href="login.php" class="btn-english">English</a>
        </div>
    </header>
    <div class="main-content">
        <aside class="sidebar">
            <div class="sidebar-title">系所成員</div>
            <ul id="category-list">
                <?php foreach($categories as $catName => $catValue): ?>
                    <?php if($catValue !== ''): ?>
                        <li data-cat="<?php echo $catValue; ?>"<?php if($selected===$catValue)echo' class="active"';?>><?php echo $catName; ?></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </aside>
        <section class="member-area">
            <div class="grid">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): 
                        $id   = htmlspecialchars($row['pro_ID'], ENT_QUOTES, 'UTF-8');
                        $name = htmlspecialchars($row['name'],    ENT_QUOTES, 'UTF-8');
                        $position = htmlspecialchars($row['position'],ENT_QUOTES, 'UTF-8');
                        $photo = !empty($row['photo']) ? htmlspecialchars($row['photo'], ENT_QUOTES, 'UTF-8') : 'uploads/none.jpg';
                        $phone = htmlspecialchars($row['phone'] ?? '-', ENT_QUOTES, 'UTF-8');
                        $email = htmlspecialchars($row['email'] ?? '-', ENT_QUOTES, 'UTF-8');
                        // 查詢所有專長
                        $exp_sql = "SELECT item FROM expertise WHERE pro_ID = '" . $conn->real_escape_string($row['pro_ID']) . "'";
                        $exp_result = $conn->query($exp_sql);
                        $expertise_arr = [];
                        if ($exp_result && $exp_result->num_rows > 0) {
                            while ($exp_row = $exp_result->fetch_assoc()) {
                                // 只保留中文字（移除英數符號）
                                $zh = preg_replace('/[^\x{4e00}-\x{9fa5}]/u', '', $exp_row['item']);
                                if ($zh !== '') $expertise_arr[] = $zh;
                            }
                        }
                        $expertise = $expertise_arr ? implode(' ', $expertise_arr) : '-';
                    ?>
                    <div class="card">
                        <a href="index_v3.php?id=<?php echo $id; ?>">
                            <img src="<?php echo $photo; ?>" alt="<?php echo $name; ?>">
                        </a>
                        <div class="card-info">
                            <div class="card-name"><a href="index_v3.php?id=<?php echo $id; ?>"><?php echo $name; ?></a></div>
                            <div class="card-position"><?php echo $position; ?></div>
                            <div class="card-contact">分機：<?php echo $phone; ?></div>
                            <div class="card-contact">信箱：<?php echo $email; ?></div>
                            <div class="card-expertise">專長：<?php echo $expertise; ?></div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="text-align:center; color:#666;">目前尚無教授資料。</p>
                <?php endif; ?>
                <?php if ($result): $result->free(); endif; ?>
            </div>
        </section>
    </div>
    <script>
    // 下拉選單互動
    const dropdown = document.querySelector('.main-nav .dropdown');
    const dropdownMenu = document.getElementById('dropdownMenu');
    const dropdownBtn = document.getElementById('memberDropdown');
    if(dropdown && dropdownMenu && dropdownBtn) {
        dropdownBtn.removeEventListener && dropdownBtn.removeEventListener('click', function(){});
        dropdown.classList.remove('open');
        // 仍需點擊外部時收合（for mobile），但 hover 為主
    }
    // 左側快捷鍵功能維持
    document.querySelectorAll('#category-list li').forEach(function(li) {
        li.addEventListener('click', function() {
            var cat = this.getAttribute('data-cat');
            window.location.href = 'teacher_map_v3.php?cat=' + encodeURIComponent(cat);
        });
    });
    // 取消上方「系所成員」點擊展開，改為直接顯示全部
    const navLinks = document.querySelectorAll('.main-nav a');
    navLinks.forEach(function(link) {
        if(link.textContent.includes('系所成員')) {
            link.addEventListener('click', function(e) {
                // 直接導向 teacher_map_v3.php 顯示全部
                window.location.href = 'teacher_map_v3.php';
            });
        }
    });
    // 下拉選單點選分類直接跳轉
    if(dropdownMenu) {
        dropdownMenu.querySelectorAll('li').forEach(function(li) {
            li.addEventListener('click', function(e) {
                e.stopPropagation(); // 防止冒泡導致下拉收合
                // 已由 onclick 實現跳轉，這裡可省略
            });
        });
    }
    </script>
</body>
</html>
