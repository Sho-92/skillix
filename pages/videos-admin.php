<?php
session_start(); // セッションを開始

require_once '../includes/functions.php'; 
// 本社専用ページへのアクセス確認(functions.php)
checkAccess('admin'); // 本社のみアクセス許可

$page = 'videos-admin'; // ページ名設定
$pageHasSidebar = true; // サイドバーが必要なページ

require_once('../includes/db.php');

// ユーザー一覧取得
$sql = "SELECT * FROM users";
$stmt = $pdo->query($sql);
$users = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<main class="main">
  <div class="dashboard-content">

    <?php include '../pages/sidebar.php'; ?>
    
    <div class="main-content">
    <h2>動画管理・編集</h2>
      <section class="admin-video-list">
        <ul id="adminVideoList">
          <!-- JavaScriptでリストを動的に追加 -->
        </ul>
      </section>
    </div>
  </div>
  <!-- 編集用モーダル -->
  <div id="editVideoModal" class="modal" style="display: none;">
    <div class="modal-content">
      <h3>動画編集</h3>
      <form id="editVideoForm">
        <input type="hidden" id="edit_video_id">
        <label for="edit_video_title">タイトル</label>
        <input type="text" id="edit_video_title" required>
        <label for="edit_video_url">YouTube URL</label>
        <input type="text" id="edit_video_url" required>
        <label for="edit_video_category">カテゴリー</label>
        <select id="edit_video_category">
          <option value="">-- カテゴリーを選択 --</option>
          <!-- 動的にカテゴリーが追加される -->
        </select>
        <button type="submit">変更を保存</button>
      </form>
      <button type="button" class="modal-close-btn">閉じる</button>
    </div>
  </div>

  <!-- 削除確認モーダル -->
  <div id="deleteVideoModal" style="display: none;">
    <div class="modal-content">
        <h3>この動画を削除してもよろしいですか?</h3>
        <div class="modal-confirm-btn">
          <button type="button" class="confirm-delete-btn" id="confirmDeleteBtn">Yes</button>
          <button type="button" class="modal-close-btn">No</button>
        </div>
    </div>
  </div>
</main>

<?php include '../includes/footer.php'; ?>
