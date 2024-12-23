<?php
session_start(); // セッションを開始

require_once('../includes/db.php'); // DB接続

// ログインフォームからデータを受け取る
if (isset($_POST['login'])) {
    $employee_id = $_POST['employee_id'];
    $password = $_POST['password'];

    // 入力チェック
    if (empty($employee_id) || empty($password)) {
      echo "Please fill in both Employee ID and Password.";
      exit;
    }

    // データベースからユーザー情報を取得
    $sql = "SELECT * FROM users WHERE employee_id = :employee_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':employee_id', $employee_id);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // ユーザーが見つかるか確認
    if ($user &&  $password === $user['password']) { // 単純な文字列比較/パスワードは最終ハッシュ化する
        // セッションIDを再生成（セッション固定攻撃を防ぐ）
        session_regenerate_id(true);
        
        // パスワードが一致した場合、セッションにユーザー情報を保存
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['employee_id'] = $user['employee_id'];
        $_SESSION['role'] = $user['role'];

        // 役割に応じてページをリダイレクト
        if ($user['role'] === 'admin') {
            header('Location: ../pages/admin.php'); // 本社用ページ
        } else {
            header('Location: ../pages/staff.php'); // スタッフ用ページ
        }
        exit;
    } else {
        // パスワードが一致しない場合
        echo "Invalid login credentials."; // 攻撃者がIDまたはパスワードのどちらが間違っているかを特定しやすくなので、エラーメッセージは具体性を抑える方が安全。
    }
}
?>
