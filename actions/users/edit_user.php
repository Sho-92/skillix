<?php

require_once('../../includes/db.php');

// JSON形式で受け取ったデータを処理
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $user_id = $data['user_id'];
    $username = $data['username'];
    $employee_id = $data['employee_id'];
    $role = $data['role'];
    $password = isset($data['password']) ? $data['password'] : null; // パスワードがあれば取得

    // パスワードが空でない場合はハッシュ化して更新
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET username = :username, employee_id = :employee_id, role = :role, password = :password WHERE id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':password', $hashedPassword);
    } else {
        $sql = "UPDATE users SET username = :username, employee_id = :employee_id, role = :role WHERE id = :user_id";
        $stmt = $pdo->prepare($sql);
    }

    // その他のパラメータをバインド
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':employee_id', $employee_id);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':user_id', $user_id);

    // SQL実行
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'User updated successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating user!']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
}

?>
