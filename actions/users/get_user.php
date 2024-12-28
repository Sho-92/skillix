<?php
// このファイルは特定のユーザーの情報を取得するための処理を行います。
// ユーザーIDを基に、そのユーザーの情報をデータベースから取得し、
// JSON形式で返します。

require_once('../../includes/db.php');

// SQLクエリで全ユーザー情報を取得
$sql = "SELECT id, username, employee_id, password, role FROM users";
$stmt = $pdo->prepare($sql);
$stmt->execute();

// 結果を取得
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ユーザー情報があればJSON形式で返す
if ($users) {
    foreach ($users as &$user) {
        $user['password'] = '******'; // パスワードを隠す
    }
    echo json_encode($users);  // JSON形式でユーザー情報を返す
} else {
    echo json_encode(["error" => "ユーザーが見つかりませんでした"]);
}
?>
