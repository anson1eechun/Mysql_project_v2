<?php
require_once 'config.php';

if (!isset($_SESSION["username"])) {
    header("Location: login_v2.php");
    exit;
}

$result = $conn->query("SELECT * FROM professor");

echo "<h2>系上教授列表</h2>";
echo "<p>登入帳號：" . htmlspecialchars($_SESSION["username"]) . "</p>";

echo "<table border='1' cellpadding='5'>
<tr><th>姓名</th><th>職位</th><th>Email</th><th>辦公室</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>" . htmlspecialchars($row["name"]) . "</td>
        <td>" . htmlspecialchars($row["position"]) . "</td>
        <td>" . htmlspecialchars($row["email"]) . "</td>
        <td>" . htmlspecialchars($row["office"]) . "</td>
    </tr>";
}
echo "</table>";
?>