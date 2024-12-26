<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Synclyeeは社員教育用のEラーニングプラットフォームです。">
  <title>Synclyee (シンクリー) - Admin Panel</title>
  <link rel="stylesheet" href="../assets/css/style.css"> <!-- グローバルスタイルの読み込み -->
  <?php
    if (isset($page)) {
      if ($page === 'admin') {
        echo '<link rel="stylesheet" href="../assets/css/admin.css">';
      } elseif ($page === 'staff') {
        echo '<link rel="stylesheet" href="../assets/css/staff.css">';
      } elseif ($page === 'account') {
        echo '<link rel="stylesheet" href="../assets/css/account.css">';
      }
    }
  ?>

  <!-- サイドバー用CSS（ページにサイドバーが必要な場合） -->
  <?php if ($pageHasSidebar): ?>
    <link rel="stylesheet" href="../assets/css/sidebar.css">
  <?php endif; ?>
  
</head>
<body>
  <header class="header">
    <h1>Synclyee</h1>
    <nav>
      <ul>
        <li><a href="admin.php">本社</a></li>
        <li><a href="staff.php">スタッフ</a></li>
      </ul>
    </nav>  
    <button class="logout">ログアウト</button>
  </header>
