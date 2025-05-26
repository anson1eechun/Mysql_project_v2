<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>登錄介面</title>
    <style>
        body {
            margin: 100px auto 0 auto; 
            width: 300px;
            padding: 50px;
            border-radius: 10px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            width: 107%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #5cb85c;
            color: white;
            font-size: 18px;
        }
        .error {
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <h2>後台登錄</h2>
    <form action=" check.php" method="post">
        <label for="username">帳號:</label>
        <input type="text" id="username" name="username" required>
        <label for="password">密碼:</label>
        <input type="password" id="password" name="password" required>
        <input type="submit" value="登陸">
    </form>
    <?php
    if (isset($_GET['error'])) {
        echo '<div class="error">帳號或密碼錯誤</div>';
    }
    ?>
</body>