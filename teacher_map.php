<?php
require_once 'config.php';

$sql = "SELECT * FROM professor";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8">
    <title>老師總覽頁</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans TC', sans-serif;
            background: linear-gradient(to bottom right, #f0f4f8, #ffffff);
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        h1 {
            text-align: center;
            font-size: 36px;
            color: #003366;
            margin-bottom: 40px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 20px;
            text-align: center;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 15px;
        }

        .card h3 {
            font-size: 20px;
            color: #005bac;
            margin: 10px 0;
        }

        .card p {
            font-size: 14px;
            color: #555;
            margin-bottom: 10px;
        }

        .card a {
            display: inline-block;
            padding: 8px 16px;
            background-color: #005bac;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
        }

        .card a:hover {
            background-color: #003f80;
        }
    </style>
</head>
<body>
    <h1>教授介紹總覽</h1>
    <div class="grid">
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $pro_ID = htmlspecialchars($row['pro_ID']);
                $name = htmlspecialchars($row['name']);
                $title = htmlspecialchars($row['title']);
                $intro = mb_substr(strip_tags($row['introduction']), 0, 50, 'UTF-8') . '...';
                $photo = htmlspecialchars($row['photo']);

                echo '<div class="card">';
                echo "<img src='uploads/{$photo}' alt='{$name}'>";
                echo "<h3>{$name}</h3>";
                echo "<p>{$title}</p>";
                echo "<p>{$intro}</p>";
                echo "<a href='profile.php?pro_ID={$pro_ID}'>查看介紹</a>";
                echo '</div>';
            }
        } else {
            echo "<p>目前尚無教授資料。</p>";
        }
        ?>
    </div>
</body>
</html>