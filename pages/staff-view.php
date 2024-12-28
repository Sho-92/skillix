<?php
session_start(); // セッションを開始

require_once '../includes/functions.php'; 
// 本社専用ページへのアクセス確認(functions.php)
checkAccess('admin'); // 本社のみアクセス許可

$page = 'staff-view'; // ページ名設定
$pageHasSidebar = true; // サイドバーが必要なページ

require_once('../includes/db.php');

?>

<?php include '../includes/header.php'; ?>

<main class="main">
  <div class="dashboard-content">

    <?php include '../pages/sidebar.php'; ?>
    
    <div class="main-content">
    <h1>スタッフページのプレビュー</h1>
      <p>以下のiframeでスタッフ用ページをリアルタイムでプレビューできます。</p>

      <div class="iframe-container">
        <iframe 
          src="../pages/staff.php" 
          class="preview-iframe"
          frameborder="0" 
          width="100%" 
          height="800px">
        </iframe>
      </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
