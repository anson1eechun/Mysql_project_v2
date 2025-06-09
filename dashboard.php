<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

$mysqli = new mysqli("localhost", "root", "", "your_database");
$result = $mysqli->query("SELECT * FROM professors");

echo "<h2>系上教授列表</h2>";
echo "<p>登入帳號：" . htmlspecialchars($_SESSION["username"]) . "</p>";

echo "<table border='1' cellpadding='5'>
<tr><th>姓名</th><th>系所</th><th>Email</th><th>辦公室</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>" . htmlspecialchars($row["name"]) . "</td>
        <td>" . htmlspecialchars($row["department"]) . "</td>
        <td>" . htmlspecialchars($row["email"]) . "</td>
        <td>" . htmlspecialchars($row["office"]) . "</td>
    </tr>";
}
echo "</table>";
?>