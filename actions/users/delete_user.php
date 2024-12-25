<?php
// このファイルはユーザーを削除するための処理を行います。
// 受け取ったユーザーIDに基づいて、指定されたユーザーをデータベースから削除します。

require_once('../../includes/db.php');

// ユーザーIDをPOSTで受け取る
if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    // ユーザーをデータベースから削除
    $sql = "DELETE FROM users WHERE id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);

    // 実行して成功した場合
    if ($stmt->execute()) {
        echo "User deleted successfully!";
    } else {
        echo "Error occurred while deleting user!";
    }
}
?>
