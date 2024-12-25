<?php
// このファイルは既存のユーザー情報を更新するための処理を行います。
// ユーザーIDを基に、フォームから送信されたデータをデータベースに更新します。

require_once('../../includes/db.php');

// ユーザーIDと新しい情報をPOSTで受け取る
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $employee_id = $_POST['employee_id'];
    $role = $_POST['role'];

    // ユーザー情報を更新
    $sql = "UPDATE users SET username = :username, employee_id = :employee_id, role = :role WHERE id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':employee_id', $employee_id);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':user_id', $user_id);

    if ($stmt->execute()) {
        echo "User updated successfully!";
    } else {
        echo "Error updating user!";
    }
}
?>

