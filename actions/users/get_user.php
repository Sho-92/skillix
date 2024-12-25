<?php
// このファイルは特定のユーザーの情報を取得するための処理を行います。
// ユーザーIDを基に、そのユーザーの情報をデータベースから取得し、
// JSON形式で返します。

require_once('../../includes/db.php');

// ユーザーIDを受け取る
$user_id = $_GET['user_id'];  // URLパラメータから受け取る

// SQLクエリでユーザー情報を取得
$sql = "SELECT * FROM users WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();

// 結果を返す
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo json_encode($user);  // JSON形式でユーザー情報を返す
} else {
    echo json_encode(["error" => "User not found"]);
}
?>
