<?php
session_start(); // セッションを開始

require_once '../includes/functions.php'; 
// 本社専用ページへのアクセス確認(functions.php)
checkAccess('admin'); // 本社のみアクセス許可

$page = 'account'; // ページ名設定
$pageHasSidebar = true; // サイドバーが必要なページ

require_once('../includes/db.php');

// ユーザー一覧取得
$sql = "SELECT * FROM users";
$stmt = $pdo->query($sql);
$users = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<main class="main">
  <h2>ユーザー管理</h2>
  <div class="input-content">
    <!-- 新規ユーザー追加フォーム -->
    <form method="post" action="../actions/users/add_user.php">
      <input type="text" name="username" placeholder="ユーザー名" required>
      <input type="text" name="employee_id" placeholder="従業員ID" required>
      <input type="password" name="password" placeholder="パスワード" required>
      <input type="password" name="password_confirm" placeholder="確認用パスワード" required>
      <select name="role">
          <option value="admin">admin</option>
          <option value="staff">Staff</option>
      </select>
      <button type="submit" name="add_user">追加</button>
    </form>
  </div>
  <div class="dashboard-content">

    <?php include '../pages/sidebar.php'; ?>
    
    <div class="main-content">
      <!-- ユーザーリスト -->
      <table>
        <tr><th>ユーザー名</th><th>従業員ID</th><th>役職</th><th>操作</th></tr>
        <?php foreach ($users as $user): ?>
          <tr>
              <td><?php echo htmlspecialchars($user['username']); ?></td>
              <td><?php echo htmlspecialchars($user['employee_id']); ?></td>
              <td><?php echo htmlspecialchars($user['role']); ?></td>
              <td class="actions">
                  <button onclick="editUser(<?php echo $user['id']; ?>)">編集</button>
                  <button onclick="deleteUser(<?php echo $user['id']; ?>)">削除</button>
              </td>
          </tr>
        <?php endforeach; ?>
      </table>
    <a href="admin.php">管理画面に戻る</a>
  </div>
  
  <!-- 編集用モーダル -->
  <div id="editModal" style="display: none;">
    <div class="modal-content">
      <h3>ユーザーの編集</h3>
      <form id="editUserForm">
        <input type="hidden" name="user_id" id="edit_user_id">
        <input type="text" name="username" id="edit_username" placeholder="Username" required>
        <input type="text" name="employee_id" id="edit_employee_id" placeholder="Employee ID" required>
        <select name="role" id="edit_role">
            <option value="admin">admin</option>
            <option value="staff">Staff</option>
        </select>
        <button type="submit" id="saveEditButton">変更を保存</button>
      </form>
      <button onclick="closeModal('editModal')" class="modal-close-btn">閉じる</button>
    </div>
  </div>

  <!-- 削除確認モーダル -->
  <div id="deleteModal" style="display: none;">
    <div class="modal-content">
        <h3>このユーザーを削除してもよろしいですか?</h3>
        <div class="modal-confirm-btn">
          <button onclick="confirmDelete()">Yes</button>
          <button onclick="closeModal('deleteModal')">No</button>
        </div>
    </div>
  </div>
</main>

<?php include '../includes/footer.php'; ?>
