<?php
// 共通のログイン・権限チェック関数 (admin/dashboard/staff)
function checkAccess($roleRequired = null) {
  if (!isset($_SESSION['user_id'])) {
      header('Location: ../index.php'); // ログインページへリダイレクト
      exit;
  }

  // ユーザーの役割を確認
  if ($roleRequired && $_SESSION['role'] !== $roleRequired) {
      // 役割が異なる場合はリダイレクト
      $redirectPage = ($roleRequired === 'head_office') ? 'staff.php' : '../index.php';
      header("Location: $redirectPage");
      exit;
  }

  // セッション有効時間（30分）
  $inactive = 30 * 60;
  if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $inactive)) {
      session_unset();
      session_destroy();
      header('Location: ../index.php'); // ログインページへリダイレクト
      exit;
  }

  $_SESSION['last_activity'] = time(); // 最終アクティビティを更新
}

?>