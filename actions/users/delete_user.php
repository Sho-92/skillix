<?php
// このファイルはユーザーを削除するための処理を行います。
// 受け取ったユーザーIDに基づいて、指定されたユーザーをデータベースから削除します。

require_once('../../includes/db.php');

// 入力されたJSONを取得
$inputData = json_decode(file_get_contents('php://input'), true);

// JSONが正しくデコードされた場合
if ($inputData === null) {
    // JSONデコードに失敗した場合のエラーハンドリング
    echo json_encode(["error" => "Invalid JSON data"]);
    exit;
}

if (isset($inputData['user_id'])) {
    $user_id = $inputData['user_id'];

    // ユーザーをデータベースから削除
    $sql = "DELETE FROM users WHERE id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);

    // 実行して成功した場合
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "User deleted successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error occurred while deleting user!"]);
    }
} else {
    // user_idが提供されていない場合
    echo json_encode(["success" => false, "error" => "No user_id provided!"]);
}
?>
