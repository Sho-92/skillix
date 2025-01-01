<?php
require_once('../../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // JSONデータを受け取る
  $inputData = json_decode(file_get_contents('php://input'), true);

  // もしJSONデータが無効であればエラーメッセージを返す
  if (!$inputData) {
      echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
      exit;
  }

  // フォームからのデータ受け取り  
  $username = $inputData['username'] ?? '';
  $employee_id = $inputData['employee_id'] ?? '';
  $password = $inputData['password'] ?? '';
  $password_confirm = $inputData['password_confirm'] ?? '';
  $role = $inputData['role'] ?? '';

  // 必須項目のチェック
  if (empty($username) || empty($employee_id) || empty($password) || empty($password_confirm) || empty($role)) {
      echo json_encode(['success' => false, 'message' => 'All fields are required']);
      exit;
  }

  // パスワードが一致するか確認
  if ($password !== $password_confirm) {
      echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
      exit;
  }

  // パスワードをハッシュ化
  $hashed_password = password_hash($password, PASSWORD_DEFAULT);

  try {
      // データベースに新しいユーザーを追加
      $sql = "INSERT INTO users (username, employee_id, password, role) VALUES (:username, :employee_id, :password, :role)";
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':username', $username);
      $stmt->bindParam(':employee_id', $employee_id);
      $stmt->bindParam(':password', $hashed_password);
      $stmt->bindParam(':role', $role);

      // SQL実行
      if ($stmt->execute()) {
          echo json_encode(['success' => true, 'message' => 'User added successfully']);
      } else {
          echo json_encode(['success' => false, 'message' => 'Error occurred while adding user']);
      }
  } catch (Exception $e) {
      echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
  }
}
?>