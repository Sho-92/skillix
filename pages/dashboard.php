<?php
session_start(); // セッションを開始

require_once '../includes/functions.php'; 
// 本社専用ページへのアクセス確認(functions.php)
checkAccess('admin'); // 本社のみアクセス許可

$page = 'dashboard'; // ページ名設定

require_once('../includes/db.php');

// ユーザー一覧取得
$sql = "SELECT * FROM users";
$stmt = $pdo->query($sql);
$users = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<main class="main">
  <h2>User Management</h2>

  <a href="staff.php">スタッフ画面表示</a>
  <a href="admin.php">管理画面に戻る</a></li>

  <!-- 新規ユーザー追加フォーム -->
  <h3>Add New User</h3>
  <form method="post" action="../actions/add_user.php">
    <input type="text" name="username" placeholder="Username" required>
    <input type="text" name="employee_id" placeholder="Employee ID" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="password" name="password_confirm" placeholder="Confirm Password" required>
    <select name="role">
        <option value="admin">admin</option>
        <option value="staff">Staff</option>
    </select>
    <button type="submit" name="add_user">Add User</button>
  </form>

  <!-- ユーザーリスト -->
  <table>
    <tr><th>Username</th><th>Employee ID</th><th>Role</th><th>Actions</th></tr>
    <?php foreach ($users as $user): ?>
      <tr>
          <td><?php echo htmlspecialchars($user['username']); ?></td>
          <td><?php echo htmlspecialchars($user['employee_id']); ?></td>
          <td><?php echo htmlspecialchars($user['role']); ?></td>
          <td>
              <button onclick="editUser(<?php echo $user['id']; ?>)">編集</button>
              <button onclick="deleteUser(<?php echo $user['id']; ?>)">削除</button>
          </td>
      </tr>
    <?php endforeach; ?>
  </table>

  <!-- 編集用モーダル -->
  <div id="editModal" style="display: none;">
    <div>
      <h3>Edit User</h3>
      <form id="editUserForm">
        <input type="hidden" name="user_id" id="edit_user_id">
        <input type="text" name="username" id="edit_username" placeholder="Username" required>
        <input type="text" name="employee_id" id="edit_employee_id" placeholder="Employee ID" required>
        <select name="role" id="edit_role">
<<<<<<< HEAD
            <option value="admin">admin</option>
=======
            <option value="head_office">Head Office</option>
>>>>>>> e9124e95980e996ff16afd641e403624f738df22
            <option value="staff">Staff</option>
        </select>
        <button type="submit" id="saveEditButton">Save Changes</button>
      </form>
      <button onclick="closeModal('editModal')">Close</button>
    </div>
  </div>

  <!-- 削除確認モーダル -->
  <div id="deleteModal" style="display: none;">
    <div>
        <h3>Are you sure you want to delete this user?</h3>
        <button onclick="confirmDelete()">Yes</button>
        <button onclick="closeModal('deleteModal')">No</button>
    </div>
  </div>
</main>

<?php include '../includes/footer.php'; ?>
