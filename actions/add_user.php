<?php
// このファイルは新しいユーザーをデータベースに追加するための処理を行います。
// フォームから送信されたデータを受け取り、パスワードのハッシュ化、
// データベースへの挿入を行い、処理が成功すればダッシュボードページにリダイレクトします。

require_once('../includes/db.php'); // データベース接続

// ユーザー追加処理
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
  // フォームからのデータ受け取り  
  $username = $_POST['username'];
    $employee_id = $_POST['employee_id'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $role = $_POST['role'];

    // パスワードが一致するか確認
    if ($password !== $password_confirm) {
      echo "Passwords do not match!";
      exit; // 一致しない場合は処理を中断
    }    

    // パスワードをハッシュ化
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    // データベースに新しいユーザーを追加
    $sql = "INSERT INTO users (username, employee_id, password, role) VALUES (:username, :employee_id, :password, :role)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':employee_id', $employee_id);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':role', $role);

    if ($stmt->execute()) {
        header('Location: ../pages/dashboard.php'); // 成功したらダッシュボードにリダイレクト
        exit;
    } else {
        echo "Error occurred!";
    }
}
?>
